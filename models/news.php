<?php

class News extends Model {

	
	public function __construct(){
		parent::__construct();
	}


	public function getDataLimit($limit=7, $lang, $last=0)
	{
		$news = array();
		$sqlLast = (isset($last) && $last!=0) ? 'AND n.fecha<"'.$last.'"' : '';
		$sql = 'SELECT n.*, nl.* FROM  noticias__list n INNER JOIN noticias__langs nl ON nl.id_new = n.id WHERE n.publicar>0 AND nl.id_lang="'.$lang.'" '.$sqlLast.' ORDER BY  n.fecha DESC  LIMIT '.$limit.';';
		
		//echo $sql;
		if($result = $this->_db->query($sql))  {	
			while($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$sql_categories = 'SELECT * FROM noticias__categorias nc INNER JOIN noticias__categorias_langs ncl ON nc.id=ncl.id_categoria AND ncl.id_lang="'.$lang.'" INNER JOIN noticias__categorias_rel ncr ON ncr.id_new = '.$row['id'].' AND ncr.id_categoria = nc.id    ;';
				if($result_categories = $this->_db->query($sql_categories))
					while($row_categories = $result_categories->fetch_array(MYSQLI_ASSOC))
						$row['categorias'][] = $row_categories;
				$row['fecha_parse'] = date('d/m/Y', $row['fecha']);
				array_push($news,$row);
			}
			return array('error'=>0, 'data'=>$news); 
		}else return array('error'=>1, 'msg'=>'No news. '.$this->_db->error);
	}
	
	public function getDataLimitPage($limit=7, $lang, $page=1, $search='', $cat='') {
		$news = array();
		//SEARCH VARS
		$search = (isset($search) && $search!='') ? 'AND (lower(nl.titulo) LIKE lower("%'.$search.'%") OR lower(nl.descripcion) LIKE lower("%'.$search.'%") OR lower(nl.descripcion_corta) LIKE lower("%'.$search.'%") OR lower(n.slug) LIKE lower("%'.$search.'%") 	)' : '';
		$category = (isset($cat) && $cat!='') ? 'AND n.id IN (SELECT id_new FROM noticias__categorias_rel ncr INNER JOIN noticias__categorias nc ON nc.id=ncr.id_categoria WHERE nc.slug="'.$cat.'" )' : '';
		
		//PAGINATION
		$limitSql = ($limit*($page-1)).','.$limit;
		
		$sql = 'SELECT  n.*, nl.* FROM  noticias__list n INNER JOIN noticias__langs nl ON nl.id_new = n.id AND nl.id_lang ="'.$lang.'" WHERE  n.publicar>0  '.$search.' '.$category.' ORDER BY  n.fecha DESC  LIMIT '.$limitSql.';';
		//echo $sql;
		if($result = $this->_db->query($sql))  {	
			while($row = $result->fetch_array(MYSQLI_ASSOC)) {
				$sql_categories = 'SELECT * FROM noticias__categorias nc INNER JOIN noticias__categorias_langs ncl ON nc.id=ncl.id_categoria AND ncl.id_lang="'.$lang.'" INNER JOIN noticias__categorias_rel ncr ON ncr.id_new = '.$row['id'].' AND ncr.id_categoria = nc.id    ;';
				if($result_categories = $this->_db->query($sql_categories))
					while($row_categories = $result_categories->fetch_array(MYSQLI_ASSOC))
						$row['categorias'][] = $row_categories;
				$row['fecha_parse'] = date('d/m/Y', $row['fecha']);
				array_push($news,$row);
			}
			return array('error'=>0, 'data'=>$news); 
		}else return array('error'=>1, 'msg'=>'No news. '.$this->_db->error);
	}
	
	public function getResults($lang,$search='', $cat=''){
		//SEARCH VARS
		$search = (isset($search) && $search!='') ? 'AND (lower(nl.titulo) LIKE lower("%'.$search.'%") OR lower(nl.descripcion) LIKE lower("%'.$search.'%") OR lower(nl.descripcion_corta) LIKE lower("%'.$search.'%") OR lower(n.slug) LIKE lower("%'.$search.'%") 	)' : '';
		$category = (isset($cat) && $cat!='') ? 'AND n.id IN (SELECT id_new FROM noticias__categorias_rel ncr INNER JOIN noticias__categorias nc ON nc.id=ncr.id_categoria WHERE nc.slug="'.$cat.'" )' : '';
		
		$sql = 'SELECT COUNT(*) as "count" FROM  noticias__list n INNER JOIN noticias__langs nl ON nl.id_new = n.id WHERE  n.publicar>0 AND nl.id_lang="'.$lang.'" '.$search.' '.$category.' ;';
		if($result = $this->_db->query($sql))  {	
			$row =  $result->fetch_array(MYSQLI_ASSOC);
			return array('error'=>0, 'data'=>$row); 
		}else return array('error'=>1, 'msg'=>'No pages. '.$this->_db->error);
	}
	
