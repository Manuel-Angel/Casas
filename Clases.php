<?php
require 'vendor/autoload.php';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Parse\ParseClient;
use Parse\ParseUser;
use Parse\ParseObject;
use Parse\ParseQuery;
ParseClient::initialize('ve3SsAciKVt8GwhmLDCzW9rQ6EkPj8ai3pWcp3Is', 'zt0dVKAQwyRTAOFkfFj5d9jzDWAH9fjaJsUR5fhD', 'QpnJBJkOEp3VmEbcaAX8r6HDixj2wCUNQ42e1c4N');
class Usuario {
    /**
     * Registra a un usuario nuevo tomando sus datos, si todo fue bien retorna el 
     * objeto conteniendo al usuario, si no, retorna un mensaje de error.
     * @param type $nombre nombre del usuario
     * @param type $contraseña su contraseña
     * @param type $email el email del usuario
     * @param type $tipo el tipo de usuario (1 persona fisica, 2 persona moral).
     * @return \ParseUser
     */
    public static function registrarUsuario($nombre, $contraseña, $email, $tipo){
        //ParseClient::initialize('ve3SsAciKVt8GwhmLDCzW9rQ6EkPj8ai3pWcp3Is', 'zt0dVKAQwyRTAOFkfFj5d9jzDWAH9fjaJsUR5fhD', 'QpnJBJkOEp3VmEbcaAX8r6HDixj2wCUNQ42e1c4N');
        $usuario =  new ParseUser();
        $usuario->set("username",$nombre);
        $usuario->set("password",$contraseña);
        $usuario->set("email",$email);
        $usuario->set("tipo",$tipo);
        try{
            $usuario->signUp();
            return $usuario;
        } catch (Exception $ex) {
            if($ex->getCode()== 203){
                return "La direccion email ". $email. " ya esta ocupada";
            }else if($ex->getCode()==202){
                return "El nombre de usuario ".$nombre." ya esta ocupado";
            }else {return "Error: " . $ex->getCode() . " " . $ex->getMessage();}
        }
    }
    /**
     * Autentica al usuario y lo convierte en el usuario actual, si la autenticacion
     * fue exitosa devuelve el usuario, si no, devuelve un mensaje de error.
     * @param type $usuario el nombre del usuario
     * @param type $contraseña la contraseña del usuario
     * @return usuario | string
     */
    public static function iniciarSesion($usuario,$contraseña){
        try{
            $user = ParseUser::logIn($usuario, $contraseña);
            return $user;
        } catch (Exception $ex) {
            if($ex->getCode() ==101){
                return "Error: El usuario o la contraseña es incorrecta";
            }else {return "Error: " . $ex->getCode() . " " . $ex->getMessage();}
        }
    }
    /**
     * Metodo para borrar un usuario, se necesita autenticar con su usuario y contraseña,
     * si no es correcta envia un mensaje de error.
     * @param type $usuario
     * @param type $contraseña
     * @return ParseUser|string
     */
    public static function borrarUsuario($usuario, $contraseña){
        $user = iniciarSesion($usuario,$contraseña);
        if($user instanceof ParseUser){
            $user->destroy();
            return "Usuario borrado";
        }
        return $user;
    }
    public static function usuarioActual(){
        return ParseUser::getCurrentUser();
    }
    public static function cerrarSesion(){
        ParseUser::logOut();
    }
    
}
