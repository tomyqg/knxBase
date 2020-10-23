/**
 * Copyright (c) 2020 khwelter, Karl-Heinz Welter
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
 * heatpump.c
 *
 * Handling for the heatpump, which can heat up the buffer and the water tank.
 *
 * Revision history
 *
 * date			rev.	who		what
 * ----------------------------------------------------------------------------
 * 2020-04-13	PA1		khw		merging hdlheatpump and hdlpellet, introduced
 *								classes;
 */
#include	<stdio.h>
#include	<stdlib.h>
#include	<stdarg.h>
#include	<string.h>
#include	<strings.h>
#include	<stdbool.h>
#include	<unistd.h>
#include	<time.h>
#include	<syslog.h>
#include	<sys/types.h>
#include	<sys/ipc.h>
#include	<sys/shm.h>
#include	<sys/msg.h>
#include	<sys/signal.h>

class	heatpump	{
		enum	mode	{
				stopped	=	0
			,	water	=	1
			,	buffer	=	2
		} ;

		private:
			int				emergencyStop ;			// heatpump is allowed to operate
			int				freeToRun ;				// heatpump is allowed to operate
			enum	mode	currentMode ;			// actual mode of operation
			int				minTimeOnPerWeek ;		// minutes to operate per week to stay operational
			int				forceWeekDay ;			// weekday at which to enforce operational run
			int				minTimeOnPerDay ;		// minutes to operate per day to stay operational
			int				minTimeOff ;			// minimum minutes to stay off before switching on again
			int				minTimeOn ;				// minimum minutes to stay on before switching off again
			int				handleWater ;			// handle water tank
			int				handleBuffer ;			// handle heating buffer
			float			adjustTempAmbient ;		// minimun ambient temperature for operation
			float			adjustTempFactor ;
			float			tempToResign ;			// temperature below which the heatpump will stop working due to efficiency
			float			tempToRestart ;			// temperature above which the heatpump will start working (again) due to efficiency
			float			waterTempOn ;			// temperature of water when heat pump shall start
			float			waterTempOff ;			// temperature of water when heat pump can stop
			float			bufferTempOn ;			// temperature of buffer when heat pump shall start
			float			bufferTempOff ;			// temperature of buffer when heat pump can stop
			float			actBufferTempOn ;		// temperature of buffer when heat pump shall start
			float			actBufferTempOff ;		// temperature of buffer when heat pump can stop

		public:
					heatpump( char *_confFile) ;
					~heatpump() ;
} ;
