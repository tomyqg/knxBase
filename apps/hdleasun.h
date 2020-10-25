/**
 * Copyright (c) 2020 Karl-Heinz Welter
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
 * hdleasun.h
 *
 * some high level functional description
 *
 * Revision history
 *
 * date		rev.	who	what
 * ----------------------------------------------------------------------------
 * 2020-10-08	PA1	khw	inception;
 *
 */

#ifndef	hdleasun_INCLUDED
#define	hdleasun_INCLUDED
/**
 * define the various states for the serial receiver
 */
typedef	enum	{
		cmdEasunQPIGS	,
		cmdEasunQMOD	,
		cmdEasunQPIRI	,
		cmdEasunQPIWS	,
		cmdEasunQPI	,
		cmdEasunQQID	,
		cmdEasunQVFW	,
		cmdEasunQVFW2	,
		cmdEasunQSID	,
		cmdEasunQPGS0
}	easunCommand ;

typedef	struct	easunQPIGS	{
	float	gridVoltage ;
	float	gridFrequency ;
	float	outputACVoltage ;
	float	outputACFrequency ;
	float	outputPowerApparent ;
	float	outputPowerActive ;
	float	loadPercent ;
	float	busVoltage ;
	float	batteryVoltage ;
	float	batteryChargingCurrent ;
	float	batteryCapacity ;
	float	inverterHeatsinkTemp ;
	float	pvInputCurrent ;
	float	pvInputVoltage ;
	float	batterySCCVoltage ;
	float	batteryDischargeCurrent ;
	bool	prioVersionSBU ;
	bool	statusChanged ;
	bool	firmwareSBUUpdated ;
	bool	statusLoad ;
	bool	batterySteady ;
	bool	charging ;
	bool	chargeModeSCC ;
	bool	chargeModeAC ;
}	easunQPIGS ;

typedef	struct	easunQMOD	{
}	easunQMOD ;
typedef	struct	easunQPIRI	{
}	easunQPIRI ;
typedef	struct	easunQPIWS	{
}	easunQPIWS ;
typedef	struct	easunQPI	{
}	easunQPI ;
typedef	struct	easunQDI	{
	float	outputACVoltage ;
	float	outputACFrequency ;
	float	chargingCurrentACMax ;
	float	batteryUndervoltage ;
	float	chargingFloatVoltage ;
	float	chargingBulkVoltage ;
	float	batteryDefaultRechargeVoltage ;
	float	chargingCurrentDCMax ;
	uint8_t	inputVoltageRange ;
	uint8_t	outputSourcePriority ;
	uint8_t	chargerSourcePriority ;
	uint8_t	batteryType ;
	bool	enableBuzzer ;
	bool	enablePowerSaving ;
	bool	enableOverloadRestart ;
	bool	enableOvertemperatureRestart ;
	bool	enableLCDBacklight ;
	bool	enableAlarmPrimarySource ;
	bool	enableFaultCodeRecord ;
	bool	enableOverloadBypass ;
	bool	enableLCDReturnToMain ;
	uint8_t	outputMode ;
	float	batteryReDischargeVoltage ;
	bool	pvOkCondition ;
	bool	pvPowerBalance ;
}	easunQDI ;
typedef	struct	easunQID	{
}	easunQID ;
typedef	struct	easunQVFW	{
}	easunQVFW ;
typedef	struct	easunQVFW2	{
}	easunQVFW2 ;
typedef	struct	easunQSID	{
}	easunQSID ;
typedef	struct	easunQPGS0	{
}	easunQPGS0 ;

extern	int	analyze( unsigned char *) ;
extern	int	analyzeQPIGS( unsigned char *, easunQPIGS *) ;
extern	int	analyzeQMOD( unsigned char *, easunQMOD *) ;
extern	int	analyzeQPIRI( unsigned char *, easunQPIRI *) ;
extern	int	analyzeQPIWS( unsigned char *, easunQPIWS *) ;
extern	int	analyzeQPI( unsigned char *, easunQPI *) ;
extern	int	analyzeQDI( unsigned char *, easunQDI *) ;
extern	int	analyzeQID( unsigned char *, easunQID *) ;
extern	int	analyzeQVFW( unsigned char *, easunQVFW *) ;
extern	int	analyzeQVFW2( unsigned char *, easunQVFW2 *) ;
extern	int	analyzeQSID( unsigned char *, easunQVFW2 *) ;
extern	int	analyzeQPGS0( unsigned char *, easunQVFW2 *) ;
extern	int	attachCRC( unsigned char *) ;

extern	int	dataToDb( easunQPIGS *, easunQDI *) ;
extern	int	dataToMQTT( easunQPIGS *, easunQDI *) ;

extern	int	dumpQPIGS( easunQPIGS *) ;
extern	int	dumpQDI( easunQDI *) ;

#endif
