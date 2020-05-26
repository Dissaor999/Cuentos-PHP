<?php

class contacto extends Controller {
	public function __construct(){
		parent::__construct();

	}

	protected $URL_BOOK='http://cuentos.dev/public/books/';


	public function index(){
        global $me;

           $this->_view->setJs(array('scripts'));
           $home_title = 'Contacto';
            $this->_view->titulo = $home_title.TS.APP_NAME;
            $this->_view->renderizar('index','default');

	}

	public function submitForm(){
        global $me;
        $errors=array();
        $messages=array();
        //print_r($_POST);
        if(isset($_POST['email'])) {


            // EDIT THE 2 LINES BELOW AS REQUIRED
            $email_to = "info@micuentomagico.com";
            $email_subject = "Nuevo Mensaje de Mi Cuento Mágico";


            // validation expected data exists
            if(!isset($_POST['first_name']) ||
                !isset($_POST['subject']) ||
                !isset($_POST['email']) ||
                !isset($_POST['phone']) ||
                !isset($_POST['message'])) {
                $errors[] = 'Lo sentimos, pero parece que hay un problema con el formulario que envió.';
            }


            $email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';


            $first_name = $_POST['first_name']; // required
            $subject = $_POST['subject']; // required
            $email_from = $_POST['email']; // required
            $telephone = $_POST['phone']; // not required
            $comments = $_POST['message']; // required


            if(!preg_match($email_exp,$email_from))
                $errors[] = 'El Email parece incorrecto.';



            if(!isset($first_name))
                $errors[] = 'El Nombre parece incorrecto.';


            if(!isset($subject))
                $errors[] = 'El Asunto parece incorrecto.';

            if(!isset($comments))
                $errors[] = 'El Mensaje parece incorrecto.';


            if(sizeof($errors) == 0) {

                // No hay errores.....
                $email_message = '
                             <!-- content -->
                             <tr>
                                <td st-content="rightimage-paragraph">
                                    <strong>Nuevo formulario de contacto.
                                </td>
                             </tr>
                             <!-- end of content -->
                             
                             
                             <!-- Spacing --><tr><td width="100%" height="20"></td></tr><!-- Spacing -->
                             
               
                             <!-- content -->
                             <tr>
                                <td style="font-family: Helvetica, arial, sans-serif; font-size: 13px; color: #95a5a6; text-align:left;line-height: 24px;" st-content="rightimage-paragraph">
                                    <strong>Nombre:</strong><br />'.$first_name.'
                                </td>
                             </tr>
                             <!-- end of content -->
                            
                            
                             <!-- Spacing --><tr><td width="100%" height="20"></td></tr><!-- Spacing -->
                             
                             
                             <!-- content -->
                             <tr>
                                <td style="font-family: Helvetica, arial, sans-serif; font-size: 13px; color: #95a5a6; text-align:left;line-height: 24px;" st-content="rightimage-paragraph">
                                    <strong>Asunto:</strong><br />'.$subject.'
                                </td>
                             </tr>
                             <!-- end of content -->
                            
                            
                             <!-- Spacing --><tr><td width="100%" height="20"></td></tr><!-- Spacing -->
                             
                             
                             <!-- content -->
                             <tr>
                                <td style="font-family: Helvetica, arial, sans-serif; font-size: 13px; color: #95a5a6; text-align:left;line-height: 24px;" st-content="rightimage-paragraph">
                                    <strong>E-Mail:</strong><br />'.$email_from.'
                                </td>
                             </tr>
                             <!-- end of content --> 
                             
                             
                             <!-- Spacing --><tr><td width="100%" height="20"></td></tr><!-- Spacing -->
                             
                             
                             <!-- content -->
                             <tr>
                                <td style="font-family: Helvetica, arial, sans-serif; font-size: 13px; color: #95a5a6; text-align:left;line-height: 24px;" st-content="rightimage-paragraph">
                                    <strong>Teléfono:</strong><br />'.$telephone.'
                                </td>
                             </tr>
                             <!-- end of content -->
                             
                             
                             <!-- Spacing --><tr><td width="100%" height="20"></td></tr><!-- Spacing -->
                             
                             
                             <!-- content -->
                             <tr>
                                <td style="font-family: Helvetica, arial, sans-serif; font-size: 13px; color: #95a5a6; text-align:left;line-height: 24px;" st-content="rightimage-paragraph">
                                    <strong>Comentarios:</strong><br />'.$comments.'
                                </td>
                             </tr>
                             <!-- end of content -->
                             
                             
                            ';



                $mail = $this->loadModel('mail');

                if($mail->sendMail('info@micuentomagico.com',$subject,$email_message,$isHTML=true)) {
                    $messages[] = 'Se ha enviado satisfactoriamente, te contestaremos en breve';
                }
                else {
                    $errors[] = 'El email no se ha enviado.';
                }


             }

         }else $errors[] = 'No hay datos.';


      echo json_encode(array('success'=>((sizeof($errors)>0) ? 0 : 1), 'errors'=>@$errors, 'messages'=>@$messages));

	}


	
}

?>