	public function getMostUsedTags($limit=7){
		$a_tags = array(); $dresp = array();
		$sql = 'SELECT tags FROM  news;';
		if($result = $this->_db->query($sql)) {	
			while($row = $result->fetch_array(MYSQLI_ASSOC))
				foreach(explode(",", $row['tags']) as $tag)
					if(array_key_exists($tag,$a_tags)) $a_tags[$tag]++; else $a_tags[$tag]=1;
			arsort($a_tags);
			foreach ($a_tags as $k=>$tag) {
				if($k>$limit) break;
				$dresp[$k] = $tag;
			}
			return array('error'=>0, 'data'=>$dresp); 
		}else return array('error'=>1, 'msg'=>'No tags. '.$this->_db->error);
	}
	
	public function getNewsLastId($date, $limit=7)
	{
		$news = array();
		$sql = 'SELECT n.*, nl.*  FROM  noticias__list n LEFT JOIN noticias__langs nl WHERE n.publicar>0 AND  n.fecha<"'.$date.'" ORDER BY n.fecha DESC LIMIT  '.$limit.';';
		if($result = $this->_db->query($sql))  {	
			while($row = $result->fetch_array(MYSQLI_ASSOC)) 
				array_push($news,$row);
			return array('error'=>0, 'data'=>$news); 
		}else return array('error'=>1, 'msg'=>'No news. '.$this->_db->error);
	}
	
	public function getNew($params=array())
	{
		$errors=array();
		if(!isset($params['id']) || empty($params['id'])) $errors[]='No hay ID';
		if(!sizeof($errors)>0){
			$news = array();
			$sql = 'SELECT n.*, nl.*  FROM  noticias__list n LEFT JOIN noticias__langs nl ON nl.id_new = n.id WHERE n.`publicar`>0 AND   n.id="'.$params['id'].'" LIMIT 1;';

            $sql = 'SELECT * FROM  noticias__list WHERE id="'.$params['id'].'" LIMIT 1;';
            if($result = $this->_db->query($sql))  {
                $news = $result->fetch_array(MYSQLI_ASSOC);
                $sql_langs = 'SELECT * FROM  noticias__langs WHERE id_new = '.$news['id'].';';
                if($result_langs = $this->_db->query($sql_langs)) {
                    while($row_langs = $result_langs->fetch_array(MYSQLI_ASSOC)){
                        $news['langs'][$row_langs['id_lang']]=$row_langs;
                    }
                }

                $sql_cats = 'SELECT * FROM  noticias__categorias_langs';
                if($result_cats = $this->_db->query($sql_cats)) {
                    while($row_cats = $result_cats->fetch_array(MYSQLI_ASSOC)) {
                        $news['cats'][$row_cats['id_lang']]=$row_cats;
                    }
                }

                $sql_cates = 'SELECT id_categoria FROM  noticias__categorias_rel  WHERE id_new = '.$news['id'].';';
                if($result_cates = $this->_db->query($sql_cates))
                    while($row_cates = $result_cates->fetch_array(MYSQLI_ASSOC))
                        $news['cates'][]=$row_cates['id_categoria'];

                return array('error'=>0, 'data'=>$news);
            }
		}
		return array('error'=>1, 'msg'=>'No news', 'errors'=>$errors);
	}
	
	public function getNewBySlug($slug, $lang)
	{
		$errors=array();
		if(!isset($slug) || empty($slug)) $errors[]='No hay Slug';
		if(!sizeof($errors)>0){
			$news = array();
			$sql = 'SELECT n.*, nl.* FROM  noticias__list n INNER JOIN noticias__langs nl ON nl.id_new = n.id WHERE n.publicar>0 AND n.slug="'.$slug.'" AND nl.id_lang="'.$lang.'";';
			//cuand '<br />'.$sql. '<br />';
			if($result = $this->_db->query($sql))  {
				if($result->num_rows>0) {
					$news = $result->fetch_array(MYSQLI_ASSOC);
					return array('error'=>0, 'data'=>$news); 
				}else {
					$sql_df = 'SELECT n.*, nl.* FROM  noticias__list n INNER JOIN noticias__langs nl WHERE n.slug="'.$slug.'" AND nl.id_lang=(SELECT `id_lang` FROM languages WHERE `default`=1 LIMIT 1);';
					if($result_df = $this->_db->query($sql_df))  {
						if($result->num_rows>0) {
							$news = $result->fetch_array(MYSQLI_ASSOC); 
							return array('error'=>0, 'data'=>$news); 
						}
					}
				}	
			}	
		}
		
		return array('error'=>1, 'msg'=>'No news '.$this->_db->error, 'errors'=>$errors);
	}

