<?php

class cusers extends Controller{
    private $_name = ' - Usuarios';


    public function __construct(){
        parent::__construct();

        if (!CUser::has_access('admin-area')) {
            $this->redireccionar('/', $this->_view->_lang, true);
        }

        $this->model_product =  $this->loadModel('products');
    }



    public function index(){
        $this->redireccionar(get_class().'/lista', $this->_view->_lang, true);
    }

    public function lista(){

        $this->model_users =  $this->loadModel('users');


        $filter = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;

        if(isset($filter) && $filter!=null)
            switch($filter){
                case 'pendientes': $this->_view->_data['users']=$this->model_users->getData(array('sql'=>' AND active=0'));
                    break;
                case 'baneados': $this->_view->_data['users']=$this->model_users->getData(array('sql'=>' AND banned>0'));
                    break;
                default: $this->_view->_data['users']=$this->model_users->getData();
            }

        else
            $this->_view->_data['users']=$this->model_users->getData();
        $this->_view->titulo =  APP_NAME.$this->_name;
        $this->_view->renderizar('lista', 'admin');
    }

    public function add() {
        $users = $this->loadModel('users');
        $this->_view->_data['langs']= Languages::getLangsDB();

        //Project

        $this->_view->setJs(array('ajax'), 'admin');
        $this->_view->titulo = APP_NAME.$this->_name." - Añadir";
        $this->_view->renderizar('add', 'admin');
    }

    public function delete(){
        $users = $this->loadModel('users');
        //Project
        $id = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;
        $params = array('id'=>$id);

        $this->_view->_data['product']= $users->getData($params,true);

        $this->_view->_data['langs']= Languages::getLangsDB();

        $this->_view->setJs(array('ajax'));


        $this->_view->titulo =  'Borrar Usuario  - '.APP_NAME;
        $this->_view->renderizar('delete', 'admin');
    }

    //View
    public function edit(){
        $users = $this->loadModel('users');

        //Project
        $id = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;
        $params = array('id'=>$id);
        $this->_view->_data['users']= $users->getData($params,true);



        $this->_view->setJs(array('ajax'), 'admin');
        $this->_view->titulo = 'Editar Usuario - '.APP_NAME;
        $this->_view->renderizar('edit', 'admin');
    }





    //Ajax Method Update
    public function action_edit() {
        $users = $this->loadModel('users');
         $this->getLibrary('class.upload'.DS.'class.upload.php');
        $response =array('error'=>1,'msg'=>'Unknown Error');
        $this->_view->setJs(array('ajax'));


        /*get vars*/
        if(isset($_POST['d']))
            $data = $_POST['d'];
        else $response['errors'][]='Faltan Datos';


        $active = (isset($data['active']))? 1 : 0;
        $banned = (isset($data['banned']))? 1 : 0;
        $newsletter = (isset($data['newsletter']))? 1 : 0;


        if(!$users->existsId(@$data['id'])) $response['errors'][] ='No existe ninguna noticia con ese ID.';


        $password = ($data['password']===$data['repassword']) ? $data['password'] : null;

        /* Upload Thumb */
        if(isset($_FILES['miniatura']['name'])) {
            $miniatura = new upload($_FILES['miniatura']);
            $miniatura->allowed = array('image/*');
            $miniatura->file_new_name_body='th_'.uniqid();
            $miniatura->image_convert = 'jpg';
            $miniatura->png_compression = 9;
            $miniatura->jpeg_quality = 80;
            //$miniatura->image_max_width = 612;

            $miniatura->image_y = 600;
            $miniatura->image_x = 900;
            $miniatura->image_resize          = true;
            $miniatura->image_ratio_crop      = false;

            $path = ROOT  .DS. 'public'. DS . 'img' . DS . 'users' . DS.'thumbs'.DS ;
            //	echo 'saving on '.$path;
            $miniatura->process($path);
            if(!$miniatura->processed) $response =$response['errors'][]='Error uploading thumb. '.$miniatura->error;
        }

        if(!isset($response['errors'])) {
          //  $fecha = $data['fecha'].' '.$data['hora'].':'.$data['minutos'];
            $vars = array(
                'id'=>$data['id'],
                'active'=>$active,
                'banned'=>$banned,
                'newsletter'=>$newsletter,
                'username'=>$data['username'],
                'password'=>$password,
                'first_name'=>$data['first_name'],
                'last_name'=>$data['last_name'],
                'email'=>$data['email'],
                'phone'=>$data['phone'],
                'gender'=>$data['gender'],
                'user_lang'=>$data['user_lang'],
                'mobile'=>$data['mobile'],
                'address'=>$data['address'],
                'address_bill'=>$data['address_bill'],
                'thumb'=>@$miniatura->file_dst_name,
            );

            //print_R($vars);
            $response = $users->update($vars);
            //$response['path']=$path;
        }
        echo json_encode($response);
    }


