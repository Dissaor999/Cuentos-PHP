<?php

class products extends Model {
	

	public function __construct(){
		parent::__construct();
	}

	function getLangs($langs, $id) {
	    $data =array();
        if($langs) {
            $sql_langs = 'SELECT pl.* FROM  product__langs pl WHERE pl.product_id = "'.$id.'";';
            if($result_langs = $this->_db->query($sql_langs)) {
                while($row_langs = $result_langs->fetch_array(MYSQLI_ASSOC)){
                    $data[$row_langs['lang_id']]=$row_langs;
                }
            }
        }
        return $data;
    }

	public function getColors() {
	    $data =array();
        $sql_langs = 'SELECT pc.* FROM  product__colors pc';
        if($result_langs = $this->_db->query($sql_langs)) {
            while($row_langs = $result_langs->fetch_array(MYSQLI_ASSOC)){
                $data[]=$row_langs;
            }
        }

        return $data;
    }

	public function getSpecials() {
	    $data =array();
        $sql_langs = 'SELECT pae.* FROM  product__acabados_especiales pae';
        if($result_langs = $this->_db->query($sql_langs)) {
            while($row_langs = $result_langs->fetch_array(MYSQLI_ASSOC)){
                $data[]=$row_langs;
            }
        }

        return $data;
    }


    /*
     * FUNCTION : GET COMBINATIONS PER product
     */
    function getCombinations($id) {
	    $data =array();

	    $sql = 'SELECT  DISTINCT pc.*, pac.acabado, c.hex, c.border, c.color as "color_t" FROM  product__combinations pc 
                INNER JOIN product__colors c on pc.color = c.id
                LEFT JOIN product__acabados_especiales pac on pc.acabado = pac.id
                WHERE pc.id_product = "'.$id.'" ORDER BY id;';

        if($result = $this->_db->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $data[]=$row;
            }
        }else echo $this->_db->error;

