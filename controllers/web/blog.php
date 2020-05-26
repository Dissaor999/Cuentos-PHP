<?php

class blog extends Controller{
	public function __construct(){
		parent::__construct();
			$this->newsModel = $this->loadModel('news');
			$this->categoriasModel = $this->loadModel('categorias');
			
			$this->_limitPosts=4;
			$this->_actualPage=1;
	}



	public function index(){
		//GET PARAMS


		$this->_actualPage = (!empty($_GET['p'])) ? $_GET['p'] : 1;
		$this->_search = (!empty($_GET['s'])) ? $_GET['s'] : '';
		$this->_category = (!empty($_GET['c'])) ? $_GET['c'] : '';

		//REQUEST MODEL
		$this->_view->_data['lastNew']=$this->newsModel->getDataLimitPage($this->_limitPosts, $this->_view->_lang, $this->_actualPage,$this->_search,$this->_category);
		$this->_view->_data['resultados']=$this->newsModel->getResults($this->_view->_lang,$this->_search,$this->_category);
		$this->_view->_data['categoriasBlog']=$this->categoriasModel->getDataCategorias($this->_view->_lang);




		//VARS VIEW
		$this->_view->_data['_search'] = $this->_search;
		$this->_view->_data['_category'] = $this->_category;
		$this->_view->_data['_limitPosts'] = $this->_limitPosts;
		$this->_view->_data['_actualPage'] = $this->_actualPage;
		$this->_view->_data['_results'] = $this->_view->_data['resultados']['data']['count'];

		//RENDER VIEW
		$this->_view->titulo = APP_NAME.' - Blog';
        $this->_view->renderizar('index','default');
	}

	
	
	public function ver(){
		$this->_view->bodyClass = array('dark');
		$this->_view->_data['lastNew']=$this->newsModel->getDataLimit(5, $this->_view->_lang);
			$this->_view->_data['categoriasBlog']=$this->categoriasModel->getDataCategorias($this->_view->_lang);

		//Get Slug
		$slug = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;
		
		if($slug!=null){
			$this->_view->_data['news']=$this->newsModel->getNewBySlug($slug, $this->_view->_lang);
			if($this->_view->_data['news']['error']>0) $this->index();
			else {
				$this->_view->titulo = @$this->_view->_data['news']['data']['titulo'].' - News - '.APP_NAME;
				$this->_view->renderizar('ver');
			}
		}else $this->index();
	}
	
	
}

?>