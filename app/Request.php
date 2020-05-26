<?php

class Request {
    private $_controlador;
    private $_metodo;
    private $_argumentos;
    private $_gets;
    private $_lang;
    public $_admin=false;
    public  $_default_lang;

    public function getDL () {return $this->_default_lang;}
    public function __construct() {

        //Definir el lenguaje predeterminado de la Base de Datos
        $lang = Languages::getDefaultLang();
        if(!$lang) $lang=DEFAULT_LANG;


        if(isset($_SERVER['REQUEST_URI'])){
            $url = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);

            if(strpos($url,'?')) {
                $gets = array_filter(explode('?', substr($url, 1) ));
                if(isset($gets[1]))    $this->_gets = $gets[1];

            }
            $url = array_filter(explode('?', substr($url, 0) ));

            $url = array_filter(explode('/', substr($url[0], 1) ));


            //COMPROBAR QUÉ ENTORNO QUIERE

            if(isset($url[0]) && $url[0]== 'admin')  {
                $this->_admin = true;
                array_shift($url);
            }

            //Comprobar si existe el lenguaje en la BBDD
            if(isset($url[0]) && Languages::existLangDB($url[0])) {
                //Si existe asigna el nuevo y lo quita de URL para utilizar los parametros originales.
                $lang = strtolower($url[0]);
                array_shift($url);
            }


            $url = array_filter($url);

            $this->_controlador = str_replace('-','_', strtolower(array_shift($url)));

            $this->_metodo = str_replace('-','_', strtolower(array_shift($url)));
            $this->_argumentos = $url;


        }
        else{
            if(!isset($this->_default_lang)) $this->_default_lang = "templates";
        }
        $this->_lang = $lang;

        if(!$this->_controlador)$this->_controlador = DEFAULT_CONTROLLER;
        if(!$this->_metodo) $this->_metodo = 'index';





    }


    public function getControlador() {
        return $this->_controlador;
    }

    public function getMetodo() {
        return $this->_metodo;
    }

    public function getArgs() {
        return $this->_argumentos;
    }
    public function getGetVars() {
        return $this->_gets;
    }
    public function getLang() {
        return $this->_lang;
    }
    public function getDataLang($la) {
        $l = new Languages($this);
        return $l->getDataLang($la);
    }
}
