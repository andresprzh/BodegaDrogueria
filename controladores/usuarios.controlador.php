<?php
class ControladorUsuarios extends ControladorLoginUsuario{

    /* ============================================================================================================================
                                                        ATRIBUTOS   
    ============================================================================================================================*/
    private $modelo;

    /* ============================================================================================================================
                                                        CONSTRUCTOR   
    ============================================================================================================================*/
    function __construct() {

        $this->modelo=new ModeloUsuarios();

    }
    /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/
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