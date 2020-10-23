/*
***************************************************************************
*
* Author: Teunis van Beelen
*
* Copyright (C) 2005, 2006, 2007, 2008, 2009, 2010, 2011, 2012, 2013, 2014, 2015 Teunis van Beelen
*
* Email: teuniz@gmail.com
*
***************************************************************************
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation version 2 of the License.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along
* with this program; if not, write to the Free Software Foundation, Inc.,
* 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*
***************************************************************************
*
* This version of GPL is at http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
*
***************************************************************************
*/

/* This revision: November 27, 2015 */

#include "tty.h"

int error;

struct termios new_port_settings;

#ifdef	__MACH__

int     ttyPrep( char *_comport, int baudrate, const char *mode) {
	return(0) ;
}
ttyHdl  *ttyOpen( char *_comport, int baudrate, const char *mode) {
	ttyHdl	*this ;
	this   	=	      (ttyHdl *) malloc( sizeof( ttyHdl)) ;
	return( this) ;
}
int ttyPoll( ttyHdl *this, unsigned char *buf, int size) {
	return( 0) ;
}
int ttySendByte( ttyHdl *this, unsigned char byte) {
	return( 0) ;
}
void ttyClose( ttyHdl *this) {
	close( this->tty) ;
	this	=	NULL ;
}

#else

int ttyPrep( char *_comport, int baudrate, const char *mode) {
}

ttyHdl  *ttyOpen( char *_comPort, int baudrate, const char *mode) {
	ttyHdl	*this ;
	int	baudr ;
	int	status ;
	this   	=	      (ttyHdl *) malloc( sizeof( ttyHdl)) ;

	switch(baudrate) {
	case      50 :
		baudr	=	B50;
		break;
	case      75 :
		baudr	=	B75;
		break;
	case     110 :
		baudr	=	B110;
		break;
	case     134 :
		baudr	=	B134;
		break;
	case     150 :
		baudr	=	B150;
		break;
	case     200 :
		baudr	=	B200;
		break;
	case     300 :
		baudr	=	B300;
		break;
	case     600 :
		baudr	=	B600;
		break;
	case    1200 :
		baudr	=	B1200;
		break;
	case    1800 :
		baudr	=	B1800;
		break;
	case    2400 :
		baudr	=	B2400;
		break;
	case    4800 :
		baudr	=	B4800;
		break;
	case    9600 :
		baudr	=	B9600;
		break;
	case   19200 :
		baudr	=	B19200;
		break;
	case   38400 :
		baudr	=	B38400;
		break;
	case   57600 :
		baudr	=	B57600;
		break;
	case  115200 :
		baudr	=	B115200;
		break;
	case  230400 :
		baudr	=	B230400;
		break;
	case  460800 :
		baudr	=	B460800;
		break;
	case  500000 :
		baudr	=	B500000;
		break;
	case  576000 :
		baudr	=	B576000;
		break;
	case  921600 :
		baudr	=	B921600;
		break;
	case 1000000 :
		baudr	=	B1000000;
		break;
	case 1152000 :
		baudr	=	B1152000;
		break;
	case 1500000 :
		baudr	=	B1500000;
		break;
	case 2000000 :
		baudr	=	B2000000;
		break;
	case 2500000 :
		baudr	=	B2500000;
		break;
	case 3000000 :
		baudr	=	B3000000;
		break;
	case 3500000 :
		baudr	=	B3500000;
		break;
	case 4000000 :
		baudr	=	B4000000;
		break;
	default      :
		printf("invalid baudrate\n") ;
		return( NULL) ;
		break;
	}

	int cbits=CS8,
		  cpar=0,
		  ipar=IGNPAR,
		  bstop=0;

	if(strlen(mode) != 3) {
		printf("invalid mode \"%s\"\n", mode) ;
		return( NULL) ;
	}

	switch(mode[0]) {
	case '8':
		cbits	=	CS8;
		break;
	case '7':
		cbits	=	CS7;
		break ;
	case '6':
		cbits	=	CS6;
		break ;
	case '5':
		cbits	=	CS5;
		break ;
	default :
		printf("invalid number of data-bits '%c'\n", mode[0]) ;
		return( NULL) ;
		break ;
	}

	switch(mode[1]) {
	case 'N':
	case 'n':
		cpar	=	0;
		ipar	=	IGNPAR;
		break ;
	case 'E':
	case 'e':
		cpar	=	PARENB;
		ipar	=	INPCK;
		break ;
	case 'O':
	case 'o':
		cpar	=	(PARENB | PARODD) ;
		ipar	=	INPCK;
		break ;
	default :
		printf("invalid parity '%c'\n", mode[1]) ;
		return( NULL) ;
		break ;
	}

	switch(mode[2]) {
	case '1':
		bstop	=	0;
		break ;
	case '2':
		bstop	=	CSTOPB;
		break ;
	default :
		printf("invalid number of stop bits '%c'\n", mode[2]) ;
		return( NULL) ;
		break ;
	}

/*
http://pubs.opengroup.org/onlinepubs/7908799/xsh/termios.h.html

http://man7.org/linux/man-pages/man3/termios.3.html
*/

	this->tty	=	open( _comPort, O_RDWR | O_NOCTTY | O_NDELAY) ;
	if( this->tty == -1) {
		perror("unable to open  ") ;
		return( NULL) ;
	}

	error	=	tcgetattr( this->tty, &this->oldPortSettings) ;
	if(error == -1) {
		close( this->tty) ;
		perror("unable to read portsettings ") ;
		return( NULL) ;
	}
	memset(&new_port_settings, 0, sizeof( new_port_settings)) ;  /* clear the new struct */

	new_port_settings.c_cflag	=	cbits | cpar | bstop | CLOCAL | CREAD;
	new_port_settings.c_iflag	=	ipar;
	new_port_settings.c_oflag	=	0;
	new_port_settings.c_lflag	=	0;
	new_port_settings.c_cc[VMIN]	=	0;      /* block untill n bytes are received */
	new_port_settings.c_cc[VTIME]	=	0;     /* block untill a timer expires (n * 100 mSec.) */

	cfsetispeed(&new_port_settings, baudr) ;
	cfsetospeed(&new_port_settings, baudr) ;

	error	=	tcsetattr( this->tty, TCSANOW, &new_port_settings) ;
	if(error == -1) {
		close( this->tty) ;
		perror("unable to adjust portsettings ") ;
		return( NULL) ;
	}

	if ( ioctl( this->tty, TIOCMGET, &status) == -1) {
		perror("unable to get portstatus") ;
		return( NULL) ;
	}

	status |= TIOCM_DTR;    /* turn on DTR */
	status |= TIOCM_RTS;    /* turn on RTS */

	if ( ioctl( this->tty, TIOCMSET, &status) == -1) {
		perror("unable to set portstatus") ;
		return( NULL) ;
	}
	return( this) ;
}


