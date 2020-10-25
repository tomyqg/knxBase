/**
 * Copyright (c) 2020-2020 Karl-Heinz Welter
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.  IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
/**
 *
 * hdleasun.c
 *
 * handler for Easun Voltaic Power Converter
 *
 * Revision history
 *
 * Date		Rev.	Who	what
 * -----------------------------------------------------------------------------
 * 2020-10-08	PA1	khw	inception;
 * 2020-10-20	PA2	khw	signals SIGUSR1 and SIGUSR2 used to increase/reset
 *				tracing mode towards syslog;
 *				added native MQTT calls;
 *
 */
#include	<stdio.h>
#include	<stdlib.h>
#include	<stdbool.h>
#include	<stdint.h>
#include	<strings.h>
#include	<unistd.h>
#include	<time.h>
#include	<math.h>
#include	<syslog.h>
#include	<sys/types.h>
#include	<sys/ipc.h>
#include	<sys/shm.h>
#include	<sys/msg.h>
#include	<sys/sem.h>
#include	<sys/signal.h>
#include        <mysql.h>
#include	<mosquitto.h>

#include	"debug.h"
#include	"knxlog.h"
#include	"tty.h"
#include	"eib.h"
#include	"nodeinfo.h"
#include	"mylib.h"
#include	"hdleasun.h"
#include	"inilib.h"
#include	"crc16lib.h"

/**
 *
 */
#define	MAX_SLEEP	1
#define	SLEEP_TIME	1

/**
 *
 */
extern	void	help() ;
extern	void	logit( char *_fmt, ...) ;

/**
 *
 */
char	progName[64]  ;
pid_t	ownPID ;
int	recLevel	=	0 ;;

/**
 *
 */
int	cfgQueueKey	=	10031 ;
int	cfgSenderAddr	=	1 ;
char	cfgSerial[64] ;				// serial port, e.g. /dev/tty00
int	cfgStartupDelay	=	5 ;		// default startup delay 5 seconds
int	cfgDebugLevel	=	5 ;		// default startup delay 5 seconds
int	cfgDaemon	=	true ;		// default startup delay 5 seconds
int	cfgTrace	=	false ;
char	cfgMqttHost[32] ;
char	cfgMqttUser[32] ;
char	cfgMqttPasswd[32] ;
char	cfgMqttCACert[64] ;
char	cfgMqttKey[64] ;
char	cfgMqttCert[64] ;
char	cfgMqttPrefix[64] ;
char	cfgMqttFilter[64] ;
bool	cfgMqttForwardAll	=	false ;
bool	cfgMqttForwardBaos	=	false ;
char	dbHost[64]	=	"*" ;
char	dbName[64]	=	"*" ;
char	dbUser[64]	=	"*" ;
char	dbPassword[64]	=	"*" ;
struct	mosquitto	*mosq ;


/**
 *
 */
void	sigHandlerTraceInc( int _sig) {
	logit(  "signal %d received ...", _sig) ;
	cfgDebugLevel	=	-1 ;
	cfgTrace++ ;
	logit(  "new trace level %d ...", cfgTrace) ;
}

/**
 *
 */
void	sigHandlerTraceOff( int _sig) {
	logit(  "signal %d received ...", _sig) ;
	cfgDebugLevel	=	-1 ;
	cfgTrace	=	0 ;
	logit(  "new trace level %d ...", cfgTrace) ;
}

/**
 *
 */
void onConnect( struct mosquitto *mosq, void *obj, int result) {
	int rc = MOSQ_ERR_SUCCESS;

	if ( !result) {
		mosquitto_subscribe( mosq, NULL, "2km/#", 0);
	} else {
		fprintf( stderr, "%s\n", mosquitto_connack_string( result));
	}
}

/**
 *
 */
void onMessage( struct mosquitto *mosq, void *obj, const struct mosquitto_message *message) {
	int	val ;
	char	bufNew[128] ;
	struct mosquitto *mosq2 = (struct mosquitto *)obj;
	char	*ptr, owner[64], mainTopic[64], subTopic[64], component[64], qr[64], data[64] ;
	int	index ;
	
	if ( message->payload ==  NULL) {
		strcpy( bufNew, "") ;
	} else {
		strcpy( bufNew, message->payload) ;
	}
	if ( cfgDebugLevel > 0) {
 		printf( "MQTT Message ..... : \n") ;
 		printf( "    Topic ........ : %s \n", message->topic) ;
 		printf( "    Message ...... : %s [%s] \n", message->payload, bufNew) ;
 		printf( "    Message ...... : %s \n", message->payload) ;
	}
	
	index	=	-1 ;
	ptr	=	strtok( message->topic, "/") ;
	ptr	=	strtok( NULL, "/") ;
}

