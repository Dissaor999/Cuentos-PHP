<?php

class cart extends Controller{
    public function __construct(){
        parent::__construct();
        global $me;
        $this->books = $this->loadModel('book');

        $this->_redsysKey = "Bb5PlojUa2F3W9bV4f+2uH9Nze9wIE+s";
        $this->_redsysURL = "https://sis.redsys.es/sis/realizarPago";

      


    }

    public function index(){
        $this->_view->titulo = 'Carrito - '.APP_NAME;
        $this->_view->_cartItems = $this->getBooks();
        $this->_view->renderizar('index', 'default');
    }

    public function datos(){
        $this->_view->titulo = APP_NAME.' - Datos de compra';

        if(isset($_POST['cupon'])){
            $orders = $this->loadModel('orders');
            $this->_view->_cupon = $orders->checkVoucher($_POST['cupon']);
        }
        $this->_view->setJs(array('datos_pago', 'ong'));
        $this->_view->_cartItems = $this->getBooks();
        $this->_view->renderizar('datos_compra', 'default');
    }

    public function pagar() {
        $this->_mail = $this->loadModel('mail');
        $this->_orders = $this->loadModel('orders');


        $this->_view->_cartItems = $this->getBooks();

        if(isset($_POST['order']) && sizeof($this->_view->_cartItems)>0) {
            $data=$_POST['order'];

            switch($data['method']){
                case 'tpv':
                    $method=1;
                    break;
                case 'paypal':
                    $method=2;
                    break;
                case 'transfer':
                    $method=3;
                    break;
            }
            if(isset($data['code']))
             $this->_view->_code = $this->_orders->checkVoucher($data['code']);

            $ong = '';

            if($data['donacion_ong']=='other') $ong = $data['other_ong'];
            else  $ong = $data['donacion_ong'];
            $data = array(
                'code'=>@$this->_view->_code['id'],
                'donacion_ong'=>@$ong,
                'name'=>$data['name'],
                'lastname'=>$data['lastname'],
                'email'=>$data['email'],
                'deliveryInstructions'=>$data['deliveryInstructions'],
                'method'=>$method,
                'address'=>array(
                    'name'=>$data['address']['name'],
                    'lastname'=>$data['address']['lastname'],
                    'address'=>$data['address']['address'],
                    'zip'=>$data['address']['zip'],
                    'city'=>$data['address']['city'],
                    'state'=>$data['address']['state'],
                    'state2'=>$data['address']['state2'],
                    'dialCode'=>$data['address']['dialCode'],
                    'phone'=>$data['address']['phone'],
                )
            );


            switch($method){
                case '1':
                    ///////////////////////////////
                    //       PAGO CON TPV       //
                    ///////////////////////////////

                    if(sizeof($this->_view->_cartItems)>0)  {

                        $this->_view->_order = $this->_orders->saveOrder($data,$this->_view->_cartItems,'2');

                        // Se incluye la librería
                        $this->getLibrary('redSys/apiRedsys.php');

                        // Se crea Objeto
                        $miObj = new RedsysAPI;

                        // Valores de entrada
                        $fuc="336801147";
                        $terminal="001";
                        $moneda="978";
                        $trans="0";

                        $url=BASE_URL.'cart/redsys_listener';
                        $urlOK=BASE_URL.'cart/redsys_ok/'.$this->_view->_order['orderID'];
                        $urlKO=BASE_URL.'cart/redsys_ko/'.$this->_view->_order['orderID'];

                        $amount=0;


                        //AÑADIR COSTES DE CADA LIBRO
                        foreach($this->_view->_cartItems as $K=> $item) {
                            $amount+=doubleval($item['book']['formatos'][$item['formato']]['precio']);
                        }

                        //AÑADIR EL PRECIO DE ENVÍO
                        $amount+=doubleval(GTS_ENVIO);//precio_envio

                        //APLICAR el porcentaje de descuento en el código
                        if(isset($this->_view->_code['quantity'])) {
                            $qtyDiscount = $amount*(($this->_view->_code['quantity'])/100);
                            $priceDiscount = doubleval($amount - $qtyDiscount);
                            $amount = $priceDiscount;
                        }



                        $amount = intval($amount*100);


                        // Se Rellenan los campos
                        $miObj->setParameter("DS_MERCHANT_AMOUNT",$amount);
                        $miObj->setParameter("DS_MERCHANT_ORDER",str_pad( $this->_view->_order['orderID'], 4, "0", STR_PAD_LEFT ));
                        $miObj->setParameter("DS_MERCHANT_MERCHANTCODE",$fuc);
                        $miObj->setParameter("DS_MERCHANT_CURRENCY",$moneda);
                        $miObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE",$trans);
                        $miObj->setParameter("DS_MERCHANT_TERMINAL",$terminal);
                        $miObj->setParameter("DS_MERCHANT_MERCHANTURL",$url);
                        $miObj->setParameter("DS_MERCHANT_URLOK",$urlOK);
                        $miObj->setParameter("DS_MERCHANT_URLKO",$urlKO);

                        //Datos de configuración
                        $this->_view->_version="HMAC_SHA256_V1";


                        // Se generan los parámetros de la petición
                        $this->_view->_request = "";
                        $this->_view->_params = $miObj->createMerchantParameters();
                        $this->_view->_signature = $miObj->createMerchantSignature($this->_redsysKey);
                        $this->_view->_url = $this->_redsysURL;


                    }



                    //$this->vaciarCarrito();
                    $this->_view->renderizar('pago_tpv', 'default');
                    break;
                case '2':
                    ///////////////////////////////
                    ///////////////////////////////
                    //       PAGO CON PAYPAL //////
                    ///////////////////////////////
                    ///////////////////////////////

                    if(sizeof($this->_view->_cartItems)>0)  {
                        $this->_view->_order = $this->_orders->saveOrder($data,$this->_view->_cartItems,'2');
                           // print_r();
                        $amount=0;


                        //AÑADIR COSTES DE CADA LIBRO
                        foreach($this->_view->_cartItems as $K=> $item) {
                            $amount+=doubleval($item['book']['formatos'][$item['formato']]['precio']);
                        }

                        //AÑADIR EL PRECIO DE ENVÍO
                        $amount+=doubleval(GTS_ENVIO);//precio_envio

                        //APLICAR el porcentaje de descuento en el código
                        if(isset($this->_view->_code['quantity'])) {
                            $qtyDiscount = $amount*(($this->_view->_code['quantity'])/100);
                            $priceDiscount = doubleval($amount - $qtyDiscount);
                            $amount = $priceDiscount;
                        }



                        //$amount = intval($amount*100);

                        $urlIPN=BASE_URL.'cart/paypal_listener';
                        $urlOK=BASE_URL.'cart/redsys_ok/'.$this->_view->_order['orderID'];
                        $urlKO=BASE_URL.'cart/redsys_ko/'.$this->_view->_order['orderID'];




                        //Here we can use paypal url or sanbox url.
                        $this->_view->_url = 'https://www.paypal.com/cgi-bin/webscr';
                        //Here we can used seller email id.
                        $this->_view->_mcode = 'VW475P3YJXBC2';
                        //here we can put cancle url when payment is not completed.
                        $this->_view->urlKO = $urlKO;
                        //here we can put cancle url when payment is Successful.
                        $this->_view->_urlOK = $urlOK;
                        //paypal call this file for ipn
                        $this->_view->_notifyurl = $urlIPN;
                        $this->_view->_amount = $amount;




                    }
                   // $this->vaciarCarrito();
                    $this->_view->renderizar('pago_paypal', 'default');
                    break;
                case '3':

                    ///////////////////////////////
                    ///////////////////////////////
                    //       PAGO CON TRANSFER //////
                    ///////////////////////////////
                    ///////////////////////////////
                    if(sizeof($this->_view->_cartItems)>0)  {
                        if($this->_view->_order = $this->_orders->saveOrder($data,$this->_view->_cartItems,'2')) {
                            $orderData = $this->_orders->getData(array('id'=>$this->_view->_order['orderID'],$this->_view->_lang), $this->_view->_lang)['data'][0];

                            // die();
                            $mailman=new Mailman();
                            $mailman->sendMail(EMAIL_ADMIN,'Nuevo pedido - #'.$this->_view->_order['orderID'],'Se ha realizado un nuevo pedido, consulta los detalles en el panel de gestión.');


                            // print_r();
                            $amount=0;


                            //AÑADIR COSTES DE CADA LIBRO
                            foreach($this->_view->_cartItems as $K=> $item) {
                                $amount+=doubleval($item['book']['formatos'][$item['formato']]['precio']);
                            }

                            //AÑADIR EL PRECIO DE ENVÍO
                            $amount+=doubleval(GTS_ENVIO);//precio_envio

                            //APLICAR el porcentaje de descuento en el código
                            if(isset($this->_view->_code['quantity'])) {
                                $qtyDiscount = $amount*(($this->_view->_code['quantity'])/100);
                                $priceDiscount = doubleval($amount - $qtyDiscount);
                                $amount = $priceDiscount;
                            }



                            $message_email='
                                <div style="font-size:13px; font-family: Open Sans; line-height: 1.3em;">
                                
                                    <p>Hola '.$orderData['name'].',</p><br />
                                    <br />
                                    <p>Tu pedido número #'.$this->_view->_order['orderID'].' ha sido realizado en la fecha '.date('d/m/Y', $orderData['date_add']).' mediante '.$orderData['metodo_pago'].' y se encuentra PENDIENTE DE PAGO,</p>
                                    <br />
                                    
                                    <p>Para realizar el pago del pedido tendrás que ir al banco con los sigueintes datos.</p>
                        
                                    <p><strong>CONCEPTO:</strong> PAGO PEDIDO N-'.$this->_view->_order['orderID'].'</p>
                                    <p><strong>Cuenta:</strong>    ES94 0081 0309 7100 0195 2499</p>
                                    <p><strong>Precio:</strong> '.$amount.'€ </p>
                                    <p><strong>Titular:</strong> GLORIA MARÍA VÉLEZ BERMÚDEZ</p>
                                    <p><strong>Oficina:</strong> 0309 - MARQUESA VIUDA DE ALDAMA, 2-4,28100 ALCOBENDAS</p>
                                    <p><strong>Teléfono:</strong> +34 916 545 100</p>
                                    
                                    <br />
                                    <p>Los datos de envío son los siguientes:</p>
                                    <br />
                                    <br />
                                    <p><strong>Nombre:</strong><br /> '.$orderData['envio_nombre'].'  '.$orderData['envio_apellidos'].',<br /><br /></p>
                                    <p><strong>Dirección:</strong><br /> '.$orderData['envio_direccion'].',<br /><br /></p>
                                    <p><strong>CP:</strong><br /> '.$orderData['envio_cp'].',<br /><br /></p>
                                    <p><strong>Localidad:</strong><br /> '.$orderData['envio_localidad'].',<br /><br /></p>
                                    <p><strong>Provincia:</strong><br /> '.$orderData['envio_provincia'].',<br /><br /></p>
                                    <p><strong>Teléfono:</strong><br /> '.$orderData['envio_prefijotelefono'].' '.$orderData['envio_telefono'].',<br /><br /></p>
                                    <p><strong>Comentarios:</strong><br /> '.$orderData['envio_comentarios'].'.</p>
                                    <br />
                                    <br />
                                </div>
                            ';



                            $mailman->sendMail($orderData['email'],'Has realizado un pedido en Mi Cuento Mágico - #'.$orderData['id'],$message_email);

                            $this->vaciarCarrito();
                            $this->_view->renderizar('pago_transferencia', 'default');
                        }
                        else {
                            $this->redireccionar('/cart',$this->_view->_lang, true);
                        }
                    }else
                        $this->redireccionar('/cart',$this->_view->_lang, true);

                    break;
            }
        }else {
            $this->_view->renderizar('no_data_pay', 'default');
        }
    }




