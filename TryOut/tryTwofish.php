<?php
        echo "---------------------------------------------------------------------------\n" ;
        print_r( mcrypt_list_algorithms()) ;
        
        $key    =       hash('sha256', 'Geheimer Schlüssel', true);
        $input  =       "Treffpunkt: 9 Uhr am geheimen Platz.";
        echo "---------------------------------------------------------------------------\n" ;
        echo $input . "\n" ;

        $td     =       mcrypt_module_open('rijndael-128', '', 'cbc', '');
        $iv     =       mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);
        mcrypt_generic_init($td, $key, $iv);
        $encrypted_data =       mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        echo "---------------------------------------------------------------------------\n" ;
        echo $encrypted_data . "\n" ;

        $td     =       mcrypt_module_open('twofish-192', '', 'cbc', '');
        $iv     =       mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);
        mcrypt_generic_init($td, $key, $iv);
        $encrypted_data =       mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        echo "---------------------------------------------------------------------------\n" ;
        echo $encrypted_data . "\n" ;

        echo "---------------------------------------------------------------------------\n" ;
?>
