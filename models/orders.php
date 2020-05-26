<?php

class orders extends Model
{
    public function __construct()
    {
        parent::__construct();
        if (!CUser::has_access('orders')) return false;
    }

    function getLangs($langs, $id)
    {

        $data = array();
        if ($langs) {
            $sql_langs = 'SELECT pl.* FROM  product__langs pl WHERE pl.product_id = "' . $id . '";';
            if ($result_langs = $this->_db->query($sql_langs)) {
                while ($row_langs = $result_langs->fetch_array(MYSQLI_ASSOC)) {
                    $data[$row_langs['lang_id']] = $row_langs;
                }
            }
        }
        return $data;
    }


    function getOrderData($id , $lang) {
        $data =array();

        $sql = 'SELECT 
                    ob.*,
                     li.slug as "book_slug",
                     li.name as "book_name",
                     lf.nombre as "format_name",
                     lf.precio as "format_price"
              FROM  orders__books ob 
                  INNER JOIN glasses_style gs ON gs.id = ob.glasses_id
                  INNER JOIN eyes_color ec ON ec.id = ob.eyes_id
                  INNER JOIN skin_colors sc ON sc.id = ob.skin_id
                  INNER JOIN hair_color hc ON hc.id = ob.hair_color_id
                  INNER JOIN hair_style hs ON hs.id = ob.hair_style_id
                  INNER JOIN clothes_color cc ON cc.id = ob.clothes_id
                  INNER JOIN gender_types gt ON gt.id = ob.gender_id
                  INNER JOIN libros li ON li.id = ob.id_book
                  INNER JOIN libros_formatos lf ON lf.id = ob.format_id
                  
                   WHERE ob.id_order = "'.$id.'";';
        //echo $sql;
        if($result = $this->_db->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                //$row['combination']=$this->getCombination( $row['combination_id']);
                $data[]=$row;
            }
        }

