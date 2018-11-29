<?php


class ModeloLoginUsuario extends Conexion {
    

    function __construct() {
      
      parent::__construct();

    }
    


    /*==========================================
            MOSTRAR USUARIOS
      ========================================*/
    public function mdlMostrarUsuarios($perfil=null,$item=null,$valor=null){
            
      //busca los items
      if($perfil == 1 or $perfil == null) {
        
        if ($item) {
          $stmt= $this->link->prepare(
            "SELECT *
            FROM usuario
            INNER JOIN perfiles ON id_perfil=perfil
            WHERE $item=:$item;
            LIMIT 1");

            $stmt->bindParam(":".$item,$valor,PDO::PARAM_STR);
        }else {
          
          $stmt= $this->link->prepare(
          "SELECT id_usuario AS id,usuario,nombre,cedula,perfil
          FROM usuario
          INNER JOIN perfiles ON id_perfil=perfil
          ORDER BY des_perfil ASC;");
        }

      }elseif( $perfil == 2){

        $stmt= $this->link->prepare(
        "SELECT id_usuario ,usuario,nombre,cedula,perfil
        FROM usuario
        INNER JOIN perfiles ON id_perfil=perfil
        WHERE perfil IN (3,5)
        ORDER BY des_perfil ASC;");

      }else {

        return "Error usuario no tiene permisos";

      }

      $stmt->execute();
        
      return $stmt;
      $stmt=null;
    } 
  

}