    //Ajax Method Modify
    public function action_add() {
        $users = $this->loadModel('users');
        $this->getLibrary('class.upload'.DS.'class.upload.php');
        $response =array('error'=>1,'msg'=>'Unknown Error');
        $this->_view->setJs(array('ajax'));

        /*get vars*/
        if(isset($_POST['d']))
            $data = $_POST['d'];
        else $response['errors'][]='Faltan Datos';


        $active = (isset($data['active']))? 1 : 0;
        $banned = (isset($data['banned']))? 1 : 0;
        $newsletter = (isset($data['newsletter']))? 1 : 0;



        $password = ($data['password']===$data['repassword']) ? $data['password'] : null;




        if(!isset($response['errors'])) {
            //  $fecha = $data['fecha'].' '.$data['hora'].':'.$data['minutos'];
            $vars = array(
                'active'=>$active,
                'banned'=>$banned,
                'newsletter'=>$newsletter,
                'username'=>$data['username'],
                'password'=>$password,
                'first_name'=>$data['first_name'],
                'last_name'=>$data['last_name'],
                'email'=>$data['email'],
                'phone'=>$data['phone'],
                'gender'=>$data['gender'],
                'user_lang'=>$data['user_lang'],
                'mobile'=>$data['mobile'],
                'address'=>$data['address'],
                'address_bill'=>$data['address_bill'],
                'thumb'=>'',
            );
            $response = $users->add($vars);
        }
        echo json_encode($response);
    }

    //Ajax Method Modify
    public function action_add_combination() {
        $users = $this->loadModel('users');
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
        if(!isset($data['size'])) $response['errors'][] = 'No hay talla';
        if(!isset($data['special'])) $response['errors'][] = 'No hay acabado especial';
        if(!isset($data['price'])) $response['errors'][] = 'No hay precio';

        if(!isset($response['errors'])) {
          //  $fecha = $data['fecha'].' '.$data['hora'].':'.$data['minutos'];
            $vars = array(
                'id_product'=>$data['id_product'],
                'price'=>$data['price'],
                'price'=>$data['price'],
                'special'=>$data['special'],
                'size'=>$data['size'],
                'color'=>$data['color'],

            );

            $response = $users->add_combination($vars);
            if($response['error']==0)
                $response['redirect']=$data['redirect'].'/'.$response['id'];
        }
        echo json_encode($response);
    }

    //Ajax Method Edit
    public function action_edit_combination() {
        $users = $this->loadModel('users');
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
        if(!isset($data['size'])) $response['errors'][] = 'No hay talla';
        if(!isset($data['special'])) $response['errors'][] = 'No hay acabado especial';
        if(!isset($data['price'])) $response['errors'][] = 'No hay precio';

        if(!isset($response['errors'])) {
          //  $fecha = $data['fecha'].' '.$data['hora'].':'.$data['minutos'];
            $vars = array(
                'id'=>$data['id'],
                'price'=>@$data['price'],
                'primary_image'=>@$data['primary_image'],
                'special'=>@$data['special'],
                'size'=>@$data['size'],
                'color'=>@$data['color'],
            );

            $response = $users->edit_combination($vars);
            if($response['error']==0)
                $response['redirect']=$data['redirect'];
        }
        echo json_encode($response);
    }

    //Method
    public function action_delete(){
        if(!Session::is_logged())$this->redireccionar('login', $this->_view->_lang, true);

        $users = $this->loadModel('users');
        if($this->getInt('id')) {
            if($users->delete(array('id'=>$this->getInt('id')))){$this->redireccionar(get_class().'/lista/?ok=1');}
        }
        else { $this->redireccionar('users/lista/?ok=2', $this->_view->_lang, true);}
        $this->redireccionar(get_class().'/lista/?ok=3', $this->_view->_lang, true);
    }









    /*GALLERY COMB*/

//Action
    public function getPhotosCombination(){
        $galeria = $this->loadModel('users');
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
        $galeria = $this->loadModel('users');
        if(isset($_POST['id'])){
            $id = $_POST['id'];
            $params = array('id'=>$id);
            $images = $galeria->deletePhotoCombination($params);

            echo json_encode(array('success'=>1, 'data'=>'Borrada'));
        }
        else echo json_encode(array('success'=>0, 'message'=>'error no data'));
    }

    public function add_images_comb(){
        $galeria = $this->loadModel('users');

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

                $thumb->image_resize = true;
                $thumb->image_x = 350;
                $thumb->image_y = 280;
                $thumb->image_ratio_crop = true;

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