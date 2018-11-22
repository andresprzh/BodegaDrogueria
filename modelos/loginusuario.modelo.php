<?php


class ModeloLoginUsuario extends Conexion {
    

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
  

}