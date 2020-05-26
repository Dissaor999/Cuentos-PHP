<?php

class pedidos extends Controller{
    private $_name = ' - Pedidos';

    public function __construct(){
        parent::__construct();

        if (!CUser::has_access('admin-area')) {
            $this->redireccionar('/');
        }

        $this->model_product =  $this->loadModel('products');
    }

    protected $URL_BOOK=ROOT.'public/img/books/';
    protected $THUMB_PREFIX='th_';

    public function index(){

        $this->_view->setJs(array('ajax'),'admin');
        $this->_orders =  $this->loadModel('orders');
        $this->_view->_data['statuses']= $this->_orders->getStatuses();




        if(isset($this->_view->_args[0])){
            switch($this->_view->_args[0]){
                case 'espera':
                    $this->_view->_data['orders']=$this->_orders->getData(array('sql'=>array(' AND uo.status=1')));
                    break;
                case 'no-pagados': $this->_view->_data['orders']=$this->_orders->getData(array('sql'=>array(' AND uo.status=1')));
                    break;
                case 'pendientes-transfer':
                    $this->_view->_data['orders']=$this->_orders->getData(array('sql'=>array(' AND uo.status=2')));
                    break;
                case 'pagados-y-no-enviados-imprenta':
                    $this->_view->_data['orders']=$this->_orders->getData(array('sql'=>array(' AND (uo.impresion_sent=0 AND uo.status>=3)')));
                    break;
                case 'pagados':
                    $this->_view->_data['orders']=$this->_orders->getData(array('sql'=>array(' AND (uo.impresion_sent=1 AND uo.status>=3)')));
                    break;
                case 'completados':
                    $this->_view->_data['orders']=$this->_orders->getData(array('sql'=>array(' AND uo.status>=3')));
                    break;
                case 'cancelados':
                    $this->_view->_data['orders']=$this->_orders->getData(array('sql'=>array(' AND uo.status=1')));
                    break;
                default:
                    $this->_view->_data['orders']=$this->_orders->getData();
                    break;
            }
        }else {
            $this->_view->_data['orders']=$this->_orders->getData(array('sql'=>' AND uo.status>=3  AND uo.impresion_sent =0 '));
        }



        $this->_view->titulo =  APP_NAME.$this->_name;
        $this->_view->renderizar('lista', 'admin');
    }

    public function detalles_impresion() {
        $_orders = $this->loadModel('orders');
        $this->_view->_orders=$_orders->getOrdersNotPrinted();
        $this->_view->setJs(array('enviar_impresion'), 'admin');
        $this->_view->titulo =  'Pedidos pendientes de impresion  - '.APP_NAME;
        $this->_view->renderizar('detalles_impresion', 'admin');
    }


    public function enviar_pedido_imprenta() {

        $_orders = $this->loadModel('orders');


        if(isset($_REQUEST['id'])) {

            $orderID = $_REQUEST['id'];

            $theData = $_orders->getData(array('id'=>$orderID))['data'];
            if(isset($theData[0]) && is_array($theData[0])) {
                $oData = $theData[0];

                if($oData['impresion_sent']==0) {
                    $wsdl = "http://webservice.podiprint.com/soap_server.php?wsdl";
                    $login="micuentomagico";
                    $password="LWhpGYmFJLRk3ksh";

                    $soap_options = array( 'login' => $login,'password' => $password,'cache_wsdl' => WSDL_CACHE_NONE);
                    $client = new SoapClient( $wsdl , $soap_options );

                    $libros = array();
                    foreach($oData['datas'] as $libro) {

                        $libros[] =array(
                            "Titulo" => $libro['book_name'],
                            "Cantidad" => "1",

                            "UrlTripa" => array("url" => BASE_URL.'libros/print_download/?type=tripas&id='.md5($libro['id'])),
                            "UrlPortada" =>  array("url" => BASE_URL.'libros/print_download/?type=portada&id='.md5($libro['id'])),

                            "Tamano" => "210x210",
                            "Paginas" => "42",

                            "PapelTripa" => "Estucado Brillo",
                            "GramajeTripa" => "170",

                            "PapelCubierta" => "Estucado Mate",
                            "GramajeCubierta" => "300",

                            "Encuadernado" => "PUR",

                            "Tapa" => "BLANDA",
                            "Acabado" => "BRILLO",

                            "Solapa" => "0",
                        );
                    }
                   // print_r($libros);

                    $hacerPedido = array(
                        'IdentificadorPedido'=> $oData['id'],
                        "Peso" => "10",
                        "Comprador" => array(
                            "Nombre" => $oData['envio_nombre'],
                            "Apellidos" => $oData['envio_apellidos'],
                            "Telefono" => $oData['envio_telefono'],
                            "Movil" => $oData['envio_telefono'],
                            "Email" => $oData['email'],
                            "Direccion" => $oData['envio_direccion'],
                            "Pais" => "ESP",
                            "Localidad" => $oData['envio_localidad'],
                            "CodigoPostal" => $oData['envio_cp'],
                        ),
                        "Libro" => $libros
                    );



                try {
                    $result = $client->hacerPedido($hacerPedido);
                    //print_r($result);
                    if($result['Estado']=='OK') {
                        $_orders->updatePedidoImprenta(1,$result['idPedidoPOD'],$orderID);
                    }
                    echo json_encode($result);
                } catch (SoapFault $fault) {
                    echo json_encode(array('Estado'=>'KO', 'error'=>$fault->faultstring));
                }
                }else echo json_encode(array('Estado'=>'KO', 'error'=>'Ya enviado anteriormente'));

            }else {
                echo json_encode(array('Estado'=>'KO', 'error'=>'No existe el pedido '.$orderID));
            }
        }
        else {
            echo json_encode(array('Estado'=>'KO'));
        }

    }


