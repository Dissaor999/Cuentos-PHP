<?php

class libros extends Controller
{


    protected $URL_BOOK=ROOT.'public/img/books/';
    protected $THUMB_PREFIX='th_';


    public function __construct()
    {
        parent::__construct();

    }

    public function index()
    {
        $books = $this->loadModel('book');


        $this->_view->books_list = $books->get_list();

        $this->_view->titulo = APP_NAME . ' - Personaliza tu cuento';
        $this->_view->renderizar('index');

    }

    public function ver()
    {
        $books = $this->loadModel('book');


        $this->_view->books_list = $books->get_list();


        //Get Slug
        $slug = (!empty($this->_view->_args[0])) ? $this->_view->_args[0] : null;

        if ($slug != null) {
            $this->_view->book = $books->get_data_libro($slug);
            $this->_view->titulo = $this->_view->book['name'].TS.APP_NAME ;
            $this->_view->renderizar('1_ver');

        } else $this->index();
    }

    public function personalizar()
    {
        $books = $this->loadModel('book');


        $this->_view->setJs(array('scripts'));


        //Get Slug
        $slug = (!empty($this->_view->_args[0])) ? $this->_view->_args[0] : null;

        if ($slug != null) {
            $this->_view->book = $books->get_data_libro($slug);

            $this->_view->titulo = $this->_view->book['name'].TS.APP_NAME ;
            $this->_view->renderizar('2_personalizar');

        } else $this->index();
    }

    public function personalizar_personaje()
    {
        $books = $this->loadModel('book');
        $this->_view->setJs(array('scripts'));

        //Get Slug
        $slug = (!empty($this->_view->_args[0])) ? $this->_view->_args[0] : null;

        $gender = $_GET['gender'];


        if ($slug != null) {
            $this->_view->book = $books->get_data_libro($slug);


            $bookid = $this->_view->book['id'];


            $this->_view->skinColors = $books->getSkinColors($bookid, $gender);
            //echo '<pre>'; print_r($this->_view->skinColors); echo '</pre>';

            $this->_view->hairStyles = $books->getHairStyles($bookid, $gender);
            $this->_view->hairColors = $books->getHairColors($bookid, $gender);

            $this->_view->eyesColor = $books->getEyesColor($bookid, $gender);
            $this->_view->glassesStyle = $books->getGlassesStyle($bookid, $gender);

            $this->_view->clothesColor = $books->getClothesColor($bookid, $gender);


            $this->_view->titulo = $this->_view->book['name'].TS.APP_NAME ;
            $this->_view->renderizar('3_personalizar_personaje');

        } else $this->index();



    }

    public function dedicatoria()
    {
        $books = $this->loadModel('book');


        $this->_view->setJs(array('scripts', 'fileinput.min', 'exif', 'croppie', 'croppieUpload'));
        $this->_view->setCss(array( 'croppie'));


        //Get Slug
        $slug = (!empty($this->_view->_args[0])) ? $this->_view->_args[0] : null;

        if ($slug != null) {
            $this->_view->book = $books->get_data_libro($slug);

            $this->_view->titulo = $this->_view->book['name'].TS.APP_NAME ;
            $this->_view->renderizar('4_dedicatoria');

        } else $this->index();
    }

