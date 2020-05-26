<?php

class View {
    private $_controlador;
    private $_js;
    public $_lang;
    public $_layout;


    public function __construct() {
        $peticion = new Request();
		// Carga de datos
        $this->_controlador = $peticion->getControlador();
        $this->_metodo = $peticion->getMetodo();
        $this->_args = $peticion->getArgs();
        $this->_js = array();
		$this->_lang =$peticion->getLang();
		//$this->_langVars =$peticion->getDataLang($this->_lang);

        define('MENU_URL', BASE_URL.$this->_lang.'/');
        define('ADMIN_LAYOUT_PATH', BASE_URL . 'views/layout/admin/');
    }
    
    public function renderizar($vista, $layout='default', $vistaOnly = false) {
	    $this->_layout=$layout;
		// Menu global para la web
		$menu = array();

		// Añadir los js de la vista customs
        $js = array();
        if(count($this->_js)){ $js = $this->_js;}

		// Variables de lenguajes
        //$_dataLangs = $this->_langVars;

		// Variables usables
        $_layoutParams = array(
            'template_css' => BASE_URL . 'views/layout/' . $layout . '/css/',
            'template_img' => BASE_URL . 'views/layout/' . $layout . '/img/',
            'template_plugins' => BASE_URL . 'views/layout/' . $layout . '/plugins/',
            'template_js' => BASE_URL . 'views/layout/' . $layout . '/js/',
            'public_js' => BASE_URL . 'public/js/',
            'public_img' => BASE_URL . 'public/img/',
            'public_css' => BASE_URL . 'public/css/',
			'menu_url'=>($this->_lang!='templates')? $this->_lang.'/' : '',
            'menu' => $menu,
            'js' => $js,
			'lang'=>$this->_lang,
			'controlador' => $this->_controlador
        );

        // Generamos la ruta de la vista
        $rutaView 	= ROOT . 'views' .DS.$layout. DS . $this->_controlador .DS.'templates'.DS.$vista . '.phtml';

        // Generamos la ruta de la vista de error
		$rutaError 	= ROOT . 'views'.DS.$layout. DS . "error" . DS .'templates'.DS . 'index.phtml';
        
		// Si no esta definido que cargue solo la vista carga el header
		if(!$vistaOnly)
            require_once ROOT . 'views'. DS . 'layout' . DS . $layout . DS . 'header.php';




		// Cargar la vista
        if(is_readable($rutaView)) require_once $rutaView;
        else  require_once $rutaError;
		
		// Si no esta definido que cargue solo la vista carga el footer
		if(!$vistaOnly)
            require_once ROOT . 'views'. DS . 'layout' . DS . $layout . DS . 'footer.php';
    }

    public function renderizarNoPermisos($vista='no-permission', $controlador='error', $layout='default') {
        $this->_layout=$layout;
        // Menu global para la web
        $menu = array();

        // Añadir los js de la vista customs
        $js = array();
        if(count($this->_js)){ $js = $this->_js;}

        // Variables de lenguajes
        //$_dataLangs = $this->_langVars;

        // Variables usables
        $_layoutParams = array(
            'template_css' => BASE_URL . 'views/layout/' . $layout . '/css/',
            'template_img' => BASE_URL . 'views/layout/' . $layout . '/img/',
            'template_plugins' => BASE_URL . 'views/layout/' . $layout . '/plugins/',
            'template_js' => BASE_URL . 'views/layout/' . $layout . '/js/',
            'public_js' => BASE_URL . 'public/js/',
            'public_img' => BASE_URL . 'public/img/',
            'public_css' => BASE_URL . 'public/css/',
            'menu_url'=>($this->_lang!='templates')? $this->_lang.'/' : '',
            'menu' => $menu,
            'js' => $js,
            'lang'=>$this->_lang,
            'controlador' => $this->_controlador
        );


        $rutaView 	= ROOT . 'views' .DS.$layout. DS . $controlador .DS.'templates'.DS.$vista . '.phtml';

        require_once ROOT . 'views'. DS . 'layout' . DS . $layout . DS . 'header.php';
        if(is_readable($rutaView)) require_once $rutaView;
        require_once ROOT . 'views'. DS . 'layout' . DS . $layout . DS . 'footer.php';
    }
    
    public function setJs(array $js, $layout='default') {
        if(is_array($js) && count($js)){
            for($i=0; $i < count($js); $i++)
                $this->_js[] = BASE_URL . 'views/' . $layout . DS. $this->_controlador.'/js/' . $js[$i] . '.js';
        } else {throw new Exception('Error de js');}
    }

    public function setCss(array $css, $layout='default') {
        if(is_array($css) && count($css)){
            for($i=0; $i < count($css); $i++)
                $this->_css[] = BASE_URL . 'views/' . $layout . DS. $this->_controlador.'/css/' . $css[$i] . '.css';
        } else {throw new Exception('Error de CSS');}
    }

    public function loadJs(array $js, $layout='default') {
        if(is_array($js) && count($js)){
            for($i=0; $i < count($js); $i++)
                $this->_js[] = ADMIN_LAYOUT_PATH . DS . $js[$i] . '.js';
        } else {throw new Exception('Error de js');}
    }

	
	/**
	 * Modifies a string to remove all non ASCII characters and spaces.
	*/
	static public function slugify($text) {
		// replace non letter or digits by - & trim
		$text =  trim(preg_replace('~[^\\pL\d]+~u', '-', $text),'-');
		// transliterate
		if (function_exists('iconv')) $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
		// remove unwanted characters && lowercase
		$text = preg_replace('~[^-\w]+~', '', strtolower($text));
		if (empty($text)){return 'n-a';}
		return $text;
	}
}