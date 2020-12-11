<?php

if ( isset( $_POST['actionRefresh'])) {
        include( "showLiefFormat.php") ;
} else if ( isset( $_POST['actionEvaluate'])) {
        include( "evalLiefFormat.php") ;
}

?>
