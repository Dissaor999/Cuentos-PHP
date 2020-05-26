<?php

class categoriasModel extends Model {

	
	public function __construct(){
		parent::__construct();
	}
	

	public function getDataCategorias($lang) {
		$categories = array();
		$sql = 'SELECT * FROM  NOTICIAS__categorias nc INNER JOIN NOTICIAS__categorias_langs ncl ON ncl.id_categoria=nc.id AND ncl.id_lang="'.$lang.'" ORDER BY id ASC;';
		if($result = $this->_db->query($sql)) {
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				array_push($categories,$row);
			}
			
			return array('error'=>0, 'data'=>$categories); 
		}else return array('error'=>1, 'msg'=>'No news. '.$this->_db->error);
	}
	
	public function getDataCategoria($categoria,$lang) {
		$categories = array();
		$sql = 'SELECT * FROM  NOTICIAS__categorias nc INNER JOIN NOTICIAS__categorias_langs ncl ON ncl.id_categoria=nc.id AND ncl.id_lang="'.$lang.'" WHERE nc.slug="'.$categoria.'" ORDER BY id ASC;';
		if($result = $this->_db->query($sql)) {
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				array_push($categories,$row);
			}
			
			return array('error'=>0, 'data'=>$categories); 
		}else return array('error'=>1, 'msg'=>'No news. '.$this->_db->error);
	}
	
}

?>