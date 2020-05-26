<?php

class formsModel extends Model {

	
	public function __construct(){
		parent::__construct();
	}

	
	public function reserva($data=array()) {
		$insert = 'INSERT INTO form__reservas_restaurante (`lang`, `nombre_apellidos`, `email`, `telefono`, `dia`, `hora`, `comentarios` ) VALUES 
		(
			"'.$data['lang'].'",
			"'.$data['nombre_apellidos'].'",
			"'.$data['email'].'",
			"'.$data['telefono'].'",
			"'.$data['dia'].'",
			"'.$data['hora'].'",
			"'.$data['comentarios'].'"
		)';

		if($this->_db->query($insert))
			return true;
		return false;
	}
	public function contacto($data=array()) {
		$insert = 'INSERT INTO form__contacto (`lang`, `nombre`, `email`, `asunto`, `dept`,  `message` ) VALUES 
		(
			"'.$data['lang'].'",
			"'.$data['name'].'",
			"'.$data['email'].'",
			"'.$data['asunto'].'",
			"'.$data['dept'].'",
			"'.$data['message'].'"
		)';

		if($this->_db->query($insert))
			return true;
		return false;
	}
	
	
	public function reserva_vip($data=array()) {
		$insert = 'INSERT INTO form__reservas_vip (`lang`, `nombre_apellidos`, `email`, `telefono`, `dia`, `comentarios` ) VALUES 
		(
			"'.$data['lang'].'",
			"'.$data['nombre_apellidos'].'",
			"'.$data['email'].'",
			"'.$data['telefono'].'",
			"'.$data['dia'].'",
			"'.$data['comentarios'].'"
		)';

		if($this->_db->query($insert))
			return true;
		return false;
	}
	
	
	public function trabaja($data=array()) {
		$insert = 'INSERT INTO form__solicitud_trabajo (`lang`, `nombre_apellidos`, `email`, `telefono`, `comentarios`, `archivo`) VALUES 
		(
			"'.$data['lang'].'",
			"'.$data['nombre_apellidos'].'",
			"'.$data['email'].'",
			"'.$data['telefono'].'",
			
			"'.$data['comentarios'].'",
			"'.$data['archivo'].'"
		)';

		if($this->_db->query($insert))
			return true;
		return false;
	}
	
	
	
	/*VALIDATIONS*/
	
	public function validateDate($date) {
		if(preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $date))
			return true;
		return false;
	}
	
	public function validateTitle($title) {
		if(preg_match('/^[a-zA-Z0-9áéíóúàèìòùäëïöüÁÉÍÓÚÀÈÌÒÙÄËÏÖÜ,.\'_\s]{1,150}$/', $title))
			return true;
		return false;
	}
	
	public function validateDesc($desc) {	
		if(preg_match('/^.{8,2000}$/', $desc))
			return true;
		return false;
	}
	public function validateShortDesc($desc) {	
		if(preg_match('/^.{8,250}$/', $desc))
			return true;
		return false;
	}
	
	public function validateVisible($vis) {	
		if($vis==0 || $vis==1)
			return true;
		return false;
	}
	public function validateSlug($slug) {	
		
		if(preg_match('/[a-z]{1}[a-z0-9\-]*/i', $slug))
			return true;
		return false;
		
	}
	
	public function slugify($text) {	
		// replace non letter or digits by -
		$text = preg_replace('~[^\\pL\d]+~u', '-', $text);
	 
		// trim
		$text = trim($text, '-');
	 
		// transliterate
		if (function_exists('iconv'))
			$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
	 
		// lowercase
		$text = strtolower($text);
	 
		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);
	 
		if (empty($text))
			return 'n-a';
	 
		return $text;
	}
}

?>