	public function validateSlug($slug, $lang)
	{
		$errors=array();
		if(!isset($slug) || empty($slug)) $errors[]='No hay Slug';
		if(!sizeof($errors)>0){
			$sql = 'SELECT n.*, nl.* FROM  noticias__list n INNER JOIN noticias__langs nl ON nl.id_new = n.id WHERE n.publicar>0 AND n.slug="'.$slug.'" AND nl.id_lang="'.$lang.'";';
			if($result = $this->_db->query($sql))
				if($result->num_rows>0) return true;
				else return false;
		}
        return false;
	}

    public function validateID($slug, $lang)
	{
		$errors=array();
		if(!isset($slug) || empty($slug)) $errors[]='No hay Slug';
		if(!sizeof($errors)>0){
			$sql = 'SELECT n.*, nl.* FROM  noticias__list n INNER JOIN noticias__langs nl ON nl.id_new = n.id WHERE n.publicar>0 AND n.slug="'.$slug.'" AND nl.id_lang="'.$lang.'";';
			if($result = $this->_db->query($sql))
				if($result->num_rows>0) return true;
				else return false;
		}
        return false;
	}



	/*ADMIN FUNCTIONS*/
    public function adm_getData($lang)
    {
        $news = array();
        $sql = 'SELECT n.*, nl.* FROM  noticias__list n INNER JOIN noticias__langs nl ON nl.id_new = n.id WHERE  nl.id_lang="'.$lang.'"  ORDER BY  n.fecha DESC ;';

        //echo $sql;
        if($result = $this->_db->query($sql))  {
            while($row = $result->fetch_array(MYSQLI_ASSOC)) {

                $sql_langs = 'SELECT * FROM  noticias__langs WHERE id_new = '.$row['id'].';';
                if($result_langs = $this->_db->query($sql_langs)) {
                    while($row_langs = $result_langs->fetch_array(MYSQLI_ASSOC)){
                        $row['langs'][$row_langs['id_lang']]=$row_langs;
                    }
                }

                $sql_categories = 'SELECT * FROM noticias__categorias nc INNER JOIN noticias__categorias_langs ncl ON nc.id=ncl.id_categoria AND ncl.id_lang="'.$lang.'" INNER JOIN noticias__categorias_rel ncr ON ncr.id_new = '.$row['id'].' AND ncr.id_categoria = nc.id    ;';
                if($result_categories = $this->_db->query($sql_categories))
                    while($row_categories = $result_categories->fetch_array(MYSQLI_ASSOC))
                        $row['categorias'][] = $row_categories;

                if($row['fecha']!='')$row['fecha_parse'] = date('d/m/Y', @$row['fecha']);
                else $row['fecha_parse'] = date('d/m/Y', time());

                array_push($news,$row);
            }
            return array('error'=>0, 'data'=>$news);
        }else return array('error'=>1, 'msg'=>'No news. '.$this->_db->error);
    }

