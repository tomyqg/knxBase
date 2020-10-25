set( lib_SRCS eib tty debug mylib inilib crc16lib knxCoreClass)
add_library( eibknxlib SHARED ${lib_SRCS})

install( TARGETS eibknxlib DESTINATION /usr/lib)
