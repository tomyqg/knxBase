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
 * myxml.c
 *
 * some useful functions
 *
 * Revision history
 *
 * date		rev.	who	what
 * ----------------------------------------------------------------------------
 * 2015-11-20	PA1	userId	inception;
 *
 */
#include	<stdio.h>
#include	<stdlib.h>
#include	<stdint.h>
#include	<string.h>
#include	<strings.h>
#include	<time.h>
#include	<math.h>
#include	<sys/types.h>
#include	<sys/ipc.h>
#include	<sys/shm.h>
#include	<libxml/xmlreader.h>

#include	"debug.h"
#include	"mylib.h"
#include	"eib.h"
/**
 * processNode:
 * @reader: the xmlReader
 *
 * Dump information about the current node
 */
int	count( xmlTextReaderPtr reader ) {
	const	xmlChar *name, *value;
	int	count ;

	name = xmlTextReaderConstName(reader);
	if (name == NULL)
	name = BAD_CAST "--";

	count	=	0 ;
	while ( xmlTextReaderRead( reader) == 1) {
		switch ( xmlTextReaderNodeType(reader)) {
		case	XML_READER_TYPE_ELEMENT	:
			if ( strcmp((char *) xmlTextReaderName( reader), "GroupAddress") == 0) {
				count++ ;
			}
			break ;
		}
	}
//	printf( ".............: contains %d lines \n", count) ;
	return count ;
}

int	lookupDPT( char *_dpt) {
	int	ret	=	-1 ;
	if ( strncmp( _dpt, "1.", 2) == 0) {
		ret	=	dtBit ;
	} else if ( strncmp( _dpt, "2.", 2) == 0) {
		ret	=	dtFloat2 ;
	} else if ( strncmp( _dpt, "3.", 2) == 0) {
		ret	=	dtFloat2 ;
	} else if ( strncmp( _dpt, "4.", 2) == 0) {
		ret	=	dtFloat2 ;
	} else if ( strncmp( _dpt, "5.", 2) == 0) {
		ret	=	dtUInt1 ;
	} else if ( strncmp( _dpt, "6.", 2) == 0) {
		ret	=	dtFloat2 ;
	} else if ( strncmp( _dpt, "7.", 2) == 0) {
		ret	=	dtUInt2 ;
	} else if ( strncmp( _dpt, "8.", 2) == 0) {
		ret	=	dtFloat2 ;
	} else if ( strncmp( _dpt, "9.", 2) == 0) {
		ret	=	dtFloat2 ;
	} else if ( strncmp( _dpt, "10.", 3) == 0) {
		ret	=	dtDate ;
	} else if ( strncmp( _dpt, "11.", 3) == 0) {
		ret	=	dtTime ;
	} else if ( strncmp( _dpt, "12.", 3) == 0) {
		ret	=	dtDateTime ;
	} else {
	}
	return ret ;
}

int	processMacroNode( xmlTextReaderPtr reader, node *nodeTable, int _baseGroupAddress, FILE *_fo, char *baseName, int _offset) {
	int	index ;
	xmlChar	*attrVal ;
	char	buf1[32] ;

	index	=	0 ;
	while ( xmlTextReaderRead( reader) == 1) {
		switch ( xmlTextReaderNodeType(reader)) {
		case	XML_READER_TYPE_ELEMENT	:
			if ( strcmp((char *) xmlTextReaderName( reader), "GroupAddress") == 0) {
				attrVal	=	xmlTextReaderGetAttribute( reader, (xmlChar *) "postfix") ;
				strcpy( nodeTable[_offset+index].name, baseName ) ;
				strcat( nodeTable[_offset+index].name, "_") ;
				strcat( nodeTable[_offset+index].name, (char *) attrVal) ;
				free( attrVal) ;
				nodeTable[_offset+index].knxGroupAddr	=	_baseGroupAddress + index ;
				attrVal	=	xmlTextReaderGetAttribute( reader, (xmlChar *) "dpt") ;
				nodeTable[_offset+index].type	=	lookupDPT( (char *) attrVal) ;
				free( attrVal) ;
				attrVal	=	xmlTextReaderGetAttribute( reader, (xmlChar *) "default") ;
				if ( attrVal != NULL) {
					strcpy( nodeTable[_offset+index].defaultVal, (char *) attrVal) ;
					free( attrVal) ;
				} else {
					strcpy( nodeTable[_offset+index].defaultVal, "") ;
				}
				if ( _fo) {
					fprintf(  _fo, "\t\t\t<GroupAddress Name=\"%s\" Address=\"%s\" />\n",
								nodeTable[_offset+index].name, eibIntToGroup( nodeTable[_offset+index].knxGroupAddr, buf1)) ;
				}

				index++ ;
			}
			break ;
		case	XML_READER_TYPE_END_ELEMENT	:
			break ;
		}
	}
	return index ;
}

