<?php

class mail extends Model {
	
	
	public function __construct(){
		parent::__construct();
	}
	
	//SEND MAIL DATA CONNECTION
	protected  
            $from = "info@micuentomagico.com",		// 	Quién lo envía
            $fromname = APP_NAME; 		//	Nombre a mostrar
	
	
	public function sendMail($to,$subject,$message,$isHTML=true,$aFiles="",$cc="",$cco="",$replyto="") {
    
		  
		  include_once(ROOT.'libs/PHPMailer-master/class.phpmailer.php');
          //include_once(ROOT.'libs/PHPMailer-master/class.smtp.php');

           $mail = new PHPMailer(true);

            if( $isHTML ) {
                    $messagedata  = $this->header ();
                    $messagedata .=  ( $message );
                    $messagedata .= $this->footer ();
            } else {
                    $messagedata = $message;
            }
			
			
            $mail -> 	From     	= $this->from;			// Mail de origen
            $mail -> 	FromName 	= utf8_decode($this->fromname); 		// Nombre del que envia
            $mail -> 	AddAddress	( $to ); 				// Mail destino, podemos agregar muchas direcciones
            $mail ->	Mailer		 = 	"smtp";
            $mail -> 	Subject  	=  utf8_decode($subject);			 
            $mail -> 	Body     	=  utf8_decode($messagedata); 		// Este es el contenido alternativo sin html
            $mail -> 	IsHTML		( $isHTML );
            $mail -> 	SMTPDebug 	= true;
            $mail -> IsSMTP(); 		//PARA OTROS
            $mail->Host = 'acornstudio.es';  // Specify main and backup SMTP servers
            $mail->Username = 'web@micuentomagico.com';                 // SMTP username
            $mail->Password = 'iu9dtY@HYuyEH';                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 465;
			//$mail->IsSendMail();	
            $mail->SMTPAuth 		= true;


			if(is_array($cc) && !empty($cc) && sizeof($cc)>0) {
				foreach(@$cc as $email => $name){
				   $mail->AddCC($email, $name);
				}
			}
            if($aFiles != "") {
				foreach ( $aFiles as $value ) {
					
					$mail -> AddAttachment ( $value['path'] , $value['file'] );
				}
            }
            if(!$mail->Send()) {
                return false;
            } else {
				
               	return true;
            }
	}
	
	public function header() {
        /*EMAIL TEMPLATE BEGINS*/
        $imgSrc   = BASE_URL.'public/img/micuentomagico-white.png'; // Change image src to your site specific settings
        $imgDesc  = APP_NAME; // Change Alt tag/image Description to your site specific settings
        $imgTitle = APP_NAME; // Change Alt Title tag/image title to your site specific settings

        return '<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1.0">
				<title>Web</title>
				<style type="text/css">
				 /* Client-specific Styles */
				 #outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
				 body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;}
				 /* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
				 .ExternalClass {width:100%;} /* Force Hotmail to display emails at full width */
				 .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing.  */
				 #backgroundTable {margin:0; padding:0; width:100% !important; line-height: 100% !important;}
				 img {outline:none; text-decoration:none;border:none; -ms-interpolation-mode: bicubic;}
				 a img {border:none;}
				 .image_fix {display:block;}
				 p {margin: 0px 0px !important;}
				 
				 table td {border-collapse: collapse;}
				 table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
				 /*a {color: #e95353;text-decoration: none;text-decoration:none!important;}*/
				 /*STYLES*/
				 
				 table[class=full] { width: 100%; clear: both; }
				 
