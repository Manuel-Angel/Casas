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
    public static function reiniciarContraseña($email){
        try {
            ParseUser::requestPasswordReset($email);
            return true;
        } catch (ParseException $ex) {
            return false;
        }
    }
    /**
     * Permite que $usuario le otorgue una calificacion ($calificacion) a otro usuario, en este 
     * caso a $usuarioCalificado. Si el usuario ya califico previamente a ese usuario,
     * su calificacion anterior se borra.
     * @param type $usuario quien asigna la calificacion
     * @param type $usuarioCalificado a quien se le asignara la calificacion
     * @param type $calificacion la calificacion a asignar
     */
    public function calificaUsuario($usuario, $usuarioCalificado, $calificacion){
        $calificacionAct=Usuario::existeCalificacion($usuario, $usuarioCalificado);
        if ($calificacionAct!=null) {
            $calificacionAct->destroy();
        }
        $calif= new ParseObject("Calificaciones");
        $calif->set("calificacion", $calificacion);
        $calif->set("idUsuario", $usuario);
        $calif->set("idUsuarioCalificado",$usuarioCalificado);
        
        $calif->save();
    }
    /**
     * Si existe una calificacion dada por $usuario a $usuarioC, devuelve este
     * registro, el metodo calificaUsuario lo usa para no repetir las calificaciones.
     * @param type $usuario
     * @param type $usuarioC
     * @return type
     */
    private function existeCalificacion($usuario,$usuarioC){
        $query = new ParseQuery("Calificaciones");
        $query ->equalTo("idUsuario", $usuario);
        $query ->equalTo("idUsuarioCalificado", $usuarioC);
        $calif= $query ->find();
        if (count($calif) >= 1) {
            return $calif[0];
        }
        return null;
    }
    /**
     * Devuelve el promedio de calificaciones que tiene actualmente $usuario.
     * @param type $usuario
     * @return type
     */
    public function getCalificacionUsuario($usuario){
        $query = new ParseQuery("Calificaciones");
        $query ->equalTo("idUsuarioCalificado", $usuario);
        $calif= $query->find();
        $fin= count($calif);
        //echo "hay ".$fin." calificaciones <br>";
        if ($fin == 0) {
            return null;
        }
        $promedio= 0;
        for($i=0;$i<$fin;$i++){
            $promedio += $calif[$i]->get("calificacion");
        }
        return $promedio / $fin;
    }
}
class Inmueble {
    public static function migrarImagenes(){
        $queryInmuebles= new ParseQuery("Inmueble");
        $inmuebles= $queryInmuebles->find();
        $fin=count($inmuebles);
        for($i=0;$i <$fin;$i++){
            echo "Inmueble: ". $inmuebles[$i]->get("direccion")." tiene imagen:<br>";
            Inmueble::migrarImagenesInmueble($inmuebles[$i]);
        }
    }
    public static function migrarImagenesInmueble($inmueble){
        $queryTabla= new ParseQuery("ImagenesDelInmueble");
        $queryTabla ->equalTo("inmuebleId",$inmueble);
        $imagenes= $queryTabla->find();
        $fin=count($imagenes);
        $relacion= $inmueble->getRelation("imagenes");
        
        for($i=0;$i<$fin;$i++){
            $imagId = $imagenes[$i]->get("imagenId");
            $imagId ->fetch();
            
            
            $relacion->add($imagId);
            //muestra
            $imagen= $imagId ->get("imagen");
            echo "<img src= ".$imagen->getUrl(). "> <br>";
        }
        $inmueble->save();
    }
    public function getImagenesInmueble($inmueble){
        $relation =  $inmueble ->getRelation("imagenes");
        //$relation->setTargetClass('imagenes'); //se supone que esto es necesario, asi lo lei en stackoverflow, pero no se que poner
        $query = $relation ->getQuery(); 
        
        $imagenes= $query->find();
        $fin= count($imagenes);
        $resp=[];
        for($i=0;$i< $fin;$i++){
            $imag= $imagenes[$i]->get("imagen");
            $resp[]=$imag->getUrl();
            //echo "<img src= " . $imag->getUrl(). " > <br>";
        }
        for($i=0;$i< $fin;$i++){
            echo "<img src= " . $resp[$i]. " > <br>";
        }
    }
    public function getImagenInmueble($inmueble){
        $relation =  $inmueble ->getRelation("imagenes");
        $query = $relation ->getQuery(); 
        $imagen= $query->first();
        $imag= $imagen->get("imagen");
        return $imag->getUrl();
    }
    
}
