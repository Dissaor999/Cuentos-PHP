<?php

class cupones extends Controller {
    public function __construct(){
        parent::__construct();
        if(!Session::is_logged()){
            $this->redireccionar('login', $this->_view->_lang, true);
        }
    }

    public function index(){
        $this->redireccionar(get_class().'/lista', $this->_view->_lang, true);
    }

    public function lista(){
        $cupones = $this->loadModel('orders');
        $this->_view->_data['cupones']  = $cupones->getVouchers();

        $this->_view->titulo =  APP_NAME." - Cupones Descuento";
        $this->_view->renderizar('lista', 'admin');
    }

    public function add() {

        $this->_view->setJs(array('ajax'));
        $this->_view->titulo = APP_NAME." - Cupones - Añadir";
        $this->_view->renderizar('add', 'admin');
    }

    public function delete(){
        $cupones = $this->loadModel('orders');
        //Project
        $id = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;
        $params = array('id'=>$id);
        $this->_view->_data['cupones']  = $cupones->getVouchers($params);


        $this->_view->setJs(array('ajax'));


        $this->_view->titulo =  'Borrar Cupón - '.APP_NAME;
        $this->_view->renderizar('delete', 'admin');
    }

    //View
    public function edit(){

        $cupones = $this->loadModel('orders');

        //Project
        $id = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;
        $params = array('id'=>$id);
        $this->_view->_data['cupones']  = $cupones->getVouchers($params);

        $this->_view->setJs(array('ajax'));
        $this->_view->titulo =' Editar cupón - '.APP_NAME;
        $this->_view->renderizar('edit', 'admin');
    }

    public function action_add(){


        /*get vars*/
        if(isset($_POST['d']))
            $data = $_POST['d'];
        else $response['errors'][]='Faltan Datos';




        if(!isset($response['errors'])) {
            $orders = $this->loadModel('orders');

            $vars = array(

                    'name'=>$data['name'],
                    'code'=>$data['code'],
                    'quantity'=>$data['quantity'],
                    'date_init'=>$data['date_init'],
                    'date_finish'=>$data['date_finish'],

            );
            $response = $orders->addVoucher($vars);
        }
        echo json_encode($response);
    }


    //Ajax Method Modify
    public function action_edit() {
        $orders = $this->loadModel('orders');


        $response =array('error'=>1,'msg'=>'Unknown Error');

        /*get vars*/
        if(isset($_POST['d']))
            $data = $_POST['d'];
        else $response['errors'][]='Faltan Datos';




        if(!isset($response['errors'])) {
            $vars = array(
                'id'=>$data['id'],
                'name'=>$data['name'],
                'code'=>$data['code'],
                'quantity'=>$data['quantity'],
                'date_init'=>$data['date_init'],
                'date_finish'=>$data['date_finish'],
            );
            $response = $orders->editVoucher($vars);
        }
        echo json_encode($response);
    }

    //Method
    public function action_delete(){
        if(!Session::get('autenticado'))$this->redireccionar('login');

        $categorias = $this->loadModel('orders');
        $response=array('error'=>1,'msg'=>'no data');
        if($this->getInt('id')) {
            if($categorias->deleteVoucher(array('id'=>$this->getInt('id')))){$this->redireccionar('cupones/lista',$this->_lang,true );}
        }
        else {$this->_view->_errors[] = 'No hay ID de categoria';  $this->redireccionar('cupones/lista',$this->_lang,true);}
        $this->redireccionar('cupones/lista',$this->_lang,true);
    }




}

?>