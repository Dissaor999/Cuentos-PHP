<?php

class User extends Controller {
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
        $this->redireccionar('user/my_profile', $this->_view->_lang, true);
    }


	public function login(){
        if(!Session::is_logged()){
            if(CUser::has_access('login')) {
                $this->_view->isLoginScreen = true;
                $this->_view->loadJs(
                    array(
                        '/assets/snippets/pages/user/login'
                    )
                );

                $this->_view->titulo = 'Iniciar Sesión · '.APP_NAME;
                $this->_view->renderizar('login','admin',false);

            }else $this->_view->renderizarNoPermisos($vista='no-permission', $controlador='error');
        }else $this->redireccionar('/user', $this->_view->_lang, true);
	}

	public function logout(){
        Session::make_logout();
        $this->redireccionar('',$this->_view->_lang,true);
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

    //View
    public function my_profile(){
        $users = $this->loadModel('users');
        //Project
        $id = Session::getId();
        $params = array('id'=>$id);
        $this->_view->_data['users']= $users->getData($params,true);

        $this->_view->setJs(array('ajax'), 'admin');
        $this->_view->titulo = 'Zona de usuario - '.APP_NAME;
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

            $path = ROOT  . 'public'. DS . 'img' . DS . 'users' . DS.'thumbs'.DS ;
            //	echo 'saving on '.$path;
            $miniatura->process($path);
            if(!$miniatura->processed) $response =array('error'=>1,'msg'=>'Error uploading thumb');
        }

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
                'thumb'=>$miniatura->file_dst_name,
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
        if(!Session::is_logged())$this->redireccionar('login');

        $users = $this->loadModel('users');
        if($this->getInt('id')) {
            if($users->delete(array('id'=>$this->getInt('id')))){$this->redireccionar(get_class().'/lista/?ok=1');}
        }
        else { $this->redireccionar('users/lista/?ok=2');}
        $this->redireccionar(get_class().'/lista/?ok=3');
    }

    public function register(){
        if(CUser::has_access('register')) {
            $this->_view->titulo = 'Regístrate · '.APP_NAME;
            $this->_view->renderizar('register','default');
        }else $this->_view->renderizarNoPermisos($vista='no-permission', $controlador='error');

    }

    public function forget(){
        if(CUser::has_access('login')) {
            $this->_view->titulo = '¿Olvidó su contraseña? · '.APP_NAME;
            $this->_view->renderizar('forget','default');
        }else $this->_view->renderizarNoPermisos($vista='no-permission', $controlador='error');

    }

    public function f_forget(){
        if(CUser::has_access('login')) {
            $response['success']=0;
            $response['message']='Ha habido un error.';

            $users = $this->loadModel('users');
            $user = $_POST['username'];

            if($userData = $users->check_username($user)) {
                if($users->setRecoverCode($user))
                    $this->sendMailRecovery($userData);
                $response['success']=1;
                $response['message']='Te hemos enviado un mail con la clave de recuperación.';
            }
            else {    $response['errors'][]='El usuario no existe.';  }
        }else $response['errors'][]='No tienes permisos.';


        echo json_encode($response);
    }

    public function recover(){
        $users = $this->loadModel('users');
        if(CUser::has_access('login')) {
            if(isset($_GET['token'])){
                $token = $_GET['token'];

                if($users->checkToken($token)) {
                    $this->_view->titulo = 'Recuperar Contraseña · ' . APP_NAME;
                    $this->_view->renderizar('recover', 'default');
                }else {
                    $this->redireccionar('/user/forget');
                }
            } else $this->redireccionar('/user/forget');
        }else $this->_view->renderizarNoPermisos($vista='no-permission', $controlador='error');

    }

    public function f_recover(){
     $response['message'][]='Ha habido algún error.';

        if(CUser::has_access('login')) {
            if(isset($_POST['token']) && isset($_POST['password']) && isset($_POST['repassword'])) {
                $token = $_POST['token'];

                $users = $this->loadModel('users');

                if($users->checkToken($token)) {
                    $pass=$_POST['password'];
                    $repass=$_POST['repassword'];
                    if($pass == $repass)
                        if($users->changePass($pass,$token))  $response = array('success'=>1, 'message'=>'Actualizado correctamente. <a href="admin/login">Iniciar Sesión</a>', 'return'=>'/user/login/');
                        else $response['errors'][]='No se ha podido cambiar la contraseña.';
                    else $response['errors'][]='Las contraseñas no coinciden.';
                }else $response['errors'][]='Token no válido.';

            } else $response['errors'][]='No hay token.';

        } else $response['errors'][]='No tienes permisos para realizar esta accion.';


        echo json_encode($response);
    }



    public function makeLogin(){
        if(CUser::has_access('register')) {

            $user = $_POST['username'];
            $password = $_POST['password'];
            $user = CUser::make_login($user,$password);

            if($user==null) {

                $this->redireccionar('/user/login?error=no-exist', $this->_view->_lang, true);
            }else {
                 if($user['active']==0)
                    $this->redireccionar('/user/login?error=not-active', $this->_view->_lang, true);

                if($user['banned']>0)
                    $this->redireccionar('/user/login?error=banned', $this->_view->_lang, true);
                
                Session::set('auth',1);
                Session::set('login',$user['login']);
                Session::set('user_id',$user['user_id']);


                $this->redireccionar('index', $this->_view->_lang, true);
            }


        }else $this->_view->renderizarNoPermisos($vista='no-permission', $controlador='error');
    }

    //Ajax Method Modify
    public function make_register() {
        if(CUser::has_access('register')) {

            $users = $this->loadModel('users');

                /*get vars*/
                if(isset($_POST['d']))
                    $data = $_POST['d'];
                else $response['errors'][]='Faltan Datos';


                if($users->check_username($data['username'])) {
                    $response['errors'][]='El nombre de usuario se encuentra en uso.';
                }else {
                    $active = 0;
                    $banned = 0;
                    $newsletter = (isset($data['newsletter']))? 1 : 0;



                    //AQUI HABRÍA QUE VALIDAR LOS CAMPOS........


                    //VALIDACIÓN QUE LA CONTRASEÑA ES IGUAL EN LOS DOS CASOS.
                   if(($data['password']===$data['repassword']))
                    $password = $data['password'];
                   else $response['errors'][]='Las contraseñas no coinciden.';



                    if(!isset($response['errors'])) {
                       $vars = array(
                            //USER DATA
                            'active'=>$active,
                            'banned'=>$banned,
                            'newsletter'=>$newsletter,
                            'username'=>$data['username'],
                            'password'=>$password,

                            //ENTITY DATA
                            'first_name'=>$data['first_name'],
                            'last_name'=>$data['last_name'],
                            'email'=>$data['email'],
                            'phone'=>$data['phone'],
                            'gender'=>$data['gender'],
                            'user_lang'=>$data['user_lang'],
                            'mobile'=>$data['mobile'],
                            'address'=>$data['address'],
                            'address_bill'=>$data['address_bill'],

                            'thumb'=>'thumb.jpg', //IMAGEN DUMMIE.
                            'roles'=>array(2), //SE LE AÑADE EL ROL DE CLIENTE.
                        );
                        $response = $users->add($vars);

                        if($response && $response['error']==0) {
                            $response['message']='Se ha registrado con éxito. En breve, un administrador se pondrá en contacto con ud. para proceder a la validación de la cuenta. Se le enviará un email cuando el proceso esté completado. ¡Muchas gracias! <br /> <br /> <a href="'.MENU_URL.'">Iniciar sesión</a>';

                            $this->sendMailValidationAdmin($vars);
                            $this->sendMailValidation($data['email'],$vars);

                        }
                    }
                }

            if(isset($response['errors'])) {$response['error']=1;}
            echo json_encode($response);
         }else $this->_view->renderizarNoPermisos($vista='no-permission', $controlador='error');

    }

    public function sendMailValidationAdmin($data) {
        $_mailer = $this->loadModel('mail');

        $message = '<!-- BLOQUE DATOS CLIENTE -->
            <div class="block">
               <!-- fulltext -->
               <table width="100%" bgcolor="#f6f4f5" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="fulltext">
                  <tbody>
                     <tr>
                        <td>
                           <table bgcolor="#ffffff" width="580" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" modulebg="edit">
                              <tbody>
                              
                                <!-- Spacing --><tr><td width="100%" height="20"></td></tr><!-- Spacing -->
                                
                                 <tr>
                                    <td>
                                       <table width="540" align="center" cellpadding="0" cellspacing="0" border="0" class="devicewidthinner">
                                          <tbody>
                                          
                                          
                                          
                                             <!-- content -->
                                             <tr>
                                                <td st-content="rightimage-paragraph">
                                                    <strong>Hola, hay un nuevo registro pendiente de validar.
                                                </td>
                                             </tr>
                                             <!-- end of content -->
                                             
                                             
                                             <!-- Spacing --><tr><td width="100%" height="20"></td></tr><!-- Spacing -->
                                             
                               
                                             <!-- content -->
                                             <tr>
                                                <td style="font-family: Helvetica, arial, sans-serif; font-size: 13px; color: #95a5a6; text-align:left;line-height: 24px;" st-content="rightimage-paragraph">
                                                    <strong>Usuario:</strong><br />'.$data['username'].'
                                                </td>
                                             </tr>
                                             <!-- end of content -->
                                            
                                            
                                             <!-- Spacing --><tr><td width="100%" height="20"></td></tr><!-- Spacing -->
                                             
                                             
                                             <!-- content -->
                                             <tr>
                                                <td style="font-family: Helvetica, arial, sans-serif; font-size: 13px; color: #95a5a6; text-align:left;line-height: 24px;" st-content="rightimage-paragraph">
                                                    <strong>Nombre:</strong><br />'.$data['first_name'].' '.$data['last_name'].'
                                                </td>
                                             </tr>
                                             <!-- end of content -->
                                            
                                            
                                             <!-- Spacing --><tr><td width="100%" height="20"></td></tr><!-- Spacing -->
                                             
                                             
                                             <!-- content -->
                                             <tr>
                                                <td style="font-family: Helvetica, arial, sans-serif; font-size: 13px; color: #95a5a6; text-align:left;line-height: 24px;" st-content="rightimage-paragraph">
                                                    <strong>E-Mail:</strong><br />'.$data['email'].'
                                                </td>
                                             </tr>
                                             <!-- end of content --> 
                                             
                                             
                                             <!-- Spacing --><tr><td width="100%" height="20"></td></tr><!-- Spacing -->
                                             
                                             
                                             <!-- content -->
                                             <tr>
                                                <td style="font-family: Helvetica, arial, sans-serif; font-size: 13px; color: #95a5a6; text-align:left;line-height: 24px;" st-content="rightimage-paragraph">
                                                    <strong>Idioma:</strong><br />'.$data['user_lang'].'
                                                </td>
                                             </tr>
                                             <!-- end of content -->
                                             
                                             
                                             <!-- Spacing --><tr><td width="100%" height="20"></td></tr><!-- Spacing -->
                                             
                                             
                                             <!-- content -->
                                             <tr>
                                                <td style="font-family: Helvetica, arial, sans-serif; font-size: 13px; color: #95a5a6; text-align:left;line-height: 24px;" st-content="rightimage-paragraph">
                                                    <strong>Telefono:</strong><br />'.$data['phone'].'
                                                </td>
                                             </tr>
                                             <!-- end of content -->
                                             
                                             
                                             <!-- Spacing --><tr><td width="100%" height="20"></td></tr><!-- Spacing -->
                                             
                                             
                                             <!-- content -->
                                             <tr>
                                                <td style="font-family: Helvetica, arial, sans-serif; font-size: 13px; color: #95a5a6; text-align:left;line-height: 24px;" st-content="rightimage-paragraph">
                                                    <strong>Email:</strong><br />'.$data['email'].'
                                                </td>
                                             </tr>
                                             <!-- end of content -->
                                            
                                            <!-- Spacing --><tr><td width="100%" height="20"></td></tr><!-- Spacing -->
                                           
                                            
                                            
                                            
                                            <!-- Spacing --><tr><td width="100%" height="20"></td></tr><!-- Spacing -->
                                            
                                            
                                          </tbody>
                                       </table>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                     </tr>
                  </tbody>
               </table>
               <!-- end of fulltext -->
            </div>';

        $_mailer->sendMail(ADMIN_EMAIL,'Nuevo Registro',$message, true);
    }

    public function sendMailValidation($mail,$data) {
        $_mailer = $this->loadModel('mail');

        $message = '<!-- BLOQUE DATOS CLIENTE -->
            <div class="block">
               <!-- fulltext -->
               <table width="100%" bgcolor="#f6f4f5" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="fulltext">
                  <tbody>
                     <tr>
                        <td>
                           <table bgcolor="#ffffff" width="580" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" modulebg="edit">
                              <tbody>
                              
                                <!-- Spacing --><tr><td width="100%" height="20"></td></tr><!-- Spacing -->
                                
                                 <tr>
                                    <td>
                                       <table width="540" align="center" cellpadding="0" cellspacing="0" border="0" class="devicewidthinner">
                                          <tbody>
                                          
                                          
                                          
                                             <!-- content -->
                                             <tr>
                                                <td st-content="rightimage-paragraph">
                                                    <strong>Hola '.$data['first_name'].',</strong> te  has registrado en la tienda de Profesionales de Luesma & Vega.
                                                    <br />
                                                    Nos pondremos en contacto contigo cuando hayamos validado tus datos.
                                                    <br /><br />
                                                    ¡Muchas gracias!
                                                    <br />
                                                    El equipo de Luesma & Vega.
                                                </td>
                                             </tr>
                                             <!-- end of content -->
                                             
                                             
                                             <!-- Spacing --><tr><td width="100%" height="20"></td></tr><!-- Spacing -->
                                          
                                            
                                          </tbody>
                                       </table>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                     </tr>
                  </tbody>
               </table>
               <!-- end of fulltext -->
            </div>';

        $_mailer->sendMail($mail,'Te has registrado en la tienda Profesional de Luesma Vega',$message, true);
    }

    public function sendMailRecovery($data) {
        $_mailer = $this->loadModel('mail');

        $message = '<!-- BLOQUE DATOS CLIENTE -->
            <div class="block">
               <!-- fulltext -->
               <table width="100%" bgcolor="#f6f4f5" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="fulltext">
                  <tbody>
                     <tr>
                        <td>
                           <table bgcolor="#ffffff" width="580" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" modulebg="edit">
                              <tbody>
                              
                                <!-- Spacing --><tr><td width="100%" height="20"></td></tr><!-- Spacing -->
                                
                                 <tr>
                                    <td>
                                       <table width="540" align="center" cellpadding="0" cellspacing="0" border="0" class="devicewidthinner">
                                          <tbody>
                                          
                                          
                                          
                                             <!-- content -->
                                             <tr>
                                                <td st-content="rightimage-paragraph" >
                                                    <strong>Hola '.$data['first_name'].',</strong> aquí tienes el código de recuperación. Recuerda que tienes sólo 1 hora para utilizar el código de recuperación!
                                                    <br />
                                                    <br />
                                                    <a href="'.MENU_URL.'admin/user/login/recover/?token='.$data['recovery-code'].'">Recuperar Contraseña</a>
                                                    <br />
                                                    <br />
                                                    o escribiendo el siguiente enlace en el navegador: '.MENU_URL.'admin/recover/?token='.$data['recovery-code'].'
                                                  
                                                    <br /><br />
                                                    ¡Muchas gracias!
                                                    <br />
                                                    El equipo de Luesma & Vega.
                                                </td>
                                             </tr>
                                             <!-- end of content -->
                                             
                                             
                                             <!-- Spacing --><tr><td width="100%" height="20"></td></tr><!-- Spacing -->
                                          
                                            
                                          </tbody>
                                       </table>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </td>
                     </tr>
                  </tbody>
               </table>
               <!-- end of fulltext -->
            </div>';

        $_mailer->sendMail($data['email']   ,'Recuperación de contraseña en Profesionales Luesma & Vega',$message, true);
    }

}

?>