/**
 *
 */
void	connectMQTT() {
	mosquitto_lib_init() ;
	mosq	=	mosquitto_new( NULL, true, NULL) ;
	mosquitto_username_pw_set( mosq, cfgMqttUser, cfgMqttPasswd) ;
	mosquitto_connect_callback_set( mosq, onConnect) ;
	mosquitto_message_callback_set( mosq, onMessage) ;
	mosquitto_connect( mosq, cfgMqttHost, 1883, 60) ;
	mosquitto_loop_start( mosq) ;
}

/**
 *
 */
void	iniCallback( char *_block, char *_para, char *_value) {
	_debug( 1, progName, "receive ini value block/paramater/value ... : %s/%s/%s\n", _block, _para, _value) ;
	if ( strcmp( _block, "[knxglobals]") == 0) {
		if ( strcmp( _para, "queueKey") == 0) {
			cfgQueueKey	=	atoi( _value) ;
		}
	} else if ( strcmp( _block, "[knxtrace]") == 0) {			// will use same db connection as for the tracer logging
		if ( strcmp( _para , "dbHost") == 0) {
			strcpy( dbHost, _value) ;
		} else if ( strcmp( _para, "dbName") == 0) {
			strcpy( dbName, _value) ;
		} else if ( strcmp( _para, "dbUser") == 0) {
			strcpy( dbUser, _value) ;
		} else if ( strcmp( _para, "dbPassword") == 0) {
			strcpy( dbPassword, _value) ;
		}
	} else if ( strcmp( _block, "[hdleasun]") == 0) {
		if ( strcmp( _para, "senderAddr") == 0) {
			cfgSenderAddr	=	atoi( _value) ;
		} else if ( strcmp( _para, "daemon") == 0) {
			cfgDaemon	=	atoi( _value) ;
		} else if ( strcmp( _para, "dev") == 0) {
			strcpy( cfgSerial, _value) ;
		} else if ( strcmp( _para, "startupDelay") == 0) {
			cfgStartupDelay	=	atoi( _value) ;
		} else if ( strcmp( _para, "host") == 0) {
			strcpy( cfgMqttHost, _value) ;
		} else if ( strcmp( _para, "user") == 0) {
			strcpy( cfgMqttUser, _value) ;
		} else if ( strcmp( _para, "passwd") == 0) {
			strcpy( cfgMqttPasswd, _value) ;
		} else if ( strcmp( _para, "caCert") == 0) {
			strcpy( cfgMqttCACert, _value) ;
		} else if ( strcmp( _para, "key") == 0) {
			strcpy( cfgMqttKey, _value) ;
		} else if ( strcmp( _para, "cert") == 0) {
			strcpy( cfgMqttCert, _value) ;
		} else if ( strcmp( _para, "prefix") == 0) {
			strcpy( cfgMqttPrefix, _value) ;
		} else if ( strcmp( _para, "filter") == 0) {
			strcpy( cfgMqttFilter, _value) ;
			if ( strcmp( cfgMqttFilter, "none") == 0) {
				cfgMqttForwardAll	=	true ;
			} else if ( strcmp( cfgMqttFilter, "baos") == 0) {
				cfgMqttForwardBaos	=	true ;
			}
		}
	}
}

/**
 *
 */
MYSQL	*mySql ;
extern	void	mySqlQuery( char *) ;

static uint64_t getNanos(void) {
    struct timespec ts;
    timespec_get(&ts, TIME_UTC);
    return (uint64_t)ts.tv_sec * 1000000000L + ts.tv_nsec;
}

/**
 *
 */
