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

/* Last revision: January 10, 2015 */

/* For more info and how to use this libray, visit: http://www.teuniz.net/RS-232/ */


#ifndef ttyINCLUDED
#define ttyINCLUDED

#include	<stdio.h>
#include	<stdlib.h>
#include	<string.h>
#include	<termios.h>
#include	<sys/ioctl.h>
#include	<unistd.h>
#include	<fcntl.h>
#include	<sys/types.h>
#include	<sys/stat.h>
#include	<limits.h>
#include	<sys/types.h>
#include	<sys/ipc.h>
#include	<sys/shm.h>
#include	<sys/msg.h>

typedef	struct	ttyHdl	{
			int	tty ;				// access point no.
		struct	termios	oldPortSettings ;
	}	ttyHdl ;

int	ttyPrep( char *, int, const char *) ;
ttyHdl	*ttyOpen( char *, int, const char *) ;
int	ttyPoll( ttyHdl *, unsigned char *, int) ;
int	ttySendCmd( ttyHdl *, unsigned char) ;
int	ttySendData( ttyHdl *, unsigned char, unsigned char) ;
int	ttySendByte( ttyHdl *, unsigned char) ;
int	ttySendBytes( ttyHdl *, unsigned char, unsigned char) ;
int	ttySendBuf( ttyHdl *, unsigned char *, int) ;

void	ttyClose( ttyHdl *) ;
void	ttycputs( ttyHdl *, const char *) ;
int	ttyIsDCDEnabled( ttyHdl *) ;
int	ttyIsCTSEnabled( ttyHdl *) ;
int	ttyIsDSREnabled( ttyHdl *) ;
void	ttyenableDTR( ttyHdl *) ;
void	ttydisableDTR( ttyHdl *) ;
void	ttyenableRTS( ttyHdl *) ;
void	ttydisableRTS( ttyHdl *) ;

#endif
