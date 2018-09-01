<?php
class ControladorUsuarios {

    private $modelo;

    function __construct() {

        $this->modelo=new ModeloUsuarios();

    }
   
    /*==================================================
                    INGREOS DE USUARIO
    ================================================*/
    public function ctrIngresoUsuario(){
        if (isset($_POST["usuario"])) {
            if (preg_match('/^[a-zA-Z0-9]+$/',$_POST["usuario"]) &&
            (preg_match('/^[a-zA-Z0-9]+$/',$_POST["contraseña"]))) {
                
                //busca en la tabla usuario en la columna usuario al dato o $valor
                
                $item="usuario";

                //obtiene el usuario ingresado
                $valor=$_POST["usuario"];
                //obtiene la contrseña ingresada
                $contraseña=$_POST["contraseña"];
                
                                
                $respuesta=$this->modelo->mdlMostrarUsuarios($item,$valor);
                $respuesta=$respuesta->fetch();
                
                //si encuentra el usuario inicia sesion

                if(strcasecmp($respuesta["usuario"],$valor)==0 &&
                password_verify($contraseña, $respuesta["password"]) && 
                $respuesta["perfil"]!=0){

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
                    echo '<br><div class="card-panel  red darken-4">Error al ingresas, vuelva a intentar</div>';
                }
                
            }
        }
    }

    public function ctrBuscarUsuarios($item=null,$valor=null){
        
        $busqueda=$this->modelo->mdlMostrarUsuarios($item,$valor);

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

    public function ctrBuscarPerfiles(){
        $busqueda=$this->modelo->mdlMostrarPerfiles();

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

    public function ctrCrearUsuario($datosusario){
        
        return $this->modelo->mdlRegistrarUsuario($datosusario);

    }

    public function ctrModificarUsuario($datosusario){
        
        return $this->modelo->mdlCambiarUsuario($datosusario);

    }
}