    public function insertarNoticia($data=array()) {
        //print_r($data);
        //Errors Array
        $errors = array();
        //SQL Vars Array
        $sql = array();
        //Gallery array
        $g = array();

        /*VARS SQL*/
        /*REQUIRED*/

        //FECHA
        $sql[] = '"'.strtotime($data['fecha']).'"';

        //VISIBLE
        $sql[] = '"'.$data['publicar'].'"';

        //DESTACADA
        $sql[] = '"'.$data['destacada'].'"';


        //SLUG
        $sql[] = '"'.$data['slug'].'"';


        /*NON REQUIRED*/
        $sql[] = '"'.$data['imagen'].'"';

        $sql[] = '"'.$data['thumb'].'"';


        /*END VARS SQL*

        /*VARS LANG*/
        $vars_langs = array_keys($data['langs']);

        foreach($vars_langs as $lang) {
            if(empty($data['langs'][$lang]['descripcion'])) array_push($errors,'No hay descripcion en el lenguaje '.$lang);
            else $sql_langs[$lang]['descripcion'] = htmlentities($data['langs'][$lang]['descripcion']);

            if(empty($data['langs'][$lang]['descripcion_corta'])) array_push($errors,'No hay descripcion corta en el lenguaje '.$lang);
            else $sql_langs[$lang]['descripcion_corta'] = htmlentities($data['langs'][$lang]['descripcion_corta']);

            if(empty($data['langs'][$lang]['titulo'])) array_push($errors,'No hay titulo en el lenguaje '.$lang);
            else $sql_langs[$lang]['titulo'] = $data['langs'][$lang]['titulo'];
        }


        /*END VARS SQL*/

        if(!isset($errors) || sizeof($errors)==0) {

            //Update new
            $update = 'INSERT INTO noticias__list (fecha, publicar, destacada, slug, img_full, img_thumb) VALUES (';
            if(sizeof($sql)>0)
                foreach ($sql as $k=>$item)
                    $update .= ($k != (sizeof($sql)-1)) ? $item.', ' : $item;
            $update .= ');';

            if($result = $this->_db->query($update)) {
                $id = $this->_db->insert_id;

                if(isset($data['categorias']) && sizeof($data['categorias'])>0){
                    $sql_add_categories = 'INSERT INTO `noticias__categorias_rel`(`id_categoria`, `id_new`) VALUES ';
                    foreach($data['categorias'] as $cat)
                        $sql_add_categories .= '("'.$cat.'","'.$id.'"),';
                    $sql_add_categories = substr($sql_add_categories, 0, -1);
                    $sql_add_categories .= ';';
                }


                //Insert new lang vars
                $updateLangs = array();
                if(sizeof($sql_langs)>0) {
                    foreach ($sql_langs as $k=>$item)  {
                        $sqlUpdate[$k] = 'INSERT INTO noticias__langs (id_new, id_lang, ';
                        foreach ($item as $kkk=>$sqq) $sqlUpdate[$k] .= $kkk.',';
                        $sqlUpdate[$k] = substr($sqlUpdate[$k], 0, -1);
                        $sqlUpdate[$k] .= ') VALUES ("'.$id.'", "'.$k.'", ' ;
                        foreach ($item as $kkk=>$sqq) $sqlUpdate[$k] .= '"'.$sqq.'",';
                        $sqlUpdate[$k] =substr($sqlUpdate[$k], 0, -1);
                        //SI EXISTE ACTUALIZA
                        $sqlUpdate[$k] .= ') ON DUPLICATE KEY UPDATE ' ;
                        foreach ($item as $kkk=>$sqq)
                            $sqlUpdate[$k] .= $kkk.'="'.$sqq.'",';
                        $sqlUpdate[$k] = substr($sqlUpdate[$k], 0, -1);
                    }
                }

                //UPDATE LANGS
                foreach($sqlUpdate as $sql_lang) {
                    //echo $sql_lang;
                    if(!$this->_db->query($sql_lang))
                        $errors[]='a habido un error modificando los lenguajes.'.@$this->_db->error;
                }

                //REMOVE CATEGORIES
                if(!$this->_db->query('DELETE FROM `noticias__categorias_rel` WHERE `id_new`='.$id)) $errors[]='a habido un error borrando las categorías.'.@$this->_db->error;

                //ADD CATEGORIES
                if(isset($sql_add_categories)) {
                    if(!$this->_db->query($sql_add_categories)) $errors[]='Ha habido un error añadiendo las categorías.'.@$this->_db->error;
                }

            }else  $errors[]='Ha habido un error modificando la noticia base.'.@$this->_db->error;



            if(!$errors) {

                return array('error'=>0, 'msg'=>'Se ha insertado existosamente. ID '.@$id, 'id'=>$id);
            }
            return array('error'=>1, 'msg'=>'No insertado', 'errors'=>$errors);
        }
        //Return Data
        return array('error'=>1, 'msg'=>'No insertado', 'errors'=>$errors);
    }