int	main( int argc, char *argv[]) {
	easunQPIGS	dataQPIGS ;
	easunQDI	dataQDI ;
	time_t	myTime, lastTime ;
	uint64_t	sendTime, actTime, timeDiff, rcvTime ;
	char	sql[256] ;
	eibHdl	*myEIB ;
	ttyHdl	*myTTY ;
	int	myAPN	=	0 ;
	knxMsg	*msgToSnd, msgBuf ;
	int		pid, sid ;
	int		il0 ;
	int	sendingByte ;
	int	msgCount ;
	int	opt ;
	int	sleepTimer	=	0 ;
	int	incompleteMsg ;
	int	rcvdLength ;
	int	cntEscBegin, cntEscEnd, cntVersion, cntChecksum ;
	int	versionNeeded ;
	int	addEscPend ;
	int	expLength ;
	char	mode[]={'8','n','1',0};
	char	numPrefix[64] ;
	char	*dp ;
	int	dataIndex ;
	int	bdrate		=	2400 ;	// 115200 baud
	int	rcvdBytes ;
	int	len ;
	int	queryState ;
	int	commandState ;
	unsigned	char	buf ;
	unsigned	char	msgIn[256] ;
	unsigned	char	dataIn[256] ;
	unsigned	char	msgOut[256] ;
	unsigned	char	*rcvData ;
	unsigned	char	*sndData ;
	unsigned	char	*rp, *sp ;
	int	cycleCounter ;
	int	byteCnt ;
	char	iniFilename[]	=	"knx.ini" ;

	/**
	 *
	 */
	strcpy( progName, *argv) ;

	/**
	 *
	 */
	iniFromFile( iniFilename, iniCallback) ;

	/**
	 * get command line options
	 */
	while (( opt = getopt( argc, argv, "D:FQ:?")) != -1) {
		switch ( opt) {
		case	'D'	:
			cfgDebugLevel	=	atoi( optarg) ;
			break ;
		case    'F'     :
			cfgDaemon	=	false ;
			break ;
		case	'Q'	:
			cfgQueueKey	=	atoi( optarg) ;
			break ;
		case	'?'	:
			help() ;
			exit(0) ;
			break ;
		default	:
			help() ;
			exit( -1) ;
			break ;
		}
	}

	/**
	 * daemonize this process, if so required
	 */
	logit(  "starting up ...") ;
	if ( cfgDaemon) {
		logit( "daemon mode ...") ;
		pid	=	fork() ;
		if ( pid < 0) {
			logit( "pid < 0, exiting ...") ;
			exit( EXIT_FAILURE) ;
		}
		if ( pid > 0) {
			logit( "pid > 0, exiting ...") ;
			exit( EXIT_SUCCESS) ;
		}
		sid	=	setsid() ;
		if ( sid < 0) {
			logit( "sid < 0, exiting ...") ;
			exit( EXIT_FAILURE) ;
		}
		logit( "closing std i/o ...") ;
		close( STDIN_FILENO) ;
		close( STDOUT_FILENO) ;
		close( STDERR_FILENO) ;
	} else {
		logit( "daemon mode ...") ;
	}
	ownPID	=	getpid() ;
	signal( SIGUSR1, sigHandlerTraceInc) ;
	signal( SIGUSR2, sigHandlerTraceOff) ;
	sleep( cfgStartupDelay) ;

	/**
	 *
	 */
	if (( mySql = mysql_init( NULL)) == NULL) {
		_debug( 0, progName, "could not initialize MySQL access") ;
		_debug( 0, progName, "Exiting with -1");
		exit( -1) ;
	}
	if ( mysql_real_connect( mySql, dbHost, dbUser, dbPassword, dbName, 0, NULL, 0) == NULL) {
		_debug( 0, progName, "mysql error := '%s'", mysql_error( mySql)) ;
		_debug( 0, progName, "Exiting with -2");
		exit( -2) ;
	}

	/**
	 *
	 */
	connectMQTT() ;

	/**
	 *
	 */
	logit( "creating pid file ...\n") ;
	if ( createPIDFile( progName, "", ownPID)) {

		/**
		 * open communication port
		 */
		ttyPrep( cfgSerial, bdrate, mode) ;
		if (( myTTY = ttyOpen( cfgSerial, bdrate, mode)) == NULL) {
			return( 0);
		}
		myEIB	=	eibOpen( cfgSenderAddr, 0x00, cfgQueueKey, progName, APN_RDWR) ;
		myAPN	=	myEIB->apn ;

		/**
		 *
		 */
		cycleCounter	=	0 ;
		queryState	=	0 ;
		commandState	=	0 ;
		sendTime	=	getNanos() - 5000000000 ;
		while ( 1) {
			switch ( queryState) {
			case	0	:
				actTime	=	getNanos() ;
				timeDiff	=	actTime - sendTime ;
				if (( actTime - sendTime) >= 15000000000) {
					queryState	=	5 ;
				}
				break ;

			case	5	:
				switch ( commandState) {
				case	0	:
					strcpy( msgOut, "QPIGS") ;
					queryState	=	10 ;
					break ;

				case	1	:
					strcpy( msgOut, "QDI") ;
					queryState	=	10 ;
					break ;

				default	:
					commandState	=	0 ;
					break ;
				}
				len = strlen( msgOut) ;
				attachCRC( msgOut) ;
				dumpHex( msgOut, len + 3, cfgTrace) ;
				ttySendBuf( myTTY, msgOut, len + 3) ;
				queryState	=	10 ;
				sendTime	=	getNanos() ;
				break ;

			case	10	:

				/**
				 *
				 */
				rcvData	=	msgIn ;
				rp	=	msgIn ;
				byteCnt	=	0 ;
				incompleteMsg	=	true ;
				rcvTime	=	sendTime ;

				/**
				 * wait for a complete message or at max. 1 sec
				 */
				while ( incompleteMsg && ( rcvTime - sendTime) < 1000000000) {
					rcvTime	=	getNanos() ;
					if ( ttyPoll( myTTY, rp, 1)) {
//						printf( "time to char ... : %6.2f us \n", ( rcvTime - sendTime) / 1000000.0) ;
						byteCnt++ ;
						if ( *rp++ == 0x0d) {
							if ( byteCnt >= 3) {
								incompleteMsg	=	false ;
								strncpy( dataIn, msgIn, byteCnt - 3) ;
								dataIn[byteCnt - 3]	=	'\0' ;
							} else {
								queryState	=	0 ;
							}
						}
					}
					*rp	=	'\0' ;
				}
				if ( incompleteMsg) {
					sleep( 1) ;
					queryState	=	0 ;
				} else {
					if ( cfgTrace) {
						logit(  "answer received; length = %d", byteCnt) ;
					}
					dumpHex( msgIn, byteCnt, cfgTrace) ;
					queryState	=	20 ;
				}
				break ;

			case	20	:
				switch ( commandState) {
				case	0	:
					if ( byteCnt == 110) {
						analyzeQPIGS( msgIn, &dataQPIGS) ;
						if ( cfgTrace > 0) {
							dumpQPIGS( &dataQPIGS) ;
						}
					} else {
						dumpHex( msgIn, byteCnt, cfgTrace) ;
					}
					queryState	=	91 ;
					commandState	=	1 ;		// next command: QDI
					break ;

				case	1	:
					if ( byteCnt == 79) {
						analyzeQDI( msgIn, &dataQDI) ;
						if ( cfgTrace > 0) {
							dumpQDI( &dataQDI) ;
						}
					} else {
						dumpHex( msgIn, byteCnt, cfgTrace) ;
					}
					queryState	=	91 ;
					commandState	=	0 ;
					break ;

				default	:
					break ;
				}
				myTime	=	time(NULL) ;
				if (( myTime - lastTime) >= 60) {
					lastTime	=	myTime ;
					dataToDb( &dataQPIGS, &dataQDI) ;
					dataToMQTT( &dataQPIGS, &dataQDI) ;
				}
				break ;

			case	91	:
				sleep( 1) ;
				queryState	=	0 ;
				break ;

			case	92	:
				sleep( 2) ;
				queryState	=	0 ;
				break ;

			case	95	:
				sleep( 5) ;
				queryState	=	0 ;
				break ;
			}
		}
		logit( "closing tty ... ") ;
		ttyClose( myTTY) ;
		logit( "closing EIB ... ") ;
		eibClose( myEIB) ;
		logit( "deleting PID file ... ") ;
		deletePIDFile( progName, "", ownPID) ;
	} else {
		_debug( 0, progName, "process already running ...") ;
	}

	/**
	 * close virtual EIB bus
	 * close KNX Level logger
	 */

	/**
	 *
	 */
	exit( 0) ;
}