int ttyPoll( ttyHdl *this, unsigned char *buf, int size) {
	int n;

	n	=	read( this->tty, buf, size) ;

	return(n) ;
}


int ttySendByte( ttyHdl *this, unsigned char byte) {
	int n;

	n	=	write( this->tty, &byte, 1) ;
	if(n<0)  return(1) ;

	return(0) ;
}


int ttySendBytes( ttyHdl *this, unsigned char byte1, unsigned char byte2) {
	int n;

	n	=	write( this->tty, &byte1, 1) ;
	n	=	write( this->tty, &byte2, 1) ;
	if(n<0)
		return( 1) ;
	return(0) ;
}


int ttySendBuf( ttyHdl *this, unsigned char *buf, int size) {
	return(write( this->tty, buf, size)) ;
}


void ttyClose( ttyHdl *this) {
	int status;

	if ( ioctl( this->tty, TIOCMGET, &status) == -1) {
		perror("unable to get portstatus") ;
	}

	status &= ~TIOCM_DTR;    /* turn off DTR */
	status &= ~TIOCM_RTS;    /* turn off RTS */

	if ( ioctl( this->tty, TIOCMSET, &status) == -1) {
		perror("unable to set portstatus") ;
	}

	tcsetattr( this->tty, TCSANOW, &this->oldPortSettings) ;
	close( this->tty) ;
}

/*
Constant  Description
TIOCM_LE        DSR (data set ready/line enable)
TIOCM_DTR       DTR (data terminal ready)
TIOCM_RTS       RTS (request to send)
TIOCM_ST        Secondary TXD (transmit)
TIOCM_SR        Secondary RXD (receive)
TIOCM_CTS       CTS (clear to send)
TIOCM_CAR       DCD (data carrier detect)
TIOCM_CD        see TIOCM_CAR
TIOCM_RNG       RNG (ring)
TIOCM_RI        see TIOCM_RNG
TIOCM_DSR       DSR (data set ready)

http://man7.org/linux/man-pages/man4/tty_ioctl.4.html
*/

int ttyIsDCDEnabled( ttyHdl *this)
{
	int status;

	ioctl( this->tty, TIOCMGET, &status) ;

	if(status&TIOCM_CAR) return(1) ;
	else return(0) ;
}

int ttyIsCTSEnabled( ttyHdl *this)
{
	int status;

	ioctl( this->tty, TIOCMGET, &status) ;

	if(status&TIOCM_CTS) return(1) ;
	else return(0) ;
}

int ttyIsDSREnabled( ttyHdl *this)
{
	int status;

	ioctl( this->tty, TIOCMGET, &status) ;

	if(status&TIOCM_DSR) return(1) ;
	else return(0) ;
}

void ttyenableDTR( ttyHdl *this)
{
	int status;

	if ( ioctl( this->tty, TIOCMGET, &status) == -1) {
		perror("unable to get portstatus") ;
	}

	status |= TIOCM_DTR;    /* turn on DTR */

	if ( ioctl( this->tty, TIOCMSET, &status) == -1) {
		perror("unable to set portstatus") ;
	}
}

void ttydisableDTR( ttyHdl *this)
{
	int status;

	if ( ioctl( this->tty, TIOCMGET, &status) == -1) {
		perror("unable to get portstatus") ;
	}

	status &= ~TIOCM_DTR;    /* turn off DTR */

	if ( ioctl( this->tty, TIOCMSET, &status) == -1) {
		perror("unable to set portstatus") ;
	}
}

void ttyenableRTS( ttyHdl *this)
{
	int status;

	if ( ioctl( this->tty, TIOCMGET, &status) == -1) {
		perror("unable to get portstatus") ;
	}

	status |= TIOCM_RTS;    /* turn on RTS */

	if ( ioctl( this->tty, TIOCMSET, &status) == -1) {
		perror("unable to set portstatus") ;
	}
}

void ttydisableRTS( ttyHdl *this)
{
	int status;

	if ( ioctl( this->tty, TIOCMGET, &status) == -1) {
		perror("unable to get portstatus") ;
	}

	status &= ~TIOCM_RTS;    /* turn off RTS */

	if ( ioctl( this->tty, TIOCMSET, &status) == -1) {
		perror("unable to set portstatus") ;
	}
}

#endif
