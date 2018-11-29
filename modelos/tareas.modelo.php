<?php

class ModeloTareas extends Conexion{
    /* ============================================================================================================================
                                                        ATRIBUTOS  
    ============================================================================================================================*/
    

    /* ============================================================================================================================
                                                        CONSTRUCTOR   
    ============================================================================================================================*/
    function __construct() {

        parent::__construct();

    }

    /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/
    // ASIGNA 1 UBICACION A UNA TAREA ESPECIFICA
    public function mdlAsignarTarea($tarea,$ubicacion){

        $stmt= $this->link->prepare(
        "INSERT INTO tareas_det(id_tarea,ubicacion) 
        SELECT * FROM (SELECT :id_tarea,:ubicacion) as temp
        WHERE NOT EXISTS (
        SELECT 1 FROM tareas_det WHERE id_tarea= :id_tarea AND ubicacion=:ubicacion
        ) LIMIT 1;
        ");
        
        $stmt->bindParam(":id_tarea",$tarea,PDO::PARAM_INT);
        $stmt->bindParam(":ubicacion",$ubicacion,PDO::PARAM_STR);

        $res=$stmt->execute();

        $stmt->closeCursor();
        return $res;
        // cierra la conexion
        $stmt=null;
    }

    // BUSCA UBICACIONES 
    public function mdlBuscarUbicaciones($tarea=null){
        
        if ($tarea!=null) {

            $stmt= $this->link->prepare(
            "SELECT ubicacion  
            FROM tareas_det 
            WHERE id_tarea=:tarea
            GROUP BY ubicacion
            ORDER BY ubicacion ASC;");
            
            $stmt->bindParam(":tarea",$tarea,PDO::PARAM_INT);
        
        // muestra todas las ubicacones
        }else {

            $stmt= $this->link->prepare(
            "SELECT ubicacion,tip_inventario  
            FROM ubicacion
            WHERE  estado=0
            ORDER BY tip_inventario ASC,ubicacion ASC;");

        }
        

        $res=$stmt->execute();
        
        return $stmt;
        $stmt->closeCursor();
        // cierra la conexion
        $stmt=null;
    }

    // BUSCA LA ULTIMA TAREA CREADA PARA UN USUARIO
    public function mdlBuscarUltimaTarea($usuario){

        $stmt= $this->link->prepare(
        "SELECT id_tarea AS tarea 
        FROM tareas 
        WHERE usuario=:usuario 
        ORDER BY creacion DESC 
        LIMIT 1;")
        ;
        
        $stmt->bindParam(":usuario",$usuario,PDO::PARAM_INT);

        $stmt->execute();
        
        return $stmt;
        $stmt->closeCursor();
        // cierra la conexion
        $stmt=null;
    }

    // CREA UNA NUEVA TAREA PARA UN USUARIO
    public function mdlCrearTarea($usuario){

        $stmt= $this->link->prepare("INSERT INTO tareas(usuario) VALUES(:usuario)");
        
        $stmt->bindParam(":usuario",$usuario,PDO::PARAM_STR);

        $res=$stmt->execute();
        if($res){
            $res=$this->link->lastInsertId();
        }
       
        $stmt->closeCursor();
        return $res;
        // cierra la conexion
        $stmt=null;
    }

    // ELIMINA 1 UBICACION A UNA TAREA ESPECIFICA
    public function mdlEliminarUbicacion($tarea,$ubicacion){

        $stmt= $this->link->prepare(
        "DELETE FROM tareas_det
        WHERE id_tarea=:id_tarea
        AND ubicacion=:ubicacion;
        ");
        
        $stmt->bindParam(":id_tarea",$tarea,PDO::PARAM_INT);
        $stmt->bindParam(":ubicacion",$ubicacion,PDO::PARAM_STR);

        $res=$stmt->execute();
    
        $stmt->closeCursor();
        return $res;
        // cierra la conexion
        $stmt=null;
    }

    public function mdlMostrarAlistadores()
    {
        $stmt= $this->link->prepare(
            "SELECT id_usuario,nombre,cedula,IF(COUNT(ubicacion)>0,1,0) AS ubicaciones
            FROM usuario
            LEFT JOIN tareas ON tareas.usuario=id_usuario
            LEFT JOIN tareas_det ON tareas_det.id_tarea=tareas.id_tarea
            WHERE perfil=3
            GROUP BY id_usuario,nombre,cedula
            ORDER BY nombre ASC;
            ");
        
        $res=$stmt->execute();
        
        if ($res) {
            return $stmt;
        }else {
            return $res;
        }
        $stmt->closeCursor();
        
        // cierra la conexion
        $stmt=null;
    }
    
}