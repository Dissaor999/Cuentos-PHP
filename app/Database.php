<?php

class Database extends mysqli {

    public function __construct() {

        parent::__construct(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

    
		$this->set_charset(DB_CHAR);

        if (mysqli_connect_error()) {
            die('Error de conexión (' . mysqli_connect_errno() . ') '   . mysqli_connect_error());
        }


    }

    public static function execute($sql) {
        $db = new Database();
       return $db->query($sql);


    }


}
