<?php

class Conexion{
    protected $link;

    function __construct() {
        /*==========================================
                     CONECTAR BASE DE DATOS
          ========================================*/
          $link=new PDO("mysql:host=localhost;dbname=bodegadrogueria",
          "root",
          "");

        
        //permite usar caracteres latinos
        $link->exec("set names utf8");

        $this->link = $link;
    }


    public function buscaritem($tabla,$item,$valor){
         // si item es diferente de nullo se busca con una condicion
        
        if ($item!=null) {
            if (is_array($item)) {
                
                $sql="SELECT * FROM $tabla WHERE";
                for($i=0;$i<count($item);$i++) {
                    $sql.=" $item[$i] = :valor$i  OR";
                }
                $sql=substr($sql, 0, -2).';';

                $stmt= $this->link->prepare($sql);

                
                for($i=0;$i<count($valor);$i++) {
                    $stmt->bindParam(":valor$i",$valor[$i],PDO::PARAM_STR);
                }

                $stmt->execute();
                return $stmt;

            }else{

                $stmt= $this->link-> prepare("SELECT * FROM $tabla WHERE $item = :$item");
                
                
                //para evitar sql injection
                $stmt->bindParam(":".$item,$valor,PDO::PARAM_STR);
                //ejecuta el comando sql
                $stmt->execute();
                
                
                // return $stmt->fetch();

                return $stmt;
                
            }
          // si item es nulo muestra todos los datos de $tabla
          }else {
  
            $stmt= $this->link-> prepare("SELECT * FROM $tabla");
            //para evitar sql injection
            $stmt->bindParam(":".$item,$valor,PDO::PARAM_STR);
            //ejecuta el comando sql
            $stmt->execute();
  
            // return $stmt->fetchAll();
            return  $stmt;
          }
  
          // cierra conexion base de datos
          $stmt=null;
    }

}