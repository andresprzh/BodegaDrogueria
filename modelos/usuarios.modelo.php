<?php


class ModeloUsuarios extends Conexion {
    

    function __construct() {
      
      parent::__construct();

    }
    


    /*==========================================
            MOSTRAR USUARIOS
      ========================================*/
    public function mdlMostrarUsuarios($item,$valor){
      $tabla='usuario';
      
      //busca los itemas
      return $this->buscaritem($tabla,$item,$valor);
      
    }




      /*==========================================
            REGISTRAR USUARIO
      ========================================*/
      static public function mdlIngresarUsuario($tabla,$datos){
        $stmt= Conexion::conectar() -> prepare("INSERT INTO $tabla(nombre,usuario,password,perfil,foto) VALUES(:nombre,:usuario,:password,:perfil,:ruta);");
        
        $stmt->bindParam(":nombre",$datos["nombre"],PDO::PARAM_STR);
        $stmt->bindParam(":usuario",$datos["usuario"],PDO::PARAM_STR);
        $stmt->bindParam(":password",$datos["password"],PDO::PARAM_STR);
        $stmt->bindParam(":perfil",$datos["perfil"],PDO::PARAM_STR);
        $stmt->bindParam(":ruta",$datos["ruta"],PDO::PARAM_STR);

        if ($stmt->execute()) {
          return "ok";
        }else {
          return "error";
        }
        
        $stmt=null;
      }
    
}