        return $data;
    }


    function getOrdersNotPrinted() {
        $data =array();

        $sql = 'SELECT distinct
 uo.*, pm.name as "metodo_pago", ov.code, ov.name as "discount_name", ov.quantity as "discount_quantity"
         FROM orders uo
            INNER JOIN pay_methods pm ON uo.paymethod = pm.id
            INNER JOIN order_status os ON uo.status= os.id 
            LEFT JOIN orders__vouchers ov ON uo.voucher_id= ov.id   
            INNER JOIN orders__books ob ON  ob.id_order = uo.id  AND (ob.format_id = 2 OR ob.format_id=3)
          WHERE uo.impresion_sent=0 AND uo.status BETWEEN 3 AND 4;';
        //echo $sql;
        if($result = $this->_db->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $row['books']=$this->get_order_books_byID_ORDER($row['id'],false);
                $data[]=$row;
            }
        }
        return $data;
    }

    function get_order_books_byID_ORDER($id,$md5=false) {
        if(!$md5)$id=md5($id);
        $data=array();
        $sql = '
          SELECT 
                ob.*, 
                gs.img_src as "gs", 
                ec.img_src as "ec", 
                sc.img_src as "sc", 
                hc.img_src as "hc", 
                lhs.img_src as "hs", 
                hsf.style_folder as "hsf", 
                cc.img_src as "cc", 
                li.slug as "book_slug",
                li.name as "book_name",
                li.id  as "book_id",
                 lf.nombre as "format_name",
                 lf.precio as "format_price"
            
            FROM orders__books ob 
            
                INNER JOIN libro_glasses_style gs ON gs.glasses_id = ob.glasses_id  AND gs.libro_id=ob.id_book
                INNER JOIN libro_eyes_color ec ON ec.ref_id = ob.eyes_id  AND ec.libro_id=ob.id_book AND ec.gender_id=ob.gender_id
                INNER JOIN libro_skin_colors sc ON sc.ref_id = ob.skin_id  AND sc.libro_id=ob.id_book AND sc.gender_id=ob.gender_id
                INNER JOIN libro_hair_color hc ON hc.ref_id = ob.hair_color_id  AND hc.libro_id=ob.id_book AND hc.gender_id=ob.gender_id
                INNER JOIN libro_hair_style lhs ON lhs.ref_id = ob.hair_style_id  AND lhs.libro_id=ob.id_book AND lhs.gender_id=ob.gender_id
                INNER JOIN hair_style hsf ON hsf.id = ob.hair_style_id 
                INNER JOIN libro_clothes_color cc ON cc.ref_id = ob.clothes_id  AND cc.libro_id=ob.id_book AND cc.gender_id=ob.gender_id
                INNER JOIN libros li ON li.id = ob.id_book 
                INNER JOIN libros_formatos lf ON lf.id = ob.format_id
            
            WHERE md5(ob.id_order)= "'.$id.'";';


        //echo $sql;

        if($result = $this->_db->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                 $data[]=$row;
            }
        }
        return $data;
    }

    function get_book_order($id,$md5=false) {
        if(!$md5)$id=md5($id);
        $data=array();
        $sql = 'SELECT DISTINCT
            ob.*, 
            gs.img_src as "gs", 
            ec.img_src as "ec", 
            sc.img_src as "sc", 
            hc.img_src as "hc", 
            lhs.img_src as "hs", 
            thsf.style_folder as "hsf", 
            ccc.img_src as "cc", 
            li.slug as "book_slug",
            li.name as "book_name",
            li.id  as "book_id"
            
            
            FROM orders__books ob 
            
            INNER JOIN libro_glasses_style gs ON gs.glasses_id = ob.glasses_id 
            INNER JOIN libro_eyes_color ec ON ec.ref_id = ob.eyes_id 
            INNER JOIN libro_skin_colors sc ON sc.ref_id = ob.skin_id 
            INNER JOIN libro_hair_color hc ON hc.ref_id = ob.hair_color_id  AND hc.libro_id = ob.id_book
            INNER JOIN libro_hair_style lhs ON lhs.ref_id = ob.hair_style_id  AND lhs.libro_id = ob.id_book
            INNER JOIN hair_style thsf ON thsf.id = ob.hair_style_id 
            INNER JOIN libro_clothes_color ccc ON ccc.ref_id = ob.clothes_id AND ccc.libro_id = ob.id_book
            INNER JOIN libros li ON li.id = ob.id_book 
            WHERE md5(ob.id) = "'.$id.'";';

        if($result = $this->_db->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)){

                   return $row;
            }
        }
    }

    function getOrderUser($id) {
        $data =array();

        $sql = 'SELECT 
            *
          FROM users  u 
          INNER JOIN user__entity ue ON ue.user_id = u.id
          WHERE  1=1 AND u.id= "'.$id.'";';

        if($result = $this->_db->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $data[]=$row;
            }
        }

        return $data;
    }

    public function getData($data=array(),$lang='es') {
       // print_r($data);
        $ssql=''; $w_sql=array();

        //Wheres
        if(isset($data['id']) && $data['id']!='') $w_sql[]=' AND uo.id="'.$data['id'].'"';
        if(isset($data['user_id']) && $data['user_id']!='') $w_sql[]=' AND uo.user_id="'.$data['user_id'].'"';

        //formación query
        if(isset($data['sql']) && is_array($data['sql'])) { foreach($data['sql'] as $K=>$val) {$w_sql[] = $val;} }
        $wsql='';
        if(isset($data['sql']) && is_array($data['sql'])) $wsql=implode(' ',@$data['sql']);


        //Query base
        $select = 'SELECT uo.*, pm.name as "metodo_pago", ov.code, ov.name as "discount_name", ov.quantity as "discount_quantity" FROM orders uo 
            INNER JOIN pay_methods pm ON uo.paymethod = pm.id
            INNER JOIN order_status os ON uo.`status`= os.id LEFT JOIN orders__vouchers ov ON uo.`voucher_id`= ov.id   
        WHERE  1=1  '.@implode($w_sql, ' ').' ORDER BY uo.id DESC';

        //echo $select;

        $items=array();
        if($result = $this->_db->query($select)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)){

                /*GET order DATA*/
                $row['datas']=$this->getOrderData( $row['id'], $lang);
                //$row['colors']=$this->getCombinationsC( $row['id']);


                array_push($items,$row);
            }
            if(!isset($id))
                return array('error'=>0, 'message'=>'','data'=>$items);
            else return($items[0]);
        }else return array('error'=>1, 'message'=>'No items. '.$this->_db->error);

    }

    function getStatuses() {
        $data =array();

        $sql = 'SELECT 
            *
          FROM order_status  os';

        if($result = $this->_db->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $data[]=$row;
            }
        }

        return $data;
    }
    //COMMON
    public function saveOrder($data, $products, $status)
    {

        //
        // if($result = $this->_db->query($update)) {
        //$id = $this->_db->insert_id;
        //}


        // ADD order
        if (isset($status)) {
            $order_insert_sql = 'INSERT INTO orders (
                `voucher_id`,
                `donacion_ong`,
               `status`,  `paymethod`, `date_add`,
              `name`, `surnames`, `email`,
              `envio_nombre`, `envio_apellidos`, `envio_direccion`, `envio_cp`, `envio_localidad`, `envio_provincia`, `envio_prefijotelefono`, `envio_telefono`,  `envio_comentarios`
            ) VALUES (
                
               "' . @$data['code'] . '", 
               "' . @$data['donacion_ong'] . '", 
               "' . $status . '",  "' . $data['method'] . '",  "' . time() . '",
                "' . $data['name'] . '",  "' . $data['lastname'] . '",  "' . $data['email'] . '",
                "' . $data['address']['name'] . '", "' . $data['address']['lastname'] . '","' . $data['address']['address'] . '", "' . $data['address']['zip'] . '", "' . $data['address']['city'] . '", "' . $data['address']['state2'] . '", "' . $data['address']['dialCode'] . '","' . $data['address']['phone'] . '",
                "' . $data['deliveryInstructions'] . '"
            );';
//echo $order_insert_sql;
            if ($result = $this->_db->query($order_insert_sql)) {
                $order_id = $this->_db->insert_id;


                //NOW INSERT BOOKS
                $order_insert_books = 'INSERT INTO orders__books (
                  `format_id`,
                  `id_order`,
                  `id_book`,
                  `gender_id`,
                  `skin_id`,
                  `hair_style_id`,
                  `hair_color_id`,
                  `eyes_id`,
                  `clothes_id`,
                  `glasses_id`,
                  `name`,
                  `dedicatoria_txt`,
                  `dedicatoria_img`
                  
                ) VALUES ';

                $i = 0;
                foreach ($products as $product) {
                   // echo '<pre>'; print_r($product);echo '</pre>';
                    $order_insert_books .= '(
                    
                        "' . $product['formato'] . '",
                        "' . $order_id . '",
                        "' . $product['book']['id'] . '",
                        "' . $product['custom_avatar']['gender'] . '",
                        "' . $product['custom_avatar']['skinColor'] . '",
                        "' . $product['custom_avatar']['hairStyle'] . '",
                        "' . $product['custom_avatar']['hairColor'] . '",
                        "' . $product['custom_avatar']['eyesColor'] . '",
                        "' . $product['custom_avatar']['clothesColor'] . '",
                        "' . $product['custom_avatar']['glassesStyle'] . '",
                        "' . $product['custom_avatar']['name'] . '",
                        "' . $product['dedicatoria']['text'] . '",
                        "' . $product['dedicatoria']['photo'] . '"';
                    $i++;
                    //CLOSING OR MORE
                    if ($i >= sizeof($products)) $order_insert_books .= ');';
                    else $order_insert_books .= '),';

                }


               // echo $order_insert_books;

                if ($result_books = $this->_db->query($order_insert_books)) {

                    return array('error' => 0, 'orderID' => $order_id, 'data'=>$data, 'products'=>$products,  'msg' => 'ALL DATA SAVED ' . $this->_db->error);

                } else {
                   // echo $this->_db->error;

                    return array('error' => 1, 'msg' => 'Imposible crear la orden de pedido. MySQLERR' . $this->_db->error);
                }

            } else {
                return array('error' => 1, 'msg' => 'Imposible crear la orden de pedido. MySQLERR' . $this->_db->error);
            }
        }

    }

    //COMMON
    public function updateStatus( $status, $id)  {
        $errors = null;
        if (isset($status)) {
            $sql = 'UPDATE orders SET `status`="' . $status . '" WHERE id="' . $id . '";';
            if (!$this->_db->query($sql)) $errors[] = 'Ha habido un error añadiendo los productos al pedido.' . @$this->_db->error;
            if (!$errors) return array('error' => 0, 'msg' => 'Se ha insertado existosamente. ID ' . @$id, 'id' => $id);
        } else return array('error' => 1, 'msg' => 'No insertado', 'errors' => $errors);

    }
        //

    //COMMON
    public function updatePedidoImprenta( $status, $pedidoPOD, $id)  {
        $errors = null;
        if (isset($status) && isset($pedidoPOD) && isset($id)) {
            $sql = 'UPDATE orders SET `impresion_sent`="'.$status.'", `id_podiprint`="' . $pedidoPOD . '" WHERE id="' . $id . '";';
            if ($this->_db->query($sql)) return true;
        }
        return false;

    }
        //

    public function existOrderId($id) {
        $errors=array();
        if(!isset($id)) $errors[]='No hay slug';

        if(!sizeof($errors)>0){
            $news = array();

            $sql = 'SELECT * FROM  orders WHERE id="'.$id.'" LIMIT 1;';

            if($result = $this->_db->query($sql))  {
                if($result->num_rows>0) return true;
            }
        }
        return false;
    }

    public function checkVoucher( $voucher) {

        $errors = null;
        $sql = 'SELECT * FROM orders__vouchers WHERE unix_timestamp()>=date_init AND unix_timestamp()<=date_finish AND code="' . $voucher . '";';

        if($result = $this->_db->query($sql)) {
            return  $result->fetch_array(MYSQLI_ASSOC);

        }

        return null;

    }

    public function getVouchers($params=array()) {
        $w_sql='';
        if(isset($params['id']) && !empty($params['id'])) {
            $w_sql = 'AND id='.$params['id'];
        }

        $errors = null;
        $sql = 'SELECT * FROM orders__vouchers WHERE 1=1 '.@$w_sql.';';

        $data = array();
        if($result = $this->_db->query($sql)) {
            while($row = $result->fetch_array(MYSQLI_ASSOC)){
                $data[]=$row;
            }
        }

        return $data;

    }



    public function addVoucher($data=array()) {
        //print_r($data);
        //Errors Array
        $errors = array();
        //SQL Vars Array
        $sql = array();
        //Gallery array
        $g = array();

        /*VARS SQL*/
        /*REQUIRED*/


        $sql = array(
            '"'.$data['name'].'"',
            '"'.$data['code'].'"',
            '"'.$data['quantity'].'"',
            '"'.strtotime($data['date_init']).'"',
            '"'.strtotime($data['date_finish']).'"'
        );


        /*END VARS SQL*/

        if(!isset($errors) || sizeof($errors)==0) {

            //Update new
            $update = 'INSERT INTO orders__vouchers (`name`, `code`, `quantity`, `date_init`, `date_finish`) VALUES (';
            if(sizeof($sql)>0)
                foreach ($sql as $k=>$item)
                    $update .= ($k != (sizeof($sql)-1)) ? $item.', ' : $item;
            $update .= ');';

            if($result = $this->_db->query($update))  $id = $this->_db->insert_id;
            else  $errors[]='Ha habido un error modificando la noticia base.'.@$this->_db->error;



            if(!$errors) return array('error'=>0, 'msg'=>'Se ha insertado existosamente. ID '.@$id, 'id'=>$id);

        }

        return array('error'=>1, 'msg'=>'No insertado', 'errors'=>$errors);
    }

    public function editVoucher($data=array()) {
        //Errors Array          //SQL Vars Array
        $errors = array();      $sql = array();

        /*VARS SQL*/

        if(empty($data['id'])) array_push($errors,'No hay ID.');

        $sql[] = 'name="'.($data['name']).'"';
        $sql[] = 'code="'.($data['code']).'"';
        $sql[] = 'quantity="'.($data['quantity']).'"';
        $sql[] = 'date_init="'.strtotime($data['date_init']).'"';
        $sql[] = 'date_finish="'.strtotime($data['date_finish']).'"';

        if(!isset($errors) || sizeof($errors)==0) {
            $update = 'UPDATE orders__vouchers SET ';
            if(sizeof($sql)>0)
                foreach ($sql as $k=>$item)
                    $update .= ($k != (sizeof($sql)-1)) ? $item.', ' : $item;
            $update .= ' WHERE id="'.$data['id'].'";';

            if(!$this->_db->query($update)) $errors[]=@$this->_db->error;
            else return array('error'=>0, 'msg'=>'Se ha actualizado existosamente');
        }
        //Return Data
        return array('error'=>1, 'msg'=>'No actualizado', 'errors'=>$errors);
    }

    public function deleteVoucher($params) {
        if(!isset($params['id']) || empty($params['id'])) $errors[]='No hay ID.';

        if(!isset($errors) || !sizeof($errors)>0) {
            $this->_db->autocommit(FALSE);
            if($result = $this->_db->query("DELETE FROM orders__vouchers WHERE id='{$params['id']}';"))  {
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

    public function existVoucher ($id) {
        $errors=array();
        if(!isset($id) || $id=='') $errors[]='No hay slug';

        if(!sizeof($errors)>0){
            $news = array();

            $sql = 'SELECT * FROM  orders__vouchers WHERE id="'.$id.'" LIMIT 1;';
            if($result = $this->_db->query($sql))  {
                if($result->num_rows>0) return true;
            }
        }
        return false;
    }



    //ADMIN FUNCTIONS
    public function delete($data = array())
    {
        $errors = array();
        $sql = array();
        $g = array();

        if (empty($data['id'])) array_push($errors, 'No hay ID.');



        $this->_db->autocommit(FALSE);

        if ($this->_db->query('DELETE FROM `orders__books` WHERE `id_order`=' . $data['id']))
            if ($this->_db->query('DELETE FROM `orders` WHERE `id`=' . $data['id'])) {
                $this->_db->commit();
                $this->_db->autocommit(TRUE);
                return true;
            }

        //Return Data

        $this->_db->rollBack();
        $this->_db->autocommit(TRUE);
        return false;
    }


    public function emailOrderPaid($orderData) {
        $message_email = '<div style="font-size:13px; font-family: Open Sans; line-height: 1.3em;">
                <p>Hola '.$orderData['name'].',</p><br />
                <br />
                <p>Tu pedido número #'.$orderData['id'].' ha sido realizado en la fecha '.date('d/m/Y', $orderData['date_add']).' mediante '.$orderData['metodo_pago'].' y su estado es PAGADO,</p>
                <br />
            <h4>Productos:</h4><br />';


            $message_email .= '<style>
            .datagrid table { border-collapse: collapse; text-align: left; width: 100%; } .datagrid {font: normal 12px/150% Arial, Helvetica, sans-serif; background: #fff; overflow: hidden; border: 1px solid #2A90B7; -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; }.datagrid table td, .datagrid table th { padding: 7px 8px; }.datagrid table thead th {background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #2A90B7), color-stop(1, #2A90B7) );background:-moz-linear-gradient( center top, #2A90B7 5%, #2A90B7 100% );filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#2A90B7\', endColorstr=\'#2A90B7\');background-color:#2A90B7; color:#FFFFFF; font-size: 12px; font-weight: bold; border-left: 0px solid #2A90B7; } .datagrid table thead th:first-child { border: none; }.datagrid table tbody td { color: #00496B; font-size: 12px;font-weight: normal; }.datagrid table tbody .alt td { background: #E1EEF4; color: #00496B; }.datagrid table tbody td:first-child { border-left: none; }.datagrid table tbody tr:last-child td { border-bottom: none; }.datagrid table tfoot td div { border-top: 1px solid #2A90B7;background: #E1EEF4;} .datagrid table tfoot td { padding: 0; font-size: 12px } .datagrid table tfoot td div{ padding: 2px; }.datagrid table tfoot td ul { margin: 0; padding:0; list-style: none; text-align: right; }.datagrid table tfoot  li { display: inline; }.datagrid table tfoot li a { text-decoration: none; display: inline-block;  padding: 2px 8px; margin: 1px;color: #FFFFFF;border: 1px solid #006699;-webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #006699), color-stop(1, #00557F) );background:-moz-linear-gradient( center top, #006699 5%, #00557F 100% );filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=\'#006699\', endColorstr=\'#00557F\');background-color:#006699; }.datagrid table tfoot ul.active, .datagrid table tfoot ul a:hover { text-decoration: none;border-color: #006699; color: #FFFFFF; background: none; background-color:#00557F;}div.dhtmlx_window_active, div.dhx_modal_cover_dv { position: fixed !important; }
            .datagrid table tr {border-bottom:border: 1px solid #2A90B7; }
            .tablepedido th,
            .tablepedido td
            { padding:4px;}
            </style>
            <table class="datagrid tablepedido" >
            <thead><tr>
                <th>Avatar</th>
                <th>Nombre</th>
                <th>Libro</th>
                <th>Formato</th>
                <th></th>
            </tr></thead><tbody>';

            foreach($orderData['datas'] as $bookPedido) {
                $btnDwnload ='';
                if($bookPedido['format_id']==1 || $bookPedido['format_id']==3) $btnDwnload='<a style=" padding:2px 7px; text-decoration:none; background:#2a90b7;color:white;" href="'.BASE_URL.'libros/descargar_ebook/'.md5($bookPedido['id']).'">DESCARGAR</a>';


                $message_email .= '
                <tr>
                    <td><img height="30" src="'.BASE_URL.'libros/generateAvatar/?book_slug='.$bookPedido['book_slug'].'&gender='.$bookPedido['gender_id'].'&glassesStyle='.$bookPedido['glasses_id'].'&skinColor='.$bookPedido['skin_id'].'&hairStyle='.$bookPedido['hair_style_id'].'&hairColor='.$bookPedido['hair_style_id'].'&eyesColor='.$bookPedido['eyes_id'].'&clothesColor='.$bookPedido['clothes_id'].'"></td>
                    <td>'.$bookPedido['name'].'</td>
                    <td>'.$bookPedido['book_name'].'</td>
                    <td>'.$bookPedido['format_name'].'</td>
                    <td>'.$btnDwnload.'</td>
                </tr>';
            }

            $message_email .= '</tbody></table>';

            $message_email .='<br />
                <h4>Datos de compra:</h4>
                <p><strong>Nombre:</strong><br /> '.$orderData['name'].'  '.$orderData['surnames'].'<br /><br /></p>
                <p><strong>Email:</strong><br /> '.$orderData['email'].'<br /><br /></p>
                <br />
                <br />
                <h4>Los datos de envío son los siguientes:</h4>
                <br />
                <p><strong>Nombre:</strong><br /> '.$orderData['envio_nombre'].'  '.$orderData['envio_apellidos'].'<br /><br /></p>
                <p><strong>Dirección:</strong><br /> '.$orderData['envio_direccion'].'<br /><br /></p>
                <p><strong>CP:</strong><br /> '.$orderData['envio_cp'].'<br /><br /></p>
                <p><strong>Localidad:</strong><br /> '.$orderData['envio_localidad'].'<br /><br /></p>
                <p><strong>Provincia:</strong><br /> '.$orderData['envio_provincia'].'<br /><br /></p>
                <p><strong>Teléfono:</strong><br /> '.$orderData['envio_prefijotelefono'].' '.$orderData['envio_telefono'].'<br /><br /></p>
                <p><strong>Comentarios:</strong><br /> '.$orderData['envio_comentarios'].'</p>
                <br />
                <br />
            </div>
            ';

            return $message_email;
    }
    
}

?>