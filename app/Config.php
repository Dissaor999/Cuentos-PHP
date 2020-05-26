<?php
date_default_timezone_set('Europe/Madrid');

        define('BASE_URL', 'http://cuento.test:8080/');//Aqui sucede la magia
        define('ADMIN_URL', BASE_URL.'admin/');

         define('DEFAULT_CONTROLLER', 'index', true);

        define('DEFAULT_LAYOUT', 'default');

        define('DEFAULT_LANG', 'es');

        define('USER_VISITOR_ROLE', '3');


        define('APP_NAME', 'Mi Cuento Mágico');
        define('APP_COMPANY', 'Mi Cuento Mágico');

        define("DB_HOST", "127.0.0.1");
        define("DB_USER", "root");
        define("DB_PASSWORD", "");
        define("DB_DATABASE", "mi_cuento");

        // define('DB_HOST', '127.0.0.1' );
        // define('DB_NAME', 'mi_cuento' );
        // define('DB_USER', 'root' );
        //define('DB_PASS', 'A1pm&h60' );
        define('DB_CHAR', 'utf8' );

    ini_set('display_errors',1);
    error_reporting(E_ALL);

        define('RSS_IG', '#' );
        define('RSS_TW', '#' );
        define('RSS_FB', '#' );
        define('GTS_ENVIO', '0' );



        define('EMAIL_ADMIN', 'info@micuentomagico.com' );


        define('MAX_FILE_SIZE', 200000000,true);

?>
