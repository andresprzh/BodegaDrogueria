<?php


class ModeloUsuarios extends Conexion {
    

    function __construct() {
      
      parent::__construct();

    }
    


    /*==========================================
            MOSTRAR USUARIOS
      ========================================*/
    public function mdlMostrarUsuarios($perfil=null,$item=null,$valor=null){
      $tabla='usuario';
      
      //busca los itemas
      if($perfil == 1 or $perfil == null) {
        return $this->buscaritem($tabla,$item,$valor);
      }elseif( $perfil == 2){
        $stmt= $this->link->prepare("SELECT *
        FROM $tabla
        WHERE perfil IN (3,5);");

        $stmt->execute();
        
        return $stmt;
      }else {
        return "Error usuario no tiene permisos";
      }
      
    }

    /*==========================================
          REGISTRAR USUARIO
    ========================================*/
    public function mdlRegistrarUsuario($datos){
      $tabla='usuario';
      $stmt= $this->link->prepare("INSERT INTO $tabla(nombre,cedula,usuario,password,perfil) VALUES(:nombre,:cedula,:usuario,:password,:perfil);");
      
      $stmt->bindParam(":nombre",$datos["nombre"],PDO::PARAM_STR);
      $stmt->bindParam(":cedula",$datos["cedula"],PDO::PARAM_STR);
      $stmt->bindParam(":usuario",$datos["usuario"],PDO::PARAM_STR);
      $stmt->bindParam(":password",$datos["password"],PDO::PARAM_STR);
      $stmt->bindParam(":perfil",$datos["perfil"],PDO::PARAM_STR);
      

      $res=$stmt->execute();
      if($res){
        $res=$this->link->lastInsertId();
      }
       
      return $res;

      $stmt=null;
    }

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

    public function mdlCambiarUsuario($datos){
      $tabla='usuario';
      $stmt= $this->link->prepare("UPDATE $tabla SET nombre=:nombre, cedula=:cedula,usuario=:usuario,password=:password,perfil=:perfil WHERE id_usuario=:id_usuario;");
      
      $stmt->bindParam(":id_usuario",$datos["id"],PDO::PARAM_INT);
      $stmt->bindParam(":nombre",$datos["nombre"],PDO::PARAM_STR);
      $stmt->bindParam(":cedula",$datos["cedula"],PDO::PARAM_STR);
      $stmt->bindParam(":usuario",$datos["usuario"],PDO::PARAM_STR);
      $stmt->bindParam(":password",$datos["password"],PDO::PARAM_STR);
      $stmt->bindParam(":perfil",$datos["perfil"],PDO::PARAM_INT);
      

      $res=$stmt->execute();
      if($res){
        $res=$datos["id"];
      }
            
      return $res;
      
      $stmt=null;
    }  
  

}