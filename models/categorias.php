<?php

class categorias extends Model {

	
	public function __construct(){
		parent::__construct();
	}
	

	public function getDataCategorias($lang) {
		$categories = array();
		$sql = 'SELECT * FROM  noticias__categorias nc INNER JOIN noticias__categorias_langs ncl ON ncl.id_categoria=nc.id AND ncl.id_lang="'.$lang.'" ORDER BY id ASC;';
		if($result = $this->_db->query($sql)) {
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				array_push($categories,$row);
			}
			
			return array('error'=>0, 'data'=>$categories); 
		}else return array('error'=>1, 'msg'=>'No news. '.$this->_db->error);
	}
	
	public function getDataCategoria($categoria,$lang) {
		$categories = array();
		$sql = 'SELECT * FROM  noticias__categorias nc INNER JOIN noticias__categorias_langs ncl ON ncl.id_categoria=nc.id AND ncl.id_lang="'.$lang.'" WHERE nc.slug="'.$categoria.'" ORDER BY id ASC;';
		if($result = $this->_db->query($sql)) {

			
			return array('error'=>0, 'data'=>$categories); 
		}else return array('error'=>1, 'msg'=>'No news. '.$this->_db->error);
	}

    public function insert_category($data=array()) {
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
        $sql[] = '"'.($data['slug']).'"';




        /*END VARS SQL*

        /*VARS LANG*/
        $vars_langs = array_keys($data['langs']);

        foreach($vars_langs as $lang) {
           $sql_langs[$lang]['nombre'] = $data['langs'][$lang]['nombre'];
        }


        /*END VARS SQL*/

        if(!isset($errors) || sizeof($errors)==0) {

            //Update new
            $update = 'INSERT INTO noticias__categorias (slug) VALUES (';
            if(sizeof($sql)>0)
                foreach ($sql as $k=>$item)
                    $update .= ($k != (sizeof($sql)-1)) ? $item.', ' : $item;
            $update .= ');';

            if($result = $this->_db->query($update)) {
                $id = $this->_db->insert_id;


                if(sizeof($sql_langs)>0) {
                    foreach ($sql_langs as $k=>$item)  {
                        $sqlUpdate[$k] = 'INSERT INTO noticias__categorias_langs (id_categoria, id_lang, ';
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
                        $errors[]='Ha habido un error.'.@$this->_db->error;
                }

            }else  $errors[]='Ha habido un error.'.@$this->_db->error;

            if(!$errors) {
                return array('error'=>0, 'msg'=>'Se ha insertado existosamente. ID '.@$id, 'id'=>$id);
            }
            return array('error'=>1, 'msg'=>'No insertado', 'errors'=>$errors);
        }
        //Return Data
        return array('error'=>1, 'msg'=>'No insertado', 'errors'=>$errors);
    }

    public function update_category($data=array()) {
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

        $sql[] = 'slug="'.$data['slug'].'"';


        $vars_langs = array_keys($data['langs']);

        foreach($vars_langs as $lang) {
            $sql_langs[$lang]['nombre'] = $data['langs'][$lang]['nombre'];
        }


        if(!isset($errors) || sizeof($errors)==0) {

            //Update new
            $update = 'UPDATE noticias__categorias SET ';
            if(sizeof($sql)>0)
                foreach ($sql as $k=>$item)
                    $update .= ($k != (sizeof($sql)-1)) ? $item.', ' : $item;
            $update .= ' WHERE id="'.$data['id'].'";';

            //Insert new lang vars
            $updateLangs = array();
            if(sizeof($sql_langs)>0) {
                foreach ($sql_langs as $k=>$item)  {
                    $sqlUpdate[$k] = 'INSERT INTO noticias__categorias_langs (id_categoria, id_lang, ';
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


            if(!$errors)
                return array('error'=>0, 'msg'=>'Se ha actualizado existosamente');

            return array('error'=>1, 'msg'=>'No actualizado', 'errors'=>$errors);
        }
        //Return Data
        return array('error'=>1, 'msg'=>'No actualizado', 'errors'=>$errors);
    }

    public function delete_category($params) {
        if(!isset($params['id']) || empty($params['id'])) $errors[]='No hay ID.';

        if(!isset($errors) || !sizeof($errors)>0) {
            $this->_db->autocommit(FALSE);
            if($r=$this->_db->query("DELETE FROM noticias__categorias WHERE id='{$params['id']}';"))  {
                if($r=$this->_db->query("DELETE FROM noticias__categorias_langs WHERE id_categoria='{$params['id']}';")) {
                    $this->_db->commit();
                    $this->_db->autocommit(TRUE);
                    //Return Data
                    return array('error'=>0, 'msg'=>'Borrado satisfactoriamente');
                }
                else {
                    $this->_db->rollBack();
                    $this->_db->autocommit(TRUE);
                    return array('error'=>1, 'msg'=>'No deleted');
                }
            }else {
                $this->_db->rollBack();
                $this->_db->autocommit(TRUE);
                return array('error'=>1, 'msg'=>'No deleted');
            }
        }

        return array('error'=>1, 'msg'=>'No deleted');
    }




    public function getData($args=array()) {
	    if(isset($args['id'])) $_sql[]=' AND nc.id="'.$args['id'].'"';
        $categories=array();
		$sql = 'SELECT * FROM  noticias__categorias nc INNER JOIN noticias__categorias_langs ncl ON ncl.id_categoria=nc.id  WHERE 1=1 '.implode(' ', $_sql).' ORDER BY nc.id ASC;';
		 if($result = $this->_db->query($sql)) {
             while($row = $result->fetch_array(MYSQLI_ASSOC)){
                 $sql_langs = 'SELECT * FROM  noticias__categorias_langs WHERE id_categoria = '.$row['id'].';';
                 if($result_langs = $this->_db->query($sql_langs)) {
                     while($row_langs = $result_langs->fetch_array(MYSQLI_ASSOC)){
                         $row['langs'][$row_langs['id_lang']]=$row_langs;
                     }
                 }
                 $categories[]=$row;
             }

         }

        return $categories;
	}

}

?>