<?php

class categorias_blog extends Controller {
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
		$categorias = $this->loadModel('categorias');
		$this->_view->_data['categorias']  = $categorias->getDataCategorias($this->_view->_lang);
		
		$this->_view->titulo =  APP_NAME." - categorias";
		$this->_view->renderizar('lista', 'admin');
	}
	
	public function add() {
		$categorias = $this->loadModel('categorias');
		
		

		$this->_view->setJs(array('ajax'));
		$this->_view->titulo = APP_NAME." - categorias - Añadir";
		$this->_view->renderizar('add', 'admin');
	}
	
	public function delete(){
		$categorias = $this->loadModel('categorias');
		//Project
		$id = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;
		$params = array('id'=>$id);
		
		$this->_view->_data['categoria']= $categorias->getData($params);
		$this->_view->_data['langs']= Languages::getLangsDB();
		
		$this->_view->setJs(array('ajax'));
		
		
		$this->_view->titulo =  'Borrar categoria - '.APP_NAME;
		$this->_view->renderizar('delete', 'admin');
	}
	
	//View
	public function edit(){
		$categorias = $this->loadModel('categorias');
		
		
		$this->_view->_data['langs']= Languages::getLangsDB();
		//Project
		$id = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;
		$params = array('id'=>$id);
		$this->_view->_data['categoria']= $categorias->getData($params);
		
		$this->_view->setJs(array('ajax'));
		$this->_view->titulo = 'Editar categoria - '.APP_NAME;
		$this->_view->renderizar('edit', 'admin');
	}
	
	public function action_add(){
	
		$categorias = $this->loadModel('categorias');
		$langs = Languages::getLangsDB();

		$response =array('error'=>1,'msg'=>'Unknown Error'); 
		
		$this->_view->_updated = 0;
		$this->_view->_errors = array();
		
		$this->_view->setJs(array('ajax'));
		
		//categoriasBlog

		/*get vars*/
			if(isset($_POST['d']))
				$data = $_POST['d'];
			else $response['errors'][]='Faltan Datos';	
			
				$lang_data = array();
				
			//Errores
			foreach($langs as $k=>$l) {
				$id_lang = strtolower($l['tagname']);
	
				if(!$this->validateTitle(@$data[$id_lang]['nombre']))
					$response['errors'][]='El <strong>título</strong>  del idioma '.$l['name'].'  no es válido o está vacío. ('.@$data[$id_lang]['nombre'].')';	
				else  $lang_data[$id_lang]['nombre']=@$data[$id_lang]['nombre'];
				
			}
			
	
			$data['slug']=$this->slugify($data['slug']);
			if(!$this->validateSlug(@$data['slug']))
				$response['errors'][]='El slug no es válido o está vacío. ('.@$data['slug'].')';	


		
			if(!isset($response['errors'])) {
				$vars = array(
					'langs'=>$lang_data,
					'slug'=>$data['slug'],
				);
				$response = $categorias->insert_category($vars);
			}
			echo json_encode($response);
	}
	
	
	//Ajax Method Modify
	public function action_edit() {
		$categorias = $this->loadModel('categorias');
		$langs = Languages::getLangsDB();

		
		$response =array('error'=>1,'msg'=>'Unknown Error'); 
		
		$this->_view->_updated = 0;
		$this->_view->_errors = array();

		$this->_view->setJs(array('ajax'));
		

		/*get vars*/
			if(isset($_POST['d']))
				$data = $_POST['d'];
			else $response['errors'][]='Faltan Datos';	
			
			$lang_data = array();
			
			
			/***********************
			*	CAMPOS IDIOMAS    **
			***********************/
			//Errores
			foreach($langs as $k=>$l) {
				$id_lang = strtolower($l['tagname']);
				$lang_data[$id_lang]['nombre']=@$data[$id_lang]['nombre'];
			}
			

			/***********************
			*	CAMPOS COMUNES    **
			***********************/
			$data['slug']=$this->slugify($data['slug']);
			if(!$this->validateSlug(@$data['slug']))
				$response['errors'][]='El slug no es válido o está vacío. ('.@$data['slug'].')';	

			if(!isset($response['errors'])) {
				$vars = array(
					'id'=>$data['id'],
					'langs'=>$lang_data,
					'slug'=>$data['slug'],
				);
				$response = $categorias->update_category($vars);
			}
			echo json_encode($response);
	}

	//Method
	public function delete_categoria(){

		$categorias = $this->loadModel('categorias');
		$response=array('error'=>1,'msg'=>'no data');
		if($this->getInt('id')) {
			if($categorias->delete_category(array('id'=>$this->getInt('id')))){$this->redireccionar(get_class().'/lista', $this->_view->_lang, true);}
		}
		else {$this->_view->_errors[] = 'No hay ID de categoria'; $this->redireccionar(get_class().'/lista', $this->_view->_lang, true);}
        $this->redireccionar(get_class().'/lista', $this->_view->_lang, true);
	}
	
	

	
}

?>