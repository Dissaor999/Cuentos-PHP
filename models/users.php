<?php

class users extends Model {



	public function __construct(){
		parent::__construct();
	}

    public function getData($data=array()) {

        $ssql='';
        $w_sql=array();
        if(isset($data['id']) && $data['id']!=null) $w_sql[]=' AND u.id="'.$data['id'].'"';


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
            *
          FROM users  u 
          INNER JOIN user__entity ue ON ue.user_id = u.id
          WHERE  1=1 '.@$data['sql'].' '.$ssql;

      // echo $select;

        $items=array();

        if($result = $this->_db->query($select)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)){

                /*GET Entity*/


                array_push($items,$row);
            }
            if(!isset($id))
                return array('error'=>0, 'message'=>'','data'=>$items);
            else return($items[0]);
        }else return array('error'=>1, 'message'=>'No items. '.$this->_db->error);

    }

    public function existsId ($id) {
        $errors=array();
        if(!isset($id) || $id=='') $errors[]='No hay slug';

        if(!sizeof($errors)>0){
             $sql = 'SELECT * FROM  users  WHERE id="'.$id.'" LIMIT 1;';
            if($result = $this->_db->query($sql))  {
                if($result->num_rows>0) return true;
            }
        }
        return false;
    }

    public function check_username ($id) {
        $errors=array();
        if(!isset($id) || $id=='') $errors[]='No hay slug';

        if(!sizeof($errors)>0){
             $sql = 'SELECT * FROM  users u  INNER JOIN user__entity ue ON ue.user_id = u.id  WHERE u.login="'.$id.'" LIMIT 1;';
            if($result = $this->_db->query($sql))  {
                if($result->num_rows>0) return $result->fetch_Array(1);
            }
        }
        return false;
    }

    //ADMIN FUNCTIONS
    public function update($data=array()) {
        $errors = array();$sql = array();$g = array();

        if(empty($data['id'])) array_push($errors,'No hay ID.');

        $sql[] = 'login="'.$data['username'].'"';
        $sql[] = 'password="'.md5($data['password']).'"';
        $sql[] = 'active="'.$data['active'].'"';
        $sql[] = 'banned="'.$data['banned'].'"';
        $sql[] = 'newsletter="'.$data['newsletter'].'"';

        $sql2[] = 'email="'.$data['email'].'"';
        $sql2[] = 'first_name="'.$data['first_name'].'"';
        $sql2[] = 'last_name="'.$data['last_name'].'"';
        $sql2[] = 'gender="'.$data['gender'].'"';
        $sql2[] = 'phone="'.$data['phone'].'"';
        $sql2[] = 'address="'.$data['address'].'"';
        $sql2[] = 'address_bill="'.$data['address_bill'].'"';
        $sql2[] = 'mobile="'.$data['mobile'].'"';
        $sql2[] = 'user_lang="'.$data['user_lang'].'"';

        /*NON REQUIRED*/
        if(!empty($data['thumb'])) $sql[] = 'photo="'.$data['thumb'].'"';



        if(!isset($errors) || sizeof($errors)==0) {
            $update = 'UPDATE users SET ';
            if(sizeof($sql)>0)
                foreach ($sql as $k=>$item)
                    $update .= ($k != (sizeof($sql)-1)) ? $item.', ' : $item;
            $update .= ' WHERE id="'.$data['id'].'";';

            $update_entity = 'UPDATE user__entity SET ';
            if(sizeof($sql2)>0)
                foreach ($sql2 as $k=>$item)
                    $update_entity .= ($k != (sizeof($sql2)-1)) ? $item.', ' : $item;
            $update_entity .= ' WHERE user_id="'.$data['id'].'";';



            if(!$this->_db->query($update))  $errors[]=@$this->_db->error;
            if(!$this->_db->query($update_entity))  $errors[]=@$this->_db->error;


            if(!$errors)
                return array('error'=>0, 'msg'=>'Se ha actualizado existosamente');

            return array('error'=>1, 'msg'=>'No actualizado', 'errors'=>$errors);
        }
        //Return Data
        return array('error'=>1, 'msg'=>'No actualizado', 'errors'=>$errors);
    }

    //ADMIN FUNCTIONS
    public function setRecoverCode($username) {

        if(!isset($errors) || sizeof($errors)==0) {
            $recovery = md5('username'.$username);
            $fecha = date('Y-m-j');
            $time = strtotime ( '+1 hour' , strtotime ( $fecha ) ) ;

            $update = 'UPDATE users SET `recovery-code` = "'.$recovery.'", `recovery-time` ="'.$time.'"  WHERE login="'.$username.'";';

            //echo $update;
            if(!$this->_db->query($update))  $errors[]=@$this->_db->error;
            else return true;

            if(!$errors)   return  false;
            return  false;
        }
        //Return Data
        return array('error'=>1, 'msg'=>'No actualizado', 'errors'=>$errors);
    }
  //ADMIN FUNCTIONS
    public function changePass($pass, $token) {

        if(!isset($errors) || sizeof($errors)==0) {


            $update = 'UPDATE users SET `password` = md5("'.$pass.'")  WHERE `recovery-code`="'.$token.'";';

            //echo $update;
            if($this->_db->query($update))return true;
            return  false;
        }
        return  false;
    }


    public function checkToken($token) {
        if(!isset($errors) || sizeof($errors)==0)
            if($this->_db->query('SELECT * FROM users WHERE  `recovery-code` = "'.$token.'";'))  return true;
        return  false;
    }

    //ADMIN FUNCTIONS
    public function add($data=array()) {
        $errors = array(); $sql = array(); $g = array();


        $sql['login'] = '"'.$data['username'].'"';
        $sql['password'] = '"'.md5($data['password']).'"';
        $sql['active'] = '"'.$data['active'].'"';
        $sql['banned'] = '"'.$data['banned'].'"';
        $sql['newsletter'] = '"'.$data['newsletter'].'"';
        $sql['photo'] = '"'.$data['thumb'].'"';

        $sql2['email'] = '"'.$data['email'].'"';
        $sql2['first_name'] = '"'.$data['first_name'].'"';
        $sql2['last_name'] = '"'.$data['last_name'].'"';
        $sql2['gender'] = '"'.$data['gender'].'"';
        $sql2['phone'] = '"'.$data['phone'].'"';
        $sql2['address'] = '"'.$data['address'].'"';
        $sql2['address_bill'] = '"'.$data['address_bill'].'"';
        $sql2['mobile'] = '"'.$data['mobile'].'"';
        $sql2['user_lang'] = '"'.$data['user_lang'].'"';



        if(!isset($errors) || sizeof($errors)==0) {

            $add = 'INSERT INTO `users`(`login`, `password`, `active`, `banned`, `newsletter`, `photo`) VALUES (
                          '.$sql['login'].',
                          '.$sql['password'].',
                          '.$sql['active'].',
                          '.$sql['banned'].',
                          '.$sql['newsletter'].',
                          '.$sql['photo'].'
                      );';



            if(!$this->_db->query($add))
                $errors[]=@$this->_db->error;
            $id = $this->_db->insert_id;


            $add_entity = 'INSERT INTO `user__entity`( `user_id`, `email`, `first_name`, `last_name`, `gender`, `phone`, `address`, `address_bill`, `mobile`, `user_lang`) VALUES (
                          '.$id.',
                          '.$sql2['email'].',
                          '.$sql2['first_name'].',
                          '.$sql2['last_name'].',
                          '.$sql2['gender'].',
                          '.$sql2['phone'].',
                          '.$sql2['address'].',
                          '.$sql2['address_bill'].',
                          '.$sql2['mobile'].',
                          '.$sql2['user_lang'].'
                      );';

            if(!$this->_db->query($add_entity))
                $errors[]=@$this->_db->error;


            if(isset($data['roles']) && is_array($data['roles'])) {
                $roles =array();
                foreach($data['roles'] as $role)   $roles[] .= '('.$id.','.$role.')';
                $add_roles = 'INSERT INTO `USER__roles`( `user_id`, `role_id`) VALUES '.implode(',',$roles).';';


                if(!$this->_db->query($add_roles))
                    $errors[]=@$this->_db->error; }

            if(!$errors)
                return array('error'=>0);

            return array('error'=>1, 'errors'=>$errors);
        }
        //Return Data
        return array('error'=>1, 'errors'=>$errors);
    }

    //ADMIN FUNCTIONS
    public function delete($data=array()) {
        $errors = array();
        $sql = array();
        $g = array();

        if(empty($data['id'])) array_push($errors,'No hay ID.');



        //REMOVE FILTERS
        if(!$this->_db->query('DELETE FROM `user__entity` WHERE `user_id`='.$data['id']))
            $errors[]='Ha habido un error borrando los detalles de la cuenta.'.@$this->_db->error;
        else
            if(!$this->_db->query('DELETE FROM `user` WHERE `id`='.$data['id']))
                $errors[]='Ha habido un error borrando la cuenta.'.@$this->_db->error;



        if(!$errors)
            return array('error'=>0, 'msg'=>'Se ha actualizado existosamente');

        //Return Data
        return array('error'=>1, 'msg'=>'No actualizado', 'errors'=>$errors);
    }
}

?>