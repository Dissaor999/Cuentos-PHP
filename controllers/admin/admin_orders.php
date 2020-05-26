<?php

class admin_orders extends Controller{
    private $_name = ' - Usuarios';

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
        $this->_orders =  $this->loadModel('orders');
        $this->_view->_data['statuses']= $this->_orders->getStatuses();


        $filter = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;

        if(isset($filter) && $filter!=null){
            switch($filter){
                case 'espera': $this->_view->_data['orders']=$this->_orders->getData(array('sql'=>' AND os.id=1'));  break;
                case 'aceptadas': $this->_view->_data['orders']=$this->_orders->getData(array('sql'=>' AND os.id>3'));  break;
                case 'denegadas': $this->_view->_data['orders']=$this->_orders->getData(array('sql'=>' AND os.id>2'));  break;
                default: $this->_view->_data['orders']=$this->_orders->getData();
            }
        }else {
            $this->_view->_data['orders']=$this->_orders->getData();
        }




        $this->_view->titulo =  APP_NAME.$this->_name;
        $this->_view->renderizar('lista', 'admin');
    }


    public function delete(){
        $orders = $this->loadModel('orders');
        //Project
        $id = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;
        $params = array('id'=>$id);

        $this->_view->_data['order']= $orders->getData($params,$this->_view->_lang);

        $this->_view->_data['langs']= Languages::getLangsDB();

        $this->_view->setJs(array('ajax'));


        $this->_view->titulo =  'Borrar Usuario  - '.APP_NAME;
        $this->_view->renderizar('delete', 'admin');
    }

    //View
    public function view(){
        $users = $this->loadModel('orders');


        //Project
        $id = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;
        $params = array('id'=>$id);
        $this->_view->_data['orders']= $users->getData($params,$this->_view->_lang);
        $this->_view->_data['statuses']= $users->getStatuses();



        $this->_view->setJs(array('ajax'), 'admin');
        $this->_view->titulo = 'Ver Pedido - '.APP_NAME;
        $this->_view->renderizar('view', 'admin');
    }


    //View
    public function export()
    {
        $orders = $this->loadModel('orders');


        //Project
        $id = (!empty($this->_view->_args[0]))? $this->_view->_args[0] : null;
        $params = array('id'=>$id);


        $order= $orders->getData($params,$this->_view->_lang);


        $statuses= $orders->getStatuses();


        //EMPIEZA LA EXPORTACIÓN

        /** Error reporting */
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        date_default_timezone_set('Europe/London');
        define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

        /** Include PHPExcel */
        $this->getLibrary('PHPExcel.php');

        // Create new PHPExcel object


// Create new PHPExcel object
        $objPHPExcel = new PHPExcel();

        /* SET SIZES */

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(6);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(6);


// Set document properties
        $objPHPExcel->getProperties()->setCreator("Luesma & Vega")
            ->setLastModifiedBy("Luesma & Vega")
            ->setTitle("PRESUPUESTO WEB PROFESIONALES " . $id)
            ->setSubject("PRESUPUESTO WEB PROFESIONALES " . $id)
            ->setDescription("PRESUPUESTO WEB PROFESIONALES " . $id)
            ->setKeywords("PRESUPUESTO WEB PROFESIONALES " . $id)
            ->setCategory("PRESUPUESTO WEB PROFESIONALES " . $id);


        /*
        *
        * DATOS DE CABECERA
        *
        * */

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A2', 'Luesma & Vega')
            ->setCellValue('A3', 'Francesc Samaranc h, 11 Nau 3')
            ->setCellValue('A4', '08750 Molins de Rei, Barcelona')
            ->setCellValue('A5', 'B65900615!');



        /*
       *
       * CABECERA DERECHA
       *
       * */
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('G2', 'Teléfono: 932 22 71 93')
            ->setCellValue('G3', 'www.luesmavega.eu')
            ->setCellValue('G4', 'luesmavega@gmail.com');

        // DATOS DE CLIENTE



        /*
        *
        * DATOS DE FACTURA
        *
        * */
        $objPHPExcel->setActiveSheetIndex(0)
            ->mergeCells('A8:B8')
            ->setCellValue('A8', 'SOLICITUD')
            ->setCellValue('A9', 'Número:')
            ->setCellValue('A10', 'Fecha:');


        /*
          *
          * ESTILOS DATOS DE FACTURA
          *
          * */
        $objPHPExcel->getActiveSheet()->getStyle('A8:B8')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'EEEEEE'
            )
        ));

        $objPHPExcel->getActiveSheet()->getStyle('A8:B11')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'EEEEEE'
            )
        ));

        $objPHPExcel->getActiveSheet()->getStyle('E8:C10')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'F5F5F5'
            )
        ));


        /*
              *
              * ESTILOS DATOS DE CLIENTE
              *
              * */
        $objPHPExcel->getActiveSheet()->getStyle('E8:E11')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'EEEEEE'
            )
        ));



        /*
         *
         * DATOS DEL CLIENTE
         *
         * */

        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('E8', 'Cliente:')
            ->setCellValue('E9', 'Dirección:')
            ->setCellValue('E11', 'NIF:');


        // print_r($order);

