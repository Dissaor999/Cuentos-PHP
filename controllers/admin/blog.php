<?php

class blog extends Controller {
	public function __construct(){
		parent::__construct();
		if(!Session::is_logged()){
            $this->redireccionar('/login', $this->_view->_lang, true);
        }
        $this->noticias = $this->loadModel('news');
		$this->noticias = new News();
	}
	
	public function index(){
		$this->redireccionar('/blog/lista', $this->_view->_lang, true);
	}
	
	public function lista(){
		$this->_view->_data['posts']  = $this->noticias->adm_getData( $this->_view->_lang);

		$this->_view->titulo =  APP_NAME." - Noticias";
		$this->_view->renderizar('lista', 'admin');
	}
	
	public function add() {

		
		$categorias = $this->loadModel('categorias');
		$this->_view->_data['categorias']  = $categorias->getDataCategorias($this->_view->_lang);
        $this->_view->_data['langs']=Languages::getLangsDB();
		$this->_view->setJs(array('ajax','bootstrap-fileupload.min'), 'admin');

		$this->_view->titulo = APP_NAME." - Noticias - Añadir";
		$this->_view->renderizar('add', 'admin');
	}
	
	public function delete(){
		//Project
		$id = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;
		$params = array('id'=>$id);
		
		$this->_view->_data['post']= $this->noticias->getNew($params);
		
		
		$this->_view->_data['langs']= Languages::getLangsDB();
		
		$this->_view->setJs(array('ajax'));


		$this->_view->titulo =  'Borrar noticia ' .$this->_view->_data['post']['data']['id'].' - '.APP_NAME;
		$this->_view->renderizar('delete', 'admin');
	}
	
	//View
	public function edit(){
		
		$categorias = $this->loadModel('categorias');
		$this->_view->_data['categorias']  = $categorias->getDataCategorias($this->_view->_lang);


		//Project
		$id = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;
		$params = array('id'=>$id);

		$this->_view->_data['post']= $this->noticias->getNew($params);
		
		$this->_view->setJs(array('ajax','bootstrap-fileupload.min'), 'admin');
		$this->_view->titulo = 'Editar noticia - '.APP_NAME;
		$this->_view->renderizar('edit', 'admin');
	}
	
	public function addNoticia(){
	
		$this->getLibrary('class.upload'.DS.'class.upload.php');
		$langs = Languages::getLangsDB();
		
		$response =array('error'=>1,'msg'=>'Unknown Error'); 
		
		$this->_view->_updated = 0;
		$this->_view->_errors = array();
		
		$this->_view->setJs(array('ajax'));
		
	
		
		/*get vars*/
			if(isset($_POST['d']))
				$data = $_POST['d'];
			else $response['errors'][]='Faltan Datos';


			$visible = (isset($data['publicar']))? 1 : 0;
			$destacada = (isset($data['destacada']))? 1 : 0;
			
            $lang_data = array();
				
			//Errores
			foreach($langs as $k=>$l) {
				$id_lang = strtolower($l['tagname']);
	
				if(!$this->validateTitle(@$data[$id_lang]['titulo']))
					$response['errors'][]='El <strong>título</strong>  del idioma '.$l['name'].'  no es válido o está vacío. ('.@$data[$id_lang]['titulo'].')';	
				else 
					$lang_data[$id_lang]['titulo']=@$data[$id_lang]['titulo'];
				
				if(!$this->validateHtml(@$data[$id_lang]['descripcion']))
					$response['errors'][]='La <strong>descripción</strong>  del idioma '.$l['name'].'  no es válida o está vacía. ('.@$data[$id_lang]['descripcion'].')';	
				else 
					$lang_data[$id_lang]['descripcion']=@$data[$id_lang]['descripcion'];
					
				if(!$this->validateHtml(@$data[$id_lang]['descripcion_corta']))
					$response['errors'][]='La <strong>descripción corta</strong>  del idioma '.$l['name'].'  no es válida o está vacía. ('.@$data[$id_lang]['descripcion_corta'].')';	
				else 
					$lang_data[$id_lang]['descripcion_corta']=@$data[$id_lang]['descripcion_corta'];
				
			}
			
	
			if(!$this->validateDate(@$data['fecha']))
				$response['errors'][]='La fecha no es válida o está vacía. ('.@$data['fecha'].')';	
			


			$data['slug']=$this->slugify($data['slug']);
			if(!$this->validateSlug(@$data['slug']))
				$response['errors'][]='El slug no es válido o está vacío. ('.@$data['slug'].')';	


			if(!$this->validateBooleanInt($visible))
				$response['errors'][]='La opción visible no es válida o está vacía. ('.$visible.')';	
			
			if(!$this->validateBooleanInt($destacada))
				$response['errors'][]='La opción destacada no es válida o está vacía. ('.$destacada.')';	
			
			
			
			/* Upload Image */			  
			if(isset($_FILES['imagen']['name'])) {
				$upload = new upload($_FILES['imagen']);
				$upload->allowed = array('image/*');
				$upload->file_new_name_body='full_'.uniqid();
					$upload->png_compression = 9;
					$upload->jpeg_quality = 50;
                $path = ROOT  . DS . 'public'. DS . 'img' . DS . 'blog' . DS;
				//echo 'saving on '.$path;
				$upload->process($path);
				if(!$upload->processed) $response =array('error'=>1,'msg'=>'Error uploading image'); 
			} else $response['errors'][]='No hay imagen.';	
			
			/* Upload Thumb */			  
			if(isset($_FILES['miniatura']['name'])) {
				$miniatura = new upload($_FILES['miniatura']);
				$miniatura->allowed = array('image/*');
				$miniatura->file_new_name_body='th_'.uniqid();
                $miniatura->png_compression = 9;
                $miniatura->jpeg_quality = 50;
                $miniatura->image_x = 612;
                $miniatura->image_y = 700;
                $path = ROOT  . DS . 'public'. DS . 'img' . DS . 'blog' . DS.'thumbs'.DS ;
				//	echo 'saving on '.$path;
				$miniatura->process($path);
				if(!$miniatura->processed) $response =array('error'=>1,'msg'=>'Error uploading thumb'); 
			} else $response['errors'][]='No hay miniatura.';	
		
			if(!isset($response['errors'])) {
				$fecha = $data['fecha'].' '.$data['hora'];
				$vars = array(
					'langs'=>$lang_data,
					'fecha'=>$fecha,
					'embed'=>@$data['embed'],
					'categorias' =>@$data['categorias'],
					'publicar'=>$visible,
					'destacada'=>$destacada,
					'slug'=>$data['slug'],
					'imagen'=>$upload->file_dst_name,
					'thumb'=>$miniatura->file_dst_name,
				);
				//print_r($vars);
				$response = $this->noticias->insertarNoticia($vars);
				$response['redirect']=ADMIN_URL.'blog/';
			}
			echo json_encode($response);
	}

