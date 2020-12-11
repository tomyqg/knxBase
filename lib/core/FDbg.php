<?php
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
 * FDbg.php General Purpose in-code Debugger
 *
 * This class implements a general purpose in-code debugger. The functionality can be
 * switched on and off during runtime at will, as well as the scoping can be changed during
 * runtime. The code (to be debugged) needs to instrumneted accordingly.
 *
 * Revision history
 *
 * Date			Rev.	Who		What
 * ----------------------------------------------------------------------------
 * 200x-xx-xx	PA1		???		inception;
 * 2015-12-17	PA2		khw		added header(s);
 * 2016-08-03   PA3     khw     added FDbg::backTrace( ...);
 * 2016-08-31   PA4     khw     microseconds made visible due to concurrency
 *                              issues with opticat-direkt parallel execution
 *                              of client->server callbacks
 *
 * To-Do
 *
 * Date			Who		What
 * ----------------------------------------------------------------------------
 * 2016-08-03   khw     implement filters
 */
/**
 * @author Karl-Heinz Welter <khwelter@icloud.com>
 * @version 0.1
 * @package wtcCore
 * @todo test1 test2
 * @todo test3 test4
 */
class	FDbg {
    /**#@+
     * Pre-defined debugger level
     */
    const	DBGL1	=	1 ;
    const	DBGL2	=	2 ;
    const	DBGL3	=	3 ;
    const	DBGL4	=	4 ;
    const	DBGL5	=	5 ;
    /**#@-*/
    const	mdTrcBegin	=	0x00000001 ;
    const	mdTrcInfo1	=	0x00010000 ;
    const	mdTrcInfo2	=	0x00020000 ;
    const	mdTrcInfo3	=	0x00040000 ;
    const	mdTrcInfo4	=	0x00080000 ;
    /**#@+
     * Some private variables, don't have to be explained
     */
    private	static	$inst	=	NULL;
    private	static	$active	=	false ;
    private	static	$htmlMode	=	false ;
    private	static	$level	=	1 ;
    private static  $nesting    =   0 ;
    private	static	$states	=	array() ;
    private	static	$levels	=	array() ;
    private	static	$files	=	array() ;
    private	static	$modules	=	array() ;
    private	static	$methods	=	array() ;
    private	static	$mask	=	0xffffffff ;
    private	static	$app	=	"" ;
    private	static	$appToTrace	=	"*" ;
    private	static	$fileList	=	"*" ;
    private	static	$fileListExclude	=	"" ;
    private	static	$modList	=	"*" ;
    private	static	$modListExclude	=	"" ;
    private	static	$methodList	=	"*" ;
    private	static	$methodListExclude	=	"" ;
    private	static	$override	=	false ;
    private	static	$microTime	=	false ;
    private	static	$memory	=	false ;
    private static  $indentBegin    =   "   +" ;
    private static  $indent         =   "   |" ;
	private	static	$debugFDbg	=	false ;
	private	static	$p	=	"-------------------------------------------------------------------> " ;
    /**
     *
     */
    private function __construct( $_app="") {
        self::$htmlMode	=	false ;
        if ( isset( $_SERVER['SERVER_NAME'])) {
            self::$htmlMode	=	true ;
        }
		self::$app	=	$_app ;
        if ( self::$app == "") {
            self::$app  =   sprintf( "%08lx", rand( 0, 65535)) ;
			$callerStack	=	debug_backtrace( true) ;
			self::$app	=	$callerStack[ count( $callerStack) - 1][ "file"] ;
        }
    }
    function	overrideOn() {
        self::$override	=	true ;
    }
    function	overrideOff() {
        self::$override	=	false ;
    }
    /**
     *
     * @param int   $_level
     * @param string $_file
     * @param string $_module
     * @param string $_method
     * @param string $_mesg
     */
    static	function	begin( $_level, $_file, $_module, $_method, $_mesg="") {
        if (self::$inst === NULL) {
            self::$inst	=	new self ;
        }
        array_push( self::$levels, $_level) ;
        array_push( self::$files, $_file) ;
        array_push( self::$modules, $_module) ;
        array_push( self::$methods, $_method) ;
        if ( self::$active && $_level <= self::$level) {
            if ( self::_traceIt( $_file, $_module, $_method)) {
                $caller	=	self::getCallerB( $_file, $_module, $_method) . ">>" . self::$nesting."<<" ;
                self::$nesting++ ;
                $apdx	=	"" ;
                if ( $_mesg != "") {
                    $apdx	=	", mesg := '".$_mesg."' " ;
                }
                error_log( $caller . ": begin" . $apdx) ;
            }
        }
    }
    /**
     *
     * @param integer $_level
     * @param string $_file
     * @param string $_module
     * @param string $_method
     * @param mixed $_mesg
     */
    static	function	end( $_mesg="") {
        if (self::$inst === NULL) {
            self::$inst	=	new self ;
        }
        $_level		=	array_pop( self::$levels) ;
        $_file		=	array_pop( self::$files) ;
        $_module	=	array_pop( self::$modules) ;
        $_method	=	array_pop( self::$methods) ;
        if ( self::$active && $_level <= self::$level) {
            if ( self::_traceIt( $_file, $_module, $_method)) {
                self::$nesting-- ;
                $caller	=	self::getCallerE( $_file, $_module, $_method) . ">>" . self::$nesting."<<" ;
                $apdx	=	"" ;
                if ( is_string( $_mesg)) {
                    $apdx	=	", mesg := '".$_mesg."' " ;
                } else if ( is_bool( $_mesg)) {
                    $apdx	=	", boolean := '".(($_mesg) ? 'true' : 'false')."' ";
                } else if ( is_int( $_mesg)) {
                    $apdx	=	", integer := ".$_mesg." ";
                }
                error_log( $caller . ": end" . $apdx) ;
            }
        }
    }
    /**
     *
     * @param integer $_level
     * @param string $_file
     * @param string $_module
     * @param string $_method
     * @param mixed $_mesg
     */
    static	function	abort( $_mesg="") {
        if (self::$inst === NULL) {
            self::$inst	=	new self ;
        }
        $_level		=	array_peek( self::$levels) ;
        $_file		=	array_peek( self::$files) ;
        $_module	=	array_peek( self::$modules) ;
        $_method	=	array_peek( self::$methods) ;
        $doTrc	=	true ;
        $caller	=	self::getCallerA( $_file, $_module, $_method) ;
        $apdx	=	"" ;
        if ( is_string( $_mesg)) {
            $apdx	=	", mesg := '".$_mesg."' " ;
        } else if ( is_bool( $_mesg)) {
            $apdx	=	", boolean := '".(($_mesg) ? 'true' : 'false')."' ";
        } else if ( is_int( $_mesg)) {
            $apdx	=	", integer := ".$_mesg." ";
        }
        error_log( $caller . ": abort" . $apdx) ;
        $_level		=	array_pop( self::$levels) ;
        $_file		=	array_pop( self::$files) ;
        $_module	=	array_pop( self::$modules) ;
        $_method	=	array_pop( self::$methods) ;
    }
    /**
     *
     * @param integer $_level
     * @param string $_mask
     * @param string $_file
     * @param string $_module
     * @param string $_method
     * @param string $_mesg
     */
    static	function	trace( $_level, $_mask, $_file, $_module, $_method, $_mesg="") {
        if (self::$inst === NULL) {
            self::$inst	=	new self ;
        }
//        $_level		=	array_peek( self::$levels) ;
        if ( self::$active && $_level <= self::$level) {
            if ( self::_traceIt( $_file, $_module, $_method)) {
                error_log( self::getCaller( $_file, $_module, $_method) . ": mesg := '".$_mesg."' ") ;
            }
        }
    }

