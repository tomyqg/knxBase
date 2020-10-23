include_directories( AFTER /usr/local/Cellar/openssl/1.0.2e/include ../libs ${MYSQL_INCLUDE_DIR})
link_directories( /usr/local/Cellar/openssl/1.0.2e/lib)

add_executable ( demomcsend demomcsend.c)
add_executable ( demomclisten demomclisten.c)

target_link_libraries ( demomcsend)
target_link_libraries ( demomclisten)

install( TARGETS demomcsend RUNTIME DESTINATION /usr/bin LIBRARY DESTINATION /usr/lib PERMISSIONS SETUID)
install( TARGETS demomclisten RUNTIME DESTINATION /usr/bin LIBRARY DESTINATION /usr/lib PERMISSIONS SETUID)
