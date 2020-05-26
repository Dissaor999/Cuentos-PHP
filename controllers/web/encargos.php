<?php

class encargos extends Controller {
	public function __construct(){
		parent::__construct();
	}

	public function index(){
        global $me;

            $home_title = 'Encargos';
            $this->_view->titulo = $home_title.TS.APP_NAME;

            $this->_view->renderizar('index','default');
	}
	
}

?>