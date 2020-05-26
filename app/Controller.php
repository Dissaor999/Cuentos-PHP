<?php

abstract class Controller {
    protected $_view;
    
    public function __construct() {

        $this->_view = new View();
    }
    
    abstract public function index();
    
    protected function loadModel($modelo) {
        $modelo = $modelo ;
        $rutaModelo = ROOT . 'models' . DS . $modelo . '.php';

        if(is_readable($rutaModelo)){
            require_once $rutaModelo;
            $modelo = new $modelo;
            return $modelo;
        }
        else {
            throw new Exception('Error de modelo');
        }
    }
    
    protected function getLibrary($libreria) {
        $rutaLibreria = ROOT . 'libs' . DS . $libreria;
        if(is_readable($rutaLibreria)){
            require_once $rutaLibreria;
        }
        else{
            throw new Exception('Error de libreria  '.$rutaLibreria);
        }
    }
    
    protected function getTexto($clave) {
        if(isset($_POST[$clave]) && !empty($_POST[$clave])){
            $_POST[$clave] = htmlspecialchars($_POST[$clave], ENT_QUOTES);
            return $_POST[$clave];
        }
        
        return '';
    }
    
    protected function getInt($clave) {
        if(isset($_POST[$clave]) && !empty($_POST[$clave])){
            $_POST[$clave] = filter_input(INPUT_POST, $clave, FILTER_VALIDATE_INT);
            return $_POST[$clave];
        }
        
        return 0;
    }
    
    protected function redireccionar($ruta = false, $lang='', $admin=false) {
		if(isset($lang) && $lang!='') $lang = $lang.'/';
        if($ruta){
            if($admin)
                header('location:' . ADMIN_URL . $lang. $ruta);
            else
                header('location:' . BASE_URL . $lang. $ruta);


        }
        else{
            if($admin)
                header('location:' . ADMIN_URL . $lang);
            else
                header('location:' . BASE_URL . $lang);

        }
    }
	
	protected function filtrarInt($int){
        $int = (int) $int;
        
        if(is_int($int)){
            return $int;
        }
        else{
            return 0;
        }
    }
    
    protected function getPostParam($clave) {
        if(isset($_POST[$clave])){
            return $_POST[$clave];
        }
    }
    
    protected function getAlphaNum($clave)
    {
        if(isset($_POST[$clave]) && !empty($_POST[$clave])){
            $_POST[$clave] = (string) preg_replace('/[^A-Z0-9_]/i', '', $_POST[$clave]);
            return trim($_POST[$clave]);
        }
        
    }
    
    public function validarEmail($email)
    {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return false;
        }
        
        return true;
    }


    /*VALIDATIONS*/

    public function validateDate($date) {
        if(
            preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $date)
            || preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])T[0-9]{2}:[0-9]{2}$/', $date)
        )
            return true;
        return false;
    }

    public function validateTitle($title) {
        if(preg_match('/^[\w\W]{1,150}$/', $title))
            return true;
        return false;
    }

    public function validateHour($title) {
        if(preg_match('/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $title))
            return true;
        return false;
    }

    public function validateMinutes($title) {
        if(preg_match('/^[0-5][0-9]$/', $title))
            return true;
        return false;
    }

    public function validateHtml($html) {
        if($html!='')
            return true;
        return false;
    }

    public function validateShortDesc($desc) {
        if(preg_match('/^.{8,250}$/', $desc))
            return true;
        return false;
    }

    public function validateBooleanInt($vis) {
        if($vis==0 || $vis==1)
            return true;
        return false;
    }

    public function validateSlug($slug) {
        if(preg_match('/[a-z]{1,50}[a-z0-9\-]*/i', $slug))
            return true;
        return false;
    }

    public function validateSeoDesc($desc) {
        if(preg_match('/^.{8,250}$/', $desc))
            return true;
        return false;
    }

    public function slugify($text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        if (function_exists('iconv'))
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text))
            return 'n-a';

        return $text;
    }

	protected function formatPermiso($clave)
    {
        if(isset($_POST[$clave]) && !empty($_POST[$clave])){
            $_POST[$clave] = (string) preg_replace('/[^A-Z_]/i', '', $_POST[$clave]);
            return trim($_POST[$clave]);
        }
        
    }
}

?>
