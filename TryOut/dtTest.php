<?php

    $format =   "M j Y g:i:s:ua" ;
    $str    =   "May 7 2015 02:42:46:000AM" ;
    print_r( date_parse_from_format( $format, $str)) ;
?>
