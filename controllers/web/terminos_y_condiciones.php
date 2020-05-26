<?php

class terminos_y_condiciones extends Controller {
	public function __construct(){
		parent::__construct();
	}

	public function index(){
        global $me;

            $home_title = 'Términos y Condiciones';
            $this->_view->titulo = $home_title.TS.APP_NAME;

            $this->_view->renderizar('index','default');
	}
	
}

?>