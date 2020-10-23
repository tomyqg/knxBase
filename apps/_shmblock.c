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
 * _shmblock.c
 *
 * complete code-block to establish the shared memory
 * must NOT be included by knxmon.c since this include file
 * does not include the IPC_CREAT mask!
 *
 * Revision history
 *
 * date		rev.	who	what
 * ----------------------------------------------------------------------------
 * 2015-11-20	PA1	khw	inception;
 * 2016-11-09	PA2	khw	added the general part for the COMKey table;
 *				its being used anywhere else in every case;
 *
 */
/**
 *
 */
	/**
	 * get and attach the shared memory for COMtable
	 */
	_debug( 1, progName, "trying to obtain shared memory COMtable ...") ;
	if (( shmCOMId = shmget( shmCOMKey, shmCOMSize, 0600)) < 0) {
		_debug( 0, progName, "shmget failed for COMtable");
		_debug( 0, progName, "Exiting with -1");
		exit( -1) ;
	}
	_debug( 1, progName, "trying to attach shared memory for COMtable") ;
	if (( sizeTable = (int *) shmat( shmCOMId, NULL, 0)) == (int *) -1) {
		_debug( 0, progName, "shmat failed for COMtable");
		_debug( 0, progName, "Exiting with -1");
		exit( -1) ;
	}
	if ( debugLevel > 10) {
		_debug( 11, progName, "Size Information Table") ;
		_debug( 11, progName, "shmCOMKey........: %d", shmCOMKey) ;
		_debug( 11, progName, "  shmCOMSize.....: %d", shmCOMSize) ;
		_debug( 11, progName, "  shmCOMId.......: %d", shmCOMId) ;
		_debug( 11, progName, "  Address........: %8lx", (unsigned long) sizeTable) ;
	}
	shmCOMSize	=	sizeTable[ SIZE_COM_TABLE] ;
	shmOPCSize	=	sizeTable[ SIZE_OPC_TABLE] ;
	shmKNXSize	=	sizeTable[ SIZE_KNX_TABLE] ;
	shmCRFSize	=	sizeTable[ SIZE_CRF_TABLE] ;
	/**
	 * setup the shared memory for OPCtable
	 */
	_debug( 1, progName,  "trying to obtain shared memory OPCtable ...") ;
	if (( shmOPCId = shmget (shmOPCKey, shmOPCSize, shmOPCFlg)) < 0) {
		_debug( 0, progName, "knxmon: shmget failed for OPCtable");
		_debug( 0, progName, "Exiting with -101");
		exit( -101);
	}
	_debug( 1, progName,  "trying to attach shared memory OPCtable") ;
	if (( data = (node *) shmat(shmOPCId, NULL, 0)) == (node *) -1) {
		_debug( 0, progName, "knxmon: shmat failed for OPCtable");
		_debug( 0, progName, "Exiting with -102");
		exit( -102);
	}
	if ( debugLevel > 10) {
		_debug( 11, progName, "Object Informtion Table") ;
		_debug( 11, progName, "shmOPCKey........: %d", shmOPCKey) ;
		_debug( 11, progName, "  shmOPCSize.....: %d", shmOPCSize) ;
		_debug( 11, progName, "  shmOPCId.......: %d", shmOPCId) ;
		_debug( 11, progName, "  Address........: %8lx", (unsigned long) data) ;
	}
	/**
	 * setup the shared memory for KNXtable
	 */
	_debug( 1, progName,  "trying to obtain shared memory KNXtable ...") ;
	if (( shmKNXId = shmget( shmKNXKey, shmKNXSize, IPC_CREAT | 0600)) < 0) {
		_debug( 0, progName, "shmget failed for KNXtable") ;
		_debug( 0, progName, "Exiting with -111");
		exit( -111) ;
	}
	_debug( 1, progName,  "trying to attach shared memory KNX table") ;
	if (( floats = (float *) shmat( shmKNXId, NULL, 0)) == (float *) -1) {
		_debug( 0, progName, "shmat failed for KNXtable") ;
		_debug( 0, progName, "Exiting with -112");
		exit( -112) ;
	}
	if ( debugLevel > 10) {
		_debug( 11, progName, "Values Table") ;
		_debug( 11, progName, "shmKNXKey........: %d", shmKNXKey) ;
		_debug( 11, progName, "  shmKNXSize.....: %d", shmKNXSize) ;
		_debug( 11, progName, "  shmKNXId.......: %d", shmKNXId) ;
		_debug( 11, progName, "  Address........: %8lx", (unsigned long) floats) ;
	}
	ints	=	(int *) floats ;
	/**
	 * setup the shared memory for CRFtable
	 */
	_debug( 1, progName,  "trying to obtain shared memory CRFtable ...") ;
	if (( shmCRFId = shmget( shmCRFKey, shmCRFSize, IPC_CREAT | 0600)) < 0) {
		_debug( 0, progName, "knxmon: shmget failed for CRFtable") ;
		_debug( 0, progName, "Exiting with -121");
		exit( -121) ;
	}
	_debug( 1, progName,  "trying to attach shared memory CRFtable...") ;
	if (( crf = (int *) shmat( shmCRFId, NULL, 0)) == (int *) -1) {
		_debug( 0, progName, "knxmon: shmat failed for CRFtable");
		_debug( 0, progName, "Exiting with -122");
		exit( -122) ;
	}
	if ( debugLevel > 10) {
		_debug( 11, progName, "Cross-Reference Table") ;
		_debug( 11, progName, "shmCRFKey........: %d", shmCRFKey) ;
		_debug( 11, progName, "  shmCRFSize.....: %d", shmCRFSize) ;
		_debug( 11, progName, "  shmCRFId.......: %d", shmCRFId) ;
		_debug( 11, progName, "  Address........: %8lx", (unsigned long) crf) ;
	}
