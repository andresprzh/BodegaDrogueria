<?php
class ControladorUsuarios {

    private $modelo;

    function __construct() {

        $this->modelo=new ModeloUsuarios();

    }
   
    /*==================================================
                    INGREO O LOGIN DE USUARIO
    *username: nombre usuario.
    *passwrod: contraseña dle usuario
    *regresa variables de usuario  si realiza la conexion o falso si no
    ================================================*/
    public function ctrIngresoUsuario($username,$password){
        if (isset($username)) {
            if (preg_match('/^[a-zA-Z0-9]+$/',$username) &&
               (preg_match('/^[a-zA-Z0-9]+$/',$password))) {
                
                //busca en la tabla usuario en la columna usuario al dato o $valor
                
                $item="usuario";

                //obtiene el usuario ingresado
                $valor=$username;
                
                
                
                                
                $respuesta=$this->modelo->mdlMostrarUsuarios(1,$item,$valor);
                $respuesta=$respuesta->fetch();
                
                //si encuentra el usuario inicia sesion

                if(strcasecmp($respuesta["usuario"],$valor)==0 &&
                password_verify($password, $respuesta["password"]) && 
                $respuesta["perfil"]!=0){

                    $_SESSION["iniciarSesion"]="ok";
                    $_SESSION["usuario"]=["id" => $respuesta["id_usuario"],
                                          "nombre" => $respuesta["nombre"],
                                          "cedula" => $respuesta["cedula"],  
                                          "usuario" => $respuesta["usuario"],
                                          "perfil" => $respuesta["perfil"],
                                          "franquicia" => $respuesta["franquicia"]
                                        ];
                    
                    // echo '<script>
                    //         window.location="inicio";
                    //       </script>';
                    return $_SESSION["usuario"];
                    // return true;
                    
                //de lo contrario muestra un mensaje de alerta
                }else {
                    // echo '<br><div class="card-panel  red darken-4">Error al ingresas, vuelva a intentar</div>';
                    return false;
                }
                
            }
        }
    }
    /*==================================================
                    BUSCA UN USUARIO
    *$perfil: perfil usuario
    *$item: variable con la que se busca el usuario.
    *$valor: valor de la variable con la que se busca el usuario
    *regresa un objeto json con un estado si encontro  o no resultados y un contenido donde esta el resultado de la busqueda
    ================================================*/
    public function ctrBuscarUsuarios($perfil=null,$item=null,$valor=null){
        
        $busqueda=$this->modelo->mdlMostrarUsuarios($perfil,$item,$valor);
        
        if ($busqueda->rowCount() > 0) {

            if($busqueda->rowCount() == 1){

                $row = $busqueda->fetch();

                //guarda los resultados en un arreglo
                $usuarios=["estado"=>'encontrado',
                           "contenido"=> ["id"=>$row["id_usuario"],
                                            "usuario"=>$row["usuario"],
                                           "nombre"=>$row["nombre"],
                                           "cedula"=>$row["cedula"],
                                           "perfil"=>$row["perfil"]
                                         ]
                         ];
               
                //retorna el usuario a la funcion
                return $usuarios;

            }else {

                $usuarios["estado"]="encontrado";

                $cont=0;

                while($row = $busqueda->fetch()){
                        
                        $usuarios["contenido"][$cont]=["id"=>$row["id_usuario"],
                                                    "usuario"=>$row["usuario"],
                                                    "nombre"=>$row["nombre"],
                                                    "cedula"=>$row["cedula"],
                                                    "perfil"=>$row["perfil"]
                                                    ];
                        
                        $cont++;

                }

                return $usuarios;

            }

        //si no encuentra resultados devuelve "error"
        }else{

            return ['estado'=>"error",
                    'contenido'=>"Usuario no encontrado en la base de datos!"];

        }
        
    }
    /*==================================================
                    BUSCA PERFILES
    *$perfil: perfil usuario que esta usando la fucnion
    *regresa un objeto json con un estado si encontro  o no resultados y un contenido donde esta el resultado de la busqueda
    ================================================*/
    public function ctrBuscarPerfiles($perfil){
        $busqueda=$this->modelo->mdlMostrarPerfiles($perfil);

        if ($busqueda->rowCount() > 0) {

            if($busqueda->rowCount() == 1){

                $row = $busqueda->fetch();

                //guarda los resultados en un arreglo
                $perfiles=["estado"=>'encontrado',
                           "contenido"=> ["id"=>$row["id_perfil"],
                                        "perfil"=>$row["des_perfil"]
                                         ]
                         ];
               
                //retorna el item a la funcion
                return $perfiles;

            }else {

                $perfiles["estado"]="encontrado";

                $cont=0;

                while($row = $busqueda->fetch()){

                    //solo muestra los items que no estan alistados
                    
                        
                        $perfiles["contenido"][$cont]=["id"=>$row["id_perfil"],
                                                    "perfil"=>$row["des_perfil"],
                                                    ];
                        
                        $cont++;

                    

                }

                return $perfiles;

            }

        //si no encuentra resultados devuelve "error"
        }else{

            return ['estado'=>"error",
                    'contenido'=>"no encontrado!"];

        }
    }
    /*==================================================
                    CREA UN NUEVO USUARIO
    *$datosusuario: arreglo con todos los datos del usuario
    *regresa si el usuario ya existe o falso o verdadero si pudo o no crear el nuevo usuario
    ================================================*/
    public function ctrCrearUsuario($datosusario){
        // busca si el usuario ya existe
        $item = ['usuario','cedula'];
        $valor = [$datosusario['usuario'],$datosusario['cedula']];
        $busqueda = $this->modelo->mdlMostrarUsuarios(1,$item,$valor);
        $resultado['estado']='encontrado';
        if ($busqueda->rowCount() > 0) {
            return "Usuario ya existe";
        }else{
            return $this->modelo->mdlRegistrarUsuario($datosusario);
        }

    }
    /*==================================================
                    MODIFICA UN USUARIO
    *$datosusuario: arreglo con todos los datos del usuario
    *regresa verdadero o falso dependiendo si pudo o no crear usuario, ambien regresa un mensaje si hay conflicto en la modificacion de  los datos
    ================================================*/
    public function ctrModificarUsuario($datosusario){
        // busca si la cedula o usuario estan disponibles
        $item = ['usuario','cedula'];
        $valor = [$datosusario['usuario'],$datosusario['cedula']];
        $busqueda = $this->modelo->mdlMostrarUsuarios(1,$item,$valor);
        $resultado['estado']='encontrado';
        if ($busqueda->rowCount() > 1) {
            return "No pueden existir 2 usuarios con la misma cedula o nombre de usuario";
        }else{
            return $this->modelo->mdlCambiarUsuario($datosusario);
        }

    }

}