    public function previsualizar()
    {
        $books = $this->loadModel('book');
        $this->_view->setJs(array('scripts', 'previsualizador', 'turn.min', 'jquery.lazy.min', 'jquery.lazy.plugins.min' ));

        //Get Slug
        $slug = (!empty($this->_view->_args[0])) ? $this->_view->_args[0] : null;

        $gender = $_POST['gender'];


        if ($slug != null) {
            $this->_view->book = $books->get_data_libro($slug);


            $bookid = $this->_view->book['id'];


            if(
                !isset($_POST['skinColor']) &&
                !isset($_POST['hairStyle']) &&
                !isset($_POST['hairColor']) &&
                !isset($_POST['eyesColor']) &&
                !isset($_POST['clothesColor']) &&
                !isset($_POST['glassesStyle'])

            ) {
                $this->redireccionar(get_class().DS.'ver/'.$slug);
            }

            $_skinColors = $books->getSkinColorsByID($bookid, $gender, $_POST['skinColor']);
            $_hairStyles = $books->getHairStylesByID($bookid, $gender, $_POST['hairStyle']);
            $_hairColors = $books->getHairColorsByID($bookid, $gender, $_POST['hairColor']);
            $_eyesColor = $books->getEyesColorByID($bookid, $gender, $_POST['eyesColor']);
            $_clothesColor = $books->getClothesColorByID($bookid, $gender, $_POST['clothesColor']);
            $_glassesStyle = $books->getGlassesStyleByID($bookid, $gender, $_POST['glassesStyle']);


            $paginas = $books->getPaginas($bookid, 'ebook');





            $this->_view->selected_gender = 'male';
            $this->_view->gender = $gender;

            switch($gender) {
                case 1: $this->_view->selected_gender='male';break;
                case 2: $this->_view->selected_gender='female';break;
                default: $this->_view->selected_gender='male';break;
            }

            $this->_view->selected_name = @$_POST['name'];



            $this->_view->_paginas = array();


            //SUBIDA DE LA IMAGEN
            $dir_subida = 'public'.DS.'img'.DS.'dedicatoria_img'.DS;
            if(!file_exists($dir_subida)) mkdir($dir_subida,0775,true);
            // split the string on commas
            // $data[ 0 ] == "data:image/jpg;base64"
            // $data[ 1 ] == <actual base64 string>
            if(isset($_POST['imgDedicatoria']) && !empty($_POST['imgDedicatoria'])){
                $data = explode( ',', $_POST['imgDedicatoria'] );
                $filename = uniqid().'.jpg';
                file_put_contents($dir_subida.$filename, base64_decode($data[1]));
            }else{
                $filename="";
            }

            foreach($paginas as $k=>$pagina) {






                $this->_view->_paginas[] = array(
                        'book'=>$this->_view->book,
                        'res'=>$pagina,
                        'png_X'=>$pagina['png_pos_x'],
                        'png_Y'=>$pagina['png_pos_y'],
                        'text'=>$pagina['text_male'],
                        'name'=> $_POST['name'],
                        'gender'=>$this->_view->selected_gender,
                        'pag_skinColor'=>$_skinColors,
                        'pag_hairStyles'=>$_hairStyles,
                        'pag_hairColors'=>$_hairColors,
                        'pag_eyesColor'=>$_eyesColor,
                        'pag_clothesColor'=>$_clothesColor,
                        'pag_glassesStyle'=>$_glassesStyle,
                        'dedicatoria'=>$_POST['dedicatoria'],
                        'dedicatoria_img'=>$filename
                );


            }
            $this->_view->titulo = $this->_view->book['name'].TS.APP_NAME ;
            $this->_view->renderizar('5_previsualizar_libro');

        } else $this->redireccionar(get_class());



    }

     public function formato()
    {
        $books = $this->loadModel('book');
        $this->_view->setJs(array('formato_selector' ));


       // print_r($_POST);

        //Get Slug
        $slug = (!empty($this->_view->_args[0])) ? $this->_view->_args[0] : null;

        $gender = $_POST['gender'];


        if ($slug != null) {
            $this->_view->book = $books->get_data_libro($slug);
            $bookid = $this->_view->book['id'];

            $this->_view->renderizar('6_formato');

        } else $this->index();



    }

    public function convertmmtopx($mm){
        $dpi=300;
        return ($mm * $dpi) / 25.4;
    }




