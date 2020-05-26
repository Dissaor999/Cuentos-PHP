<?php

class admin_product extends Controller{

    public function __construct(){
        parent::__construct();

        if (!CUser::has_access('admin-area')) {
            $this->redireccionar('/');
        }

        $this->model_product =  $this->loadModel('products');
    }



    public function index(){

        $this->redireccionar(get_class().'/lista');
    }

    public function lista(){

        $this->model_product =  $this->loadModel('products');

        $this->_view->_data['products']=$this->model_product->getData();

        $this->_view->titulo =  APP_NAME." - products";
        $this->_view->renderizar('lista', 'admin');
    }

    public function add() {
        $products = $this->loadModel('products');
        $this->_view->_data['langs']= Languages::getLangsDB();

        //Project

        $this->_view->_data['filters']= $products->getFilter('');


        $this->_view->setJs(array('ajax'), 'admin');
        $this->_view->titulo = APP_NAME." - products - Añadir";
        $this->_view->renderizar('add', 'admin');
    }

    public function delete(){
        $products = $this->loadModel('products');
        //Project
        $id = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;
        $params = array('id'=>$id);

        $this->_view->_data['product']= $products->getData($params,true);

        $this->_view->_data['langs']= Languages::getLangsDB();

        $this->_view->setJs(array('ajax'));


        $this->_view->titulo =  'Borrar Producto  - '.APP_NAME;
        $this->_view->renderizar('delete', 'admin');
    }

    //View
    public function edit(){
        $products = $this->loadModel('products');
        $this->_view->_data['langs']= Languages::getLangsDB();

        //Project
        $id = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;
        $params = array('id'=>$id);
        $this->_view->_data['product']= $products->getData($params,true);
        $this->_view->_data['filters']= $products->getFilter('');


        $this->_view->setJs(array('ajax'), 'admin');
        $this->_view->titulo = 'Editar Producto - '.APP_NAME;
        $this->_view->renderizar('edit', 'admin');
    }

    //View
    public function edit_combination(){
        $products = $this->loadModel('products');
        $this->_view->_data['langs']= Languages::getLangsDB();
        //Project
        $id = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;
        $this->_view->_data['combination']= $products->getCombination($id);


        $this->_view->_data['product']= $products->getData( array('id'=>$this->_view->_data['combination'][0]['id_product']),true)['data'][0];

        /*CAMPOS PARA LA COMBINACION*/
        $this->_view->_data['colors']= $products->getColors();
        $this->_view->_data['specials']= $products->getSpecials();


        $this->_view->setJs(array('ajax'), 'admin');
        $this->_view->titulo = 'Editar Comnbinación - '.APP_NAME;
        $this->_view->renderizar('combination/edit', 'admin');
    }


    //View
    public function add_combination(){
        $products = $this->loadModel('products');
        $this->_view->_data['langs']= Languages::getLangsDB();

        //Project
        $id = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;
        $this->_view->_data['product']= $products->getData( array('id'=>$id) ,false)['data'][0];
        // print_r($this->_view->_data['product']);
        /*CAMPOS PARA LA COMBINACION*/
        $this->_view->_data['colors']= $products->getColors();
        $this->_view->_data['specials']= $products->getSpecials();


        $this->_view->setJs(array('ajax'));
        $this->_view->titulo = 'Añadir Comnbinación - '.APP_NAME;
        $this->_view->renderizar('combination/add', 'admin');
    }



