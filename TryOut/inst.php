<?php

/**
 *
 */
 new x() ;

class A
{
    function __construct()
    {
        echo "Konstruiere A\n" ;
    }
}
class B
{
    function __construct()
    {
        echo "Konstruiere B\n" ;
    }
}
class x
{
    function __construct()
    {
        echo "Konstruiere x\n" ;
        A::__construct() ;
    }
}
