<?php

class index extends Controller {
    public function __construct(){
        parent::__construct();

        if (!CUser::has_access('admin-area')) {
            $this->redireccionar('user/login/', $this->_view->_lang, 'admin');
        }

    }

    public function index(){
        $this->_view->titulo = 'Panel de administración - '.APP_NAME;
        $this->_view->renderizar('index', 'admin');
    }

	
}

?>