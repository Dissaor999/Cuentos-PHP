<?php

class admin_dashboard extends Controller{

    public function __construct(){
        parent::__construct();

        if (!CUser::has_access('admin-area')) {
            $this->redireccionar('/');
        }

        $this->model_product =  $this->loadModel('products');
    }

    public function index(){
        $this->_view->titulo = 'Panel de administración - '.APP_NAME;
        $this->_view->renderizar('index', 'admin');
    }
}

?>