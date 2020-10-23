/**
 * Copyright (c) 2016 Karl-Heinz Welter
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
 * hdlemh.h
 *
 * some high level functional description
 *
 * Revision history
 *
 * date		rev.	who	what
 * ----------------------------------------------------------------------------
 * 2016-06-23	PA1	khw	inception;
 *
 */

#ifndef	hdlemh_INCLUDED
#define	hdlemh_INCLUDED
/**
 * define the various states for the serial receiver
 */
typedef	enum	{
		emhWaitBegin	,
		emhWaitVersion	,
		emhWaitEnd	,
		emhWaitChecksum	,
		emhWaitTerm		// wait for the terminating 0x00
}	emhModeRcv ;

typedef	struct	SML_PublicOpen_Res	{
	unsigned	char	codepage[32] ;
	unsigned	char	clientId[32] ;
	unsigned	char	reqField[32] ;
	unsigned	char	serverId[32] ;
}	SML_PublicOpen_Res ;

typedef	struct	SML_PublicClose_Res	{
	unsigned	char	codepage[32] ;
	unsigned	char	clientId[32] ;
	unsigned	char	reqField[32] ;
	unsigned	char	serverId[32] ;
}	SML_PublicClose_Res ;

typedef	struct	SML_Time	{
	unsigned	int	secIndex ;
	unsigned	int	timestamp ;
}	SML_Time ;

typedef	struct	SML_GetList_Res	{
	unsigned	char	clientId[32] ;
	unsigned	char	serverId[32] ;
	unsigned	char	listName[32] ;
	SML_Time		actSensorTime ;
	SML_Time		actGatewayTime ;
}	SML_GetList_Res ;

typedef	union	SML_MessageBody	{
	unsigned	int	MessageType ;
	SML_PublicOpen_Res	OpenResponse ;
	SML_PublicClose_Res	CloseResponse ;
	SML_GetList_Res		GetListResponse ;
}	SML_MessageBody ;

typedef	struct	SML_Message	{
	unsigned	char	transactionId[32] ;
	unsigned	char	groupNo ;
	unsigned	char	abortOnError ;
	SML_MessageBody		messageBody ;
	unsigned	int	crc16 ;
}	SML_Message ;

typedef	struct	smlFile	{
}	smlFile ;

extern	unsigned char	*analyze( unsigned char *, unsigned char *, const char *, const char *) ;

#endif
