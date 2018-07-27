<?php
class ControladorUsuarios {

    
   
    /*==================================================
                    INGREOS DE USUARIO
    ================================================*/
    static public function ctrIngresoUsuario(){
        if (isset($_POST["usuario"])) {
            if (preg_match('/^[a-zA-Z0-9]+$/',$_POST["usuario"]) &&
            (preg_match('/^[a-zA-Z0-9]+$/',$_POST["contraseña"]))) {
                
                //busca en la tabla usuario en la columna usuario al dato o $valor
                
                $item="usuario";

                //obtiene el usuario ingresado
                $valor=$_POST["usuario"];
                //obtiene la contrseña ingresada
                $contraseña=$_POST["contraseña"];
                
                //Busca los datos del usuario, si existe 
                $modelo=new ModeloUsuarios();
                
                // $respuesta=ModeloUsuarios::MdlMostrarUsuarios($tabla,$item,$valor);
                $respuesta=$modelo->mdlMostrarUsuarios($item,$valor);
                $respuesta=$respuesta->fetch();
                
                //si encuentra el usuario inicia sesion

                if($respuesta["usuario"]==$valor &&
                password_verify($contraseña, $respuesta["password"])){

                    $_SESSION["iniciarSesion"]="ok";
                    $_SESSION["usuario"]=["id" => $respuesta["id_usuario"],
                                          "nombre" => $respuesta["nombre"],
                                          "cedula" => $respuesta["cedula"],  
                                          "usuario" => $respuesta["usuario"],
                                          "perfil" => $respuesta["perfil"]
                                        ];
                    
                    echo '<script>
                            window.location="inicio";
                          </script>';
                    
                    
                //de lo contrario muestra un mensaje de alerta
                }else {
                    echo '<br><div class="card-panel  red darken-4">Error al ingresas, vuelva aintentar</div>';
                }
                
            }
        }
    }
}