	public function uploader(){
        // A list of permitted file extensions
        $allowed = array('png', 'jpg', 'gif','zip');
        if(isset($_FILES['file']) && $_FILES['file']['error'] == 0){

            $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            if(!in_array(strtolower($extension), $allowed)){
                echo '{"status":"error"}';
                exit;
            }
            if(move_uploaded_file($_FILES['file']['tmp_name'],'images/'.$_FILES['file']['name'])){
                $tmp='images/'.$_FILES['file']['name'];
                $new = '../images/'.$_FILES['file']['name']; //adapt path to your needs;
                if(copy($tmp,$new)){
                    echo 'images/'.$_FILES['file']['name'];
                    //echo '{"status":"success"}';
                }
                exit;
            }
        }
        echo '{"status":"error"}';
        exit;
    }
	
	//Ajax Method Modify
	public function editNoticia() {
		$langs = Languages::getLangsDB();
		
		$this->getLibrary('class.upload'.DS.'class.upload.php');
		
		$response =array('error'=>1,'msg'=>'Unknown Error'); 
		
		$this->_view->_updated = 0;
		$this->_view->_errors = array();

		$this->_view->setJs(array('ajax'));
		
		
		
		/*get vars*/
			if(isset($_POST['d']))
				$data = $_POST['d'];
			else $response['errors'][]='Faltan Datos';	
			
			
			//News
			$visible = (isset($data['visible']))? 1 : 0;
			$destacada = (isset($data['destacada']))? 1 : 0;
		
			
			
			$lang_data = array();
			
			
			/***********************
			*	CAMPOS IDIOMAS    **
			***********************/
			//Errores
			foreach($langs as $k=>$l) {
				$id_lang = strtolower($l['tagname']);
				if(!$this->validateTitle(@$data[$id_lang]['titulo'])) {
					$response['errors'][]='El <strong>título</strong>  del idioma '.$l['name'].'  no es válido o está vacío. ('.@$data[$id_lang]['titulo'].')';	
				}else {
					$lang_data[$id_lang]['titulo']=@$data[$id_lang]['titulo'];
				}
				if(!$this->validateHtml(@$data[$id_lang]['descripcion'])) {
					$response['errors'][]='La <strong>descripción</strong>  del idioma '.$l['name'].'  no es válida o está vacía. ('.@$data[$id_lang]['descripcion'].')';	
				}else {
					$lang_data[$id_lang]['descripcion']=@$data[$id_lang]['descripcion'];
				}
				
				if(!$this->validateHtml(@$data[$id_lang]['descripcion_corta'])) {
					$response['errors'][]='La <strong>descripción corta</strong>  del idioma '.$l['name'].'  no es válida o está vacía. ('.@$data[$id_lang]['descripcion_corta'].')';	
				}else {
					$lang_data[$id_lang]['descripcion_corta']=@$data[$id_lang]['descripcion_corta'];
				}
			}
			
			/***********************
			*	CAMPOS COMUNES    **
			***********************/
			if(!$this->validateDate(@$data['fecha']))
				$response['errors'][]='La fecha no es válida o está vacía. ('.@$data['fecha'].')';	
			
			if(!$this->validateHour(@$data['hora']))
				$response['errors'][]='La hora no es válida o está vacía. ('.@$data['hora'].')';	
			

			$data['slug']=$this->slugify($data['slug']);
			if(!$this->validateSlug(@$data['slug'], $this->_view->_lang))
				$response['errors'][]='El slug no es válido o está vacío. ('.@$data['slug'].')';	

			if(!$this->validateBooleanInt($visible))
				$response['errors'][]='La opción visible no es válida o está vacía. ('.$visible.')';	
			
			if(!$this->validateBooleanInt($visible))
				$response['errors'][]='La opción visible no es válida o está vacía. ('.$visible.')';	
			
			if(!$this->noticias->existsId(@$data['id'])) $response['errors'][] ='No existe ninguna noticia con ese ID.';
			
			
			
			/* Upload Thumb */			  
			if(isset($_FILES['imagen']['name'])) {
				$upload = new upload($_FILES['imagen']);
				$upload->allowed = array('image/*');
				$upload->file_new_name_body='th_'.uniqid();
					$upload->image_convert = 'jpg';
					$upload->png_compression = 9;
					$upload->jpeg_quality = 50;
					//$upload->image_max_width = 1920;
				$path = ROOT . DS . 'public'. DS . 'img' . DS . 'blog' . DS ;
                //chmod($path, 0775);
                //	echo 'saving on '.$path;
				$upload->process($path);
				if(!$upload->processed) $response['errors'][] = 'Error uploading image: '.$upload->error;
			}
			
			
			/* Upload Thumb */			  
			if(isset($_FILES['miniatura']['name'])) {

				$miniatura = new upload($_FILES['miniatura']);
				$miniatura->allowed = array('image/*');
				$miniatura->file_new_name_body='th_'.uniqid();
					//$miniatura->image_convert = 'jpg';
					$miniatura->png_compression = 9;
					$miniatura->jpeg_quality = 50;

					//$miniatura->image_x = 612;
					$miniatura->image_y = 700;
					//$miniatura->image_resize          = true;
					$miniatura->image_ratio_crop      = true;
					
				$path = ROOT  . DS . 'public'. DS . 'img' . DS . 'blog' . DS.'thumbs'.DS ;
				//chmod($path, 0775);
				//	echo 'saving on '.$path;
				$miniatura->process($path);
				if(!$miniatura->processed){
					//print_r($miniatura);

                    $response['errors'][]= 'Error uploading thumb: '.$miniatura->error;

                }
			}
		
			if(!isset($response['errors'])) {
				$fecha = $data['fecha'].' '.$data['hora'];
				$vars = array(
					'id'=>$data['id'],
					'langs'=>$lang_data,
					'fecha'=>$fecha,
					'embed'=>@$data['embed'],
					'categorias' =>@$data['categorias'],
					'publicar'=>$visible,
					'destacada'=>$destacada,
					'slug'=>$data['slug'],
					'imagen'=>@$upload->file_dst_name,
					'thumb'=>@$miniatura->file_dst_name,
				);

					$response = $this->noticias->updateNoticia($vars);
			}
			echo json_encode($response);
	}

	//Method
	public function deleteNoticia(){

		$response=array('error'=>1,'msg'=>'no data');
		if($this->getInt('id')) {
			if($this->noticias->deleteNoticia(array('id'=>$this->getInt('id')))){$this->redireccionar(get_class().'/lista', $this->_view->_lang, true);}
		}
		else {$this->_view->_errors[] = 'No hay ID de noticia';  $this->redireccionar(get_class().'/lista', $this->_view->_lang, true);}
		$this->redireccionar(get_class().'lista', $this->_view->_lang, true);
	}
	
	

	
}

?>