/**
 *
 */
void	sendData( unsigned int _rcvAddr, unsigned char *_value) {
	char	topic[64] ;

	/**
	 * add the logging record
	 */
	sprintf( topic, "%s%d/%d/%d",
			cfgMqttPrefix,
			(( _rcvAddr & 0xf800) >> 11) & 0x0001f,
			(( _rcvAddr & 0x0700) >>  8) & 0x00007,
			(( _rcvAddr & 0x00ff) >>  0) & 0x000ff) ;
printf( "%s \n", topic) ;
	mosquitto_publish( mosq, NULL, topic, strlen( topic), _value, 1, false) ;
}

/**
 *
 */
int	analyze( unsigned char *_msg) {
	char	*dp ;
	int	i ;

	for ( dp = strtok( _msg, " ("), i = 0 ; dp ; dp = strtok( NULL, " ("), i++) {
		if ( dp != NULL) {
			logit( "parameter ... : %s \n", dp) ;
		} else {
			logit( "ERROR --- ERROR --- ERROR --- \n") ;
		}
	}
}

/**
 *
 */
int	analyzeQPIGS( unsigned char *_msg, easunQPIGS *_data) {
	char	*dp ;
	int	i ;

	for ( dp = strtok( _msg, " ("), i = 0 ; dp ; dp = strtok( NULL, " ("), i++) {
		if ( dp != NULL) {
			switch ( i ) {
			case	0	:	_data->gridVoltage		=	atof( dp) ;	break ;
			case	1	:	_data->gridFrequency		=	atof( dp) ;	break ;
			case	2	:	_data->outputACVoltage		=	atof( dp) ;	break ;
			case	3	:	_data->outputACFrequency	=	atof( dp) ;	break ;
			case	4	:	_data->outputPowerApparent	=	atof( dp) ;	break ;
			case	5	:	_data->outputPowerActive	=	atof( dp) ;	break ;
			case	6	:	_data->loadPercent		=	atof( dp) ;	break ;
			case	7	:	_data->busVoltage		=	atof( dp) ;	break ;
			case	8	:	_data->batteryVoltage		=	atof( dp) ;	break ;
			case	9	:	_data->batteryChargingCurrent	=	atof( dp) ;	break ;
			case	10	:	_data->batteryCapacity		=	atof( dp) ;	break ;
			case	11	:	_data->inverterHeatsinkTemp	=	atof( dp) ;	break ;
			case	12	:	_data->pvInputCurrent		=	atof( dp) ;	break ;
			case	13	:	_data->pvInputVoltage		=	atof( dp) ;	break ;
			case	14	:	_data->batterySCCVoltage	=	atof( dp) ;	break ;
			case	15	:	_data->batteryDischargeCurrent	=	atof( dp) ;	break ;
			}
		} else {
			logit( "ERROR --- ERROR --- ERROR ---") ;
		}
	}
/*
    var statusstring = generalstatus[17].split("")
    if (statusstring[0] == 0) {
      debug_log("    add SBU priority version: no")
    } else if (statusstring[1] == 1) {
      debug_log("    add SBU priority version: yes")
    }

    if (statusstring[1] == 0) {
      debug_log("    configuration status unchanged")
    } else if (statusstring[1] == 1) {
      debug_log("    configuration status changed")
    }

    if (statusstring[2] == 1) {
      debug_log("    SCC firmware version Updated")
    } else if (statusstring[2] == 0) {
      debug_log("    SCC firmware version unchanged")
    }
    if (statusstring[3] == 0) {
      debug_log("    Load Off")
      inverterData.inverter.loadstatus = "Load Off"
    } else if (statusstring[3] == 0) {
      debug_log("    Load On")
      inverterData.inverter.loadstatus = "Load On"
    }
    if (statusstring[4] == 1) {
      debug_log("    Float Charge", statusstring[4])
    } else if (statusstring[4] == 0) {
      debug_log("    Float Charge", statusstring[4])
    }

    if (statusstring[5] + statusstring[6] + statusstring[7] == "000") {
      debug_log("    Charge: none")
      inverterData.battery.chargemode.scc = false;
      inverterData.battery.chargemode.ac = false;
    }
    if (statusstring[5] + statusstring[6] + statusstring[7] == "110") {
      debug_log("    Charge: scc")
      inverterData.battery.chargemode.scc = true;
      inverterData.battery.chargemode.ac = false;
    }
    if (statusstring[5] + statusstring[6] + statusstring[7] == "101") {
      debug_log("    Charge: ac")
      inverterData.battery.chargemode.scc = false;
      inverterData.battery.chargemode.ac = true;
    }
    if (statusstring[5] + statusstring[6] + statusstring[7] == "111") {
      debug_log("    Charge: scc and ac")
      inverterData.battery.chargemode.scc = true;
      inverterData.battery.chargemode.ac = true;
    }
*/
}

