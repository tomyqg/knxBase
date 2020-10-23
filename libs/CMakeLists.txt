set( lib_SRCS eib tty debug mylib inilib)
add_library( eibknxlib SHARED ${lib_SRCS})

install( TARGETS eibknxlib DESTINATION /usr/lib)
