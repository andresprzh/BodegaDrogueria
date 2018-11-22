<?php

class ControladorLoginUsuario {
    /* ============================================================================================================================
                                                        ATRIBUTOS   
    ============================================================================================================================*/
    private $modelo;
    /* ============================================================================================================================
                                                        CONSTRUCTOR   
    ============================================================================================================================*/
    function __construct() {

        $this->modelo=new ModeloLoginUsuario();

    }
    /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/
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
    
}