    public function delete(){
        $orders = $this->loadModel('orders');
        //Project
        $id = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;
        $params = array('id'=>$id);


        $this->_view->_data['orders']= $orders->getData($params,$this->_view->_lang);
        $this->_view->_data['statuses']= $orders->getStatuses();

        $this->_view->_data['langs']= Languages::getLangsDB();

        $this->_view->setJs(array('ajax'));


        $this->_view->titulo =  'Borrar Usuario  - '.APP_NAME;
        $this->_view->renderizar('delete', 'admin');
    }

    //View
    public function view(){
        $orders = $this->loadModel('orders');

        //Project
        $id = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;
        $params = array('id'=>$id);

        $this->_view->_data['orders']= $orders->getData($params,$this->_view->_lang);
        $this->_view->_data['statuses']= $orders->getStatuses();

        $this->_view->setJs(array('ajax'), 'admin');
        $this->_view->titulo = 'Ver Pedido - '.APP_NAME;
        $this->_view->renderizar('view', 'admin');
    }

    //View
    public function export()
    {
        $orders = $this->loadModel('orders');


        //Project


        $order = $orders->getData();


        $statuses= $orders->getStatuses();


        //EMPIEZA LA EXPORTACIÓN

        /** Error reporting */
        error_reporting(FALSE);
        ini_set('display_errors', FALSE);
        ini_set('display_startup_errors', FALSE);
        date_default_timezone_set('Europe/London');
        define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

        /** Include PHPExcel */
        $this->getLibrary('PHPExcel.php');

        // Create new PHPExcel object


		// Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        /* SET SIZES */




// Set document properties
        $objPHPExcel->getProperties()->setCreator(APP_NAME)
            ->setLastModifiedBy(APP_NAME)
            ->setTitle("PEDIDOS ".date('d-m-Y',time()))
            ->setSubject("PEDIDOS ".date('d-m-Y',time()) )
            ->setDescription("PEDIDOS ".date('d-m-Y',time()) )
            ->setKeywords("PEDIDOS ".date('d-m-Y',time()))
            ->setCategory("PEDIDOS ".date('d-m-Y',time()) );


        /*
        *
        * DATOS DE CABECERA
        *
        * */

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A2', 'ID')
            ->setCellValue('B2', 'Fecha')
            ->setCellValue('C2', 'Nombre')
            ->setCellValue('D2', 'Apellidos')
            ->setCellValue('E2', 'Email')

            ->setCellValue('F2', 'Nombre')
            ->setCellValue('G2', 'Apellidos')
            ->setCellValue('H2', 'Dirección')
            ->setCellValue('I2', 'Código Postal')
            ->setCellValue('J2', 'Localidad')
            ->setCellValue('K2', 'Provincia')
            ->setCellValue('L2', 'Prefijo Teléfono')
            ->setCellValue('M2', 'Teléfono')
            ->setCellValue('N2', 'Comentarios Envío')



            ->setCellValue('O2', 'Productos')
            ->setCellValue('P2', 'Precio Total')
            ->setCellValue('Q2', 'Estado')
            ->setCellValue('R2', 'Descuento')
            ->setCellValue('S2', 'Metodo Pago');





        $row_iterator=3;

        foreach($order['data'] as $order) {
            $total_pedido=0;
            foreach($order['datas'] as $prod){$total_pedido+=$prod['format_price'];}

            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$row_iterator, $order['id'])
                ->setCellValue('B'.$row_iterator, date('d/m/Y H:i', $order['date_add']))
                ->setCellValue('C'.$row_iterator, $order['name'])
                ->setCellValue('D'.$row_iterator, $order['surnames'])
                ->setCellValue('E'.$row_iterator, $order['email'])

                ->setCellValue('F'.$row_iterator, $order['envio_nombre'])
                ->setCellValue('G'.$row_iterator, $order['envio_apellidos'])
                ->setCellValue('H'.$row_iterator, $order['envio_direccion'])
                ->setCellValue('I'.$row_iterator, $order['envio_cp'])
                ->setCellValue('J'.$row_iterator, $order['envio_localidad'])
                ->setCellValue('K'.$row_iterator, $order['envio_provincia'])
                ->setCellValue('L'.$row_iterator, $order['envio_prefijotelefono'])
                ->setCellValue('M'.$row_iterator, $order['envio_telefono'])
                ->setCellValue('N'.$row_iterator, $order['envio_comentarios'])



                ->setCellValue('O'.$row_iterator, sizeof($order['datas']))
                ->setCellValue('P'.$row_iterator, $total_pedido)
                ->setCellValue('Q'.$row_iterator, $order['status'])
                ->setCellValue('R'.$row_iterator, $order['voucher_id'])
                ->setCellValue('S'.$row_iterator, $order['paymethod']);


                 $row_iterator++;
        }



        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle("PEDIDOS ".date('d-m-Y',time()));


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel5)
       
        header('Content-Disposition: attachment;filename=PEDIDOS '.date('d-m-Y',time()).'.xls');
        header('Cache-Control: max-age=0');


		ob_clean();
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		 header('Content-Type: application/vnd.ms-excel');
        $objWriter->save('php://output');


    }

    public function download_pdf()  {

        $books = $this->loadModel('book');
        $orders = $this->loadModel('orders');
        $id = $_GET['id'];
        $type = $_GET['type'];


        if (isset($id) && isset($type)) {
            $bookOrder = $orders->get_book_order($id);

            $this->paginas = $books->getPaginas($bookOrder['id_book'], $type);


            $selected_gender = 'male';

            switch($bookOrder['gender_id']) {
                case 1: $selected_gender='male';break;
                case 2: $selected_gender='female';break;
                default: $selected_gender='male';break;
            }


            $nombre = @$bookOrder['name'];


            $this->_view->_paginas = array();

            //load library
            $this->getLibrary('fpdf/fpdf.php');

            //format in A4
            $pdf = new FPDF('P','mm',array(216,216));


            $i=0;
            foreach($this->paginas as $pagina) {
                //add the page
                $pdf->AddPage();

                $orden_pagina  = $pagina['orden_pagina'];




                $imagen = $this->generarBGCombinacion(array(
                    'book_slug'=>$bookOrder['book_slug'],
                    'background_image'=>$pagina['background_image'],
                    'page_number'=>$orden_pagina,
                    'gender'=>$selected_gender,
                    'background_img'=>$pagina['background_image'],
                    'skin_img'=>$bookOrder['sc'],
                    'hair_style'=> $bookOrder['hsf'],
                    'hair_img'=>$bookOrder['hc'],
                    'eyes_img'=>$bookOrder['ec'],
                    'glasses_img'=> $bookOrder['gs'],
                    'clothes_img'=> $bookOrder['cc']
                ));


                $PPP=216;

                //ADD BACKGROUND COMBINATION
                $pdf->Image($imagen['image'],0,0,$PPP);


                /////////////////////////////////////////
                /// ///////////////////////////////////////
                /// //////////////////////////////////////

                //ADD TEXT
                $text =   $books->getTextPagina($bookOrder['book_slug'] ,$orden_pagina);
                $is_dedicatoria =   $books->isDedicatoria($bookOrder['book_slug'],$orden_pagina);


                $tc = explode(',',$text[0]['text_color']);

                $text_x = $text[0]['text_x'];
                $text_y = $text[0]['text_y'];
                $font_size = $text[0]['font_size'];
                $font_pdf = $text[0]['font_pdf'];


                // Establecer la fuente
                $pdf->AddFont($font_pdf,'',$font_pdf.'.php');

                        if(!$is_dedicatoria) {

                            $pdf->SetFont($font_pdf,'',$font_size*1.2);
                            /*PRINT MALE OR FEMALE TEXT*/
                            switch($bookOrder['gender_id']){
                                case 1:  $texto=$text[0]['text_male'];  break;
                                case 2: $texto=$text[0]['text_female'];   break;
                                default: $texto=$text[0]['text_male'];  break;
                            }

                            $texto = str_replace("{{nl}}", PHP_EOL.PHP_EOL, $texto); //saltos de linea
                            $texto = str_replace("{{name}}", @$nombre, $texto); //nombre del niño

                            $pdf->SetTextColor($tc[0]*1.5, $tc[1]*1.6, $tc[2]*1.6);
                            $pdf->SetXY($text_x/2.5,$text_y/2.5);
                            $pdf->Multicell(185, $font_size/2, utf8_decode($texto),0,"L");

                        }
                else {

                    $pdf->SetFont($font_pdf,'',$font_size);
                    // TEXTO DEDICATORIA
                    $dedicatoria = @$bookOrder['dedicatoria_txt'];
                    $dedicatoria_img = @$bookOrder['dedicatoria_img'];

                    //Text rotated around its origin
                    $pdf->Rotate(2.4,0,0);
                    $pdf->SetXY(24,66);
                    $pdf->Multicell(70,$font_size/2, utf8_decode($dedicatoria),0,"L");
                    $pdf->Rotate(0);


                    /*******************************
                     *****************************
                    /*****AÑADIR IMAGEN DEDICATORIA******
                    /*****************************
                    /****************************/

                    if(isset($dedicatoria_img) && $dedicatoria_img!='') {
                        $dedicatoria_img_src= ROOT.DS.'public'.DS.'img'.DS.'dedicatoria_img'.DS.$dedicatoria_img;

                        $pdf->Image($dedicatoria_img_src, 102, 110, 68);
                    }else {
                        //($book_slug, $gender, $sc, $hs, $hc, $ec, $cc, $gs)
                        $avatar = imagescale(
                            $this->getAvatar(
                                $bookOrder['book_slug'], $bookOrder['gender_id'], $bookOrder['sc'], $bookOrder['hsf'], $bookOrder['hc'], $bookOrder['ec'], $bookOrder['cc'], $bookOrder['gs']), 220, 240
                        );

                        $dedicatoria_img_path= ROOT .DS. 'public' . DS . 'img' . DS . 'dedicatoria_img' . DS ;
                        $dedicatoria_img_avatar = $dedicatoria_img_path.uniqid().'.jpg';
                        imagejpeg($avatar, $dedicatoria_img_avatar, 90);

                        $pdf->Image($dedicatoria_img_avatar, 102, 110, 68);
                    }

                }


                /////////////////////////////////////////
                /// ///////////////////////////////////////
                /// //////////////////////////////////////

                //AÑADOR MARCAS DE CORTE
                /*$pdf->Line( 0, 3, 5, 3); // ARRIBA IZQ H
                $pdf->Line( 3,  0,  3, 5); // ARRIBA IZQ V

                $pdf->Line( $PPP-3, 0,  $PPP-3, 5); // ARRIBA DER H
                $pdf->Line( $PPP-5, 3,  $PPP, 3); // ARRIBA DER V


                $pdf->Line( 0, $PPP-3, 5, $PPP-3); // ABAJO IZQ H
                $pdf->Line( 3,  $PPP,  3, $PPP-5); // ABAJO IZQ V

                $pdf->Line( $PPP, $PPP-3,  $PPP-5, $PPP-3); // ABAJO DER H
                $pdf->Line( $PPP-3, $PPP,  $PPP-3, $PPP-5); // ABAJO DER V
*/
                $i++;
                //if($i>2) break;
            }

            $pdf->SetCompression(true);
            $pdf->Output();


        }
    }

    public function generateAvatar() {

        $books = $this->loadModel('book');

        $book_slug = $_GET['book_slug'];
        $gender = $_GET['gender'];

        $this->_view->book = $books->get_data_libro($book_slug);

        $bookid = $this->_view->book['id'];


        $this->skinColors = $books->getSkinColors($bookid, $gender);foreach($this->skinColors as $color) $skinColors[$color['id']] = $color;
        $this->hairStyles = $books->getHairStyles($bookid, $gender);foreach($this->hairStyles as $color) $hairStyles[$color['id']] = $color;
        $this->hairColors = $books->getHairColors($bookid, $gender);foreach($this->hairColors as $color) $hairColors[$color['id']] = $color;
        $this->eyesColor = $books->getEyesColor($bookid, $gender);foreach($this->eyesColor as $color) $eyesColor[$color['id']] = $color;
        $this->clothesColor = $books->getClothesColor($bookid, $gender);foreach($this->clothesColor as $color) $clothesColor[$color['id']] = $color;
        $this->glassesStyle = $books->getGlassesStyle($bookid, $gender);foreach($this->glassesStyle as $color) $glassesStyle[$color['id']] = $color;
        //ORDENAR ARRAYS

        $pag_skinColor = $skinColors[$_GET['skinColor']]['img_src'];
        $pag_hairStyles = $hairStyles[$_GET['hairStyle']]['style_folder'];
        $pag_hairColors = $hairColors[$_GET['hairColor']]['img_src'];
        $pag_eyesColor = $eyesColor[$_GET['eyesColor']]['img_src'];
        $pag_clothesColor = $clothesColor[$_GET['clothesColor']]['img_src'];
        $pag_glassesStyle = $glassesStyle[$_GET['glassesStyle']]['img_src'];

        $gender_folder = 'male';
        switch($gender) {case 2:$gender_folder='female';break;}


        $imagen = $this->generarImgAvatar(array(
            'book_slug'=>$book_slug,
            'gender'=>$gender_folder,
            'skin_img'=>$pag_skinColor,
            'hair_style'=>$pag_hairStyles,
            'hair_img'=>$pag_hairColors,
            'eyes_img'=>$pag_eyesColor,
            'glasses_img'=>$pag_glassesStyle,
            'clothes_img'=>$pag_clothesColor,
        ));

        $gd = $this->LoadJpeg($imagen['image']);

        header('Content-Type: image/jpeg');
        imagejpeg($gd);
        imagedestroy($gd);

    }

    private function LoadJpeg($imgname)
    {
        if(is_file($imgname)) chmod($imgname, 777);

        /* Attempt to open */
        $im = @imagecreatefromjpeg($imgname);

        /* See if it failed */
        if(!$im) {
            /* Create a black image */
            $im  = imagecreatetruecolor(1200, 100);
            $bgc = imagecolorallocate($im, 0, 0, 0);
            $tc  = imagecolorallocate($im, 255,255, 255);

            imagefilledrectangle($im, 0, 0, 150, 30, $bgc);

            imagestring($im, 1, 5, 5, 'Error loading ' . $imgname, $tc);
        }

        return $im;
    }

    public function getAvatar($book_slug, $gender, $sc, $hs, $hc, $ec, $cc, $gs) {
        $gender_folder = 'male';
        switch($gender) {case 2:$gender_folder='female';break;}

        $imagen = $this->generarImgAvatar(array(
            'book_slug'=>$book_slug,
            'gender'=>$gender_folder,
            'skin_img'=>$sc,
            'hair_style'=>$hs,
            'hair_img'=>$hc,
            'eyes_img'=>$ec,
            'glasses_img'=>$gs,
            'clothes_img'=>$cc,
        ));
        return $this->LoadJpeg($imagen['image']);
    }

    public function generarImgAvatar($data) {
        $images_path=ROOT.'tmp_img/'.$data['book_slug'].DS.'avatars';

        //CREAR DIRECTORIOS SI NO EXISTEN
        if(!file_exists($images_path)) mkdir($images_path,0777, true);

        //FONDO PARA LA COMBINACION
        $img_name = $images_path.'/'.md5($data['book_slug'].
                '_ge_'.$data['gender'].
                '_sc_'.$data['skin_img'].
                '_hs_'.$data['hair_style'].
                '_hc_'.$data['hair_img'].
                '_ec_'.$data['eyes_img'].
                '_gs_'.$data['glasses_img'].
                '_cc_'.$data['clothes_img']). '.jpg';

        $ruta_base_pagina =  $this->URL_BOOK . $data['book_slug'] . '/avatar'.DS.$data['gender'];


        if(!file_exists($img_name)) {
            $images = array(
                'skin'=>        $ruta_base_pagina. '/skin/' . $data['skin_img'],
                'hair'=>        $ruta_base_pagina . '/hair/styles/style' . $data['hair_style'] . '/' . $data['hair_img'],
                'clothes'=>     $ruta_base_pagina . '/clothes/' . $data['clothes_img'],
                'eyes'=>        $ruta_base_pagina . '/eyes/' . $data['eyes_img'],
                'glasses'=>     $ruta_base_pagina . '/glasses/' . $data['glasses_img'],
            );

            $layers=array();
            $layers['skin'] = imagecreatefrompng($images['skin']);
            list($width_main, $height_main) = getimagesize($images['skin']);

            $gd = imagecreatetruecolor($width_main, $height_main);
            $white = imagecolorallocate($gd, 244, 244, 244);
            imagefill($gd, 0, 0, $white);

            if (file_exists($images['clothes'])) {  $layers['clothes'] = imagecreatefrompng($images['clothes']); }    //ADD CLOTHES
            if (file_exists($images['hair'])) { $layers['hair'] = imagecreatefrompng($images['hair']);}     //ADD HAIR IMAGE
            if (file_exists($images['eyes']))   {  $layers['eyes'] = imagecreatefrompng($images['eyes']); }   //ADD EYES
            if (file_exists($images['glasses']))  {  $layers['glasses'] = imagecreatefrompng($images['glasses']); } //ADD GLASSES

            foreach ($layers as $layer) {
                imagecopy($gd, $layer,  0, 0, 0, 0, $width_main, $height_main);
            }

            imagejpeg($gd,$img_name,80);
            chmod($img_name, 777);
        }

        return array( 'image'=>$img_name );
    }

    public function d_portada()  {

        ini_set('memory_limit', '1G');

        $books = $this->loadModel('book');
        $this->_view->setJs(array('scripts', 'previsualizador', 'turn.min' ));

        //Get Slug
        $slug = $_GET['bs'];

        $gender = $_GET['g'];


        if ($slug != null) {
            $this->_view->book = $books->get_data_libro($slug);


            $bookid = $this->_view->book['id'];

            $paginas = $books->getPaginas($bookid, 1);

            $this->paginas=array();
            foreach($paginas as $pagina) { $this->paginas[$pagina['orden_pagina']]=$pagina;}

            $selected_skinColor = $_GET['sc'];
            $selected_hairStyle = $_GET['hs'];
            $selected_hairColor = $_GET['hc'];
            $selected_eyesColor = $_GET['ec'];
            $selected_clothesColor = $_GET['cc'];
            $selected_glassesStyle = $_GET['gs'];

            $selected_gender = 'male';

            switch($_GET['g']) {
                case 1: $selected_gender='male';break;
                case 2: $selected_gender='female';break;
                default: $selected_gender='male';break;
            }

            $this->_view->_paginas = array();

            //load library
            $this->getLibrary('fpdf/fpdf.php');


            $PPP=216;

            //format in A4
            $pdf = new FPDF('L','mm',array($PPP*2,$PPP));


            /************************************/
            /************************************/
            /*********** AÑADIR PÁGINAS *********/
            /************************************/
            /************************************/
            //PORTADA Y CONTRAPORTADA - PRIMERA Y ULTIMA PÁGINA
            $paginas_count =array(1);
            array_push($paginas_count , sizeof($this->paginas));
            //dedicatoria (antepenultima y penultima pagina)
            array_push($paginas_count , sizeof($this->paginas)-1);
            array_push($paginas_count , 2 );



            for($i=0; $i < sizeof($paginas_count); $i++) {

                //add the page
                if($i%2==0) $pdf->AddPage();


                $pag_skinColor = $books->getSkinColorsByID($bookid, $gender,  $selected_skinColor);
                $pag_hairStyles = $books->getHairStylesByID($bookid, $gender, $selected_hairStyle);
                $pag_hairColors = $books->getHairColorsByID($bookid, $gender, $selected_hairColor);
                $pag_eyesColor = $books->getEyesColorByID($bookid, $gender, $selected_eyesColor);
                $pag_clothesColor = $books->getClothesColorByID($bookid, $gender, $selected_clothesColor);
                $pag_glassesStyle = $books->getGlassesStyleByID($bookid, $gender, $selected_glassesStyle);
                $book_slug = $slug;
                $background_image = $pagina['background_image'];
                $skin_image = $pag_skinColor['img_src'];
                $hair_style = $pag_hairStyles['style_folder'];
                $hair_image = $pag_hairColors['img_src'];
                $eyes_image = $pag_eyesColor['img_src'];
                $glasses_image = $pag_glassesStyle['img_src'];
                $clothes_image = $pag_clothesColor['img_src'];

                //$_pngX = $this->paginas[$paginas_count[$i]]['png_pos_x'];
               // $_pngY = $this->paginas[$paginas_count[$i]]['png_pos_y'];

                $selected_name = @$_GET['n'];

                $args_image = array(
                    'book_slug'=>$book_slug,
                    'background_image'=>$background_image,
                    'page_number'=>$paginas_count[$i],
                    'gender'=>$selected_gender,
                    'background_img'=>$background_image,
                    'skin_img'=>$skin_image,
                    'hair_style'=>$hair_style,
                    'hair_img'=>$hair_image,
                    'eyes_img'=>$eyes_image,
                    'glasses_img'=>$glasses_image,
                    'clothes_img'=>$clothes_image,
                );


                $imagen = $this->generarBGCombinacion($args_image);




                //ADD BACKGROUND COMBINATION
                $pdf->Image(  $imagen['image'], (($i%2!=0) ?  0 : $PPP)  ,  0,  $PPP );

                if(isset($i) && isset($paginas_count) && isset($this->paginas[$paginas_count[$i]]['dedicatoria']) && $this->paginas[$paginas_count[$i]]['dedicatoria']==1) {
                    /*AÑADIR TEXTO DEDICATORIA*/
                    $pdf->SetFont('Arial');
                    $pdf->SetTextColor(255, 0, 0);
                    $pdf->SetXY(30, 30);
                    $pdf->RotatedText(25,71,'Hola Juan esto es una prueba de texto!',2);
                }

                $separacion_lateral =4.15;
                //AÑADOR MARCAS DE CORTE
                $pdf->Line( 0, 3, 5, 3); // ARRIBA IZQ H
                $pdf->Line( $separacion_lateral,  0,  $separacion_lateral, 5); // ARRIBA IZQ V

                $pdf->Line( ($PPP*2)-$separacion_lateral, 0,  ($PPP*2)-$separacion_lateral, 5); // ARRIBA DER H
                $pdf->Line( ($PPP*2)-5, 3,  ($PPP*2), 3); // ARRIBA DER V



                $pdf->Line( 0, $PPP-3, 5, ($PPP)-3); // ABAJO IZQ H
                $pdf->Line( $separacion_lateral,  $PPP,  $separacion_lateral, ($PPP)-5); // ABAJO IZQ V


                $pdf->Line( ($PPP*2), $PPP-3,  ($PPP*2)-5, $PPP-3); // ABAJO DER H
                $pdf->Line( ($PPP*2)-$separacion_lateral, $PPP,  ($PPP*2)-$separacion_lateral, $PPP-5); // ABAJO DER V

            }
            $pdf->SetCompression(true);
            $pdf->Output();


        }
    }

    public  function chmod_r($dir)
    {
        $dp = opendir($dir);
        while($file = readdir($dp))
        {
            if (($file == ".") || ($file == "..")) continue;

            $path = $dir . "/" . $file;
            $is_dir = is_dir($path);

            $this->set_perms($path, $is_dir);
            if($is_dir) $this->chmod_r($path);
        }
        closedir($dp);
    }

    public function set_perms($file, $is_dir)
    {
        $perm = substr(sprintf("%o", fileperms($file)), -4);
        $dirPermissions = "0777";
        $filePermissions = "0777";

        if($is_dir && $perm != $dirPermissions)  {
            //echo("<br />Dir: " . $file . "\n");
            //chmod($file, octdec($dirPermissions));
        }
        else if(!$is_dir && $perm != $filePermissions)  {
           // echo("<br />File: " . $file . "\n");
            //chmod($file, octdec($filePermissions));
        }

        flush();
    }

    /*
        * expects Array (
        *      background_image
        *      page_number
        *      gender
        *      background_img
        *      skin_img
        *      hair_style
        *      hair_img
        *      eyes_img
        *      glasses_img
        *      clothes_img
        * )
        * */
    public function generarBGCombinacion($data) {
        $images_path=ROOT.'tmp_img/'.$data['book_slug'];
        $thumb_path=ROOT.'tmp_img/'.$data['book_slug'].DS.'thumbs';

        //CREAR DIRECTORIOS SI NO EXISTEN
        if(!file_exists($images_path)) mkdir($images_path,0775, true);
        if(!file_exists($thumb_path)) mkdir($thumb_path,0775, true);

        //FONDO PARA LA COMBINACION
        $img_name = $images_path.'/p'.$data['page_number'].'_'.md5($data['book_slug'].
                '_pag_'.$data['page_number'].
                '_ge_'.$data['gender'].
                '_bg_'.$data['background_img'].
                '_skin_'.$data['skin_img'].
                '_hairstyle_'.$data['hair_style'].
                '_hairimage_'.$data['hair_img'].
                '_eye_'.$data['eyes_img'].
                '_glass_'.$data['glasses_img'].
                '_clo_'.$data['clothes_img']). '.jpg';




        $ruta_base_pagina =  $this->URL_BOOK . $data['book_slug'] . '/pages/pagina_' .  $data['page_number'] ;
        $ruta_base_pagina_avatar =  $ruta_base_pagina. '/avatar/' . $data['gender'];

       //echo $ruta_base_pagina_avatar . '/clothes/' . $data['clothes_img'];

        if( !file_exists($img_name) )  {
            $images = array(
                'bg'=>          $ruta_base_pagina . '/background/' . $data['background_image'],
                'skin'=>        $ruta_base_pagina_avatar. '/skin/' . $data['skin_img'],
                'hair'=>        $ruta_base_pagina_avatar . '/hair/styles/style' . $data['hair_style'] . '/' . $data['hair_img'],
                'eyes'=>        $ruta_base_pagina_avatar . '/eyes/' . $data['eyes_img'],
                'glasses'=>     $ruta_base_pagina_avatar . '/glasses/' . $data['glasses_img'],
                'clothes'=>     $ruta_base_pagina_avatar . '/clothes/' . $data['clothes_img']
            );



            $layers[] = imagecreatefromjpeg($images['bg']);
            list($width_main, $height_main) = getimagesize($images['bg']);

            $gd = imagecreatetruecolor($width_main, $height_main);


            //ADD SKIN

            if (file_exists($images['skin'])) {  $layers[] = imagecreatefrompng($images['skin']); }

            //ADD CLOTHES
            if (file_exists($images['clothes']))  $layers[] = imagecreatefrompng($images['clothes']);

            //ADD CLOTHES
            if (file_exists($images['eyes'])) { $layers[] = imagecreatefrompng($images['eyes']); }

            //ADD HAIR IMAGE
            if (file_exists($images['hair'])) { $layers[] = imagecreatefrompng($images['hair']); }

            //ADD EYES IMAGE
            if (file_exists($images['glasses'])) { $layers[] = imagecreatefrompng($images['glasses']);}





            for ($i = 0; $i < count($layers); $i++)
                imagecopy($gd, $layers[$i],  0, 0, 0, 0, $width_main, $height_main);

            if(!imagejpeg($gd,$img_name,70)) die('bad 1');

            chmod($img_name, 775);
        }

        return array(
            'image'=>$img_name,
         //   'thumb'=>$thumb_name
        );
    }

    //Ajax Method Update
    public function edit_status() {
        $order = $this->loadModel('orders');
        $response =array('error'=>1,'msg'=>'Unknown Error');

        /*get vars*/
        if(!isset($_POST['d']) && empty($_POST['d']))
         $response['errors'][]='Faltan Datos';
        else{
          $d = $_POST['d'];
            $orderData = $order->getData(array('id'=>$d['id']), $this->_view->_lang)['data'][0];
            $response = $order->updateStatus($d['status_id'], $d['id']);


            //COMPROBAR ESTADOS Y SI SON PAGADOS ENVIARLE EL EMAIL
            if($orderData['status'] !=  $d['status_id']) {
               /*AQUI HAY QUE ENVIAR UN EMAIL A CLIENTE*/

                switch($d['status_id']) {
                    case 1:
                        //nuevo pedido;
                        break;
                    case 2:
                        //pendiente de pago
                        break;
                    case 3:

                        //pagado
                            $message_email = $order->emailOrderPaid($orderData);
                            $mailman=new Mailman();
                            $mailman->sendMail($orderData['email'],'Tu pedido se ha procesado satisfactoriamente - #'.$orderData['id'],$message_email);
                           // print_r($orderData['email']);
                           // print_r($message_email);

                        break;
                    case 4:
                        //enviado
                        break;
                    case 5:
                        //error tpv
                        break;

                }
            }


        }
        echo json_encode($response);
    }

    //Ajax Method Update
    public function action_edit() {
        $users = $this->loadModel('orders');
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

            $path = ROOT  .DS. 'public'. DS . 'img' . DS . 'orders' . DS.'thumbs'.DS ;
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
        $users = $this->loadModel('orders');
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

            $path = ROOT  . 'public'. DS . 'img' . DS . 'orders' . DS.'thumbs'.DS ;
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
        $users = $this->loadModel('orders');
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
        $users = $this->loadModel('orders');
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

        $orders = $this->loadModel('orders');
        $response=array('error'=>1,'msg'=>'no data');
        if($this->getInt('id')) {
            if($orders->delete(array('id'=>$this->getInt('id')))){

                $this->redireccionar('admin'.DS.get_class().'/lista?ok=1');
            }
        }
        else {
            $this->_view->_errors[] = 'No hay ID de pedido';
            $this->redireccionar('admin'.DS.'pedidos/lista?ok=2');
        }

    }

}

?>