    //Ajax Method Update
    public function action_edit() {
        $products = $this->loadModel('products');
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
        $featured = (isset($data['featured']))? 1 : 0;



        $lang_data = array();


        //print_r($langs);

        /***********************
         *	CAMPOS IDIOMAS    **
         ***********************/
        //Errores
        foreach($langs as $k=>$l) {
            $id_lang = strtolower($l['tagname']);
            if(!$this->validateTitle(@$data[$id_lang]['name'])) $response['errors'][]='El <strong>name</strong>  del idioma '.$l['name'].'  no es válido o está vacío. ('.@$data[$id_lang]['name'].')';
            else   $lang_data[$id_lang]['name']=@$data[$id_lang]['name'];

            if(!$this->validateHtml(@$data[$id_lang]['short_desc']))  $response['errors'][]='La <strong>short_desc</strong>  del idioma '.$l['name'].'  no es válida o está vacía. ('.@$data[$id_lang]['short_desc'].')';
            else   $lang_data[$id_lang]['short_desc']=@$data[$id_lang]['short_desc'];

            if(!$this->validateHtml(@$data[$id_lang]['long_desc']))  $response['errors'][]='La <strong>long_desc</strong>  del idioma '.$l['name'].'  no es válida o está vacía. ('.@$data[$id_lang]['long_desc'].')';
            else  $lang_data[$id_lang]['long_desc']=@$data[$id_lang]['long_desc'];

            if(!$this->validateSlug(@$data[$id_lang]['slug']))  $response['errors'][]='La <strong>slug</strong>  del idioma '.$l['name'].'  no es válida o está vacía. ('.@$data[$id_lang]['slug'].')';
            else  $lang_data[$id_lang]['slug']=@$data[$id_lang]['slug'];
        }

        /***********************
         *	CAMPOS COMUNES    **
         ***********************/
       /* if(!$this->validateDate(@$data['fecha']))
            $response['errors'][]='La fecha no es válida o está vacía. ('.@$data['fecha'].')';

        if(!$this->validateHour(@$data['hora']))
            $response['errors'][]='La hora no es válida o está vacía. ('.@$data['hora'].')';

        if(!$this->validateMinutes(@$data['minutos']))
            $response['errors'][]='Los minutos no son válidos o están vacíos. ('.@$data['minutos'].')';
*/
        if(!$this->validateVisible($visible))
            $response['errors'][]='La opción visible no es válida o está vacía. ('.$visible.')';

        if(!$products->existsId(@$data['id'])) $response['errors'][] ='No existe ninguna noticia con ese ID.';



        /* Upload Thumb */
      /*  if(isset($_FILES['imagen']['name'])) {
            $upload = new upload($_FILES['imagen']);
            $upload->allowed = array('image/*');
            $upload->file_new_name_body='th_'.uniqid();
            $upload->image_convert = 'jpg';
            $upload->png_compression = 9;
            $upload->jpeg_quality = 50;
            //$upload->image_max_width = 1920;
            $path = ROOT . '..' . DS . 'public'. DS . 'img' . DS . 'products' . DS ;
            //	echo 'saving on '.$path;
            $upload->process($path);
            if(!$upload->processed) $response =array('error'=>1,'msg'=>'Error uploading thumb');
        }*/

        /* Upload Thumb */
        if(isset($_FILES['file']['name'])) {
            $miniatura = new upload($_FILES['file']);
            $miniatura->allowed = array('image/*');
            $miniatura->file_new_name_body='th_'.uniqid();
            $miniatura->image_convert = 'jpg';
            $miniatura->png_compression = 9;
            $miniatura->jpeg_quality = 80;
            $miniatura->image_max_width = 1920;
            //$miniatura->image_max_width = 612;

            $miniatura->image_resize          = false;
            $miniatura->image_ratio_crop      = false;

            $path = ROOT  . 'public'. DS . 'img' . DS . 'products' . DS.'thumbs'.DS ;
            //	echo 'saving on '.$path;
            $miniatura->process($path);
            if(!$miniatura->processed) $response =array('error'=>1,'msg'=>'Error uploading thumb');
        }

        if(!isset($response['errors'])) {
          //  $fecha = $data['fecha'].' '.$data['hora'].':'.$data['minutos'];
            $vars = array(
                'id'=>$data['id'],
                'langs'=>$lang_data,
             //   'fecha'=>$fecha,
                'visible'=>$visible,
                'featured'=>$featured,
                'filters'=>array(
                    implode(',',$data['familia']),
                    // implode(',',$data['material']),
                    implode(',',$data['tipologia']),
                ),
                'thumb'=>@$miniatura->file_dst_name,
            );


            $response = $products->update($vars);
        }
        echo json_encode($response);
    }


