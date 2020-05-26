<?php


class Session
{


    public static function init(){
        if(!@session_start()){
            if(session_status() == PHP_SESSION_NONE) session_destroy();
            session_start();
        }
    }
    public static function is_logged() {
        if(isset($_SESSION['auth']) && $_SESSION['auth']>0) return true;
        return false;
    }
    public static function destroy($clave = false) {
        if($clave){
            if(is_array($clave)){
                for($i = 0; $i < count($clave); $i++){
                    if(isset($_SESSION[$clave[$i]])){
                        unset($_SESSION[$clave[$i]]);
                    }
                }
            }
            else{
                if(isset($_SESSION[$clave])){
                    unset($_SESSION[$clave]);
                }
            }
        }
        else{
            session_destroy();
        }
    }
    public static function getId() {
        return Session::get('user_id');
    }
    public static function set($clave, $valor){
        if(!empty($clave))
        $_SESSION[$clave] = $valor;
    }
    
    public static function get($clave){
        if(isset($_SESSION[$clave]))
            return $_SESSION[$clave];
    }


    public static function make_logout() {
        Session::destroy();

    }



}