    //  CART METHODS
    public function addToCart(){

        if(
            isset($_POST['formato']) &&
            isset($_POST['book_slug']) &&
            isset($_POST['name']) &&
            isset($_POST['gender']) &&
            isset($_POST['skin_image']) &&
            isset($_POST['hair_style']) &&
            isset($_POST['hair_image']) &&
            isset($_POST['eyes_image']) &&
            isset($_POST['clothes_image']) &&
            isset($_POST['glasses_style']) &&
            isset($_POST['dedicatoria']) &&
            isset($_POST['dedicatoria_img'])
        ) {

            $data = $_POST;
            if($this->books->existBySlug($data['book_slug'])) {

                //create tmp array books cart
                $books = array();

                //get the books from cookies
                if(isset($_COOKIE['books']))  $books = json_decode($_COOKIE["books"], true);

                //set new index
                if(isset($books)) $cartID = sizeof($books)+1;
                else $cartID = 1;

                //add the new
                $books[$cartID]=array(
                    'cart-id'=>$cartID,
                    'book_slug'=>$data['book_slug'],
                    'skinColor'=>$data['skin_image'],
                    'hairStyle'=>$data['hair_style'],
                    'hairColor'=>$data['hair_image'],
                    'eyesColor'=>$data['eyes_image'],
                    'glassesStyle'=>$data['glasses_style'],
                    'clothesColor'=>$data['clothes_image'],
                    'formato'=>$data['formato'],
                    'name'=>$data['name'],
                    'gender'=>$data['gender'],
                    'dedicatoria_text'=>$data['dedicatoria'],
                    'dedicatoria_img'=>@$data['dedicatoria_img']
                );


                setcookie("books", json_encode($books), time()+3600, '/');

                $this->redireccionar('cart/?success=1');

            } else $this->redireccionar('cart/?error=2');
        }else $this->redireccionar('cart/?error=1');
    }