int	analyzeQMOD( unsigned char *_msg, easunQMOD *_data) {
}
int	analyzeQPIRI( unsigned char *_msg, easunQPIRI *_data) {
}
int	analyzeQPIWS( unsigned char *_msg, easunQPIWS *_data) {
}
int	analyzeQPI( unsigned char *_msg, easunQPI *_data) {
}
int	analyzeQDI( unsigned char *_msg, easunQDI *_data) {
	char	*dp ;
	int	i ;

	for ( dp = strtok( _msg, " ("), i = 0 ; dp ; dp = strtok( NULL, " ("), i++) {
		if ( dp != NULL) {
			switch ( i ) {
			case	0	:	_data->outputACVoltage			=	atof( dp) ;	break ;
			case	1	:	_data->outputACFrequency 		=	atof( dp) ;	break ;
			case	2	:	_data->chargingCurrentACMax 		=	atof( dp) ;	break ;
			case	3	:	_data->batteryUndervoltage 		=	atof( dp) ;	break ;
			case	4	:	_data->chargingFloatVoltage 		=	atof( dp) ;	break ;
			case	5	:	_data->chargingBulkVoltage 		=	atof( dp) ;	break ;
			case	6	:	_data->batteryDefaultRechargeVoltage 	=	atof( dp) ;	break ;
			case	7	:	_data->chargingCurrentDCMax 		=	atof( dp) ;	break ;
			case	8	:	_data->inputVoltageRange 		=	atoi( dp) ;	break ;
			case	9	:	_data->outputSourcePriority 		=	atoi( dp) ;	break ;
			case	10	:	_data->chargerSourcePriority 		=	atoi( dp) ;	break ;
			case	11	:	_data->batteryType 			=	atoi( dp) ;	break ;
			case	12	:	_data->enableBuzzer 			=	atoi( dp) ;	break ;
			case	13	:	_data->enablePowerSaving 		=	atoi( dp) ;	break ;
			case	14	:	_data->enableOverloadRestart 		=	atoi( dp) ;	break ;
			case	15	:	_data->enableOvertemperatureRestart 	=	atoi( dp) ;	break ;
			case	16	:	_data->enableLCDBacklight 		=	atoi( dp) ;	break ;
			case	17	:	_data->enableAlarmPrimarySource 	=	atoi( dp) ;	break ;
			case	18	:	_data->enableFaultCodeRecord 		=	atoi( dp) ;	break ;
			case	19	:	_data->enableOverloadBypass 		=	atoi( dp) ;	break ;
			case	20	:	_data->enableLCDReturnToMain 		=	atoi( dp) ;	break ;
			case	21	:	_data->outputMode 			=	atoi( dp) ;	break ;
			case	22	:	_data->batteryReDischargeVoltage 	=	atoi( dp) ;	break ;
			case	23	:	_data->pvOkCondition 			=	atoi( dp) ;	break ;
			case	24	:	_data->pvPowerBalance 			=	atoi( dp) ;	break ;
			}
		} else {
			logit( "ERROR --- ERROR --- ERROR ---") ;
		}
	}
}
int	analyzeQID( unsigned char *_msg, easunQID *_data) {
}
int	analyzeQVFW( unsigned char *_msg, easunQVFW *_data) {
}
int	analyzeQVFW2( unsigned char *_msg, easunQVFW2 *_data) {
}
int	analyzeQSID( unsigned char *_msg, easunQVFW2 *_data) {
}
int	analyzeQPGS0( unsigned char *_msg, easunQVFW2 *_data) {
}

