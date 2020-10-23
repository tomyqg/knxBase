/**
 * Copyright (c) 2015-2018 wimtecc, Karl-Heinz Welter
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
 * hdlshades.h
 *
 * setup date for shades
 *
 * Revision history
 *
 * date		rev.	who	what
 * ----------------------------------------------------------------------------
 * 2018-04-22	PA1	khw	spin of
 *
 */
typedef	enum	{
		modeTime
	,	modeBright
	,	modeTB
}	shadeMode ;
/**
 * structure with
 */
typedef	struct	{
		int			id ;				// sequential id-no of entry
		bool		active ;			// active, 0= false, 1= true
		char		name[32] ;			// human readable name

		int			gaPosition ;		// actual position
		shadeMode	modeUp ;
		shadeMode	modeDown ;
		int			handleWeather ;		// react to weather conditions, 0= false, 1= true
		int			weatherDelay ;		// time to wait for correct weather condition before lifting the shade
		int			timeDown ;			// time for moving down
		int			timeUp ;
		int			timeDelayDown ;		// time delay in minutes for moving down
		int			timeDelayUp ;
		int			brightnessDown ;	// brightness for moving down
		int			brightnessUp ;		// brightness for moving up
	}	shadeSetup ;
/**
 *
 */
shadeSetup	myShades[]	=	{
	{1,false,"Wohnzimmer_____________________",0,modeTime  ,modeTime  ,false, 545,2345, 0, 0,  100},
	{1,true ,"test___________________________",0,modeBright,modeBright,false,   0,1439,99,99}
} ;
