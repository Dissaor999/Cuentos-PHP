<?php


    define('DS', '/');
    define('TS', ' - ');
    define('ROOT', realpath(dirname(__FILE__)) . DS);

    define('APP_PATH', ROOT . 'app' . DS );
    define('FONT_PATH', ROOT . 'fonts' . DS );

    ini_set('display_errors',1);
    error_reporting(E_ALL);
    require_once APP_PATH . 'Session.php';
    Session::init();

    require_once APP_PATH . 'Config.php';
    require_once APP_PATH . 'Request.php';
    require_once APP_PATH . 'Languages.php';

    require_once APP_PATH . 'Bootstrap.php';
    require_once APP_PATH . 'Controller.php';
    require_once APP_PATH . 'Model.php';

    require_once APP_PATH . 'Registro.php';

    require_once APP_PATH . 'Database.php';
    require_once APP_PATH . 'Mailman.php';

    global $db;
    $db = new Database();

    require_once APP_PATH . 'User.php';

    global $me;
    $me = new CUser();


    require_once APP_PATH . 'View.php';

    try{
        Bootstrap::run(new Request);


    } catch(Exception $e){
        echo $e->getMessage();
    }