    //Ajax Method Modify
    public function action_add() {
        $products = $this->loadModel('products');
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
        $featured = (isset($data['featured']))? 1 : 0;



        $lang_data = array();


        //print_r($langs);

        /***********************
         *	CAMPOS IDIOMAS    **
         ***********************/
        //Errores
        foreach($langs as $k=>$l) {
            $id_lang = strtolower($l['tagname']);
            if(!$this->validateTitle(@$data[$id_lang]['name'])) $response['errors'][]='El <strong>name</strong>  del idioma '.$l['name'].'  no es válido o está vacío. ('.@$data[$id_lang]['name'].')';
            else   $lang_data[$id_lang]['name']=@$data[$id_lang]['name'];

            if(!$this->validateHtml(@$data[$id_lang]['short_desc']))  $response['errors'][]='La <strong>short_desc</strong>  del idioma '.$l['name'].'  no es válida o está vacía. ('.@$data[$id_lang]['short_desc'].')';
            else   $lang_data[$id_lang]['short_desc']=@$data[$id_lang]['short_desc'];

            if(!$this->validateHtml(@$data[$id_lang]['long_desc']))  $response['errors'][]='La <strong>long_desc</strong>  del idioma '.$l['name'].'  no es válida o está vacía. ('.@$data[$id_lang]['long_desc'].')';
            else  $lang_data[$id_lang]['long_desc']=@$data[$id_lang]['long_desc'];

            if(!$this->validateSlug(@$data[$id_lang]['slug']))  $response['errors'][]='La <strong>slug</strong>  del idioma '.$l['name'].'  no es válida o está vacía. ('.@$data[$id_lang]['slug'].')';
            else  $lang_data[$id_lang]['slug']=@$data[$id_lang]['slug'];
        }

        /***********************
         *	CAMPOS COMUNES    **
         ***********************/
        /* if(!$this->validateDate(@$data['fecha']))
             $response['errors'][]='La fecha no es válida o está vacía. ('.@$data['fecha'].')';

         if(!$this->validateHour(@$data['hora']))
             $response['errors'][]='La hora no es válida o está vacía. ('.@$data['hora'].')';

         if(!$this->validateMinutes(@$data['minutos']))
             $response['errors'][]='Los minutos no son válidos o están vacíos. ('.@$data['minutos'].')';
 */
        if(!$this->validateVisible($visible))
            $response['errors'][]='La opción visible no es válida o está vacía. ('.$visible.')';

       // if(!$products->existsId(@$data['id'])) $response['errors'][] ='No existe ninguna noticia con ese ID.';



        /* Upload Thumb */
        if(isset($_FILES['miniatura']['name'])) {
            $miniatura = new upload($_FILES['miniatura']);
            $miniatura->allowed = array('image/*');
            $miniatura->file_new_name_body='th_'.uniqid();
            $miniatura->image_convert = 'jpg';
            $miniatura->png_compression = 9;
            $miniatura->jpeg_quality = 80;
            //$miniatura->image_max_width = 612;

            $miniatura->image_resize          = true;
            $miniatura->image_ratio_crop      = false;

            $path = ROOT  . 'public'. DS . 'img' . DS . 'products' . DS.'thumbs'.DS ;
            //	echo 'saving on '.$path;
            $miniatura->process($path);
            if(!$miniatura->processed) $response =array('error'=>1,'msg'=>'Error uploading thumb');
        }

        if(!isset($response['errors'])) {
            //  $fecha = $data['fecha'].' '.$data['hora'].':'.$data['minutos'];
            $vars = array(
                //'id'=>$data['id'],
                'langs'=>$lang_data,
                'visible'=>$visible,
                'featured'=>$featured,
                'filters'=>array(
                    implode(',',$data['serie']),
                   // implode(',',$data['material']),
                    implode(',',$data['style']),

                ),
                'thumb'=>@$miniatura->file_dst_name,
            );

            print_R($vars);
            $response = $products->add($vars);
        }
        echo json_encode($response);
    }

    //Ajax Method Modify
    public function action_add_combination() {
        $products = $this->loadModel('products');
        $this->getLibrary('class.upload'.DS.'class.upload.php');
        $response =array('error'=>1,'msg'=>'Unknown Error');

        $this->_view->setJs(array('ajax'));


        /*get vars*/
        if(isset($_POST['d']))
            $data = $_POST['d'];
        else $response['errors'][]='Faltan Datos';


        /***********************
         *	CAMPOS COMUNES    **
         ***********************/

        if(!isset($data['id_product'])) $response['errors'][] = 'No hay id product';
        if(!isset($data['color'])) $response['errors'][] = 'No hay color';
        if(!isset($data['alto'])) $response['errors'][] = 'No hay alto';
        if(!isset($data['ancho'])) $response['errors'][] = 'No hay ancho';
        if(!isset($data['sku'])) $response['errors'][] = 'No hay SKU';
        if(!isset($data['special'])) $response['errors'][] = 'No hay acabado especial';
        if(!isset($data['price'])) $response['errors'][] = 'No hay precio';

        if(!isset($response['errors'])) {
          //  $fecha = $data['fecha'].' '.$data['hora'].':'.$data['minutos'];
            $vars = array(
                'id_product'=>$data['id_product'],
                'price'=>$data['price'],
                'special'=>$data['special'],
                'alto'=>$data['alto'],
                'ancho'=>$data['ancho'],
                'sku'=>$data['sku'],
                'color'=>$data['color'],

            );

            $response = $products->add_combination($vars);
            if($response['error']==0)
                $response['redirect']=$data['redirect'].'/'.$response['id'];
        }
        echo json_encode($response);
    }

