<?php
class Languages extends Request {

	public $_langs;

	public function __construct($pet) {
	}
	
	public static function existLangDB($l) {
        global $db;
		$sql = 'SELECT tagname FROM languages WHERE lower(tagname)="'.$l.'";';
		if($result = $db->query($sql))  if($result->num_rows>0) return true;
		return false;
	}
	
	public static function getDefaultLang() {
        global $db;
		$sql = 'SELECT tagname FROM languages WHERE is_default = 1;';

		if($result = $db->query($sql)) {
            if ($result->num_rows > 0) {
                $rw = $result->fetch_array(MYSQLI_ASSOC);
                return $rw['tagname'];
            }
        }
		return NULL;
	}
	public static function getLangsDB() {
        global $db;
		$langs = Array();
		$sql = 'SELECT * FROM languages;';

		if($result = $db->query($sql)) while($row = $result->fetch_array(MYSQLI_ASSOC)) array_push($langs,$row);
		return $langs;
	
	}

	public function getDataLang($lang) {
		
		if($this->existLangDB($lang)) {
            global $db;
			$langs = Array();
			$sql = 'SELECT * FROM languages_defaults WHERE id_lang = "'.$lang.'";';
			if($result = $db->query($sql))  {
				while($row = $result->fetch_array(MYSQLI_ASSOC)) $langs[$row['key']]=$row['value'];		
				return $langs;
			}
		}
		return NULL;
	}

	public static function getString($lang, $key,$string) {
        global $db;
        $sql = 'SELECT value FROM languages_defaults WHERE module="'.$key.'" AND `key`="'.$string.'";';

        if($result = $db->query($sql)) {
            $val = $result->fetch_array(MYSQLI_ASSOC);
            return $val['value'];
        }
        else return '';
	}

}
?>