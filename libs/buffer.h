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

#ifndef	BUFFER_INCLUDED
#define	BUFFER_INCLUDED

#include	<sys/types.h>

class	buffer	{
	private:

		char	tagTemp[8][32] ;			// tags of the actual temperatures in the KNX monitor
		char	tagAmbTemp ;				// tags of the actual temperatures in the KNX monitor

		int		ndxTemp[8] ;				// indices of the temperatures in the KNX monitor
		int		ndxAmbTemp ;				// index of the ambient temperature in the KNX monitor

		//	actual values

		float	tempActual[8] ;
		float	tempActualAvg ;

		float	tempAmbient ;				// ambient temperature

		//	target temperatures

		float	tempTarget ;
		float	tempTargetAdj ;				// adjusted target temperature, adjustment based on ambient temperature

	public:
				buffer() ;
				~buffer() ;
		int		config( char *) ;
		int		needsHeating() ;			// returns if heating is needed

		float	getTemp() ;					// returns actual temperature

		void	_debug() ;
} ;

#endif
