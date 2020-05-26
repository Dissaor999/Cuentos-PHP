<?php

class nosotros extends Controller {
	public function __construct(){
		parent::__construct();

	}



	public function index(){
        global $me;


            $home_title = 'Nosotros';
            $this->_view->titulo = $home_title.TS.APP_NAME;



            $this->_view->renderizar('index','default');

	}

	public function equipo(){
        global $me;


            $home_title = 'Equipo';
            $this->_view->titulo = $home_title.TS.APP_NAME;



            $this->_view->renderizar('equipo','default');

	}
	
}

?>