//die();
        $user = $order['data'][0]['user'][0];

        $objPHPExcel->setActiveSheetIndex(0)

            ->mergeCells('F8:G8')
            ->setCellValue('F8', $user['first_name'].' '.$user['last_name'])

            ->mergeCells('F9:G9')
            ->setCellValue('F9', $user['address'])

            ->mergeCells('F11:G11')
            ->setCellValue('F11', $user['phone']);



        $objPHPExcel->setActiveSheetIndex(0)
            ->mergeCells('A13:B13')
            ->setCellValue('A13', 'Código')

            ->mergeCells('C13:G13')
            ->setCellValue('C13','Descripción')
            ->setCellValue('H13', 'Cant.')
            ->setCellValue('I13', 'Precio')
            ->setCellValue('J13', 'Importe');

        /*
                    *
                    * ESTILOS CABECERA PRODUCTOS
                    *
                    * */
        $objPHPExcel->getActiveSheet()->getStyle('A13:K13')->getFill()->applyFromArray(array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'startcolor' => array(
                'rgb' => 'EEEEEE'
            )
        ));
        // echo '<pre>';
        // print_R($order['data'][0]['datas']);

        //echo '</pre>';
        // die();

        $ptotal=0;

        $row=14;
        if (isset($order['data'][0]['datas']) && sizeof($order['data'][0]['datas']) > 0){
            foreach ($order['data'][0]['datas'] as $k => $prod) {
                $objPHPExcel->setActiveSheetIndex(0)
                    ->mergeCells('A'.$row.':B'.$row)
                    ->setCellValue('A'.$row, $prod['sku']) //codigo

                    ->mergeCells('C'.$row.':G'.$row)
                    ->setCellValue('C'.$row,$prod['name'].' - '.$prod['combination']['color_t'].' - '.$prod['combination']['alto'].'x'.$prod['combination']['ancho']) //nombre prod
                    ->setCellValue('H'.$row, $prod['quantity']) //cant
                    ->setCellValue('I'.$row, $prod['price']) //precio
                    ->setCellValue('J'.$row, $prod['price']*$prod['quantity'].'€'); //total

                /*
                *
                * ESTILOS CABECERA PRODUCTOS
                *
                * */
                $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':K'.$row)->getFill()->applyFromArray(array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array(
                        'rgb' => 'FAFAFA'
                    )
                ));
                $ptotal+=$prod['price']*$prod['quantity'];
                $row++;
            }
        }
        $row++;


        $row++;



        $english_format_number = number_format($ptotal, 2, '.', '');
        $english_format_number_base = number_format(($ptotal*0.79), 2, '.', '');

        //  echo 'Total'.$ptotal.PHP_EOL;
        // echo 'Total f'.$english_format_number.PHP_EOL;
        // echo 'Total fb'.$english_format_number_base.PHP_EOL;


        $objPHPExcel->setActiveSheetIndex(0)
            ->mergeCells('A'.$row.':C'.$row)
            ->setCellValue('A'.$row, 'Base Imponible:')
            ->setCellValue('D'.$row, $english_format_number_base)

            ->setCellValue('F'.$row, 'IVA:')
            ->setCellValue('G'.$row, '21%')


            ->setCellValue('J'.$row, 'Total:')
            ->setCellValue('K'.$row, $english_format_number.'€');
        $row++;
        $row++;
        $row++;









        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('PRESUPUESTO '.$id);


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="PROFESIONALES_'.$id.'.xls"');
        header('Cache-Control: max-age=0');

        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

      // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');


    }



    //Ajax Method Update
    public function edit_status() {
        $order = $this->loadModel('orders');
        $response =array('error'=>1,'msg'=>'Unknown Error');
        $this->_view->setJs(array('ajax'));


        /*get vars*/
        if(isset($_POST['d']))
            $d = $_POST['d'];
        else $response['errors'][]='Faltan Datos';


        $response = $order->updateStatus($d['status_id'], $d['id']);


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
                $this->redireccionar(get_class().'/lista');
            }
        }
        else {
            $this->_view->_errors[] = 'No hay ID de noticia';
            $this->redireccionar('users/lista');
        }
        $this->redireccionar(get_class().'/lista');
    }









    /*GALLERY COMB*/

//Action
    public function getPhotosCombination(){
        $galeria = $this->loadModel('orders');
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
        $galeria = $this->loadModel('orders');
        if(isset($_POST['id'])){
            $id = $_POST['id'];
            $params = array('id'=>$id);
            $images = $galeria->deletePhotoCombination($params);

            echo json_encode(array('success'=>1, 'data'=>'Borrada'));
        }
        else echo json_encode(array('success'=>0, 'message'=>'error no data'));
    }

    public function add_images_comb(){
        $galeria = $this->loadModel('orders');

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