    /*GENERADORES DE IMÁGENES*/
    public function generarImagenPagina() {


        $books              = $this->loadModel('book');
        $book_slug          = $_GET['bs'];
        $this->_view->book  = $books->get_data_libro($book_slug);
        $bookid             = $this->_view->book['id'];
        $orden_pagina       = $_GET['o'];
        $__bg               = $_GET['bg'];
        $gender             = $_GET['g'];

        switch($gender) {
            case 1: $selected_gender='male';    break;
            case 2: $selected_gender='female';  break;
            default: $selected_gender='male';   break;
        }

        $i_sc = $books->getSkinColorsByID($bookid, $gender,$_GET['sc']);
        $i_hs = $books->getHairStylesByID($bookid, $gender,$_GET['hs']);
        $i_hc = $books->getHairColorsByID($bookid, $gender,$_GET['hc']);
        $i_ec = $books->getEyesColorByID($bookid, $gender,$_GET['ec']);
        $i_cc = $books->getClothesColorByID($bookid, $gender,$_GET['cc']);
        $i_gs = $books->getGlassesStyleByID($bookid, $gender,$_GET['gs']);


        $nombre             = @$_GET['n'];
        $dedicatoria        = @$_GET['d'];
        $dedicatoria_img    = @$_GET['di'];


        $args_image = array(
            'book_slug'             =>$book_slug,
            'background_image'      =>$__bg,
            'page_number'           =>$orden_pagina,
            'gender'                =>$selected_gender,
            'background_img'        =>$__bg,
            'skin_img'              =>$i_sc['img_src'],
            'hair_style'            =>$i_hs['style_folder'],
            'hair_img'              =>$i_hc['img_src'],
            'eyes_img'              =>$i_ec['img_src'],
            'glasses_img'           =>$i_gs['img_src'],
            'clothes_img'           =>$i_cc['img_src'],
        );

        $imagen = $this->generarBGCombinacion($args_image);



        $gd = $this->LoadJpeg($imagen['thumb']);

        //AQUI AÑADIMOS EL TEXTO PARA LA PÁGINA
        $books              =   $this->loadModel('book');
        $text               =   $books->getTextPagina($book_slug,$orden_pagina);
        $is_dedicatoria     =   $books->isDedicatoria($book_slug,$orden_pagina);


        $_textColor     = explode(',',$text[0]['text_color']);
        $text_angle     = $text[0]['text_angle'];
        $text_x         = $text[0]['text_x'];
        $text_y         = $text[0]['text_y'];
        $font_size      = $text[0]['font_size']*0.8;
        $font_family    = $text[0]['font_family'];


        // Create some colors
       // print_r($text);
        $text_color     = imagecolorallocate($gd, $_textColor[0], $_textColor[1], $_textColor[2]);
        $fuente         = FONT_PATH.$font_family;

        if(!$is_dedicatoria) {
            switch($gender){
                case 1:     $texto=$text[0]['text_male'];       break;
                case 2:     $texto=$text[0]['text_female'];     break;
                default:    $texto=$text[0]['text_male'];       break;
            }
            $texto  = str_replace("{{nl}}", PHP_EOL, $texto); //saltos de linea
            $texto  = str_replace("{{name}}", @$nombre, $texto); //nombre del niño
            imagettftext($gd, $font_size, floatval($text_angle), $text_x,$text_y, $text_color, $fuente,$texto );
        }
        else {
            //ADD DEDICATORIA

            $dedicatoria    = str_replace("{{nl}}{nl}}", "{{nl}}", $dedicatoria);
            $dedicatoria    = str_replace("{{nl}}", PHP_EOL, $dedicatoria);

            //saltos de linea
            $cropped        = wordwrap($dedicatoria,32,"\n",false);

            if($dedicatoria!='')
                imagettftext($gd, $font_size*0.8, 3, 60,165, $text_color, $fuente,$cropped);

            // AÑADIR IMAGEN DEDICATORIA
            if(isset($dedicatoria_img) && $dedicatoria_img!='') {
                $image = imagescale($this->LoadJpeg(ROOT.'public'.DS.'img'.DS.'dedicatoria_img'.DS.$dedicatoria_img), 130, 180);
            }else {
                $image = imagescale($this->getAvatar($book_slug, $gender, $i_sc['img_src'], $i_hs['style_folder'], $i_hc['img_src'], $i_ec['img_src'], $i_cc['img_src'], $i_gs['img_src']), 130, 180);
            }

            imagecopyresized ( $gd , $image , 240 , 252 ,0 , 0 , 172, 180, 123, 180);
            imagedestroy($image);
        }

        header('Content-Type: image/jpeg');
        ob_clean();
        ob_flush();
        imagejpeg($gd);
        imagedestroy($gd);

    }

    public function resizeImage($filename, $max_width, $max_height)
    {
        list($orig_width, $orig_height) = getimagesize($filename);

        $width = $orig_width;
        $height = $orig_height;

        # taller
        if ($height > $max_height) {
            $width = ($max_height / $height) * $width;
            $height = $max_height;
        }

        # wider
        if ($width > $max_width) {
            $height = ($max_width / $width) * $height;
            $width = $max_width;
        }

        $image_p = imagecreatetruecolor($width, $height);

        $image = imagecreatefromjpeg($filename);

        imagecopyresampled($image_p, $image, 0, 0, 0, 0,
            $width, $height, $orig_width, $orig_height);

        return $image_p;
    }

