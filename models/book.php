<?php

class Book extends Model {
	

	public function __construct(){
		parent::__construct();
	}

	public function get_list() {

        $response=array();
        if($result = $this->_db->query('SELECT * FROM  libros order by orden;'))
            while($row = $result->fetch_array(MYSQLI_ASSOC)){

                if($formatos = $this->_db->query('SELECT * FROM  libros_formatos;'))
                    while($row_formatos = $formatos->fetch_array(MYSQLI_ASSOC)) {
                        $row['formatos'][$row_formatos['id']]=$row_formatos;
                    }

                array_push($response,$row);
            }

            return @$response;
    }

    public function get_data_libro($slug) {
        $response=array();
        if($result = $this->_db->query('SELECT * FROM  libros where slug="'.$slug.'" order by orden;'))
            while($row = $result->fetch_array(MYSQLI_ASSOC)){

                if($formatos = $this->_db->query('SELECT * FROM  libros_formatos;'))
                    while($row_formatos = $formatos->fetch_array(MYSQLI_ASSOC)) {
                        $row['formatos'][$row_formatos['id']]=$row_formatos;
                    }

                return $row;
            }



    }


    //GETTERS MASTERDATA
    public function getSkinColors($book_id, $gender) {
      //echo 'SELECT * FROM libro_hair_color tab1 INNER JOIN skin_colors tab2 on tab1.ref_id=tab2.id WHERE libro_id="'.$book_id.'" and gender_id="'.$gender.'" ORDER BY tab2.order ASC;';
        if($result = $this->_db->query('SELECT * FROM libro_skin_colors tab1 INNER JOIN skin_colors tab2 on tab1.ref_id=tab2.id WHERE libro_id="'.$book_id.'" and gender_id="'.$gender.'" ORDER BY tab2.order ASC;')) return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getHairStyles($book_id, $gender) {
        if($result = $this->_db->query('SELECT * FROM libro_hair_style  tab1 INNER JOIN hair_style tab2 on tab1.ref_id=tab2.id WHERE libro_id="'.$book_id.'" and gender_id="'.$gender.'" ORDER BY tab2.order ASC;')) return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getHairColors($book_id, $gender) {
        if($result = $this->_db->query('SELECT * FROM libro_hair_color tab1 INNER JOIN hair_color tab2 on tab1.ref_id=tab2.id WHERE libro_id="'.$book_id.'" and gender_id="'.$gender.'" ORDER BY tab2.order ASC;')) return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getEyesStyle($book_id, $gender) {
        if($result = $this->_db->query('SELECT * FROM libro_eyes_style  tab1 INNER JOIN eyes_style tab2 on tab1.ref_id=tab2.id WHERE libro_id="'.$book_id.'" and gender_id="'.$gender.'" ORDER BY tab2.order ASC;')) return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getEyesColor($book_id, $gender) {
        if($result = $this->_db->query('SELECT * FROM libro_eyes_color  tab1 INNER JOIN eyes_color tab2 on tab1.ref_id=tab2.id WHERE libro_id="'.$book_id.'" and gender_id="'.$gender.'" ORDER BY tab2.order ASC;')) return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getClothesColor($book_id, $gender) {
        if($result = $this->_db->query('SELECT * FROM libro_clothes_color  tab1 INNER JOIN clothes_color tab2 on tab1.ref_id=tab2.id WHERE libro_id="'.$book_id.'" and gender_id="'.$gender.'" ORDER BY tab2.order ASC;')) return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getGlassesStyle($book_id) {
        if($result = $this->_db->query('SELECT * FROM libro_glasses_style  tab1 INNER JOIN glasses_style tab2 on tab1.glasses_id=tab2.id WHERE libro_id="'.$book_id.'"  ORDER BY tab2.order ASC;')) return $result->fetch_all(MYSQLI_ASSOC);
    }




    public function getSkinColorsByID($book_id, $gender, $id) {
       $sql='SELECT * FROM libro_skin_colors tab1 INNER JOIN skin_colors tab2 on tab1.ref_id=tab2.id WHERE tab1.ref_id = "'.$id.'" AND libro_id="'.$book_id.'" and gender_id="'.$gender.'" ORDER BY tab2.order ASC;';
       //echo $sql;
        if($result = $this->_db->query($sql)) return $result->fetch_array(MYSQLI_ASSOC);
    }

    public function getHairStylesByID($book_id, $gender, $id) {
        if($result = $this->_db->query('SELECT * FROM libro_hair_style  tab1 INNER JOIN hair_style tab2 on tab1.ref_id=tab2.id WHERE tab1.ref_id = "'.$id.'" AND libro_id="'.$book_id.'" and gender_id="'.$gender.'" ORDER BY tab2.order ASC;')) return $result->fetch_array(MYSQLI_ASSOC);
    }

    public function getHairColorsByID($book_id, $gender, $id) {
        if($result = $this->_db->query('SELECT * FROM libro_hair_color tab1 INNER JOIN hair_color tab2 on tab1.ref_id=tab2.id WHERE tab1.ref_id = "'.$id.'" AND libro_id="'.$book_id.'" and gender_id="'.$gender.'" ORDER BY tab2.order ASC;')) return $result->fetch_array(MYSQLI_ASSOC);
    }

    public function getEyesStyleByID($book_id, $gender, $id) {
        if($result = $this->_db->query('SELECT * FROM libro_eyes_style  tab1 INNER JOIN eyes_style tab2 on tab1.ref_id=tab2.id WHERE tab1.ref_id = "'.$id.'" AND libro_id="'.$book_id.'" and gender_id="'.$gender.'" ORDER BY tab2.order ASC;')) return $result->fetch_array(MYSQLI_ASSOC);
    }

    public function getEyesColorByID($book_id, $gender, $id) {
        if($result = $this->_db->query('SELECT * FROM libro_eyes_color  tab1 INNER JOIN eyes_color tab2 on tab1.ref_id=tab2.id WHERE tab1.ref_id = "'.$id.'" AND libro_id="'.$book_id.'" and gender_id="'.$gender.'" ORDER BY tab2.order ASC;')) return $result->fetch_array(MYSQLI_ASSOC);
    }

    public function getClothesColorByID($book_id, $gender, $id) {
        $sql= 'SELECT * FROM libro_clothes_color  tab1 INNER JOIN clothes_color tab2 on tab1.ref_id=tab2.id WHERE tab1.ref_id = "'.$id.'" AND tab1.libro_id="'.$book_id.'" and gender_id="'.$gender.'"   ORDER BY tab2.order ASC;';
        if($result = $this->_db->query($sql)) return $result->fetch_array(MYSQLI_ASSOC);
    }

    public function getGlassesStyleByID($book_id, $gender, $id) {
	    $sql= 'SELECT * FROM libro_glasses_style  tab1 INNER JOIN glasses_style tab2 on tab1.glasses_id=tab2.id WHERE tab1.glasses_id = "'.$id.'" AND tab1.libro_id="'.$book_id.'"   ORDER BY tab2.order ASC;';
	    if($result = $this->_db->query($sql)) return $result->fetch_array(MYSQLI_ASSOC);
    }




    public function getPaginas($book_id, $model=1) {
	    switch($model){
            case "ebook":
                //to do
                $__sql = '  ORDER BY libros_paginas.orden_pagina ASC';
                break;
            case "tripas":
                // solo tripas
                $__sql = 'AND  pdf_libro=1 ORDER BY libros_paginas.orden_pagina ASC';
                break;
            case "portada":
                //solo portada
                $__sql = 'AND pdf_portada=1  ORDER BY libros_paginas.orden_portada ASC';
                break;
            default:
            //to do
            $__sql = '  ORDER BY libros_paginas.orden_pagina ASC';

        }
        if($result = $this->_db->query('SELECT * FROM libros_paginas  INNER JOIN libros   on libros_paginas.id_libro=libros.id WHERE 1=1  AND libros_paginas.id_libro="'.$book_id.'" '.@$__sql.' ;')) return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getPaginasMD5($book_id, $model=1) {
	    switch($model){
            case "ebook":
                //to do
                $__sql = '  ';
                break;
            case "tripas":
                // solo tripas
                $__sql = 'AND  pdf_libro=1 ';
                break;
            case "portada":
                //solo portada
                $__sql = 'AND pdf_portada=1  ';
                break;

        }
        if($result = $this->_db->query('SELECT * FROM libros_paginas  INNER JOIN libros   on libros_paginas.id_libro=libros.id WHERE 1=1 '.@$__sql.' AND md5(libros_paginas.id_libro)="'.$book_id.'"  ORDER BY libros_paginas.orden_pagina ASC;')) return $result->fetch_all(MYSQLI_ASSOC);
    }


    public function getTextPagina($book_slug, $page_number) {
	   // echo 'SELECT text FROM libros_paginas lp INNER JOIN libros l on l.id = lp.id_libro AND l.slug="'.$book_slug.'" WHERE lp.orden_pagina="'.$page_number.'";';
        if($result = $this->_db->query('SELECT * FROM libros_paginas lp INNER JOIN libros l on l.id = lp.id_libro WHERE l.slug="'.$book_slug.'" and lp.orden_pagina="'.$page_number.'";')) return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function isDedicatoria($book_slug, $page_number) {
	    if($result = $this->_db->query('SELECT dedicatoria FROM libros_paginas lp INNER JOIN libros l on l.id = lp.id_libro WHERE l.slug="'.$book_slug.'" and lp.orden_pagina="'.$page_number.'";')) $res= $result->fetch_row();

	    if($res[0]>0) return true; else return false;
    }



    /*EXISTS BY FUNCTIONS*/
    public function existBySlug($slug) {
        $sql = 'SELECT * FROM  libros where slug="'.$slug.'";';
        if($result = $this->_db->query($sql))
             if(sizeof($result->fetch_array(MYSQLI_ASSOC))>0) return true;
            else return false;
    }

    /*EXISTS BY FUNCTIONS*/
    public function getBySlug($slug) {
        $response=array();
        if($result = $this->_db->query('SELECT * FROM  libros where slug="'.$slug.'" order by orden;'))
            while($row = $result->fetch_array(MYSQLI_ASSOC)){

                if($formatos = $this->_db->query('SELECT * FROM  libros_formatos;'))
                    while($row_formatos = $formatos->fetch_array(MYSQLI_ASSOC)) {
                        $row['formatos'][$row_formatos['id']]=$row_formatos;
                    }

                return $row;
            }

    }


}

?>