    //Ajax Method Edit
    public function action_edit_combination() {
        $products = $this->loadModel('products');
        $this->getLibrary('class.upload'.DS.'class.upload.php');
        $response =array('error'=>1,'msg'=>'Unknown Error');

        $this->_view->setJs(array('ajax'));


        /*get vars*/
        if(isset($_POST['d']))
            $data = $_POST['d'];
        else $response['errors'][]='Faltan Datos';


        /***********************
         *	CAMPOS COMUNES    **
         ***********************/

        if(!isset($data['id'])) $response['errors'][] = 'No hay id';
        if(!isset($data['color'])) $response['errors'][] = 'No hay color';
        if(!isset($data['alto'])) $response['errors'][] = 'No hay alto';
        if(!isset($data['ancho'])) $response['errors'][] = 'No hay ancho';
        if(!isset($data['sku'])) $response['errors'][] = 'No hay sku';
        if(!isset($data['special'])) $response['errors'][] = 'No hay acabado especial';
        if(!isset($data['price'])) $response['errors'][] = 'No hay precio';

        if(!isset($response['errors'])) {
          //  $fecha = $data['fecha'].' '.$data['hora'].':'.$data['minutos'];
            $vars = array(
                'id'=>$data['id'],
                'price'=>@$data['price'],
                'primary_image'=>@$data['primary_image'],
                'special'=>@$data['special'],
                'alto'=>@$data['alto'],
                'ancho'=>@$data['ancho'],
                'sku'=>@$data['sku'],
                'color'=>@$data['color'],
            );

            $response = $products->edit_combination($vars);
            if($response['error']==0)
                $response['redirect']=$data['redirect'];
        }
        echo json_encode($response);
    }

    //Method
    public function action_delete(){
        if(!Session::get('auth'))$this->redireccionar('login');

        $products = $this->loadModel('products');
        $response=array('error'=>1,'msg'=>'no data');
        if($this->getInt('id')) {
            if($products->delete(array('id'=>$this->getInt('id')))){$this->redireccionar(get_class().'/lista');}
        }
        else {$this->_view->_errors[] = 'No hay ID de noticia';  $this->redireccionar('products/lista');}
        $this->redireccionar(get_class().'/lista');
    }









    /*GALLERY COMB*/

//Action
    public function getPhotosCombination(){
        $galeria = $this->loadModel('products');
        if(isset($_POST['id'])){
            $id = $_POST['id'];
            $params = array('id'=>$id);
            $images = $galeria->getImagesCombination($params);

            echo json_encode(array('success'=>1, 'data'=>$images['data']));
        }
        else echo json_encode(array('success'=>0, 'message'=>'error no data'));
    }

    //Action
    public function deletePhotoCombiantion(){
        $galeria = $this->loadModel('products');
        if(isset($_POST['id'])){
            $id = $_POST['id'];
            $params = array('id'=>$id);
            $images = $galeria->deletePhotoCombination($params);

            echo json_encode(array('success'=>1, 'data'=>'Borrada'));
        }
        else echo json_encode(array('success'=>0, 'message'=>'error no data'));
    }

    public function add_images_comb(){
        $galeria = $this->loadModel('products');

        $this->getLibrary('class.upload'.DS.'class.upload.php');

        $response =array('error'=>1,'msg'=>'Unknown Error');

        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            if(isset($_FILES['file']['name'])) {
                $name = 'image_'.time().'_'.uniqid();
                $path = ROOT . DS . 'public'. DS . 'img' . DS . 'combinations';

                $upload = new upload($_FILES['file']);
                $upload->allowed = array('image/*');
                $upload->file_new_name_body=$name;
                $upload->process($path);
                if(!$upload->processed) $response =array('error'=>1,'msg'=>'Error uploading image');
                else {
                    $response =array('error'=>0,'msg'=>'Upload OK');
                }
            }else { $response['errors'][]='La Imagen no es válida o está vacía.'; }
            //Thumbs
            if(isset($_FILES['file']['name'])) {
                $path = ROOT . DS . 'public'. DS . 'img' . DS . 'combinations'.DS.'thumbs';

                $thumb = new upload($_FILES['file']);
                $thumb->allowed = array('image/*');
                $thumb->file_new_name_body=$name.'_th';

                $thumb->image_resize = false;

                $thumb->image_ratio_crop = false;

                $thumb->process($path);
                if(!$thumb->processed) $response =array('error'=>1,'msg'=>'Error uploading thumb');
                else {
                    $response =array('error'=>0,'msg'=>'Upload OK');
                }
            }else { $response['errors'][]='La Imagen no es válida o está vacía.'; }

        }else $response =array('error'=>1,'msg'=>'No ID');

        if(!isset($response['errors'])) {
            $params = array(
                'id'=>$id,
                'src'=>$upload->file_dst_name,
                'src_thumb'=>$thumb->file_dst_name,
            );
            $response = $galeria->addPhoto(
                $params
            );
        }else {
            $response =array('error'=>1,'msg'=>'Hay errores en las imagenes.');
        }
        echo json_encode($response);
    }


}

?>