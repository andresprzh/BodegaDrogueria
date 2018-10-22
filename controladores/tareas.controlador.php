<?php

class ControladorTareas {
    /* ============================================================================================================================
                                                        ATRIBUTOS   
    ============================================================================================================================*/
    
    private $modelo;

    /* ============================================================================================================================
                                                        CONSTRUCTOR   
    ============================================================================================================================*/
    function __construct() {

        $this->modelo=new ModeloTareas();

    }
    /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/

    function ctrCrearTareas($usuario){
        
        $res=$this->modelo->mdlCrearTarea($usuario);

        return $res;
    }

    function ctrAsignarUbicacion($ubicacion,$usuario,$tarea=null){
        // busca la ultima tarea creada para el usuario
        if ($tarea==null) {
            $tarea=$this->modelo->mdlBuscarUltimaTarea($usuario);
            if ($tarea->rowCount()) {
                $tarea=$tarea->fetch()["tarea"];
            }
        }
        
        $res=$this->modelo->mdlAsignarTarea($tarea,$ubicacion);
        return $res;
    }   

    function ctrBuscarUbicaciones($usuario=null,$tarea=null){
        
        if ($usuario!=null && $tarea==null ) {
            
            $tarea=$this->modelo->mdlBuscarUltimaTarea($usuario);
            
            if ($tarea->rowCount()>0) {
                $tarea=$tarea->fetch()["tarea"];
            }
    
        }
        
        $busqueda=$this->modelo->mdlBuscarUbicaciones($tarea);

        if ($busqueda->rowCount()> 0) {
            while($row = $busqueda->fetch()){
                $res[$row["ubicacion"]]=$row["ubicacion"];
            }
        }else{
            $res=false;
        }
        
        return $res;
    }

}