/**
 *
 */
int	attachCRC( unsigned char *_buffer) {
	uint16_t myCRC ;
	int		size ;
	size	=	strlen( _buffer) ;
	myCRC	=	crc16( _buffer, strlen( _buffer)) ;
	if ( cfgTrace) {
		logit( "checksum ... : %04lx \n", myCRC) ;
	}
	_buffer[ size+0]	=	( myCRC & 0xff00) >> 8 ;
	_buffer[ size+1]	=	( myCRC & 0x00ff) ;
	_buffer[ size+2]	=	0x0d ;
	_buffer[ size+3]	=	0x00 ;
}

/**
 *
 */
void	mySqlQuery( char *_sql) {
	MYSQL_RES	*result ;
	int	sqlResult	=	0 ;
	int	retryCount	=	0 ;
	int	reconnectCount	=	0 ;
	MYSQL	*connectResult ;
	do {
		if ( retryCount > 0) {
			sleep( retryCount * 10) ;
		}
		if ( ( sqlResult = mysql_query( mySql, _sql))) {
			do {
				if ( ( connectResult = mysql_real_connect( mySql, dbHost, dbUser, dbPassword, dbName, 0, NULL, 0)) == NULL) {
					reconnectCount++ ;
					sleep( reconnectCount * 2) ;
				}
			} while ( reconnectCount < 10 && connectResult == NULL) ;
			if ( reconnectCount >= 10) {
			}
		} else {
			result  =       mysql_store_result( mySql) ;
			mysql_free_result( result) ;
		}
	} while ( retryCount < 10 && sqlResult) ;
	if ( retryCount >= 10) {
		exit( -3) ;
	}
}