void	processNode( xmlTextReaderPtr reader, node *nodeTable) {
	int	index ;
	int	tabs ;
	const xmlChar *name, *value;
	xmlChar	*attrVal ;
	FILE	*fo ;
	char	macroName[64] ;
	char	macroPrefix[64] ;
	int	macroBaseGroupAddress ;
	xmlTextReaderPtr macroReader;

	name = xmlTextReaderConstName(reader);
	if (name == NULL)
	name = BAD_CAST "--";

	fo	=	fopen( "/tmp/groupsForETS.xml", "w+") ;
	if ( fo) {
		fprintf( fo, "<?xml version=\"1.0\" encoding=\"utf-8\" standalone=\"yes\"?>\n") ;
	}

	tabs	=	0 ;
	index	=	0 ;
	while ( xmlTextReaderRead( reader) == 1) {
		switch ( xmlTextReaderNodeType(reader)) {
		case	XML_READER_TYPE_ELEMENT	:
			if ( strcmp((char *) xmlTextReaderName( reader), "macro") == 0) {
				attrVal	=	xmlTextReaderGetAttribute( reader, (xmlChar *) "name") ;
				strcpy( macroName, (char *) attrVal) ;
				free( attrVal) ;
				attrVal	=	xmlTextReaderGetAttribute( reader, (xmlChar *) "prefix") ;
				strcpy( macroPrefix, (char *) attrVal) ;
				free( attrVal) ;
				attrVal	=	xmlTextReaderGetAttribute( reader, (xmlChar *) "baseGroupAddress") ;
				macroBaseGroupAddress	=	eibGroupToInt((char *) attrVal) ;
				free( attrVal) ;

				/**
				 * open the xml file and build the table
				 */
				macroReader	=	xmlReaderForFile( macroName, NULL, XML_PARSE_NOENT) ;
				index	+=	processMacroNode( macroReader, nodeTable, macroBaseGroupAddress, fo, macroPrefix, index) ;

				/**
				 * Once the document has been fully parsed check the validation results
				 */
				xmlFreeTextReader( macroReader) ;

			} else if ( strcmp((char *) xmlTextReaderName( reader), "GroupAddress") == 0) {
				attrVal	=	xmlTextReaderGetAttribute( reader, (xmlChar *) "Name") ;
				strcpy( nodeTable[index].name, (char *) attrVal) ;
				free( attrVal) ;
				attrVal	=	xmlTextReaderGetAttribute( reader, (xmlChar *) "alias") ;
				strcpy( nodeTable[index].alias, (char *) attrVal) ;
				free( attrVal) ;
				attrVal	=	xmlTextReaderGetAttribute( reader, (xmlChar *) "Address") ;
				nodeTable[index].knxGroupAddr	=	eibGroupToInt( attrVal) ;
				free( attrVal) ;
				attrVal	=	xmlTextReaderGetAttribute( reader, (xmlChar *) "dpt") ;
				nodeTable[index].type	=	lookupDPT( (char *) attrVal) ;
				free( attrVal) ;
				attrVal	=	xmlTextReaderGetAttribute( reader, (xmlChar *) "default") ;
				if ( attrVal != NULL) {
					strcpy( nodeTable[index].defaultVal, (char *) attrVal) ;
					free( attrVal) ;
				} else {
					strcpy( nodeTable[index].defaultVal, "") ;
				}

//				printf( "Element node\n") ;
//				printf( "  Id............: %s \n", (char *) xmlTextReaderGetAttribute( reader, (xmlChar *) "id")) ;
//				printf( "  Name..........: '%s' \n", nodeTable[index].name) ;
//				printf( "  Alias.........: '%s' \n", nodeTable[index].alias) ;
//				printf( "  DPT...........: %s(%d) \n", (char *) xmlTextReaderGetAttribute( reader, (xmlChar *) "dpt"), nodeTable[index].type) ;
//				printf( "  Group addr....: %s \n", (char *) xmlTextReaderGetAttribute( reader, (xmlChar *) "Address")) ;
//				printf( "  Trigger only..: %s \n", (char *) xmlTextReaderGetAttribute( reader, (xmlChar *) "trigger")) ;

				if ( fo) {
					if ( tabs == 1) {
						fprintf( fo, "\t") ;
					} else if ( tabs == 2) {
						fprintf( fo, "\t\t") ;
					} else if ( tabs == 3) {
						fprintf( fo, "\t\t\t") ;
					}
					fprintf( fo, "<GroupAddress Name=\"%s\" Address=\"%s\" />\n",
								(char *) xmlTextReaderGetAttribute( reader, (xmlChar *) "Name"),
								(char *) xmlTextReaderGetAttribute( reader, (xmlChar *) "Address")) ;
				}
				index++ ;

			} else if ( strcmp((char *) xmlTextReaderName( reader), "GroupAddress-Export") == 0) {
				if ( fo) {
					if ( tabs == 1) {
						fprintf( fo, "\t") ;
					} else if ( tabs == 2) {
						fprintf( fo, "\t\t") ;
					} else if ( tabs == 3) {
						fprintf( fo, "\t\t\t") ;
					}
					fprintf( fo, "<GroupAddress-Export xmlns=\"%s\">\n",
								(char *) xmlTextReaderGetAttribute( reader, (xmlChar *) "xmlns")) ;
								
				}
				tabs++ ;
			} else if ( strcmp((char *) xmlTextReaderName( reader), "GroupRange") == 0) {
				if ( fo) {
					if ( tabs == 1) {
						fprintf( fo, "\t") ;
					} else if ( tabs == 2) {
						fprintf( fo, "\t\t") ;
					} else if ( tabs == 3) {
						fprintf( fo, "\t\t\t") ;
					}
					fprintf( fo, "<GroupRange Name=\"%s\" RangeStart=\"%d\" RangeEnd=\"%d\">\n",
								(char *) xmlTextReaderGetAttribute( reader, (xmlChar *) "Name"),
								eibGroupToInt( (char *) xmlTextReaderGetAttribute( reader, (xmlChar *) "RangeStart")),
								eibGroupToInt( (char *) xmlTextReaderGetAttribute( reader, (xmlChar *) "RangeEnd"))) ;
				}
				tabs++ ;
			}
			break ;
		case	XML_READER_TYPE_END_ELEMENT	:
			tabs-- ;
			if ( strcmp((char *) xmlTextReaderName( reader), "macroLight") == 0) {
			} else if ( strcmp((char *) xmlTextReaderName( reader), "GroupAddress-Export") == 0) {
				if ( fo) {
					if ( tabs == 1) {
						fprintf( fo, "\t") ;
					} else if ( tabs == 2) {
						fprintf( fo, "\t\t") ;
					} else if ( tabs == 3) {
						fprintf( fo, "\t\t\t") ;
					}
					fprintf( fo, "</GroupAddress-Export>\n") ;
				}
			} else if ( strcmp((char *) xmlTextReaderName( reader), "GroupRange") == 0) {
				if ( fo) {
					if ( tabs == 1) {
						fprintf( fo, "\t") ;
					} else if ( tabs == 2) {
						fprintf( fo, "\t\t") ;
					} else if ( tabs == 3) {
						fprintf( fo, "\t\t\t") ;
					}
					fprintf( fo, "</GroupRange>\n") ;
				}
			}
			break ;
		}
	}
	if ( fo) {
		fclose( fo) ;
	}
}