    public function removeItem(){
        if(isset($_GET['item']) && isset($_COOKIE['books'])) {
            $books = json_decode($_COOKIE["books"], true);
            unset($books[$_GET['item']]);
            //set the cookie
            setcookie("books", json_encode($books), time()+3600, '/');
        }
        $this->redireccionar('cart');
    }

    public function vaciarCarrito() {
        if (isset($_COOKIE['books'])) {
            setcookie("books", json_encode(array()), time()+3600, '/');
        }
    }




    //GET CART ITEMS
    public function getBooks(){
        $pedidos=array();
        if(isset($_COOKIE['books']))
            $cartBooks = json_decode($_COOKIE["books"]);


        if(isset($cartBooks) && sizeof($cartBooks)>0) {

            foreach ($cartBooks as $index => $pedido) {

                $libro = $this->books->getBySlug(
                    $pedido->book_slug
                );

                $pedidos[] = array(
                    'id-object' => $index,
                    'cart-id' => @$pedido->cart_id,
                    'book' => $libro,
                    'formato' => $pedido->formato,
                    'custom_avatar' => array(
                        'skinColor' => $pedido->skinColor,
                        'hairStyle' => $pedido->hairStyle,
                        'hairColor' => $pedido->hairColor,
                        'eyesColor' => $pedido->eyesColor,
                        'glassesStyle' => $pedido->glassesStyle,
                        'clothesColor' => $pedido->clothesColor,
                        'name' => $pedido->name,
                        'gender' => $pedido->gender,
                    ),
                    'dedicatoria' => array(
                        'photo' => $pedido->dedicatoria_img,
                        'text' => $pedido->dedicatoria_text
                    )
                );
            }
        }

        return $pedidos;
    }