        return $data;
    }

    function getCombinationsByColorCombination($idCombination) {
	    $data =array();

	    $sql = 'SELECT   pc1.*, pcs.color, pcs.hex, pcs.`font-color`, pcs.border, pas.acabado
        FROM product__combinations pc1 
        LEFT JOIN product__acabados_especiales pas ON pc1.acabado = pas.id 
        INNER JOIN product__combinations pc2 ON pc2.id_product = pc1.id_product AND pc1.color = pc2.color AND pc2.id='.$idCombination.'
        
        INNER JOIN product__colors pcs ON pcs.id = pc2.color  ORDER BY pc1.id';

        if($result = $this->_db->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $data[]=$row;
            }
        }else echo $this->_db->error;

        return $data;
    }


    function getCombinationsC($id) {
        $data =array();

        $sql = 'SELECT pc.*, c.hex, c.color as "color_t"FROM  product__combinations pc 
                INNER JOIN product__colors c on pc.color = c.id
                WHERE pc.id_product = "'.$id.'" GROUP BY pc.color;';

        if($result = $this->_db->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $row['images']=$this->getImagesCombination( array('id'=>$row['id']))['data'];
                $data[]=$row;

            }
        }else echo $this->_db->error;

        return $data;
    }

    function getFiltersProduct($id) {
        $data =array();

        $sql = 'SELECT value_id FROM  product__filters WHERE product_id = "'.$id.'" ;';

        if($result = $this->_db->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $data[]=$row['value_id'];
            }
        }else echo $this->_db->error;

        return $data;
    }



    /*
     * FUNCTION : GET SPECIFIC COMBINATION BY ID
     */
    function getCombination($id) {
	    $data =array();

	    $sql = 'SELECT pc.*, c.color as "color_t" FROM  product__combinations pc 
                INNER JOIN product__colors c on pc.color = c.id
                WHERE pc.id = "'.$id.'";';

        if($result = $this->_db->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $data[]=$row;
            }
        }else echo $this->_db->error;

        return $data;
    }


    //COMMON
	public function getData($data=array(),$langs=false) {

        $ssql='';
        $w_sql=array();
	    //Wheres
        if(isset($data['id']) && $data['id']!=null) $w_sql[]=' AND p.id="'.$data['id'].'"';
        if(isset($data['slug']) && $data['slug']!=null) $w_sql[]=' AND pl.slug="'.$data['slug'].'"';

        //formación query
        if(count($w_sql>0)) {
            foreach($w_sql as $i=>$k) {
                if(sizeof($w_sql)>1 && $k==sizeof($w_sql))
                $ssql .= ' AND '.$k;
                else $ssql .= $k;
            }
        }

        //Query base
        $select = 'SELECT 
            p.id,
            pl.slug,
           (SELECT fvl.name FROM product__filters pf INNER JOIN FILTER__values fv ON fv.id = pf.value_id INNER JOIN FILTER__values_lang fvl ON fvl.value_id= pf.value_id INNER JOIN FILTER f ON f.`key`="style" AND f.id = fv.filter_id WHERE pf.product_id = p.id ) as "familia",
           (SELECT fvl.name FROM product__filters pf INNER JOIN FILTER__values fv ON fv.id = pf.value_id INNER JOIN FILTER__values_lang fvl ON fvl.value_id= pf.value_id INNER JOIN FILTER f ON f.`key`="tipology" AND f.id = fv.filter_id WHERE pf.product_id = p.id) as "tipologia",

            pl.name,
            pl.short_desc, 
            pl.long_desc,
            p.main_img, 
            pl.slug, 
            p.visible,
            p.featured
          FROM product p 
          LEFT JOIN product__langs pl ON p.id=pl.product_id AND pl.lang_id = "'.Languages::getDefaultLang().'"
           
          
          WHERE  1=1 '.$ssql;

        //echo $select;

        $items=array();

        if($result = $this->_db->query($select)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)){

                /*GET LANGS*/
                $row['langs']=$this->getLangs($langs, $row['id']);
                /*GET COMBINATIONS*/
                $row['combinations']=$this->getCombinations($row['id']);
                $row['colors']=$this->getCombinationsC($row['id']);
                $row['filters']=$this->getFiltersProduct($row['id']);


                array_push($items,$row);
            }
            if(!isset($id))
                return array('error'=>0, 'message'=>'','data'=>$items);
            else return($items[0]);
        }else return array('error'=>1, 'message'=>'No items. '.$this->_db->error);

    }

    public function getDataByCombination($data=array(),$langs=false) {

        $ssql='';
        $w_sql=array();
	    //Wheres
        if(isset($data['id']) && $data['id']!=null) $w_sql[]=' AND p.id="'.$data['id'].'"';
        if(isset($data['slug']) && $data['slug']!=null) $w_sql[]=' AND pl.slug="'.$data['slug'].'"';

        //formación query
        if(count($w_sql>0)) {
            foreach($w_sql as $i=>$k) {
                if(sizeof($w_sql)>1 && $k==sizeof($w_sql))
                $ssql .= ' AND '.$k;
                else $ssql .= $k;
            }
        }

        //Query base
        $select = 'SELECT 
            p.id,
            
            pl.name,
            pl.short_desc, 
            pl.long_desc,
            p.main_img, 
            pl.slug, 
            p.visible,
            p.featured
          FROM product p 
          INNER JOIN product__langs pl ON p.id=pl.product_id AND pl.lang_id = "'.Languages::getDefaultLang().'" 
          WHERE  1=1 '.$ssql;


        $items=array();

        if($result = $this->_db->query($select)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)){

                /*GET LANGS*/
                $row['langs']=$this->getLangs($langs, $row['id']);
                /*GET COMBINATIONS*/
                $row['combinations']=$this->getCombinations( $row['id']);
                $row['colors']=$this->getCombinationsC( $row['id']);


                array_push($items,$row);
            }
            if(!isset($id))
                return array('error'=>0, 'message'=>'','data'=>$items);
            else return($items[0]);
        }else return array('error'=>1, 'message'=>'No items. '.$this->_db->error);

    }

    public function getProductByCombination($data=array(),$langs=false) {

        $ssql='';
        $w_sql=array();
	    //Wheres

        //Query base
        $select = 'SELECT  DISTINCT
            p.id,
            
            pl.name,
            pl.short_desc, 
            pl.long_desc,
            (SELECT pci.src_thumb FROM product__combinations_images pci WHERE pci.id_combination = pc.id) as "main_img",
            pl.slug, 
            p.visible,
            p.featured, 
            c.hex, c.border, c.color as "color_t"
          FROM product p 
          INNER JOIN product__langs pl ON p.id=pl.product_id AND pl.lang_id = "'.Languages::getDefaultLang().'" 
          INNER JOIN product__combinations pc  ON p.id=pc.id_product AND pc.id="'.$data['id'].'"
          INNER JOIN product__colors c on pc.color = c.id
          
          ;';


        $items=array();

        if($result = $this->_db->query($select)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)){

                /*GET LANGS*/
                $row['langs']=$this->getLangs($langs, $row['id']);
                /*GET COMBINATIONS*/
                $row['combinations']=$this->getCombinations( $row['id']);
                $row['colors']=$this->getCombinationsC( $row['id']);


                array_push($items,$row);
            }
            if(!isset($id))
                return array('error'=>0, 'message'=>'','data'=>$items);
            else return($items[0]);
        }else return array('error'=>1, 'message'=>'No items. '.$this->_db->error);

    }

    //COMMON
	public function getDataFiltered($filters=array(),$langs=false) {
       // print_r($filters);
        $ssql='';
        $wsql='';
        $w_sql=array();
        $lj_sql=array();
	    //Wheres
        if(isset($filters['color']) && $filters['color']!=null) $lj_sql[]=' AND  pc.color = "'.$filters['color'].'" ';
        if(isset($filters['special']) && $filters['special']!=null) $lj_sql[]=' AND  pc.acabado = "'.$filters['special'].'" ';
        if(isset($filters['price']) && $filters['price']!=null) $lj_sql[]=' AND  pc.price BETWEEN  '.$filters['price'].'';
        if(isset($filters['alto']) && $filters['alto']!=null) $lj_sql[]=' AND  pc.alto <=  "'.$filters['alto'].'"';
        if(isset($filters['ancho']) && $filters['ancho']!=null) $lj_sql[]=' AND  pc.ancho <=  "'.$filters['ancho'].'"';
        if(isset($filters['tipologia']) && $filters['tipologia']!=null) $w_sql[]=' AND  p.id IN (SELECT pf.product_id FROM product__filters pf INNER JOIN FILTER__values fv ON pf.value_id = fv.id INNER JOIN FILTER f on f.id = fv.filter_id WHERE f.key = "tipology" and pf.value_id="'.$filters['tipologia'].'") ';

        //formación query
        if(count($w_sql>0)) {
            foreach($w_sql as $i=>$k) {
                if(sizeof($w_sql)>1 && $k==sizeof($w_sql))
                $ssql .= ' AND '.$k;
                else $ssql .= $k;
            }
        }


        //Query base
        $select = 'SELECT DISTINCT
            p.id,
            pl.name,
            pl.short_desc, 
            pl.long_desc,
            p.main_img, 
            pl.slug, 
            p.visible,
            p.featured 
          FROM product p 
          INNER JOIN product__langs pl ON p.id=pl.product_id AND pl.lang_id = "'.Languages::getDefaultLang().'" 
           INNER JOIN product__combinations pc ON pc.id_product = p.id '.implode(' ',$lj_sql).'
          WHERE  1=1 '.$ssql;

        $items=array();

       // print_r($select);


        if($result = $this->_db->query($select)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)){

                /*GET LANGS*/
                $row['langs']=$this->getLangs($langs, $row['id']);
                /*GET COMBINATIONS*/
                $row['combinations']=$this->getCombinations( $row['id']);
                $row['colors']=$this->getCombinationsC( $row['id']);


                array_push($items,$row);
            }
            if(!isset($id))
                return array($items, $select);
            else return($items[0]);
        }else return array('error'=>1, 'message'=>'No items. '.$this->_db->error);

    }


    //CLIENT FUNCTIONS
    public function getDataCart($id=null,$lang=DEFAULT_LANG) {
        //print_r($this);
        $ssql='';
        $w_sql=array();
	    //Wheres
        $ids = array();

        if(isset($_SESSION['cart']['product']) && sizeof($_SESSION['cart']['product'])>0) {
            foreach($_SESSION['cart']['product'] as $k=>$q)
                $ids[]=$k;

           // if($id!=null) $w_sql[]=' ';

            //formación query
            if(count($w_sql>0)) {
                foreach($w_sql as $i=>$k) {
                    if(sizeof($w_sql)>1 && $k==sizeof($w_sql))
                    $ssql .= ' AND '.$k;
                    else $ssql .= $k;
                }
            }

            //Query base
            $select = 'SELECT 
                pl.slug,
                pc.id,
                pc.alto,
                pc.ancho,
                pas.acabado,
                pc.id_product,
                pc.price,
                pl.name,
                pl.short_desc,
                pl.long_desc,
                 (SELECT distinct pci.src_thumb FROM product__combinations_images pci WHERE pci.id_combination = pc.id  LIMIT 1) as "main_img",
           c.hex, c.border, c.color as "color_t",
                p.visible
                FROM product p INNER JOIN product__langs pl ON p.id=pl.product_id AND pl.lang_id="'.$lang.'"
                
               
                INNER JOIN product__combinations pc ON pc.id_product = p.id AND pc.id IN ('.implode(',',$ids).')
                INNER JOIN product__colors c on pc.color = c.id
                 LEFT JOIN product__acabados_especiales pas ON pc.acabado = pas.id 
               WHERE 
              
              1=1 '.$ssql .'GROUP BY pc.id';



            $items=array();
            //die($select);
            //llamada bd
            if($result = $this->_db->query($select)) {
                while($row = $result->fetch_array(MYSQLI_ASSOC)) {

                    array_push($items, $row);
                }
                if(!isset($id))
                    return array('error'=>0, 'message'=>'','data'=>$items);
                else return($items[0]);
            }else return array('error'=>1, 'message'=>'No items. '.$this->_db->error);
        }else return array('error'=>1, 'message'=>'No items. '.$this->_db->error);
    }



    //ADMIN FUNCTIONS
    public function update($data=array()) {
        if (CUser::has_access('admin-area')) {


                $errors = array();$sql = array();$g = array();

            if(empty($data['id'])) array_push($errors,'No hay ID.');

            //$sql[] = 'fecha="'.strtotime($data['fecha']).'"';
            $sql[] = 'visible="'.$data['visible'].'"';
            $sql[] = 'featured="'.$data['featured'].'"';

            /*NON REQUIRED*/
           // if(!empty($data['imagen'])) $sql[] = 'img_full="'.$data['imagen'].'"';
            if(!empty($data['thumb'])) $sql[] = 'main_img="'.$data['thumb'].'"';

            /*END VARS SQL*

            /*VARS LANG*/
            $vars_langs = array_keys($data['langs']);
            foreach($vars_langs as $lang) {
                $sql_langs[$lang]=array(
                    'short_desc'    =>  htmlentities($data['langs'][$lang]['short_desc']),
                    'long_desc'     =>  htmlentities($data['langs'][$lang]['long_desc']),
                    'name'          =>  $data['langs'][$lang]['name'],
                    'slug'          =>  $data['langs'][$lang]['slug'],
                );
            }
           // print_r( $data['langs']);

            $sql_filters=null;
            if(sizeof($data['filters'])>0){
                $sql_filters = 'INSERT INTO product__filters (product_id, value_id) VALUES ';
               foreach($data['filters'] as $k=>$v)   $sql_filters .= '("'.$data['id'].'", "'.$v.'"),';
                $sql_filters =   substr($sql_filters, 0, -1).';';
            }
            /*END VARS SQL*/


            if(!isset($errors) || sizeof($errors)==0) {

                //Update new
                $update = 'UPDATE product SET ';
                if(sizeof($sql)>0)
                    foreach ($sql as $k=>$item)
                        $update .= ($k != (sizeof($sql)-1)) ? $item.', ' : $item;
                $update .= ' WHERE id="'.$data['id'].'";';

                //Insert new lang vars
                $updateLangs = array();
                if(sizeof($sql_langs)>0) {
                    foreach ($sql_langs as $k=>$item)  {
                        $sqlUpdate[$k] = 'INSERT INTO product__langs (product_id, lang_id, ';
                        foreach ($item as $kkk=>$sqq) $sqlUpdate[$k] .= $kkk.',';
                        $sqlUpdate[$k] = substr($sqlUpdate[$k], 0, -1);
                        $sqlUpdate[$k] .= ') VALUES ("'.$data['id'].'", "'.$k.'", ' ;
                        foreach ($item as $kkk=>$sqq) $sqlUpdate[$k] .= '"'.$sqq.'",';
                        $sqlUpdate[$k] =substr($sqlUpdate[$k], 0, -1);
                        //SI EXISTE ACTUALIZA
                        $sqlUpdate[$k] .= ') ON DUPLICATE KEY UPDATE ' ;
                        foreach ($item as $kkk=>$sqq)
                            $sqlUpdate[$k] .= $kkk.'="'.$sqq.'",';
                        $sqlUpdate[$k] = substr($sqlUpdate[$k], 0, -1);
                    }
                }


                foreach($sqlUpdate as $sql_lang)  if(!$this->_db->query($sql_lang)) $errors[]=@$this->_db->error;
                if(!$this->_db->query($update))  $errors[]=@$this->_db->error;

                //REMOVE FILTERS
                if(!$this->_db->query('DELETE FROM `product__filters` WHERE `product_id`='.$data['id'])) $errors[]='Ha habido un error borrando los filtros.'.@$this->_db->error;

                //ADD FILTERS
               // echo $sql_filters;
                if(isset($sql_filters) && sizeof($sql_filters)>0){
                    if(!$this->_db->query($sql_filters)) $errors[]='Ha habido un error añadiendo los filtros.'.@$this->_db->error;
                } else $errors[]='No hay categoriasBlog';


                if(!$errors)
                    return array('error'=>0, 'msg'=>'Se ha actualizado existosamente');

                return array('error'=>1, 'msg'=>'No actualizado', 'errors'=>$errors);
            }
            //Return Data
            return array('error'=>1, 'msg'=>'No actualizado', 'errors'=>$errors);
        }
    }

  //ADMIN FUNCTIONS
    public function add($data=array()) {
        if (CUser::has_access('admin-area')) {
             $errors = array();$sql = array();$g = array();



                //$sql[] = 'fecha="'.strtotime($data['fecha']).'"';
                $sql['visible'] = '"'.$data['visible'].'"';
                $sql['featured'] = '"'.$data['featured'].'"';
               $sql['main_img'] = '"'.$data['thumb'].'"';

                /*NON REQUIRED*/
               // if(!empty($data['imagen'])) $sql[] = 'img_full="'.$data['imagen'].'"';
                if(!empty($data['thumb'])) $sql[] = 'main_img="'.$data['thumb'].'"';

                /*END VARS SQL*

                /*VARS LANG*/
                $vars_langs = array_keys($data['langs']);
                foreach($vars_langs as $lang) {
                    $sql_langs[$lang]=array(
                        'short_desc'    =>  htmlentities($data['langs'][$lang]['short_desc']),
                        'long_desc'     =>  htmlentities($data['langs'][$lang]['long_desc']),
                        'name'          =>  $data['langs'][$lang]['name'],
                        'slug'          =>  $data['langs'][$lang]['slug'],
                    );
                }

                /*END VARS SQL*/
        /*
                        langs'=>$lang_data,
                        //   'fecha'=>$fecha,
                        'precio'=>$data['precio'],
                        'visible'=>$visible,
                        'featured'=>$featured,
                        'filters'=>array(
                            implode(',',$data['tipology']),
                            implode(',',$data['material']),
                            implode(',',$data['style']),

                        ),
                        'thumb'=>@$miniatura->file_dst_name,

        */
                if(!isset($errors) || sizeof($errors)==0) {

                    //Update new
                    $update = 'INSERT INTO `product`( `featured`,  `visible`, `main_img`) VALUES (
                        
                                  '.$sql['featured'].',
                                  '.$sql['visible'].',
                                  '.$sql['main_img'].'
                              );';


                    if(!$this->_db->query($update))
                        $errors[]=@$this->_db->error;
                    $id = $this->_db->insert_id;
                        //Insert new lang vars
                    if(sizeof($sql_langs)>0) {
                        foreach ($sql_langs as $k=>$item)  {
                            $sqlUpdate[$k] = 'INSERT INTO product__langs (product_id, lang_id, ';
                            foreach ($item as $kkk=>$sqq) $sqlUpdate[$k] .= $kkk.',';
                            $sqlUpdate[$k] = substr($sqlUpdate[$k], 0, -1);
                            $sqlUpdate[$k] .= ') VALUES ("'.$id.'", "'.$k.'", ' ;
                            foreach ($item as $kkk=>$sqq) $sqlUpdate[$k] .= '"'.$sqq.'",';
                            $sqlUpdate[$k] =substr($sqlUpdate[$k], 0, -1);
                            //SI EXISTE ACTUALIZA
                            $sqlUpdate[$k] .= ');';
                        }
                    }



                    foreach($sqlUpdate as $sql_lang) {
                        if(!$this->_db->query($sql_lang))
                            $errors[]=@$this->_db->error;
                    }
                    if(!$errors)
                        return array('error'=>0, 'msg'=>'Se ha actualizado existosamente');

                    return array('error'=>1, 'msg'=>'No actualizado', 'errors'=>$errors);
                }
                //Return Data
                return array('error'=>1, 'msg'=>'No actualizado', 'errors'=>$errors);
            }
    }

    //ADMIN FUNCTIONS
    public function delete($data=array()) {
        if (CUser::has_access('admin-area')) {
            $errors = array();$sql = array();$g = array();

                if(empty($data['id'])) array_push($errors,'No hay ID.');



                //REMOVE FILTERS
                if(!$this->_db->query('DELETE FROM `product__filters` WHERE `product_id`='.$data['id'])) $errors[]='Ha habido un error borrando los filtros.'.@$this->_db->error;
                if(!$this->_db->query('DELETE FROM `product__langs` WHERE `product_id`='.$data['id'])) $errors[]='Ha habido un error borrando los filtros.'.@$this->_db->error;
                if(!$this->_db->query('DELETE FROM `product` WHERE `id`='.$data['id'])) $errors[]='Ha habido un error borrando los filtros.'.@$this->_db->error;



                if(!$errors)
                    return array('error'=>0, 'msg'=>'Se ha actualizado existosamente');

                return array('error'=>1, 'msg'=>'No actualizado', 'errors'=>$errors);

                //Return Data
                return array('error'=>1, 'msg'=>'No actualizado', 'errors'=>$errors);
        }
    }

   /*PROPDUCT IMAGES*/
    public function getImages($params=array()) {
        if(!isset($params['id']) || empty($params['id'])) $errors[]='No hay ID';

        if(!isset($errors)){
            $images = array();

            //IMAGES
            $sql_img = 'SELECT * FROM  product__combinations_images pcimg WHERE pcimg.id_combination = '.$params['id'].' ORDER BY id DESC;';
            if($result_img = $this->_db->query($sql_img))
                while($row_img = $result_img->fetch_array(MYSQLI_ASSOC))
                    $images[]=$row_img;

            return array('error'=>0, 'data'=>$images);

        }
        return array('error'=>1, 'msg'=>'No news', 'errors'=>$errors);
    }

    public function addPhoto($data=array()) {
        if (CUser::has_access('admin-area')) {
                if(!isset($errors) || sizeof($errors)==0) {
                $update = 'INSERT INTO product__combinations_images (src_img , src_thumb, id_combination) VALUES ("'.$data['src'].'","'.$data['src_thumb'].'","'.$data['id'].'")';


              // echo $update;
               if($result = $this->_db->query($update))
                    $id = $this->_db->insert_id;
                else  $errors[]='MySQL error. '.@$this->_db->error;

                if(!isset($errors))
                    return array('jsonrpc'=>"2.0", 'result'=>array('cleanFileName'=>$data['src']), 'id'=>@$id);

                return array('error'=>1, 'msg'=>'No insertado', 'errors'=>$errors);
            }
            return array('error'=>1, 'msg'=>'No insertado', 'errors'=>$errors);
        }
    }

    public function deletePhoto($data=array()) {
        if (CUser::has_access('admin-area')) {
                if(!isset($errors) || sizeof($errors)==0) {
                //Update new
                $update = 'DELETE FROM product__combinations_images WHERE id = "'.$data['id'].'"';
                if($result = $this->_db->query($update)) {
                }else  $errors[]='MySQL error. '.@$this->_db->error;
                if(!isset($errors))
                    return array('success'=>1, 'message'=>'Borrado');
                return array('success'=>0, 'msg'=>'No Borrado', 'errors'=>$errors);
            }
            return array('success'=>0, 'msg'=>'No Borrado', 'errors'=>$errors);
        }
    }


    /* COMBINATIONS*/

    //ADMIN FUNCTIONS
    //ADMIN FUNCTIONS
    public function edit_combination($data=array()) {
        $errors = array();$sql = array();$g = array();

        if(empty($data['id'])) array_push($errors,'No hay ID.');


        $sql[] = 'price="'.$data['price'].'"';
        $sql[] = 'color="'.$data['color'].'"';
        $sql[] = 'alto="'.$data['alto'].'"';
        $sql[] = 'ancho="'.$data['ancho'].'"';
        $sql[] = 'sku="'.$data['sku'].'"';
        $sql[] = 'acabado="'.$data['special'].'"';
        $sql[] = 'primary_image="'.$data['primary_image'].'"';


        if(!isset($errors) || sizeof($errors)==0) {

            //Update new
            $update = 'UPDATE product__combinations SET ';
            if(sizeof($sql)>0)
                foreach ($sql as $k=>$item)
                    $update .= ($k != (sizeof($sql)-1)) ? $item.', ' : $item;
            $update .= ' WHERE id="'.$data['id'].'";';

            if(!$this->_db->query($update))
                $errors[]=@$this->_db->error;

            if(!$errors)
                return array('error'=>0, 'msg'=>'Se ha actualizado existosamente');

            return array('error'=>1, 'msg'=>'No actualizado', 'errors'=>$errors);
        }
        //Return Data
        return array('error'=>1, 'msg'=>'No actualizado', 'errors'=>$errors);
    }

    public function add_combination($data=array()) {
        if (CUser::has_access('admin-area')) {
            //Errors Array
            $errors = array();
            //SQL Vars Array
            $sql = array();


            $sql[] = '"'.@$data['id_product'].'"';
            $sql[] = '"'.@$data['special'].'"';
            $sql[] = '"'.@$data['color'].'"';
            $sql[] = '"'.@$data['price'].'"';
            $sql[] = '"'.@$data['alto'].'"';
            $sql[] = '"'.@$data['ancho'].'"';
            $sql[] = '"'.@$data['sku'].'"';
            $sql[] = '"'.@$data['primary_image'].'"';


            if(!isset($errors) || sizeof($errors)==0) {

                //Update new
                $update = 'INSERT INTO product__combinations (id_product,acabado, color, price, alto, ancho, sku, primary_image) VALUES (';
                if(sizeof($sql)>0)
                    foreach ($sql as $k=>$item)
                        $update .= ($k != (sizeof($sql)-1)) ? $item.', ' : $item;
                $update .= ');';

                if($result = $this->_db->query($update)) {$id = $this->_db->insert_id; }
                else  $errors[]='Ha habido un error modificando la noticia base.'.@$this->_db->error;

                if(!$errors)
                    return array('error'=>0, 'id'=>$id, 'msg'=>'Se ha insertado existosamente. ID '.@$id);
                return array('error'=>1, 'msg'=>'No insertado', 'errors'=>$errors);
            }
            //Return Data
            return array('error'=>1, 'msg'=>'No insertado', 'errors'=>$errors);
        }
    }

    public function getImagesCombination($params=array()) {
        if(!isset($params['id']) || empty($params['id'])) $errors[]='No hay ID';

        if(!isset($errors)){
            $images = array();

            //IMAGES
            $sql_img = 'SELECT * FROM  product__combinations_images pcimg WHERE pcimg.id_combination = '.$params['id'].' ORDER BY id DESC;';
            if($result_img = $this->_db->query($sql_img))
                while($row_img = $result_img->fetch_array(MYSQLI_ASSOC))
                    $images[]=$row_img;

            return array('error'=>0, 'data'=>$images);

        }
        return array('error'=>1, 'msg'=>'No news', 'errors'=>$errors);
    }

    public function addPhotoCombination($data=array()) {
        if (CUser::has_access('admin-area')) {
                if(!isset($errors) || sizeof($errors)==0) {
                $update = 'INSERT INTO product__combinations_images (src_img , src_thumb, id_combination) VALUES ("'.$data['src'].'","'.$data['src_thumb'].'","'.$data['id'].'")';
                if($result = $this->_db->query($update))
                    $id = $this->_db->insert_id;
                else  $errors[]='MySQL error. '.@$this->_db->error;

                if(!isset($errors))
                    return array('jsonrpc'=>"2.0", 'result'=>array('cleanFileName'=>$data['src']), 'id'=>@$id);

                return array('error'=>1, 'msg'=>'No insertado', 'errors'=>$errors);
            }
            return array('error'=>1, 'msg'=>'No insertado', 'errors'=>$errors);
        }
    }

    public function deletePhotoCombination($data=array()) {
        if (CUser::has_access('admin-area')) {
                if(!isset($errors) || sizeof($errors)==0) {
                //Update new
                $update = 'DELETE FROM product__combinations_images WHERE id = "'.$data['id'].'"';
                if($result = $this->_db->query($update)) {
                }else  $errors[]='MySQL error. '.@$this->_db->error;
                if(!isset($errors))
                    return array('success'=>1, 'message'=>'Borrado');
                return array('success'=>0, 'msg'=>'No Borrado', 'errors'=>$errors);
            }
            return array('success'=>0, 'msg'=>'No Borrado', 'errors'=>$errors);
        }
    }

    public function exists($id=null) {
        $select = 'SELECT p.id FROM product p WHERE p.id = "'.$id.'";';
        if($result = $this->_db->query($select)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)) return true;
            return true;
        }
        return false;
    }

    public function existsId ($id) {
        $errors=array();
        if(!isset($id) || $id=='') $errors[]='No hay slug';

        if(!sizeof($errors)>0){
            $news = array();

            $sql = 'SELECT * FROM  product  WHERE id="'.$id.'" LIMIT 1;';
            if($result = $this->_db->query($sql))  {
                if($result->num_rows>0) return true;
            }
        }
        return false;
    }

    public function existsCombination ($id) {
        $errors=array();
        if(!isset($id) || $id=='') $errors[]='No hay slug';

        if(!sizeof($errors)>0){
            $news = array();

            $sql = 'SELECT * FROM  product__combinations  WHERE id="'.$id.'" LIMIT 1;';
            if($result = $this->_db->query($sql))  {
                if($result->num_rows>0) return true;
            }
        }
        return false;
    }


    /*
     *  GET FILTERS
     *  by param
     * */

    public function getFilter($filter='') {

        $items=array();
        if($filter!='')  $issetfilter = 'WHERE f.key = "'.$filter.'"';

        $select = 'SELECT * FROM FILTER f INNER JOIN FILTER__lang fl on fl.filter_id=f.id '.@$issetfilter.';';
        if($result = $this->_db->query($select)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $sql_filters = 'SELECT * FROM  FILTER__values fv INNER JOIN FILTER__values_lang fvl ON fvl.value_id = fv.id WHERE fv.filter_id = "'.$row['id'].'";';
                if($result_filters  = $this->_db->query($sql_filters)) {
                    while($row_filters  = $result_filters->fetch_array(MYSQLI_ASSOC)){
                        $row['values'][]=$row_filters;
                    }
                }
                array_push($items,$row);
            }
            return array('error'=>0, 'message'=>'','data'=>$items);
        }
        return array('error'=>1, 'message'=>'No data');
    }


    /*
     *  getEstilos
     * */

    public function getEstilos() {

        $items=array();
        $select = 'SELECT * FROM FILTER f INNER JOIN FILTER__lang fl on fl.filter_id=f.id WHERE f.key = "style";';
        if($result = $this->_db->query($select)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $sql_filters = 'SELECT * FROM  FILTER__values fv INNER JOIN FILTER__values_lang fvl ON fvl.value_id = fv.id WHERE fv.filter_id = "'.$row['id'].'";';
                if($result_filters  = $this->_db->query($sql_filters)) {
                    while($row_filters  = $result_filters->fetch_array(MYSQLI_ASSOC)){
                        $row['values'][]=$row_filters;
                    }
                }
                array_push($items,$row);
            }
            return array('error'=>0, 'message'=>'','data'=>$items);
        }
        return array('error'=>1, 'message'=>'No data');
    }

    public function getMateriales() {

        $items=array();
        $select = 'SELECT * FROM FILTER f INNER JOIN FILTER__lang fl on fl.filter_id=f.id WHERE f.key = "material";';
        if($result = $this->_db->query($select)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $sql_filters = 'SELECT * FROM  FILTER__values fv INNER JOIN FILTER__values_lang fvl ON fvl.value_id = fv.id WHERE fv.filter_id = "'.$row['id'].'";';
                if($result_filters  = $this->_db->query($sql_filters)) {
                    while($row_filters  = $result_filters->fetch_array(MYSQLI_ASSOC)){
                        $row['values'][]=$row_filters;
                    }
                }
                array_push($items,$row);
            }
            return array('error'=>0, 'message'=>'','data'=>$items);
        }
        return array('error'=>1, 'message'=>'No data');
    }

    public function getTipologias() {

        $items=array();
        $select = 'SELECT * FROM FILTER f INNER JOIN FILTER__lang fl on fl.filter_id=f.id WHERE f.key = "tipologia";';
        if($result = $this->_db->query($select)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $sql_filters = 'SELECT * FROM  FILTER__values fv INNER JOIN FILTER__values_lang fvl ON fvl.value_id = fv.id WHERE fv.filter_id = "'.$row['id'].'";';
                if($result_filters  = $this->_db->query($sql_filters)) {
                    while($row_filters  = $result_filters->fetch_array(MYSQLI_ASSOC)){
                        $row['values'][]=$row_filters;
                    }
                }
                array_push($items,$row);
            }
            return array('error'=>0, 'message'=>'','data'=>$items);
        }
        return array('error'=>1, 'message'=>'No data');
    }

    public function getFamilias() {

        $items=array();
        $select = 'SELECT * FROM FILTER f INNER JOIN FILTER__lang fl on fl.filter_id=f.id WHERE f.key = "familia";';
        if($result = $this->_db->query($select)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $sql_filters = 'SELECT * FROM  FILTER__values fv INNER JOIN FILTER__values_lang fvl ON fvl.value_id = fv.id WHERE fv.filter_id = "'.$row['id'].'";';
                if($result_filters  = $this->_db->query($sql_filters)) {
                    while($row_filters  = $result_filters->fetch_array(MYSQLI_ASSOC)){
                        $row['values'][]=$row_filters;
                    }
                }
                array_push($items,$row);
            }
            return array('error'=>0, 'message'=>'','data'=>$items);
        }
        return array('error'=>1, 'message'=>'No data');
    }


}

?>