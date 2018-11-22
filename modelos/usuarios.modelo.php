<?php


class ModeloUsuarios extends ModeloLoginUsuario {
    

    function __construct() {
      
      parent::__construct();

    }
    
    /*==========================================
          REGISTRAR USUARIO
    ========================================*/
    public function mdlRegistrarUsuario($datos){
      $tabla='usuario';
      $stmt= $this->link->prepare(
      "INSERT INTO $tabla(nombre,cedula,usuario,password,perfil,franquicia) 
      VALUES(:nombre,:cedula,:usuario,:password,:perfil,:franquicia);");
      
      $stmt->bindParam(":nombre",$datos["nombre"],PDO::PARAM_STR);
      $stmt->bindParam(":cedula",$datos["cedula"],PDO::PARAM_STR);
      $stmt->bindParam(":usuario",$datos["usuario"],PDO::PARAM_STR);
      $stmt->bindParam(":password",$datos["password"],PDO::PARAM_STR);
      $stmt->bindParam(":perfil",$datos["perfil"],PDO::PARAM_STR);
      $stmt->bindParam(":franquicia",$datos["franquicia"],PDO::PARAM_STR);
      

      $res=$stmt->execute();
      if($res){
        $res=$this->link->lastInsertId();
      }
       
      return $res;

      $stmt=null;
    }
    /*==========================================
          MOSTRAR PERFILES
    ========================================*/
    public function mdlMostrarPerfiles($perfil=null,$item=null,$valor=null){
      $tabla='perfiles';
      
      //busca los itemas
      if($perfil == 1 or $perfil == null) {
        return $this->buscaritem($tabla,$item,$valor);
      }elseif( $perfil == 2){
        $stmt= $this->link->prepare("SELECT *
        FROM $tabla
        WHERE id_perfil IN (3,5);");

        $stmt->execute();
        
        return $stmt;
      }else {
        return "Error usuario no tiene permisos";
      }

      
    }  
    /*==========================================
          MOSTRAR FRANQUICIAS
    ========================================*/
    public function mdlMostrarFranquicias(){
      $tabla ='usuario';
      $stmt = $this->link->prepare(
      "SELECT * 
      FROM franquicias 
      WHERE codigo<>'NFRA'
      ORDER BY codigo ASC;");
      
      

      $res = $stmt->execute();
      if($res){
        $res = $stmt;
      }            
      return $res;
      
      $stmt=null;
    }  
    /*==========================================
          MODIFICA USUARIO
    ========================================*/
    public function mdlCambiarUsuario($datos){
      $tabla ='usuario';
      $stmt = $this->link->prepare(
      "UPDATE $tabla 
      SET nombre=:nombre, cedula=:cedula,usuario=:usuario,
      password=:password,perfil=:perfil,franquicia=:franquicia 
      WHERE id_usuario=:id_usuario;");
      
      $stmt->bindParam(":id_usuario",$datos["id"],PDO::PARAM_INT);
      $stmt->bindParam(":nombre",$datos["nombre"],PDO::PARAM_STR);
      $stmt->bindParam(":cedula",$datos["cedula"],PDO::PARAM_STR);
      $stmt->bindParam(":usuario",$datos["usuario"],PDO::PARAM_STR);
      $stmt->bindParam(":password",$datos["password"],PDO::PARAM_STR);
      $stmt->bindParam(":perfil",$datos["perfil"],PDO::PARAM_INT);
      $stmt->bindParam(":franquicia",$datos["franquicia"],PDO::PARAM_STR);
      

      $res = $stmt->execute();
      if($res){
        $res = $datos["id"];
      }
        // else{
        //   $res = $stmt->errorInfo();
        // }
            
      return $res;
      
      $stmt=null;
    }  
  

}