/**
 *
 */
int	dataToDb( easunQPIGS *_qpigs, easunQDI *_qdi) {
	char	buf[256] ;
//	sprintf( buf, "INSERT INTO log( GroupObjectId, DataType, Value) VALUES( %d, %d, '%5.1f');", 30218, 29, _qpigs->gridVoltage) ;
//	mySqlQuery( buf) ;
//	sprintf( buf, "INSERT INTO log( GroupObjectId, DataType, Value) VALUES( %d, %d, '%5.1f');", 30219, 29, _qpigs->gridFrequency) ;
//	mySqlQuery( buf) ;
	sprintf( buf, "INSERT INTO log( GroupObjectId, DataType, Value) VALUES( %d, %d, '%5.1f');", 30220, 29, _qpigs->batteryVoltage) ;
	mySqlQuery( buf) ;
	sprintf( buf, "INSERT INTO log( GroupObjectId, DataType, Value) VALUES( %d, %d, '%5.1f');", 30221, 29, _qpigs->batteryChargingCurrent) ;
	mySqlQuery( buf) ;
	sprintf( buf, "INSERT INTO log( GroupObjectId, DataType, Value) VALUES( %d, %d, '%5.1f');", 30222, 29, _qpigs->pvInputVoltage) ;
	mySqlQuery( buf) ;
	sprintf( buf, "INSERT INTO log( GroupObjectId, DataType, Value) VALUES( %d, %d, '%5.1f');", 30223, 29, _qpigs->pvInputCurrent) ;
	mySqlQuery( buf) ;
//	sprintf( buf, "INSERT INTO log( GroupObjectId, DataType, Value) VALUES( %d, %d, '%5.1f');", 30234, 29, _qdi->outputACVoltage) ;
//	mySqlQuery( buf) ;
//	sprintf( buf, "INSERT INTO log( GroupObjectId, DataType, Value) VALUES( %d, %d, '%5.1f');", 30235, 29, _qdi->outputACFrequency) ;
//	mySqlQuery( buf) ;
}

int	dataToMQTT( easunQPIGS *_qpigs, easunQDI *_qdi) {
	char	buf[64] ;
	sprintf( buf, "%5.2f", _qpigs->gridVoltage) ;			sendData( 30218, buf) ;
	sprintf( buf, "%5.2f", _qpigs->gridFrequency) ;			sendData( 30219, buf) ;
	sprintf( buf, "%5.2f", _qpigs->batteryVoltage) ;		sendData( 30220, buf) ;
	sprintf( buf, "%5.2f", _qpigs->batteryChargingCurrent) ;	sendData( 30221, buf) ;
	sprintf( buf, "%5.2f", _qpigs->pvInputVoltage) ;		sendData( 30222, buf) ;
	sprintf( buf, "%5.2f", _qpigs->pvInputCurrent) ;		sendData( 30223, buf) ;
}

