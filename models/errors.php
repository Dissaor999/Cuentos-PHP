<?php

class errorsModel extends Model {

	
	public function __construct(){
		parent::__construct();
	}
	
	/*GET ALL DATA*/
	public function getErrors($lang) {
		$messages = array();
		$sql = 'SELECT fm.* FROM form__messages fm WHERE fm.lang="'.$lang.'";';
		//echo $sql;
		if($result = $this->_db->query($sql))  {	
			while($row = $result->fetch_array(MYSQLI_ASSOC)) 
				$messages[$row['key']] = $row['value'];
			return array('error'=>0, 'data'=>$messages); 
		}else return array('error'=>1, 'msg'=>'No messages. '.$this->_db->error);
	}
	
	
}

?>