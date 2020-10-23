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

#ifndef	HEATING_INCLUDED

#define	HEATING_INCLUDED

#include	<sys/types.h>

#include	"buffer.h"

#define	heatingOnPellet		0x01
#define	heatingOnHeatpump	0x02
#define	heatingOnSolar		0x04
#define	waterOnPellet		0x10
#define	waterOnHeatpump		0x20
#define	waterOnSolar		0x40

class	heating	{
	private:
		int		allowedModes	=	0x07 ;	// mode is a bit combination of:
											//	%xxxxxxx1	heating on pellets allowed
											//	%xxxxxx1x	heating on heatpump allowed
											//	%xxxxx1xx	heating on solar allowed
											//	%xxx1xxxx	pottable water on pellets allowed
											//	%xx1xxxxx	pottable water on heatpump allowed
											//	%x1xxxxxx	pottable water on solar allowed

		int		currentMode		=	0x00 ;	// mode is a bit combination of: (x don't care)
											//	%xxx0xxx1	heating on pellets
											//	%xx0xxx1x	heating on heatpump
											//	%xxxxx1xx	heating on solar
											//	%xxx1xxx0	pottable water on pellets
											//	%xx1xxx0x	pottable water on heatpump
											//	%x1xxxxxx	pottable water on heatpump
											// valid modes are: (all other combinations are invalid)
											//	%00000x00	not operational (x don't care)
											//	%00000x01	heating on pellet
											//	%00000x10	heating on heatpump
											//	%0x010x00	pottable water on pellet
											//	%0x100x00	pottable water on heatpump
											//	%0x010x10	pottable water on heatpump, heating on pellet
											//	%0x100x01	pottabel water on pellet, heating on heatpump

		int		emergencyOffHeatpump	=	false ;
		int		emergencyOffPellet		=	false ;
		int		emergencyOffSolar		=	false ;

		float	ambTempHPWaterOff		=	  5.0 ;		// ambient temperature at which heatpump will be de-activated for pottable water
		float	ambTempHPWaterOn		=	  8.0 ;		// ambient temperature at which heatpump will be re-activated for pottable water

		float	ambTempHPHeatingOff		=	  -10.0 ;	// ambient temperature at which heatpump will be de-activated for pottable water
		float	ambTempHPHeatingOn		=	   -6.0 ;	// ambient temperature at which heatpump will be re-activated for pottable water

		class	buffer	bufferWater ;
		class	buffer	bufferHeating ;

		//	actual values

	public:
					heating( char *) ;
					~heating() ;
} ;

#endif
