/**
 * Copyright (c) 2015-2017 wimtecc, Karl-Heinz Welter
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
 * knxip.c
 *
 * message structures for knx<->ip bridge
 *
 * Revision history
 *
 * Date			Rev.	Who		what
 * ----------------------------------------------------------------------------
 * 2017-11-21	PA1		khw		extracted from knxipbridgehd.c
 */

#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <errno.h>
#include <time.h>
#include <string.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <sys/ioctl.h>
#include <netinet/in.h>
#include <net/if.h>
#include <arpa/inet.h>

#include	"knxip.h"
#include	"mylib.h"

void	createKNXHPAI( knxHPAI *_myHPAI, struct in_addr _ownIPAddr, int _port) {
	_myHPAI->len		=	(u_char) sizeof( knxHPAI) ;
	_myHPAI->hostProt	=	(u_char) 0x01 ;
	_myHPAI->addr[0]	=	( _ownIPAddr.s_addr >>  0) & 0xff ;
	_myHPAI->addr[1]	=	( _ownIPAddr.s_addr >>  8) & 0xff ;
	_myHPAI->addr[2]	=	( _ownIPAddr.s_addr >> 16) & 0xff ;
	_myHPAI->addr[3]	=	( _ownIPAddr.s_addr >> 24) & 0xff ;
	_myHPAI->port		=	(u_short) KNX_LEN( _port) ;
}

void	createKNXDIB01( knxDIB01 *_myDIB) {
	_myDIB->len			=	(u_char) sizeof( knxDIB01) ;
	_myDIB->dibType		=	(u_char) DEVICE_INFO ;		// DEVICE_INFO
	_myDIB->medium		=	(u_char) 0x02 ;		// TPuart
	_myDIB->status		=	0x00 ;
	_myDIB->knxAdr		=	KNX_LEN( 0x1001) ;
	_myDIB->instId		=	KNX_LEN( 0x0000) ;
	_myDIB->serno[0]	=	0x00 ;
	_myDIB->serno[1]	=	0xc5 ;
	_myDIB->serno[2]	=	0x01 ;
	_myDIB->serno[3]	=	0x00 ;
	_myDIB->serno[4]	=	0x30 ;
	_myDIB->serno[5]	=	0x2d ;
	_myDIB->mcAddr[0]	=	0 ; // 224 ;
	_myDIB->mcAddr[1]	=	0 ; // 0 ;
	_myDIB->mcAddr[2]	=	0 ; // 23 ;
	_myDIB->mcAddr[3]	=	0 ; // 12 ;
	/**
	 *	14:10:9f:d5:e4:f3
	 *	00:24:6d:00:51:a0		=> Weinzierl 00:24:6d:xx:xx:xx
	 */
	_myDIB->macAddr[0]	=	0x00 ;
	_myDIB->macAddr[1]	=	0x24 ;
	_myDIB->macAddr[2]	=	0x6d ;
	_myDIB->macAddr[3]	=	0x00 ;
	_myDIB->macAddr[4]	=	0x51 ;
	_myDIB->macAddr[5]	=	0xa1 ;
//	strcpy( _myDIB->name, "wimtecc KNX IP Bridge") ;
	strcpy( _myDIB->name, "KNX IP BAOS 772") ;
}

void	createKNXDIB02( knxDIB02 *_myDIB) {
	_myDIB->len	=	 (u_char) sizeof( knxDIB02) ;
	_myDIB->dibType	=	(u_char) SUPP_SVC_FAMILIES ;	// SUPP_SVC_FAMILIES
	_myDIB->sid1	=	0x02 ;
	_myDIB->sidData1	=	0x01 ;
	_myDIB->sid2	=	0x03 ;
	_myDIB->sidData2	=	0x01 ;
	_myDIB->sid3	=	0x04 ;
	_myDIB->sidData3	=	0x01 ;
}

void	createKNXDIBFE( knxDIBFE *_myDIB) {
	_myDIB->len	=	 (u_char) sizeof( knxDIBFE) ;
	_myDIB->dibType	=	(u_char) MFR_DATA ;				// MFR_DATA
	_myDIB->knxManufacturerID	=	0xc500 ;
	_myDIB->manufacturerSpecificData[0]	=	0x01 ;
	_myDIB->manufacturerSpecificData[1]	=	0x04 ;
	_myDIB->manufacturerSpecificData[2]	=	0xf0 ;
	_myDIB->manufacturerSpecificData[3]	=	0x20 ;
}

void	createIPMessage( IPMessage *_ipMessage, u_char *_msgBody, int _len) {
	_ipMessage->len		=	0x06 ;
	_ipMessage->protVer	=	(u_char) 0x10 ;
	_ipMessage->req		=	(u_short) 0x202 ;
	_ipMessage->totLen	=	KNX_LEN( 0x06 + _len) ;
	memcpy( _ipMessage->message, _msgBody, _len) ;
} ;