    //CONTROLLERS FOR PAY METHODS
    public function paypal_ko(){
        print_R($_POST);
        print_R($_GET);
    }

    //CONTROLLERS FOR PAY METHODS
    public function redsys_ok(){

        $this->vaciarCarrito();

        $this->_view->titulo = 'Pedido pagado satisfactoriamente - '. APP_NAME;
        $this->_view->renderizar('pedido_pagado', 'default');
    }

    public function paypal_listener() {
        $this->_orders = $this->loadModel('orders');
        $mailman=new Mailman();
        $mailman=new Mailman();

        $id_pedido = '';
        $id_pedido = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;





        if($_REQUEST['payment_status']="Completed" ) {
            if(!$this->_orders->existOrderId($id_pedido))
                $mailman->sendMail('alex@acornstudio.es','No existe  ',$id_pedido);
            else {

                $mailman->sendMail('alex@acornstudio.es','existe  ',$id_pedido);
                $orderData = $this->_orders->getData(array('id'=>$id_pedido))['data'][0];

                $mailman->sendMail('alex@acornstudio.es','ID PEDIDO ',print_r($orderData,true));

                $mailman->sendMail(EMAIL_ADMIN,'Nuevo pedido - #'.$id_pedido,'Se ha realizado un nuevo pedido, consulta los detalles en el administrador.');

                // AQUÍ HAY QUE ENVIAR UN EMAIL A CLIENTE
                $message_email = $this->_orders->emailOrderPaid($orderData);


                $mailman->sendMail($orderData['email'],'Has realizado un pedido en Mi Cuento Mágico - #'.$orderData['id'],$message_email);

                $updated = $this->_orders->updateStatus(3,$id_pedido);
            }
        }

    }
    public function paypal_ok() {

         $this->_orders = $this->loadModel('orders');
        $mailman=new Mailman();

        $id_pedido = '';
        $id_pedido = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;

        if($_POST['payment_status']="Completed" && $this->_orders->existOrderId($id_pedido)) {

            $orderData = $this->_orders->getData(array('id'=>$id_pedido,$this->_view->_lang), $this->_view->_lang)['data'][0];

            $mailman->sendMail(EMAIL_ADMIN,'Nuevo pedido - #'.$id_pedido,'Se ha realizado un nuevo pedido, consulta los detalles en el administrador.');

            // AQUÍ HAY QUE ENVIAR UN EMAIL A CLIENTE
            $message_email = $this->_orders->emailOrderPaid($orderData);


            $mailman->sendMail($orderData['email'],'Has realizado un pedido en Mi Cuento Mágico - #'.$orderData['id'],$message_email);

            $updated = $this->_orders->updateStatus(3,$id_pedido);

        }
/*
        // Se incluye la librería
        $this->getLibrary('redSys/apiRedsys.php');

        // Se crea Objeto
        $miObj = new RedsysAPI;


        if (
            isset( $_REQUEST['Ds_MerchantParameters'] ) &&
            isset( $_REQUEST['Ds_Signature'] )
        ) {


            $datos = $_REQUEST["Ds_MerchantParameters"];
            $signatureRecibida = $_REQUEST["Ds_Signature"];

            $decodec = $miObj->decodeMerchantParameters($datos);
            $firma = $miObj->createMerchantSignatureNotif($this->_redsysKey,$datos);


            if ($firma === $signatureRecibida){

                //print_r( $_GET);
                $data = json_decode($decodec, true);

                $mailman->sendMail('alex@acornstudio.es','Revisar Pedido REDSYS '.$data['Ds_Order'],print_r($_POST,true));



                $this->_orders = $this->loadModel('orders');

                $DsResponse = $data["Ds_Response"];
                $DsResponse += 0;
                if($DsResponse <= 99) { //si la respuesta del TPV es OK
                    if($this->_orders->existOrderId($data['Ds_Order'])) {  //si el pedido es OK

                        $orderData = $this->_orders->getData(array('id'=>$data['Ds_Order'],$this->_view->_lang), $this->_view->_lang)['data'][0];

                        $mailman->sendMail(EMAIL_ADMIN,'Nuevo pedido - #'.$data['Ds_Order'],'Se ha realizado un nuevo pedido, consulta los detalles en el administrador.');

                        // AQUÍ HAY QUE ENVIAR UN EMAIL A CLIENTE
                        $message_email = $this->_orders->emailOrderPaid($orderData);


                        $mailman->sendMail($orderData['email'],'Has realizado un pedido en Mi Cuento Mágico - #'.$orderData['id'],$message_email);

                        $updated = $this->_orders->updateStatus(3,$data['Ds_Order']);
                    }else  {
                        $mailman->sendMail(EMAIL_ADMIN,'No existe - #'.$data['Ds_Order'],'Ha habido un error en el pago del pedido.');
                    }
                }else {
                    $mailman->sendMail(EMAIL_ADMIN,'Error en pedido - #'.$data['Ds_Order'],'Ha habido un error en el pago del pedido.');
                    $updated = $this->_orders->updateStatus(5,$data['Ds_Order']);
                }
            }
        }
        */
    }