    public function resize_crop_image($max_width, $max_height, $source_file, $dst_dir, $quality = 80){
        $imgsize = getimagesize($source_file);
        $width = $imgsize[0];
        $height = $imgsize[1];
        $mime = $imgsize['mime'];

        switch($mime){
            case 'image/gif':
                $image_create = "imagecreatefromgif";
                $image = "imagegif";
                break;

            case 'image/png':
                $image_create = "imagecreatefrompng";
                $image = "imagepng";
                $quality = 7;
                break;

            case 'image/jpeg':
                $image_create = "imagecreatefromjpeg";
                $image = "imagejpeg";
                $quality = 80;
                break;

            default:
                return false;
                break;
        }

        $dst_img = imagecreatetruecolor($max_width, $max_height);
        $src_img = $image_create($source_file);

        $width_new = $height * $max_width / $max_height;
        $height_new = $width * $max_height / $max_width;
        //if the new width is greater than the actual width of the image, then the height is too large and the rest cut off, or vice versa
        if($width_new > $width){
            //cut point by height
            $h_point = (($height - $height_new) / 2);
            //copy image
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
        }else{
            //cut point by width
            $w_point = (($width - $width_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        }

        $image($dst_img, $dst_dir, $quality);
       // if($dst_img)imagedestroy($dst_img);
        if($src_img)imagedestroy($src_img);

        return  $dst_img;

       // return $return;
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
        ob_clean();
        ob_flush(); imagejpeg($gd);
        imagedestroy($gd);

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

    private function LoadJpeg($imgname) {
        ini_set("display_errors", 1);
        error_reporting(E_ALL);
        if(is_file($imgname)) chmod($imgname, 0777);

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


    public function descargar_ebook()  {

        $books = $this->loadModel('book');
        $orders = $this->loadModel('orders');

        $type="ebook";

        $id = (!empty($this->_view->_args[0])) ? $this->_view->_args[0] : null;


        if (isset($id) && isset($type)) {
            $bookOrder = $orders->get_book_order($id,true);

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




                $imagen = $this->generarBGCombinacion_pdf(array(
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
                    $pdf->Multicell(175, $font_size/1.6, utf8_decode($texto),0,"L");

                }
                else {

                    $pdf->SetFont($font_pdf, '', $font_size);
                    // TEXTO DEDICATORIA
                    $dedicatoria = @$bookOrder['dedicatoria_txt'];
                    $dedicatoria_img = @$bookOrder['dedicatoria_img'];

                    //Text rotated around its origin
                    $pdf->Rotate(2.4, 0, 0);
                    $pdf->SetXY(24, 66);
                    $pdf->Multicell(70, $font_size / 1.7, utf8_decode($dedicatoria), 0, "L");
                    $pdf->Rotate(0);


                    /*******************************
                     *****************************
                     * /*****AÑADIR IMAGEN DEDICATORIA******
                     * /*****************************
                     * /****************************/

                    if (isset($dedicatoria_img) && $dedicatoria_img!='') {
                        $dedicatoria_img_src = ROOT . 'public' . DS . 'img' . DS . 'dedicatoria_img' . DS . $dedicatoria_img;
                        // $image = imagescale($this->getAvatar($book_slug, $gender, $i_sc['img_src'], $i_hs['style_folder'], $i_hc['img_src'], $i_ec['img_src'], $i_cc['img_src'], $i_gs['img_src']), 130, 180);
                        $pdf->Image($dedicatoria_img_src, 102, 110, 68);
                    }else {
                        //($book_slug, $gender, $sc, $hs, $hc, $ec, $cc, $gs)
                        $avatar = imagescale(
                            $this->getAvatar(
                                $bookOrder['book_slug'], $selected_gender, $bookOrder['sc'], $bookOrder['hsf'], $bookOrder['hc'], $bookOrder['ec'], $bookOrder['cc'], $bookOrder['gs']), 220, 240
                        );

                        $dedicatoria_img_path= ROOT . 'public' . DS . 'img' . DS . 'dedicatoria_img' . DS ;
                        $dedicatoria_img_avatar = $dedicatoria_img_path.uniqid().'.jpg';
                        imagejpeg($avatar, $dedicatoria_img_avatar, 90);

                        $pdf->Image($dedicatoria_img_avatar, 102, 110, 68);
                    }
                }


                $i++;
                //if($i>2) break;
            }

            $pdf->SetCompression(true);
            $pdf->Output();


        }
    }

    public function print_download()  {
        $PPP=216;


        $books = $this->loadModel('book');
        $orders = $this->loadModel('orders');

        $type       = $_GET['type'];
        $id         = $_GET['id'];


        if (isset($id) && isset($type)) {
            $bookOrder = $orders->get_book_order($id,true);

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
            switch($type) {
                case 'portada':
                    $pdf = new FPDF('L','mm',array($PPP*2,$PPP));
                    break;

                default:
                    $pdf = new FPDF('L','mm',array(216,216));
                    break;
            }



                    $i=0;
                    foreach($this->paginas as $pagina) {
                        $orden_pagina  = $pagina['orden_pagina'];

                        $imagen = $this->generarBGCombinacion_pdf(array(
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


                        switch($type) {
                            case 'portada':
                                //add the page
                                if($i%2==0) $pdf->AddPage();
                                //ADD BACKGROUND COMBINATION
                                $pdf->Image(  $imagen['image'], (($i%2!=0) ?  0 : $PPP)  ,  0,  $PPP );
                                break;

                            default:
                                $pdf->AddPage('L',array(216,216));
                                $pdf->Image($imagen['image'],0,0,$PPP);
                                break;
                        }


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

                            $pdf->SetFont($font_pdf, '', $font_size);
                            // TEXTO DEDICATORIA
                            $dedicatoria = @$bookOrder['dedicatoria_txt'];
                            $dedicatoria_img = @$bookOrder['dedicatoria_img'];

                            //Text rotated around its origin
                            $pdf->Rotate(2.4, 0, 0);
                            $pdf->SetXY(24, 66);
                            $pdf->Multicell(70, $font_size / 2, utf8_decode($dedicatoria), 0, "L");
                            $pdf->Rotate(0);


                            /*******************************
                             *****************************
                             * /*****AÑADIR IMAGEN DEDICATORIA******
                             * /*****************************
                             * /****************************/

                            if (isset($dedicatoria_img) && $dedicatoria_img!='') {
                                $dedicatoria_img_src = ROOT . 'public' . DS . 'img' . DS . 'dedicatoria_img' . DS . $dedicatoria_img;
                                // $image = imagescale($this->getAvatar($book_slug, $gender, $i_sc['img_src'], $i_hs['style_folder'], $i_hc['img_src'], $i_ec['img_src'], $i_cc['img_src'], $i_gs['img_src']), 130, 180);
                                $pdf->Image($dedicatoria_img_src, 102, 110, 68);
                            }else {
                                //($book_slug, $gender, $sc, $hs, $hc, $ec, $cc, $gs)
                                $avatar = imagescale(
                                    $this->getAvatar(
                                        $bookOrder['book_slug'], $bookOrder['gender_id'], $bookOrder['sc'], $bookOrder['hsf'], $bookOrder['hc'], $bookOrder['ec'], $bookOrder['cc'], $bookOrder['gs']), 220, 240
                                );

                                $dedicatoria_img_path= ROOT . 'public' . DS . 'img' . DS . 'dedicatoria_img' . DS ;
                                $dedicatoria_img_avatar = $dedicatoria_img_path.uniqid().'.jpg';
                                imagejpeg($avatar, $dedicatoria_img_avatar, 90);

                                $pdf->Image($dedicatoria_img_avatar, 102, 110, 68);
                            }
                        }
                        switch($type) {
                            case 'tripas':
                                //add the page
                                if ($i==0) {
                                    $pdf->SetLineWidth(7);
                                    $pdf->SetFillColor(255,255,255);
                                    $pdf->SetDrawColor(255,255,255);
                                    $pdf->Line( 3.5,  0,  3.5,$PPP);
                                } // ARRIBA IZQ H
                                 if (($i+1)==sizeof($this->paginas)) {
                                    $pdf->SetLineWidth(7);
                                    $pdf->SetFillColor(255,255,255);
                                    $pdf->SetDrawColor(255,255,255);
                                    $pdf->Line( $PPP-3.5,  0,  $PPP-3.5,$PPP);
                                } // ARRIBA IZQ H

                                break;
                        }

                        $i++;
                    }


            switch($type) {
                case 'portada':
                    //add the page
                    if ($i % 2 == 0 && $i>0) {
                        $pdf->SetLineWidth(13.48);
                        $pdf->SetFillColor(255,255,255);
                        $pdf->SetDrawColor(255,255,255);
                        $pdf->Line($PPP, 0,  $PPP,$PPP);
                    } // ARRIBA IZQ H

                    break;
            }
            $pdf->SetCompression(true);
            $pdf->Output();


        }
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
    public function generarBGCombinacion_pdf($data) {
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

        /* $thumb_name = $thumb_path.'/th_p'.$data['page_number'].'_'.md5($data['book_slug'].
                 '_pag_'.$data['page_number'].
                 '_ge_'.$data['gender'].
                 '_bg_'.$data['background_img'].
                 '_skin_'.$data['skin_img'].
                 '_hairstyle_'.$data['hair_style'].
                 '_hairimage_'.$data['hair_img'].
                 '_eye_'.$data['eyes_img'].
                 '_glass_'.$data['glasses_img'].
                 '_clo_'.$data['clothes_img']). '.jpg';
 */

        //touch($img_name);
        // touch($thumb_name);

         chmod($img_name, 0775);
          //chmod($thumb_name, 0775);


        $ruta_base_pagina =  $this->URL_BOOK . $data['book_slug'] . '/pages/pagina_' .  $data['page_number'] ;
        $ruta_base_pagina_avatar =  $ruta_base_pagina. '/avatar/' . $data['gender'];



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

            chmod($img_name, 0775);
        }

        return array(
            'image'=>$img_name,
            //   'thumb'=>$thumb_name
        );
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
        $thumb_name = $thumb_path.'/th_p'.$data['page_number'].'_'.md5($data['book_slug'].
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

        if( !file_exists($thumb_name) )  {
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

            $thumb_width=$width_main/5;
            $thumb_height=$height_main/5;

            $thumb = imagecreatetruecolor($thumb_width, $thumb_height);
            imagecopyresized($thumb, $gd, 0, 0, 0, 0,  $thumb_width, $thumb_height, $width_main, $height_main);

            imagejpeg($thumb, $thumb_name,70);
            imagedestroy($thumb);


            chmod($thumb_name, 0775);
        }

        return array(
            //'image'=>$img_name,
              'thumb'=>$thumb_name
        );
    }




    /*
     *
     expects Array (
        'book_slug'=>$book_slug,
        'gender'=>$gender_folder,
        'skin_img'=>$pag_skinColor,
        'hair_style'=>$pag_hairStyles,
        'hair_img'=>$pag_hairColors,
        'eyes_img'=>$pag_eyesColor,
        'glasses_img'=>$pag_glassesStyle,
        'clothes_img'=>$pag_clothesColor,
     )
     * */
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


       if(! file_exists($img_name)) {
           $images = array(
               'skin' => $ruta_base_pagina . '/skin/' . $data['skin_img'],
               'hair' => $ruta_base_pagina . '/hair/styles/style' . $data['hair_style'] . '/' . $data['hair_img'],
               'clothes' => $ruta_base_pagina . '/clothes/' . $data['clothes_img'],
               'eyes' => $ruta_base_pagina . '/eyes/' . $data['eyes_img'],
               'glasses' => $ruta_base_pagina . '/glasses/' . $data['glasses_img'],
           );

           $layers = array();
           $layers['skin'] = imagecreatefrompng($images['skin']);
           list($width_main, $height_main) = getimagesize($images['skin']);

           $gd = imagecreatetruecolor($width_main, $height_main);
           $white = imagecolorallocate($gd, 244, 244, 244);
           imagefill($gd, 0, 0, $white);


           if (file_exists($images['clothes'])) {
               $layers['clothes'] = imagecreatefrompng($images['clothes']);
           }    //ADD CLOTHES

           if (file_exists($images['hair'])) {
               $layers['hair'] = imagecreatefrompng($images['hair']);
           }     //ADD HAIR IMAGE


           if (file_exists($images['eyes'])) {
               $layers['eyes'] = imagecreatefrompng($images['eyes']);
           }   //ADD EYES

           if (file_exists($images['glasses'])) {
               $layers['glasses'] = imagecreatefrompng($images['glasses']);
           } //ADD GLASSES

           foreach ($layers as $layer) {

               imagecopy($gd, $layer, 0, 0, 0, 0, $width_main, $height_main);
           }

           imagejpeg($gd,$img_name,80);
       }



        chmod($img_name, 0777);

        return array( 'image'=>$img_name );
    }

}

?>