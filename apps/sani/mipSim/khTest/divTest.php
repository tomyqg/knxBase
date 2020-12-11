<?php
/**
 * Created by PhpStorm.
 * User: khwelter
 * Date: 25.08.2016
 * Time: 12:37
 */
?>

<html>
    <head>
        <style type="text/css">
.t1 {
background-color: #990000 ;
}
.t1s1 {
    background-color: #00ff00 ;
}
.t2s1 {
    background-color: #aaaaff ;
}
.t2s2 {
    background-color: #ffaaff ;
}
.c2 {

}
.c3 {

}
.c4 {

}
        </style>
        <script type="text/javascript" src="../lib/jquery-1.10.1.min.js"></script>
        <script type="text/javascript" src="../lib/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
        <link rel="stylesheet" href="../lib/jquery-ui-1.10.3.custom/css/start/jquery-ui-1.10.3.custom.min.css">
        <script>
            $( function() {
                            console.log( "starting up ...") ;
                            var screen1 =   $("#top1") ;
                            var screen2 =   $("#top2") ;
                            screen1.find("#sub1").addClass( "t1s1") ;
                            screen2.find("#sub1").addClass( "t2s1") ;
                        }) ;
            $( function() {
                console.log( "starting up ...") ;
                var screen3 =   $("#top2 #sub2") ;
                screen3.addClass( "t2s2") ;
                $( "#top1 #mydate1").datepicker();
                $( "#top2 #mydate2").datepicker();
            }) ;
        </script>

    </head>
    <body>
        <div id="top1">
            <div id="sub1">
                Hello top1.sub1
                <input id="mydate1" type="text" >
            </div>
            <div id="sub1">
                Hello top1.sub2
            </div>
        </div>
        <div id="top2">
            <div id="sub1">
                Hello top2.sub1
                <input id="mydate2" type="text" >
            </div>
            <div id="sub2">
                Hello top3.sub2
            </div>
        </div>
    </body>
</html>