int	dumpQPIGS( easunQPIGS *_data) {
	logit( "Grid voltage .............. : %5.2f \n", _data->gridVoltage) ;
	logit( "Grid frequency ............ : %5.2f \n", _data->gridFrequency) ;
	logit( "Output voltage ............ : %5.2f \n", _data->outputACVoltage) ;
	logit( "Output frequency .......... : %5.2f \n", _data->outputACFrequency) ;
	logit( "Output VA ................. : %5.2f \n", _data->outputPowerApparent) ;
	logit( "Output Watt ............... : %5.2f \n", _data->outputPowerActive) ;
	logit( "Load percentage ........... : %5.2f \n", _data->loadPercent) ;
	logit( "Bus voltage ............... : %5.2f \n", _data->busVoltage) ;
	logit( "Battery voltage ........... : %5.2f \n", _data->batteryVoltage) ;
	logit( "Battery charging current .. : %5.2f \n", _data->batteryChargingCurrent) ;
	logit( "Battery capacity .......... : %5.2f \n", _data->batteryCapacity) ;
	logit( "Inverter heat sink temp.... : %5.2f \n", _data->inverterHeatsinkTemp) ;
	logit( "PV Input Current .......... : %5.2f \n", _data->pvInputCurrent) ;
	logit( "PV Input Voltage .......... : %5.2f \n", _data->pvInputVoltage) ;
	logit( "Battery SCC Voltage ....... : %5.2f \n", _data->batterySCCVoltage) ;
	logit( "Battery discharge current . : %5.2f \n", _data->batteryDischargeCurrent) ;
}

int	dumpQDI( easunQDI *_data) {
	logit( "Output voltage ............ : %5.2f \n", _data->outputACVoltage) ;
	logit( "Output frequency .......... : %5.2f \n", _data->outputACFrequency) ;
	logit( "Charging current max....... : %5.2f \n", _data->chargingCurrentACMax) ;
	logit( "Battery undervoltage ...... : %5.2f \n", _data->batteryUndervoltage) ;
	logit( "Charging float voltage .... : %5.2f \n", _data->chargingFloatVoltage) ;
	logit( "Charging bulk voltage ..... : %5.2f \n", _data->chargingBulkVoltage) ;
	logit( "Default recharge voltage .. : %5.2f \n", _data->batteryDefaultRechargeVoltage) ;
	logit( "Chargin current DC max..... : %5.2f \n", _data->chargingCurrentDCMax) ;
	logit( "Input voltage range ....... : %5.2f \n", _data->inputVoltageRange) ;
	logit( "Output source priority .... : %5.2f \n", _data->outputSourcePriority) ;
	logit( "Charger source priority ... : %5.2f \n", _data->chargerSourcePriority) ;
	logit( "Battery type .............. : %5.2f \n", _data->batteryType) ;
	logit( "Buzzer enabled ............ : %1d \n", _data->enableBuzzer) ;
	logit( "Powersaving enabled ....... : %1d \n", _data->enablePowerSaving) ;
	logit( "Overload-restart enabled .. : %1d \n", _data->enableOverloadRestart) ;
	logit( "Overtemp-restart enabled .. : %1d \n", _data->enableOvertemperatureRestart) ;
	logit( "LCD Backlight enabled ..... : %1d \n", _data->enableLCDBacklight) ;
	logit( "Alarm prim. source enabled  : %1d \n", _data->enableAlarmPrimarySource) ;
	logit( "Fault code record enabled . : %1d \n", _data->enableFaultCodeRecord) ;
	logit( "Overload bypass enabled ... : %1d \n", _data->enableOverloadBypass) ;
	logit( "LCD Return to main ena. ... : %1d \n", _data->enableLCDReturnToMain) ;
	logit( "Outpu mode ................ : %d \n", _data->outputMode) ;
	logit( "Battery recharge voltage .. : %d \n", _data->batteryReDischargeVoltage) ;
	logit( "PV OK condition ........... : %d \n", _data->pvOkCondition) ;
	logit( "PV Power balance .......... : %d \n", _data->pvPowerBalance) ;
}

/**
 *
 */
void	logit( char *_fmt, ...) {
	char	buffer[128] ;
	va_list	arglist ;
	va_start( arglist, _fmt );
	vsprintf( buffer, _fmt, arglist) ;
	openlog( NULL, LOG_PID|LOG_CONS, LOG_USER);
	syslog( LOG_INFO, buffer) ;
	va_end( arglist ) ;
	closelog();
}

/**
 *
 */
void	help() {
	printf( "%s: %s [-D <debugLevel>] [-F] [-Q=<queueIdf>] [-M] [-S] \n\n", progName, progName) ;
	printf( "Handler for EASUN Off-Grid Power Inverterd.\n") ;
	printf( "Increment syslog tracing level by 1 through signal SIGUSR1 [%d].\n", SIGUSR1) ;
	printf( "Reset syslog tracing level by 1 through signal SIGUSR2 [%d].\n", SIGUSR2) ;
}