				 /*################################################*/
				 /*IPAD STYLES*/
				 /*################################################*/
				 @media only screen and (max-width: 640px) {
				 a[href^="tel"], a[href^="sms"] {
				 text-decoration: none;
				 color: #ffffff; /* or whatever your want */
				 pointer-events: none;
				 cursor: default;
				 }
				 .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
				 text-decoration: default;
				 color: #ffffff !important;
				 pointer-events: auto;
				 cursor: default;
				 }
				 table[class=devicewidth] {width: 440px!important;text-align:center!important;}
				 table[class=devicewidthinner] {width: 420px!important;text-align:center!important;}
				 table[class="sthide"]{display: none!important;}
				 img[class="bigimage"]{width: 420px!important;height:219px!important;}
				 img[class="col2img"]{width: 420px!important;height:258px!important;}
				 img[class="image-banner"]{width: 440px!important;height:106px!important;}
				 td[class="menu"]{text-align:center !important; padding: 0 0 10px 0 !important;}
				 td[class="logo"]{padding:10px 0 5px 0!important;margin: 0 auto !important;}
				 img[class="logo"]{max-height:35px; padding:0!important;margin: 0 auto !important;}

				 }
				 /*##############################################*/
				 /*IPHONE STYLES*/
				 /*##############################################*/
				 @media only screen and (max-width: 480px) {
				 a[href^="tel"], a[href^="sms"] {
				 text-decoration: none;
				 color: #ffffff; /* or whatever your want */
				 pointer-events: none;
				 cursor: default;
				 }
				 .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
				 text-decoration: default;
				 color: #ffffff !important; 
				 pointer-events: auto;
				 cursor: default;
				 }
				 table[class=devicewidth] {width: 280px!important;text-align:center!important;}
				 table[class=devicewidthinner] {width: 260px!important;text-align:center!important;}
				 table[class="sthide"]{display: none!important;}
				 img[class="bigimage"]{width: 260px!important;height:136px!important;}
				 img[class="col2img"]{width: 260px!important;height:160px!important;}
				 img[class="image-banner"]{width: 280px!important;height:68px!important;}
				 }
			  </style>
		   </head>
		<body>
	
		<div class="block">
		   <!-- start of header -->
		   <table width="100%" bgcolor="#f6f4f5" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="header">
			  <tbody>
				 <tr>
					<td>
					   <table width="580" style="background:#66" bgcolor="#2a9ed1" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth" hlitebg="edit" shadow="edit">
						  <tbody>
							 <tr>
								<td>
								   <!-- logo -->
								   <table width="280" cellpadding="0" cellspacing="0" border="0" align="left" class="devicewidth">
									  <tbody>
										 <tr>
											<td valign="middle" width="270" style="padding: 10px 0 10px 20px;" class="logo">
											   <div class="imgpop">
												  <a href="'.BASE_URL.'" alt="'.$imgDesc.'" border="0" style="display:block; border:none; outline:none; text-decoration:none;" st-image="edit" class="logo" /><img height="55" src="'.$imgSrc.'" /></a>
											   </div>
											</td>
										 </tr>
									  </tbody>
								   </table>
								   <!-- End of logo -->
								   <!-- menu -->
								   <table width="280" cellpadding="0" cellspacing="0" border="0" align="right" class="devicewidth">
									  <tbody>
										 <tr>
											<td width="270" valign="middle" style="font-family: Helvetica, Arial, sans-serif;font-size: 14px; color: #ffffff;line-height: 24px; padding: 10px 0;" align="right" class="menu" st-content="menu">
												 
												
											</td>
											<td width="20"></td>
										 </tr>
									  </tbody>
								   </table>
								   <!-- End of Menu -->
								</td>
							 </tr>
						  </tbody>
					   </table>
					</td>
				 </tr>
			  </tbody>
		   </table>
		   </div>
		  <!-- BLOQUE DATOS CLIENTE -->
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
                                          
                                          
		   ';
    }
	
	public function footer() {
        return ' 
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
            </div>
            
				<div class="block">
				   <!-- Start of preheader -->
				   <table width="100%" bgcolor="#f6f4f5" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="postfooter">
					  <tbody>
						 <tr>
							<td width="100%">
							   <table width="580" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
								  <tbody>
									 <!-- Spacing -->
									 <tr>
										<td width="100%" height="5"></td>
									 </tr>
									 <!-- Spacing -->
									 <tr>
										<td align="center" valign="middle" style="font-family: Helvetica, arial, sans-serif; font-size: 10px;color: #999999" st-content="preheader">
										   Este es un correo informativo, si quieres enviar algún mail a nuestro equipo, por favor ponte en contacto con <a class="hlite" href="mailto:info@micuentomagico.com" style="text-decoration: none; color: #0db9ea">info@micuentomagico.com</a> 
										</td>
									 </tr>
									 <!-- Spacing -->
									 <tr>
										<td width="100%" height="5"></td>
									 </tr>
									 <!-- Spacing -->
								  </tbody>
							   </table>
							</td>
						 </tr>
					  </tbody>
				   </table>
				   <!-- End of preheader -->
				</div>
			</body>
		</html>';
    }
}
