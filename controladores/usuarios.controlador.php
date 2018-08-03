<?php
class ControladorUsuarios {

    private $modelo;

    function __construct($req) {

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

    public function ctrBuscarUsuarios()
    {
        
        $busqueda=$this->modelo->mdlMostrarUsuarios();

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
               
                //retorna el item a la funcion
                return $usuarios;

            }else {

                $usuarios["estado"]="encontrado";

                $cont=0;

                while($row = $busqueda->fetch()){

                    //solo muestra los items que no estan alistados
                    if($row['estado']==0){
                        
                        $usuarios["contenido"][$cont]=["id"=>$row["id_usuario"],
                                                    "usuario"=>$row["usuario"],
                                                    "nombre"=>$row["nombre"],
                                                    "cedula"=>$row["cedula"],
                                                    "perfil"=>$row["perfil"]
                                                    ];
                        
                        $cont++;

                    }

                }

                return $usuarios;

            }

        //si no encuentra resultados devuelve "error"
        }else{

            return ['estado'=>"error",
                    'contenido'=>"Item no encontrado en la base de datos!"];

        }
        
    }
}