    public function updateNoticia($data=array()) {
        //print_r($data);
        //Errors Array
        $errors = array();
        //SQL Vars Array
        $sql = array();
        //Gallery array
        $g = array();

        /*VARS SQL*/
        /*REQUIRED*/

        if(empty($data['id'])) array_push($errors,'No hay ID.');

        if(empty($data['fecha'])) array_push($errors,'No hay fecha.');
        else $sql[] = 'fecha="'.strtotime($data['fecha']).'"';

        $sql[] = 'publicar="'.$data['publicar'].'"';

        $sql[] = 'destacada="'.$data['destacada'].'"';

        if(empty($data['slug'])) array_push($errors,'Falta el slug.');
        else $sql[] = 'slug="'.$data['slug'].'"';




        /*NON REQUIRED*/
        if(!empty($data['imagen'])) $sql[] = 'img_full="'.$data['imagen'].'"';
        if(!empty($data['thumb'])) $sql[] = 'img_thumb="'.$data['thumb'].'"';



        /*END VARS SQL*

        /*VARS LANG*/
        $vars_langs = array_keys($data['langs']);

        //echo '<pre>'; print_r($data); echo '</pre>';

        foreach($vars_langs as $lang) {


            if(empty($data['langs'][$lang]['descripcion'])) array_push($errors,'No hay descripción en el lenguaje '.$lang);
            else  {
                $sql_langs[$lang]['descripcion'] = htmlspecialchars($data['langs'][$lang]['descripcion']);
            }

            if(empty($data['langs'][$lang]['descripcion_corta'])) array_push($errors,'No hay descripción corta en el lenguaje '.$lang);
            else  {
                $sql_langs[$lang]['descripcion_corta'] = ($data['langs'][$lang]['descripcion_corta']);
            }

            if(empty($data['langs'][$lang]['titulo'])) array_push($errors,'No hay titulo en el lenguaje '.$lang);
            else  {
                $sql_langs[$lang]['titulo'] = $data['langs'][$lang]['titulo'];
            }

        }

        if(sizeof($data['categorias'])>0){
            $sql_add_categories = 'INSERT INTO `noticias__categorias_rel`(`id_categoria`, `id_new`) VALUES ';
            foreach($data['categorias'] as $cat)
                $sql_add_categories .= '("'.$cat.'","'.$data['id'].'"),';
            $sql_add_categories = substr($sql_add_categories, 0, -1);
            $sql_add_categories .= ';';
        }
        //echo $sql_add_categories;


        /*END VARS SQL*/

        //echo '<pre>'; print_r($sql_langs);	echo '</pre>';

        if(!isset($errors) || sizeof($errors)==0) {

            //Update new
            $update = 'UPDATE noticias__list SET ';
            if(sizeof($sql)>0)
                foreach ($sql as $k=>$item)
                    $update .= ($k != (sizeof($sql)-1)) ? $item.', ' : $item;
            $update .= ' WHERE id="'.$data['id'].'";';

            //Insert new lang vars
            $updateLangs = array();
            if(sizeof($sql_langs)>0) {
                foreach ($sql_langs as $k=>$item)  {
                    $sqlUpdate[$k] = 'INSERT INTO noticias__langs (id_new, id_lang, ';
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



            //echo $sqlUpdate;
            foreach($sqlUpdate as $sql_lang)
                if(!$this->_db->query($sql_lang))
                    $errors[]=@$this->_db->error;

            if(!$this->_db->query($update))
                $errors[]=@$this->_db->error;

            //REMOVE CATEGORIES
            if(!$this->_db->query('DELETE FROM `noticias__categorias_rel` WHERE `id_new`='.$data['id'])) $errors[]='a habido un error borrando las categorías.'.@$this->_db->error;

            //ADD CATEGORIES
            if(isset($sql_add_categories)){
                if(!$this->_db->query($sql_add_categories)) $errors[]='Ha habido un error añadiendo las categorías.'.@$this->_db->error;
            } else $errors[]='No hay categorias';


            if(!$errors)
                return array('error'=>0, 'msg'=>'Se ha actualizado existosamente');

            return array('error'=>1, 'msg'=>'No actualizado', 'errors'=>$errors);
        }
        //Return Data
        return array('error'=>1, 'msg'=>'No actualizado', 'errors'=>$errors);
    }

    public function deleteNoticia($params) {
        if(!isset($params['id']) || empty($params['id'])) $errors[]='No hay ID.';

        if(!isset($errors) || !sizeof($errors)>0) {
            $this->_db->autocommit(FALSE);
            if($result = $this->_db->query("DELETE FROM noticias__list WHERE id='{$params['id']}';"))  {
                $this->_db->commit();
                $this->_db->autocommit(TRUE);
                //Return Data
                return array('error'=>0, 'msg'=>'Borrado satisfactoriamente');

            }else {
                $this->_db->rollBack();
                $this->_db->autocommit(TRUE);
                return array('error'=>1, 'msg'=>'No deleted');
            }
        }

        return array('error'=>1, 'msg'=>'No deleted');
    }

    public function existsId ($id) {
        $errors=array();
        if(!isset($id) || $id=='') $errors[]='No hay slug';

        if(!sizeof($errors)>0){
            $news = array();

            $sql = 'SELECT * FROM  noticias__list WHERE id="'.$id.'" LIMIT 1;';
            if($result = $this->_db->query($sql))  {
                if($result->num_rows>0) return true;
            }
        }
        return false;
    }

}

?>