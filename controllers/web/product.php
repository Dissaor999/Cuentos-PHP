<?php

class product extends Controller{


    public function __construct(){
        parent::__construct();
        global $me;

        if (!$me->has_access('product')) {
            $this->redireccionar('user/login');
        }

        $this->model_product =  $this->loadModel('products');
    }
	
	public function index(){
		$this->_view->titulo = APP_NAME.' - Productos';
		$this->_view->renderizar('index', 'default');
	}

    public function getQuickCart() {
	    if(isset($_POST['idcombination'])){
	        echo json_encode($this->model_product->getCombinationsByColorCombination($_POST['idcombination']));
        }else echo json_encode(array('error'=>'No hay datos'));
    }

    public function getJSONProducts(){

	    /*GET VARS*/
        $special  = @$_POST['special'];
        $tipologia  = @$_POST['tipologia'];
	    $colors  = @$_POST['color'];
        $prices  = @$_POST['price'];

	    $alto   = @$_POST['alto'];
	    $ancho   = @$_POST['ancho'];


        if(isset($prices) && $prices!='') {
          $price  = explode(';',$prices);
            $priceS = $price[0].' AND '.$price[1];
        }
        //echo $priceS;
	    $filters=array(
	        'color'=>$colors,
            'alto'=>$alto,
            'ancho'=>$ancho,
            'price'=>@$priceS,
            'special'=>$special,
            'tipologia'=>$tipologia,
        );

        echo json_encode($this->model_product->getDataFiltered($filters));
    }
	
	public function details(){
        $this->_view->setJs(array('scripts'), 'default');
        //Get Slug
		$slug = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;
		if($slug!=null){
            //echo $slug;
            $this->_view->_data['product']=$this->model_product->getData(array('slug'=>$slug));


            $this->_view->titulo = 'Ver Producto - '.APP_NAME;



           $this->_view->renderizar('detail','default');

		}

	}
	
	
}

?>