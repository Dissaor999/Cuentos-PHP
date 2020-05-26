<?php
class Bootstrap
{
    public static function run(Request $peticion){
	
        $controller = $peticion->getControlador() ;

        $controller_path=ROOT . 'controllers' . DS;

        if($peticion->_admin) $controller_path.='admin'.DS;
        else $controller_path.='web'.DS;


        $rutaControlador = $controller_path  . $controller . '.php';

        $metodo = $peticion->getMetodo();
        $args = $peticion->getArgs();

        if(is_readable($rutaControlador)){
            require_once $rutaControlador;
			$rcontroller = str_replace('-', '_', $controller);
            $controller = new $rcontroller;
           
            if (is_callable(array($controller, $metodo)))  $metodo = $peticion->getMetodo();  else $metodo = 'index';
            if (isset($args)) call_user_func_array(array($controller, $metodo), $args); else call_user_func(array($controller, $metodo));
            
        } else {
            $controller='verror';
            $rutaControlador = $controller_path  . $controller . '.php';

            require_once $rutaControlador;
            $controller = new $controller;
            $metodo = 'index';
            if(is_callable(array($controller, $metodo)))  $metodo = $peticion->getMetodo();  else $metodo = 'index';

            if (isset($args)) call_user_func_array(array($controller, $metodo), $args); else call_user_func(array($controller, $metodo));

        }

    }
}

?>