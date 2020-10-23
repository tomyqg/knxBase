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
 * Description:
 *
 * The heater is a controller taking care of maintaining temperatures in two separate buffers with
 * three distinct heating sources:
 *	- a pellet burner
 *	- a heatpump
 *	- a solar thermal system
 * Due to the implementation of the system, i.e. a x-style valve, the heatpump and pellet burner can
 * exclusively heat up one buffer. it is neither possible that both heaters are heating up the same buffer
 * not that two buffers are heated up by the same heater at any moment in time.
 *
 * The basic control sequence is as follows:
 *
 *	WHILE true
 *		determine if 
 *			- water needs heating
 *			- heating needs heating (funny, isn't it)
 *
 *	Depending on current mode:
 *
 *		- idle:
 *			if water needed
 *				if pellet available
 *					next mode := water on pellet, heating off
 *				elseif heatpump available and actHeatpumpOffTime > minHeatpumpOffTime
 *					next mode := water on heatpump, heating off
 *				endif
 *			elseif heating needed
 *				if pellet available
 *					next mode := heating on pellet, water off
 *				elseif heatpump available
 *					next mode := heating on heatpump, water off
 *		- water on pellet, heating off
 *			if water needed
 *				if heating needed
 *					if heatpump available
 *						next mode := water on pellet, heating on heatpump
 *					else
 *						alert: no source for heating
 *				endif
 *			else
 *				next mode := idle
 *			endif
 *		- water on heatpump, heating off
 *			if water needed
 *				if heating needed
 *					if pellet available
 *						next mode := water on heatpump, heating on pellet
 *					else
 *						alert: no source for heating
 *				endif
 *			else
 *				next mode := idle
 *			endif
 *
 *
 *
 */

#include	<string.h>

#include	"heater.h"

heater::heater() {
} ;

heater::~heater() {
}

int		heater::config( char *_confFile) {
} ;

void	heater::_debug() {
}