    public function redsys_listener() {
		
		
        $mailman=new Mailman();



        // Se incluye la librería
        $this->getLibrary('redSys/apiRedsys.php');

        // Se crea Objeto
        $miObj = new RedsysAPI;


        if (
            isset( $_REQUEST['Ds_MerchantParameters'] ) &&
            isset( $_REQUEST['Ds_Signature'] )
        ) {


            $datos = $_REQUEST["Ds_MerchantParameters"];
            $signatureRecibida = $_REQUEST["Ds_Signature"];

            $decodec = $miObj->decodeMerchantParameters($datos);
            $firma = $miObj->createMerchantSignatureNotif($this->_redsysKey,$datos);


            if ($firma === $signatureRecibida){

                //print_r( $_GET);
                $data = json_decode($decodec, true);

                $mailman->sendMail('alex@acornstudio.es','Revisar Pedido REDSYS '.$data['Ds_Order'],print_r($_POST,true));



                $this->_orders = $this->loadModel('orders');

                $DsResponse = $data["Ds_Response"];
                $DsResponse += 0;
                if($DsResponse <= 99) { //si la respuesta del TPV es OK
                    if($this->_orders->existOrderId($data['Ds_Order'])) {  //si el pedido es OK

                        $orderData = $this->_orders->getData(array('id'=>$data['Ds_Order'],$this->_view->_lang), $this->_view->_lang)['data'][0];

                        $mailman->sendMail(EMAIL_ADMIN,'Nuevo pedido - #'.$data['Ds_Order'],'Se ha realizado un nuevo pedido, consulta los detalles en el administrador.');

                        /* AQUÍ HAY QUE ENVIAR UN EMAIL A CLIENTE */
                        $message_email = $this->_orders->emailOrderPaid($orderData);


                        $mailman->sendMail($orderData['email'],'Has realizado un pedido en Mi Cuento Mágico - #'.$orderData['id'],$message_email);

                        $updated = $this->_orders->updateStatus(3,$data['Ds_Order']);
                    }else  {
                        $mailman->sendMail(EMAIL_ADMIN,'No existe - #'.$data['Ds_Order'],'Ha habido un error en el pago del pedido.');
                    }
                }else {
                    $mailman->sendMail(EMAIL_ADMIN,'Error en pedido - #'.$data['Ds_Order'],'Ha habido un error en el pago del pedido.');
                    $updated = $this->_orders->updateStatus(5,$data['Ds_Order']);
                }
            }
        }
    }


