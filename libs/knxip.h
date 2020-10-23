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
 * knxip.h
 *
 * message structures for knx<->ip bridge
 *
 * Revision history
 *
 * Date			Rev.	Who		what
 * ----------------------------------------------------------------------------
 * 2017-11-21	PA1		khw		extracted from knxipbridgehd.c
 */

#define	SEARCH_REQUEST				0x0201
#define	SEARCH_RESPONSE				0x0202
#define	DESCRIPTION_REQUEST			0x0203
#define	DESCRIPTION_RESPONSE		0x0204
#define	CONNECT_REQUEST				0x0205
#define	CONNECT_RESPONSE			0x0206
#define	CONNECTIONSTATE_REQUEST		0x0207
#define	CONNECTIONSTATE_RESPONSE	0x0208
#define	DISCONNECT_REQUEST			0x0209
#define	DISCONNECT_RESPONSE			0x020a

#define	DEVICE_MGMT_CONNECTION		0x03
#define	TUNNEL_CONNECTION			0x04
#define	REMLOG_CONNECTION			0x06
#define	REMCONF_CONNECTION			0x07
#define	OBJSVR_CONNECTION			0x08

#define	E_NO_ERROR					0x00
#define	E_CONNECTION_ID				0x21
#define	E_CONNECTION_TYPE			0x22
#define	E_CONNECTION_OPTION			0x23
#define	E_NO_MORE_CONNECTIONS		0x24
#define	E_DATA_CONNECTION			0x26
#define	E_KNX_CONNECTION			0x27

#define	DEVICE_INFO					0x01
#define	SUPP_SVC_FAMILIES			0x02
#define	IP_CONFIG					0x03
#define	IP_CUR_CONFIG				0x04
#define	KNX_ADDRESSES				0x05
#define	MFR_DATA					0xfe

#define KNX_LEN(vhl)          ((vhl) & 0xff) << 8 | ((((vhl) & 0xff00) >> 8) & 0x00ff)

typedef	struct	IPHeader	{
	u_char	len ;
	u_char	protVer ;
	u_short	req ;
	u_short	totLen ;
}	IPHeader ;

typedef	struct	IPBody	{
	u_char	message[256] ;
}	IPBody ;

typedef	struct	IPMessage	{
	u_char	len ;
	u_char	protVer ;
	u_short	req ;
	u_short	totLen ;
	u_char	message[256] ;
}	IPMessage ;

typedef	struct	knxHPAI	{
	u_char	len ;
	u_char	hostProt ;
	u_char	addr[4] ;
	u_short	port ;
}	knxHPAI ;

typedef	struct	knxCRI	{
	u_char	len ;
	u_char	connectionTypeCode ;
	u_char	hpData[16] ;
}	knxCRI ;

typedef	struct	knxCRD	{
	u_char	len ;
	u_char	connectionTypeCode ;
	u_char	hpData[16] ;
}	knxCRD ;

typedef	struct	knxDIB	{
	u_char	len ;
	u_char	dibType ;
	u_char	dibData[64] ;
}	knxDIB ;

typedef	struct	knxDIB01	{				// Device information DIB
	u_char	len ;
	u_char	dibType ;
	u_char	medium ;
	u_char	status ;
	u_short	knxAdr ;
	u_short	instId ;
	u_char	serno[6] ;
	u_char	mcAddr[4] ;
	u_char	macAddr[6] ;
	char	name[30] ;
}	knxDIB01 ;

typedef	struct	knxDIB02	{				// Supported service families DIB
	u_char	len ;
	u_char	dibType ;
	u_char	sid1 ;
	u_char	sidData1 ;
	u_char	sid2 ;
	u_char	sidData2 ;
	u_char	sid3 ;
	u_char	sidData3 ;
}	knxDIB02 ;

typedef	struct	knxDIB03	{				// IP Config DIB
	u_char	len ;
	u_char	dibType ;
	u_char	ipAddr[4] ;
	u_char	subnetMask[4] ;
	u_char	defaultGateway[4] ;
	u_char	ipCapabilities ;
	u_char	ipAssignmentMethod ;
}	knxDIB03 ;

typedef	struct	knxDIB04	{				// IP Current Config DIB
	u_char	len ;
	u_char	dibType ;
	u_char	currIPAddr[4] ;
	u_char	currSubnetMask[4] ;
	u_char	currDefaultGateway[4] ;
	u_char	dhcpServer[4] ;
	u_char	currIPAssignmentMethod ;
	u_char	reserved ;
}	knxDIB04 ;

typedef	struct	knxDIB05	{				// KNX Address DIB
	u_char	len ;
	u_char	dibType ;
	u_short	knxIndividualAddress ;
	u_short	additionalIndividualAddress1 ;
	u_short	additionalIndividualAddress2 ;
}	knxDIB05 ;

typedef	struct	knxDIBFE	{				// Manufacturer data DIB
	u_char	len ;
	u_char	dibType ;
	u_short	knxManufacturerID ;
	u_char	manufacturerSpecificData[32] ;
}	knxDIBFE ;

typedef	struct	knxCTI	{		// Connection Type Information
	u_char	len ;
	u_char	dibType ;
	u_char	sid1 ;
	u_char	sidData1 ;
	u_char	sid2 ;
	u_char	sidData2 ;
	u_char	sid3 ;
	u_char	sidData3 ;
}	knxCTI ;

typedef	struct	strSEARCH_REQUEST	{
	IPHeader	header ;
	knxHPAI		discoveryEndpoint ;
}	strSEARCH_REQUEST ;

typedef	struct	strSEARCH_RESPONSE	{
	IPHeader	header ;
	knxHPAI		controlEndpoint ;
	knxDIB01	deviceHardware ;
	knxDIB02	supportedServiceFamilies ;
}	strSEARCH_RESPONSE ;

typedef	struct	strDESCRIPTION_REQUEST	{
	IPHeader	header ;
	knxHPAI		controlEndpoint ;
}	strDESCRIPTION_REQUEST ;

typedef	struct	strDESCRIPTION_RESPONSE	{
	IPHeader	header ;
	knxDIB01	deviceHardware ;
	u_char		otherDeviceInformation[16] ;
}	strDESCRIPTION_RESPONSE ;

typedef	struct	strCONNECT_REQUEST	{
	IPHeader	header ;
	knxHPAI		controlEndpoint ;
	knxHPAI		dataEndpoint ;
	knxCRI		connectionRequestInformation ;
}	strCONNECT_REQUEST ;

typedef	struct	strCONNECT_RESPONSE	{
	IPHeader	header ;
	u_char		communicationChannelID ;
	u_char		status ;
	knxHPAI		dataEndpoint ;
	knxCRD		connectionRequestDataBlock ;
}	strCONNECT_RESPONSE ;


extern	void	createIPMessage( IPMessage *, u_char *, int) ;
extern	void	createKNXHPAI( knxHPAI *, struct in_addr, int) ;
extern	void	createKNXDIB01( knxDIB01 *) ;
extern	void	createKNXDIB02( knxDIB02 *) ;
extern	void	createKNXDIBFE( knxDIBFE *) ;
