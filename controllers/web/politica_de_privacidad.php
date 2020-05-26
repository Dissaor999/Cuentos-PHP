<?php

class politica_de_privacidad extends Controller {
	public function __construct(){
		parent::__construct();
	}

	public function index(){
        global $me;
        $home_title = 'Política de privacidad';
        $this->_view->titulo = $home_title.TS.APP_NAME;

        $this->_view->renderizar('index','default');
	}
	
}

?>