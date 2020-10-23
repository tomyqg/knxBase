cmake_minimum_required(VERSION 3.7.2)
message( STATUS "CMAKE_SOURCE_DIR.............: " ${CMAKE_SOURCE_DIR})
set( CMAKE_MODULE_PATH "${CMAKE_SOURCE_DIR}/build/cmake")
project (KNXTOOLS)

#
# Find Libxml2
#
FIND_PACKAGE(LibXml2)
if ( LIBXML2_FOUND)
	message( STATUS "libxml include directory.....: " ${LIBXML2_INCLUDE_DIR})
	message( STATUS "libxml libraries.............: " ${LIBXML2_LIBRARIES})
  INCLUDE_DIRECTORIES(${LIBXML2_INCLUDE_DIR})
  LIST(APPEND ADDITIONAL_LIBS ${LIBXML2_LIBRARIES})
  SET(HAVE_LIBXML2 1)
  # libxml2's include files use iconv.h
  # We need a directory path of iconv.h so that it won't fail to check
  # "libxml/xmlreader.h".
  FIND_PATH(ICONV_INCLUDE_DIR iconv.h)
  INCLUDE_DIRECTORIES(${ICONV_INCLUDE_DIR})
  SET(CMAKE_REQUIRED_INCLUDES ${ICONV_INCLUDE_DIR} ${LIBXML2_INCLUDE_DIR})
#  CHECK_INCLUDE_FILES("libxml/xmlreader.h" HAVE_LIBXML_XMLREADER_H)
  SET(CMAKE_REQUIRED_INCLUDES "")
ELSE(LIBXML2_FOUND)
  #
  # Find Expat
  #
  FIND_PACKAGE(EXPAT)
  IF(EXPAT_FOUND)
    INCLUDE_DIRECTORIES(${EXPAT_INCLUDE_DIR})
    LIST(APPEND ADDITIONAL_LIBS ${EXPAT_LIBRARIES})
    SET(HAVE_LIBEXPAT 1)
    LA_CHECK_INCLUDE_FILE("expat.h" HAVE_EXPAT_H)
  ENDIF(EXPAT_FOUND)
ENDIF(LIBXML2_FOUND)

find_package( MariaDBClient)
if ( MariaDBClient_FOUND)
	message( STATUS "MariaDBClient include dir............: " ${MariaDBClient_INCLUDE_DIR})
	message( STATUS "MariaDBClient libraries..............: " ${MariaDBClient_LIBRARIES})
	message( "found MariaDBClient ...")
	include_directories( ${MariaDBClient_INCLUDE_DIRS})
endif( MariaDBClient_FOUND)

# - Find libmosquitto
# Find the native libmosquitto includes and libraries
#
#  MOSQUITTO_INCLUDE_DIR - where to find mosquitto.h, etc.
#  MOSQUITTO_LIBRARIES   - List of libraries when using libmosquitto.
#  MOSQUITTO_FOUND       - True if libmosquitto found.

if (NOT MOSQUITTO_INCLUDE_DIR)
  find_path(MOSQUITTO_INCLUDE_DIR mosquitto.h)
endif()

if (NOT MOSQUITTO_LIBRARY)
  find_library(
    MOSQUITTO_LIBRARY
    NAMES mosquitto)
endif()

include(FindPackageHandleStandardArgs)

find_package_handle_standard_args(
  MOSQUITTO DEFAULT_MSG
  MOSQUITTO_LIBRARY MOSQUITTO_INCLUDE_DIR)

message(STATUS "libmosquitto include dir: ${MOSQUITTO_INCLUDE_DIR}")
message(STATUS "libmosquitto: ${MOSQUITTO_LIBRARY}")
set(MOSQUITTO_LIBRARIES ${MOSQUITTO_LIBRARY})

mark_as_advanced(MOSQUITTO_INCLUDE_DIR MOSQUITTO_LIBRARY)

add_subdirectory (apps)
add_subdirectory (libs)

# install( DIRECTORY knx.d DESTINATION /etc)

message( "cmake configuration finished")
