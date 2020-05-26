<?php

class user extends Controller {
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){

        if(!Session::is_logged()){
            $this->login();
        }else $this->form();
	}

	public function makeLogout(){
        Session::make_logout();
        $this->redireccionar('');
    }

    public function login(){
        if(CUser::has_access('login')) {
              $this->_view->titulo = 'Iniciar Sesión · '.APP_NAME;
              $this->_view->renderizar('login','default');
        }else $this->_view->renderizarNoPermisos($vista='no-permission', $controlador='error');
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
                        if($users->changePass($pass,$token))  $response = array('success'=>1, 'message'=>'Actualizado correctamente. <a href="/user/login">Iniciar Sesión</a>', 'return'=>'/user/login/');
                        else $response['errors'][]='No se ha podido cambiar la contraseña.';
                    else $response['errors'][]='Las contraseñas no coinciden.';
                }else $response['errors'][]='Token no válido.';

            } else $response['errors'][]='No hay token.';

        } else $response['errors'][]='No tienes permisos para realizar esta accion.';


        echo json_encode($response);
    }

    public function orders(){
        if(CUser::has_access('orders')) {

                $this->orders =  $this->loadModel('orders');


                $id = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;

                if($id!=null){

                    $this->_view->_data['order']=$this->orders->getData(array('id'=>$id));
                    $this->_view->titulo = 'Ver Pedido - '.APP_NAME;
                    $this->_view->renderizar('order_detail','default');

                }else{

                    $this->_view->_data['orders']=$this->orders->getData(array('user_id'=>Session::getId()) ,$this->_view->_lang);
                    $this->_view->titulo = 'Mis Pedidos · '.APP_NAME;
                    $this->_view->renderizar('orders','default');

                }
        }else $this->_view->renderizarNoPermisos($vista='no-permission', $controlador='error');


    }

    public function makeLogin(){
        if(CUser::has_access('register')) {
            $user = $_POST['username'];
            $password = $_POST['password'];
            $user = CUser::make_login($user,$password);
            if($user==null) {
                $this->redireccionar('user/login/?error=1');
            }
            else {
                if($user['active']==0)
                $this->redireccionar('user/login/?error=2');

                if($user['banned']>0)
                $this->redireccionar('user/login/?error=3');
                
                Session::set('auth',1);
                Session::set('login',$user['login']);
                Session::set('user_id',$user['user_id']);
                $this->redireccionar('');
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
                                                    <a href="'.MENU_URL.'user/recover/?token='.$data['recovery-code'].'">Recuperar Contraseña</a>
                                                    <br />
                                                    <br />
                                                    o escribiendo el siguiente enlace en el navegador: '.MENU_URL.'user/recover/?token='.$data['recovery-code'].'
                                                  
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