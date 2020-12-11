/**
 * Copyright (c) 2015, 2016 wimtecc, Karl-Heinz Welter
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
 */
var debugDiv;
var	debugLevel	=	0 ;
var now	=	new Date() ;
var	debugDialog	=	null ;
var	nestLevel	=	0 ;
/**
 *
 * @return
 */
function	dOpen( _level) {
	debugLevel	=	_level ;
}
function	dClose() {
}
function	_debugSetLevel( _level) {
	debugLevel	=	_level ;
}
function	dLevel( _level) {
	dOpen( _level) ;
}
function	_debugL( _level, _data) {
	if ( debugLevel >= _level) {
		now	=	new Date() ;
		console.log( now.getTime() + ": " + _data) ;
	}
}

function	_debug() {
}
function	dBegin( _level, _file, _mod, _method) {
	var	msg ;
	if ( debugLevel >= _level) {
		nestLevel++ ;
		now	=	new Date() ;
		msg	=	now.getTime() + ": " + _level + ":" + nestLevel.toString() + ":" + _file + "::" + _mod + "::" + _method + ": begin\n" ;
		console.log( msg) ;
	}
}
function	dEnd( _level, _file, _mod, _method) {
	var	msg ;
	if ( debugLevel >= _level) {
		now	=	new Date() ;
		msg	=	now.getTime() + ": " + _level + ":" + nestLevel.toString() + ":" + _file + "::" + _mod + "::" + _method + ": end\n" ;
		console.log( msg) ;
		nestLevel-- ;
	}
}
function	dTrace( _level, _file, _mod, _method, _mesg) {
	var	msg ;
	if ( debugLevel >= _level) {
		now	=	new Date() ;
		msg	=	now.getTime() + ": " + _level + ": " + nestLevel.toString() + ":" + _file + "::" + _mod + "::" + _method + ": " + _mesg + "\n" ;
		console.log( msg) ;
	}
}
function	dClear() {
}
