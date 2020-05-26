<?php

class index extends Controller {
	public function __construct(){
		parent::__construct();

	}

	protected $URL_BOOK='http://cuentos.dev/public/books/';

    public function generatePDF () {


       $body = @$_GET['body'];
       $head = @$_GET['head'];
       $object = @$_GET['object'];

        $book_name='book_1'; //book name
        $page=1; //ini page

        //load library
        $this->getLibrary('fpdf/fpdf.php');


        //format in A4
        $pdf = new FPDF('P','mm','A4');

        //add the first page
        $pdf->AddPage();


        //Background
        $pdf->Image($this->URL_BOOK.$book_name.'/pages/'.$page.'/background/background.jpg',0,0,-300);


        if(isset($body))
           $pdf->Image($this->URL_BOOK.$book_name.'/pages/'.$page.'/char/pj-'.$body.'.png',74,198,-300);

        if(isset($head))
            $pdf->Image($this->URL_BOOK.$book_name.'/pages/'.$page.'/heads/head-'.$head.'.png',97.4,198.5,-300);



        if(isset($object))
            $pdf->Image($this->URL_BOOK.$book_name.'/pages/'.$page.'/objects/obj-'.$object.'.png',101,201,-300);



        $pdf->Output();


    }

	public function index(){
        global $me;

            $books = $this->loadModel('book');

            $this->_view->books_list=$books->get_list();


          $this->_view->setJs(array('scripts'));


            $this->_view->titulo = 'Cuentos personalizados para niños '.TS.APP_NAME;
            $this->_view->description = 'Cuentos y libros personalizados para niños de 0 a 9 años: el regalo perfecto para cualquier ocasión. ¡Envíos gratis!';



            $this->_view->renderizar('index','default');

	}
	
}

?>