    /**
     *
     * @param integer $_level
     * @param string $_mask
     * @param string $_file
     * @param string $_module
     * @param string $_method
     * @param string $_mesg
     */
    static	function	traceBool( $_level, $_mask, $_file, $_module, $_method, $_mesg, $_bool) {
        if (self::$inst === NULL) {
            self::$inst	=	new self ;
        }
//        $_level		=	array_peek( self::$levels) ;
        if ( self::$active && $_level <= self::$level) {
            if ( self::_traceIt( $_file, $_module, $_method)) {
                error_log( self::getCaller( $_file, $_module, $_method) . ": mesg := '".$_mesg."' := " . ($_bool?"TRUE":"FALSE")) ;
            }
        }
    }

    /**
     *
     * @param string $_level
     * @param string $_mask
     * @param string $_file
     * @param string $_module
     * @param string $_method
     * @param string $_mesg
     * @param string $_data
     */
    static	function	traceData( $_level, $_mask, $_file, $_module, $_method, $_mesg="", $_data="") {
        $_level		=	current( self::$levels) ;
        if (self::$inst === NULL) {
            self::$inst	=	new self ;
        }
        if ( self::$active && $_level <= self::$level) {
            if ( self::_traceIt( $_file, $_module, $_method)) {
                error_log( self::getCaller( $_file, $_module, $_method) . ": ".$_mesg.":\n" . $_data) ;
            }
        }
    }
    /**
     * dumpL
     *
     * Sends a debugger message to the "output channel" if the debugger is active and the
     * message bit mask matches the current scope bit mask.
     *
     * Vorgeschlagene Dump-Level:
     * 0xFF......	Basis Funktionalitaet, z.B. Package(s): Basis
     * 0x..FF....	Basis Funktionalitaet, z.B. Package(s): PDFDoc, Support
     * 0x....FF..	Basis Funktionalitaet, z.B. Package(s):	Application
     * 0x......FF	Basis Funktionalitaet, z.B. Package(s):	Presentation
     *
     * @param int $_level
     * @param string $_file
     * @param mixed $_p1
     * @param mixed $_p2
     * @param mixed $_p3
     */
    public		function traceBin( $_level, $_mask, $_file, $_module, $_method, $_p1='', $_p2='', $_p3='') {
        $_level		=	current( self::$levels) ;
        if (self::$inst === NULL) {
            self::$inst	=	new self ;
        }
        if ( self::$active && $_level <= self::$level) {
            if ( self::_traceIt( $_file, $_module, $_method)) {
                error_log( self::getCaller( $_file, $_module, $_method) . ": mesg := '".$_p1."' ") ;
            }
        }
    }
    /**
     *
     * @param int   $_level
     * @param string $_file
     * @param string $_module
     * @param string $_method
     * @param string $_mesg
     */
    static	function	traceBack( $_level, $_file, $_module, $_method, $_mesg="") {
        if (self::$inst === NULL) {
            self::$inst	=	new self ;
        }
        if ( self::$active && $_level <= self::$level) {
            if ( self::_traceIt( $_file, $_module, $_method)) {
                $caller	=	self::getCaller( $_file, $_module, $_method) ;
                $apdx	=	"" ;
                if ( $_mesg != "") {
                    $apdx	=	", mesg := '".$_mesg."' " ;
                }
                error_log( $caller . ": begin" . $apdx) ;
                error_log( print_r( debug_backtrace( /*true, 3*/), true)) ;
            }
        }
    }
    /**
     *
     * @param int   $_level
     * @param string $_file
     * @param string $_module
     * @param string $_method
     * @param string    $_mesg
     * @param array $_array
     */
    static	function	traceArray( $_level, $_file, $_module, $_method, $_mesg, $_array) {
        if (self::$inst === NULL) {
            self::$inst	=	new self ;
        }
        if ( self::$active && $_level <= self::$level) {
            if ( self::_traceIt( $_file, $_module, $_method)) {
                $caller	=	self::getCaller( $_file, $_module, $_method) ;
                $apdx	=	"" ;
                if ( $_mesg != "") {
                    $apdx	=	", mesg := '".$_mesg."' " ;
                }
                error_log( $caller . "Array" . $apdx) ;
                error_log( print_r( $_array, true)) ;
            }
        }
    }
    /**
     *
     * @param unknown_type $_list
     */
    static	function	setFileList( $_list) {
        self::$fileList	=	$_list ;
    }
    /**
     *
     * @param unknown_type $_list
     */
    static	function	setModuleList( $_list) {
        self::$modList	=	$_list ;
    }
    /**
     *
     * @param unknown_type $_list
     */
    static	function	setMethodList( $_list) {
        self::$methodList	=	$_list ;
    }
    /**
     *
     * @param unknown_type $_list
     */
    static	function	setFileListExclude( $_list) {
        self::$fileListExclude	=	$_list ;
    }
    /**
     *
     * @param unknown_type $_list
     */
    static	function	setModuleListExclude( $_list) {
        self::$modListExclude	=	$_list ;
    }
    /**
     *
     * @param unknown_type $_list
     */
    static	function	setMethodListExclude( $_list) {
        self::$methodListExclude	=	$_list ;
    }
    /**
     * get
     *
     * Return the ONE AND ONLY instance of the debugger
     *
     * @return FDbg
     */
    public	static	function get() {
        if (self::$inst === NULL) {
            self::$inst	=	new self ;
        }
        return self::$inst ;
    }
    /**
     *	setTextMode()
     *	=============
     *
     * Puts the debugger into Text Mode. Lineendings are basically issued as '\n'
     *
     * @return void
     */
    public		function setTextMode() {
        self::$htmlMode	=	false ;
    }
    /**
     *	setHTMLMode()
     *	=============
     * Puts the debugger into HTML Mode. Lineendings are basically issued as '<br/>\n'
     * ausgegeben.
     *
     * @return void
     */
    public		function setHTMLMode() {
        self::$htmlMode	=	true ;
    }
    /**
     *	enable()
     *	========
     *
     * Enables the debugger output.<br/>
     * When using dumpF(...) this switch is *NOT* considered.
     *
     * @return	void
     */
    public	static	function enable() {
        self::$active	=	true ;
    }
    /**
     *	disable()
     *	=========
     *
     * Disables the debugger output.<br/>
     * When using dumpF(...) this switch is *NOT* considered.
     *
     * @return	void
     */
    public	static	function disable() {
        self::$active	=	false ;
    }
    /**
     * setEnable
     *
     * Another way to enable/disable the output.^
     *
     * @param bool $_active
     * @return void
     */
    public	static	function setEnable( $_active) {
        self::$active	=	$_active ;
    }
    /**
     * setLevel
     *
     * Sets the level (scope) of the current debugger output.
     *
     * @param int $_level
     */
    public	static	function setMask( $_mask) {
        if ( is_string( $_mask)) {
            $myMask	=	hexdec(substr($_mask, 1)) ;
            $_mask	=	$myMask ;
        }
        self::$mask	=	$_mask ;
    }
    /**
     * setApp
     *
     * Sets the level (scope) of the current debugger output.
     *
     * @param string $_app
     */
    public	static	function setApp( $_app) {
        self::$app	=	$_app ;
		( self::$debugFDbg ? error_log( "app ............................. : " . $_app) : null) ;
    }
    /**
     * setApp
     *
     * Sets the level (scope) of the current debugger output.
     *
     * @param string $_appToTrace
     */
    public	static	function setAppToTrace( $_appToTrace) {
        self::$appToTrace	=	$_appToTrace ;
		( self::$debugFDbg ? error_log( "appToTrace ...................... : " . $_appToTrace) : null) ;
    }
    /**
     * setLevel
     *
     * Sets the level (scope) of the current debugger output.
     *
     * @param int $_level
     * @return void
     */
    public	static	function setLevel( $_level) {
        self::$level	=	intval( $_level) ;
    }
    public  static  function getLevel() {
        return self::$level ;
    }
    /**
     * __clone
     *
     * Dummy method to prohibit cloning !
     */
    private function __clone() {}
    /**
     * enabled
     *
     * Returns the status.
     *
     * @return bool whether or not the debugger is enabled
     */
    public	function	enabled() {
        return self::$active ;
    }
    /**
     * _push
     * saves current state, level, file and method on the stack
     *
     * @return void
     */
    public	function	_push() {
        array_push( self::$states, self::$active) ;
        array_push( self::$levels, self::$level) ;
        array_push( self::$files, self::$files) ;
        array_push( self::$modules, self::$modules) ;
        array_push( self::$methods, self::$methods) ;
    }
    /**
     * _pop
     *
     * retrieves last state, level, file and method on the stack
     *
     * @return void
     */
    public	function	_pop() {
        self::$active	=	array_pop( self::$states) ;
        self::$level	=	array_pop( self::$levels) ;
        self::$files	=	array_pop( self::$files) ;
        self::$modules	=	array_pop( self::$modules) ;
        self::$methods	=	array_pop( self::$methods) ;
    }
    /**
     *
     */
    private	static	function	getCallerB( $_file, $_module, $_method) {
        $caller	=	"" ;
        if ( self::$microTime) {
            $caller .=  strtok( microtime(), " ") ;
        }
        if ( self::$memory) {
            $caller .=  "::[" . sprintf( "%9d", memory_get_usage()) . "]" ;
        }
        if ( self::$app != "") {
            $caller	.=	":" . self::$app . ":>" . str_repeat( self::$indent, self::$nesting) . "   >" ;
        }
        $caller	.=	$_file . /* "::" . $_module . */ "::" . $_method ;
        return $caller ;
    }
    /**
     *
     */
    private	static	function	getCallerE( $_file, $_module, $_method) {
        $caller	=	"" ;
        if ( self::$microTime) {
            $caller .=  strtok( microtime(), " ") ;
        }
        if ( self::$memory) {
            $caller .=  "::[" . sprintf( "%9d", memory_get_usage()) . "]" ;
        }
        if ( self::$app != "") {
            $caller	.=	":" . self::$app . ":>" . str_repeat( self::$indent, self::$nesting) . "   <" ;
        }
        $caller	.=	$_file . /* "::" . $_module . */ "::" . $_method ;
        return $caller ;
    }
    /**
     *
     */
    private	static	function	getCallerA( $_file, $_module, $_method) {
        $caller	=	"" ;
        if ( self::$microTime) {
            $caller .=  strtok( microtime(), " ") ;
        }
        if ( self::$memory) {
            $caller .=  "::[" . sprintf( "%9d", memory_get_usage()) . "]" ;
        }
        if ( self::$app != "") {
            $caller	.=	":" . self::$app . ":>" . str_repeat( self::$indent, self::$nesting) . "   @" ;
        }
        $caller	.=	$_file . /* "::" . $_module . */ "::" . $_method ;
        return $caller ;
    }
    /**
     *
     */
    private	static	function	getCaller( $_file, $_module, $_method) {
        $caller	=	"" ;
        if ( self::$microTime) {
            $caller .=  strtok( microtime(), " ") ;
        }
        if ( self::$memory) {
            $caller .=  "::[" . sprintf( "%9d", memory_get_usage()) . "]" ;
        }
        if ( self::$app != "") {
            $caller	.=	":" . self::$app . ":>" . str_repeat( self::$indent, self::$nesting) . "   " ;
        }
        $caller	.=	$_file . /* "::" . $_module . */ "::" . $_method ;
        return $caller ;
    }
    /**
     *
     */
    private	static	function	_traceIt( $_file, $_module, $_method)
    {
        $ret = false;
//        array_push(self::$levels, $_level);
//        array_push(self::$files, $_file);
//        array_push(self::$modules, $_module);
//        array_push(self::$methods, $_method);
//        error_log( $_file . "---------->" . self::$fileListExclude) ;
//        error_log( "...: " . self::$fileListExclude . " ... " . $_file) ;
        if (self::$appToTrace == self::$app || self::$appToTrace == "*") {
            if ((self::$fileList == "*" || strpos(self::$fileList, $_file) !== false) &&
                (self::$fileListExclude == "" || strpos(self::$fileListExclude, $_file) === false)) {
//                error_log( "'".self::$modList."'::'".self::$modListExclude."' >> '".$_module."'::'");
                if ((self::$modList == "*" || strpos(self::$modList, ",".$_module.",") !== false) &&
                    (self::$modListExclude == "" || strpos(self::$modListExclude, ",".$_module.",") === false)) {
//                    error_log( "          '".self::$methodList."'::'".self::$methodListExclude."' >> '".$_method."'::'");
                    if ((self::$methodList == "*" || strpos(self::$methodList, $_method) !== false) &&
                        (self::$methodListExclude == "" || strpos(self::$methodListExclude, $_method) === false)) {
                        $ret = true;
                    } else {
                        ( self::$debugFDbg ? error_log( self::$p . "kicked out on method ... " . $_method) : null) ;
                    }
                } else {
                    ( self::$debugFDbg ? error_log( self::$p . "kicked out on module ... " . $_module . "[".self::$modList."]" . "[".self::$modListExclude."]") : null) ;
                }
            } else {
                ( self::$debugFDbg ? error_log( self::$p . "kicked out on file ... " . $_file . "[".self::$fileList."]" . "[".self::$fileListExclude."]") : null) ;
            }
        } else {
            ( self::$debugFDbg ? error_log( self::$p . "kicked out on app ... " . self::$appToTrace  . " versus actual " . self::$app) : null) ;
        }
        return $ret ;
    }
}
/**
 * peek at the top of a stack
 *
 * @package wtcCore
 * @subpackage Tracing
 */
function	array_peek( $_v) {
    $v	=	array_pop( $_v) ;
    array_push( $_v, $v) ;
    return $v ;
}
?>
