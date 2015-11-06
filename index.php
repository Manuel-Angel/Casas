<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
            require 'vendor/autoload.php';
            require 'Clases.php';
            use Parse\ParseClient;
            ParseClient::initialize('ve3SsAciKVt8GwhmLDCzW9rQ6EkPj8ai3pWcp3Is', 'zt0dVKAQwyRTAOFkfFj5d9jzDWAH9fjaJsUR5fhD', 'QpnJBJkOEp3VmEbcaAX8r6HDixj2wCUNQ42e1c4N');
            use Parse\ParseObject;
            use Parse\ParseQuery;
            use Parse\ParseUser;
            /*$pars = new ParseObject("TestObject");
            $pars->set("foo","asd");
            $pars->save();*/
            
            /********************** Pruebas con los usuarios ******************/
            /* creando un usuario*/
            /*$result= Usuario::registrarUsuario("cuenta de prueba", "123","prueba@hotmail.com",1);
            if($result instanceof ParseUser){
                echo 'Registro satisfactorio del usuario '. $result->get("username")."<br>";
            }else {
                echo $result."<br>";
            }*/
            /*iniciar sesion*/
            $usuario = Usuario::iniciarSesion("Manuel Angel Muñoz Solano1", "1234");
            if($usuario instanceof ParseUser){
                echo 'Bienvenido ' . $usuario->getUsername() ."<br>";
            }else {
                echo $usuario."<br>";
            }
            /*Usar el usuario logeado*/
            $actual= Usuario::usuarioActual();
            if($actual!=null){
                echo "El usuario actual es ".$actual->getUserName()." correo: ".$actual->get("email");
            }else {echo "Ningun usuario conectado";}
            /*cerrar sesion*/
            Usuario::cerrarSesion();
                    
            
            /*todos los usuarios */
            $consulta= new ParseQuery("_User");
            $consulta->equalTo("tipo",1);
            try{
                $objetos=$consulta->find();
            } catch (ParseException $ex) {
                echo "balio berga ". $ex;
            }
            
            echo "se encontraron " . count($objetos). " resultados <br>";
            
            for($i=0;$i<count($objetos);$i++){
                echo $objetos[$i]->getObjectId()." ".$objetos[$i]->get("foo")."<br>";
                //$objetos[$i]->destroy();
            }
            //$objeto= $consulta->get("Dxl8ifn73z");
            //$objeto->destroy();
        ?>
    </body>
</html>
