/**
 * Copyright (c) 2019 wimtecc, Karl-Heinz Welter
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
 * sendbit.c
 *
 * send a bit value (DPT 1.001) to a given group address
 *
 * Revision history
 *
 * Date			Rev.	Who	what
 * ----------------------------------------------------------------------------
 * 2019-01-13	PA1		khw	inception; derived from sendfloat.c
 *
 */
#include	<stdio.h>
#include	<stdlib.h>
#include	<unistd.h>
#include	<string.h>
#include	<strings.h>
#include	<sys/msg.h>

#include	"debug.h"
#include	"eib.h"
#include	"knxtpbridge.h"
#include	"nodeinfo.h"
#include	"mylib.h"
#include	"inilib.h"

extern	void	help() ;

char	progName[64] ;

/**
 *
 */
int main( int argc, char *argv[]) {
			eibHdl		*myEIB ;
			int		myAPN	=	0 ;
			short		group ;
	unsigned	char		buf[16] ;
			int		msgLen ;
			int		opt ;
			int		sender	=	0 ;
			int		receiver	=	0 ;
			int		value ;
			char	rcvrAddr[32] ;
	/**
	 *
	 */
	strcpy( progName, *argv) ;
	printf( "%s: starting up ... \n", progName) ;

	/**
	 * get command line options
	 */
	while (( opt = getopt( argc, argv, "D:a:?")) != -1) {
		switch ( opt) {
		case	'D'	:
			debugLevel	=	atoi( optarg) ;
			break ;
		case	'a'	:
			strcpy( rcvrAddr, optarg) ;
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
	 *
	 */
	if ( strstr( rcvrAddr, "/") == NULL) {
		value	=	atoi( rcvrAddr) ;
		eibIntToGroup( value, rcvrAddr) ;
		printf( "Groupaddress %d => %s \n", value, rcvrAddr) ;
	} else {
		printf( "Groupaddress %s => %d \n", rcvrAddr, eibGroupToInt( rcvrAddr)) ;
	}
	/**
	 *
	 */
	exit( 0);
}
void	help() {
	printf( "%s: %s -a=<Addr> \n\n", progName, progName) ;
	printf( "converts a plain decimal address into EIB/KNX group address format or vice versa \n") ;
}
