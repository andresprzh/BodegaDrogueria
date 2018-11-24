<?php

class ControladorTareas extends ControladorLoginUsuario{
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

    function ctrAsignarUbicacion($ubicacion,$usuario,$tarea=null){
        // busca la ultima tarea creada para el usuario
        if ($tarea==null) {
            $tarea=$this->modelo->mdlBuscarUltimaTarea($usuario);
            
            if ($tarea->rowCount()>0) {
                $tarea=$tarea->fetch()["tarea"];
                
            }else {
                $tarea=$this->modelo->mdlCrearTarea($usuario);
                
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
                
            }else{
                return false;
            }
            
    
        }
        
        $busqueda=$this->modelo->mdlBuscarUbicaciones($tarea);
        
        if ($busqueda->rowCount()> 0) {
            // $res= $busqueda->fetchAll();
            while($row = $busqueda->fetch()){
                if ($usuario!=null) {
                    $res[trim($row["ubicacion"])]=trim($row["ubicacion"]);
                }else {
                    $res[]=["ubicacion"=>trim($row["ubicacion"]),
                            "tip_inventario"=>$row["tip_inventario"]];
                }
                // $res[trim($row["ubicacion"])]=trim($row["ubicacion"]);
            }
            $busqueda->closeCursor();
        }else{
            $res=false;
        }
        
        return $res;
    }

    function ctrCrearTareas($usuario){
        
        $res=$this->modelo->mdlCrearTarea($usuario);

        return $res;
    }


    function ctrEliminarUbicacion($ubicacion,$usuario,$tarea=null){
        // busca la ultima tarea creada para el usuario
        if ($tarea==null) {
            $tarea=$this->modelo->mdlBuscarUltimaTarea($usuario);
            if ($tarea->rowCount()) {
                $tarea=$tarea->fetch()["tarea"];
            }
        }
        
        $res=$this->modelo->mdlEliminarUbicacion($tarea,$ubicacion);
        return $res;
    } 

}