/**
 * streamFile:
 * @filename: the file name to parse
 *
 * Parse, validate and print information about an XML file.
 */
node	*getNodeTable( const char *filename, int *_count) {
	xmlTextReaderPtr reader;
	int ret;
	node	*nodeTable ;
	nodeTable	=	NULL ;
	/*
	* Pass some special parsing options to activate DTD attribute defaulting,
	* entities substitution and DTD validation
	*/
//	reader = xmlReaderForFile(filename, NULL,
//				XML_PARSE_DTDATTR |  /* default DTD attributes */
//				XML_PARSE_NOENT |    /* substitute entities */
//				XML_PARSE_DTDVALID); /* validate with the DTD */
	reader = xmlReaderForFile(filename, NULL, XML_PARSE_DTDATTR | XML_PARSE_NOENT) ;
	if (reader != NULL) {
		*_count	=	1000 ;		// count( reader);
		nodeTable	=	malloc( *_count * ( sizeof( node))) ;
		xmlFreeTextReader(reader) ;
		/**
		 * re-open the xml file and build the table
		 */
		reader	 =	xmlReaderForFile(filename, NULL, XML_PARSE_NOENT) ;
		processNode( reader, nodeTable) ;
		/**
		 * Once the document has been fully parsed check the validation results
		 */
//		if ( xmlTextReaderIsValid( reader) != 1) {
//			fprintf(stderr, "Document %s does not validate\n", filename);
//		}
		xmlFreeTextReader(reader);
	} else {
		fprintf(stderr, "Unable to open %s\n", filename);
	}
	return nodeTable ;
}