    /* TESTING */
    public function testemail(){
		echo 'z';
        $orderid=1243;
        $this->_orders = $this->loadModel('orders');
        $mailman=new Mailman();

        $orderData = $this->_orders->getData(array('id'=>$orderid), $this->_view->_lang)['data'][0];


        // AQUÍ HAY QUE ENVIAR UN EMAIL A CLIENTE
        $message_email='
                            <div style="font-size:13px; font-family: Open Sans; line-height: 1.3em;">
                            
                                <p>Hola '.$orderData['name'].',</p><br />
                                <br />
                                <p>Tu pedido ha sido realizado en la fecha '.date('d/m/Y', $orderData['date_add']).' mediante '.$orderData['metodo_pago'].' y su estado es '.$orderData['status'].',</p>
                                <br />
                                <p>Productos:</p><br />';


        $message_email .= '<style>
                            .tablepedido th,
                            .tablepedido td
                            { padding:4px;}
                            </style>
                        <table class="tablepedido" border="1" cellspacing="0" style="border:1px solid #2a90b7;">
                            <tr>
                                <th>Avatar</th>
                                <th>Nombre</th>
                                <th>Libro</th>
                                <th>Precio</th>
                                <th>Formato</th>
                                <th></th>
                            </tr>';

        foreach($orderData['datas'] as $bookPedido) {
            $btnDwnload ='';
            if($bookPedido['format_id']==1 || $bookPedido['format_id']==3) $btnDwnload='<a style=" padding:2px 7px; text-decoration:none; background:#2a90b7;color:white;" href="'.BASE_URL.'libros/descargar_ebook/'.md5(1).'">DESCARGAR</a>';
            $message_email .= '
                        <tr>
                            <td><img height="30" src="'.BASE_URL.'libros/generateAvatar/?book_slug='.$bookPedido['book_slug'].'&gender='.$bookPedido['gender_id'].'&glassesStyle='.$bookPedido['glasses_id'].'&skinColor='.$bookPedido['skin_id'].'&hairStyle='.$bookPedido['hair_style_id'].'&hairColor='.$bookPedido['hair_style_id'].'&eyesColor='.$bookPedido['eyes_id'].'&clothesColor='.$bookPedido['clothes_id'].'"></td>
                            <td>'.$bookPedido['name'].'</td>
                            <td>'.$bookPedido['book_name'].'</td>
                            <td>'.$bookPedido['format_price'].'</td>
                            <td>'.$bookPedido['format_name'].'</td>
                            <td>'.$btnDwnload.'</td>
                        </tr>';

        }
        $message_email .= '</table>';

        $message_email .='<br />
                                <p>Los datos de envío son los siguientes:</p>
                                <br />
                                <br />
                                <p><strong>Nombre:</strong><br /> '.$orderData['envio_nombre'].'  '.$orderData['envio_apellidos'].',<br /><br /></p>
                                <p><strong>Dirección:</strong><br /> '.$orderData['envio_direccion'].',<br /><br /></p>
                                <p><strong>CP:</strong><br /> '.$orderData['envio_cp'].',<br /><br /></p>
                                <p><strong>Localidad:</strong><br /> '.$orderData['envio_localidad'].',<br /><br /></p>
                                <p><strong>Provincia:</strong><br /> '.$orderData['envio_provincia'].',<br /><br /></p>
                                <p><strong>Teléfono:</strong><br /> '.$orderData['envio_prefijotelefono'].' '.$orderData['envio_telefono'].',<br /><br /></p>
                                <p><strong>Comentarios:</strong><br /> '.$orderData['envio_comentarios'].'.</p>
                                <br />
                                <br />
                            </div>
                        ';
        $mailman->sendMail('alex@acornstudio.es','Has realizado un pedido en Mi Cuento Mágico - #'.$orderData['id'],$message_email);


    }
}

?>