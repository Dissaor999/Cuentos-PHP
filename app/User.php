<?php

class Cuser {
    public function __construct() {}


    static function has_access($module) {
        if(Session::get('auth')) {
            $role = Cuser::getroles(Session::get('user_id'));
            if (!Cuser::getLevel($module , $role))  return false;
        }
        else {
            if (!Cuser::getLevel($module,array(3))) return false;
        }
        return true;
    }

    static function getroles($user=null) {
        global $db;
        if($user==null)return null;
        else {
            $sql = 'SELECT ur.role_id  FROM user__roles ur  WHERE ur.user_id="'.$user.'";';
            $role_list=array();
            foreach($db->query($sql)->fetch_all(MYSQLI_ASSOC) as $k=>$v) $role_list[]=$v['role_id'];
            return $role_list;
        }
        return null;

    }


    static function get_string($what) {
           global $db;
            $sql = 'SELECT '.$what.'  FROM  user__entity ue   WHERE ue.user_id="'.Session::getId().'";';
            $role_list=array();
            foreach($db->query($sql)->fetch_all(MYSQLI_ASSOC) as $k=>$v) $role_list[]=$v[$what];
            if($role_list[0]!=null) return $role_list[0];
            return null;

    }

    static function getName() {return Cuser::get_string('first_name');}
    static function getSurnames() {return Cuser::get_string('last_name');}
    static function getPhone() {return Cuser::get_string('phone');}
    static function getMobile() {return Cuser::get_string('mobile');}
    static function getGender() {return Cuser::get_string('gender');}
    static function getEmail() {return Cuser::get_string('email');}
    static function getPhoto() {return Cuser::get_string('photo');}
    static function getFacLoc() {return Cuser::get_string('address_bill');}
    static function getAddr() {return Cuser::get_string('address');}





    static function getLevel($module,$role) {
        global $db;
        $sql = 'SELECT rr.role_id FROM roles__rights rr INNER JOIN rights r on rr.right_id=r.id AND r.name="'.$module.'" WHERE rr.role_id IN ("'. implode(' , ',$role).'");';
        $result = $db->query($sql)->fetch_array(MYSQLI_ASSOC);
        if(sizeof($result)>0)
            return 1;

        return 0;

    }

    static function make_login($user,$pass) {
        global $db;
        $sql = 'SELECT * FROM users u INNER JOIN user__entity ue on ue.user_id = u.id  WHERE u.login = "'.$user.'" AND u.password=md5("'.$pass.'");';
        $result = $db->query($sql)->fetch_array(MYSQLI_ASSOC);

        if(sizeof($result)>0)
            return $result;

        return null;

    }

}