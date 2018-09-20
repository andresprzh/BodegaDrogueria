<?php

class ModeloAlistar extends Conexion{
    /* ============================================================================================================================
                                                        ATRIBUTOS  
    ============================================================================================================================*/
    protected $req;

    /* ============================================================================================================================
                                                        CONSTRUCTOR   
    ============================================================================================================================*/
    function __construct($req) {

        $this->req=$req;
        parent::__construct();

    }

    /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/
    //funcion que asigna los la cantidad alistada a cada item en la caja
    public function mdlAlistarItem($items,$numcaja){

        // // obtiene numero de rquisicion y el nombre del alistador
        // $no_req=$this->req[0];$alistador=$this->req[1];
        // $cod_barras=$items["codigo"];
        // $alistados=$items["alistados"];
        
        // $stmt= $this->link->prepare('CALL empacar(:cod_barras,:alistados,:alistador,:tipocaja,:no_req)');
        
        // $stmt->bindParam(":cod_barras",$cod_barras,PDO::PARAM_STR);
        // $stmt->bindParam(":alistados",$alistados,PDO::PARAM_INT);
        // $stmt->bindParam(":alistador",$alistador,PDO::PARAM_INT);
        // $stmt->bindParam(":tipocaja",$tipocaja,PDO::PARAM_STR);
        // $stmt->bindParam(":no_req",$no_req,PDO::PARAM_STR);

        // $res=$stmt->execute();
        // // $stmt->closeCursor();
        // // retorna el resultado de la sentencia
	    // return $stmt->errorInfo();

        // // cierra la conexion
        // $stmt=null;


        $no_req=$this->req[0];$alistador=$this->req[1];
        $cod_barras=$items["codigo"];
        $alistados=$items["alistados"];
        
        $stmt= $this->link->prepare('UPDATE pedido
		SET alistado=:alistados,estado=2
		WHERE Item=(SELECT ID_ITEMS FROM COD_BARRAS WHERE ID_CODBAR=:cod_barras)
		AND no_req=:no_req;
        ');

        $stmt->bindParam(":cod_barras",$cod_barras,PDO::PARAM_STR);
        $stmt->bindParam(":alistados",$alistados,PDO::PARAM_INT);
        $stmt->bindParam(":no_req",$no_req,PDO::PARAM_STR);

        $res=$stmt->execute();
        $stmt->closeCursor();
        // retorna el resultado de la sentencia
	    return $res;

        // cierra la conexion
        $stmt=null;
        
    }

    public function mdlCerrarCaja($tipocaja,$numcaja){

        $no_req=$this->req[0];$alistador=$this->req[1];
        
        $stmt= $this->link->prepare('UPDATE caja
		SET tipo_caja=:tipocaja,estado=1
		WHERE no_caja=:numcaja;
        ');

        $stmt->bindParam(":tipocaja",$tipocaja,PDO::PARAM_STR);
        $stmt->bindParam(":numcaja",$numcaja,PDO::PARAM_INT);

        $res=$stmt->execute();
        $stmt->closeCursor();  
        // retorna el resultado de la sentencia
	    return $res;

        // cierra la conexion
        $stmt=null;
    }
    
    //crea una caja nueva correspoendiente a la requisicion  
    public function mdlCrearCaja(){

        //obtiene el codigo del alistador
        $alistador=$this->req[1];
        // se agregan los datos a la tabla       
        $stmt= $this->link->prepare('INSERT INTO caja(alistador) VALUES(:alistador)');
        
        $stmt->bindParam(":alistador",$alistador,PDO::PARAM_INT);
        
        $res=$stmt->execute();
        
        // retorna el resultado de la sentencia
        return $res;

        // cierra la conexion
        $stmt=null;
    }

    // saca el item de una caja cambiando la caja a 1 su estado a 0 y la cantidad alistada a 0
    public function mdlEliminarItemCaja($item,$no_caja){
        $no_req=$this->req[0];$alistador=$this->req[1];
        
        
        $sql="UPDATE pedido SET no_caja=1,estado=0,alistado=0 WHERE item=:item AND no_req=:no_req; AND no_caja=:no_caja";

        $stmt= $this->link->prepare($sql);

        $stmt->bindParam(":item",$item,PDO::PARAM_STR);
        $stmt->bindParam(":no_req",$no_req,PDO::PARAM_STR);
        $stmt->bindParam(":no_caja",$no_caja,PDO::PARAM_INT);

        $res=$stmt->execute();
        
        return $res;

        // cierra la conexion
        $stmt=null;
    }

    //busca items de la tabla pedido usando el codigo de barras
    public function mdlMostrarItems($cod_barras){
        
        $no_req=$this->req[0];$alistador=$this->req[1];
        $stmt= $this->link->prepare("CALL buscarcod(:cod_barras,:no_req,:alistador,'%%');");

        $stmt->bindParam(":cod_barras",$cod_barras,PDO::PARAM_STR);
        $stmt->bindParam(":no_req",$no_req,PDO::PARAM_STR);
        $stmt->bindParam(":alistador",$alistador,PDO::PARAM_INT);

        $stmt->execute();

        $res=$stmt;

        return $stmt;

        // cierra la conexion
        $stmt=null;

    }

    public function mdlMostrarItemsCaja($numcaja){
        $no_req=$this->req[0];$alistador=$this->req[1];
        

        $stmt= $this->link->prepare("CALL buscarcod('%%','%%',:alistador,:numcaja);");

        $stmt->bindParam(":alistador",$alistador,PDO::PARAM_INT);
        $stmt->bindParam(":numcaja",$numcaja,PDO::PARAM_STR);

        $stmt->execute();

        return $stmt;

        // cierra la conexion
        $stmt=null;
    }

    public function mdlMostrarIE($item)
    {

        $no_req=$this->req[0];

        $sql="SELECT ID_ITEM,ID_REFERENCIA,ID_CODBAR,DESCRIPCION 
        FROM ITEMS
        INNER JOIN COD_BARRAS ON ID_ITEMS=ID_ITEM
        LEFT JOIN pedido on item=ID_ITEM
        WHERE (ID_ITEM = :item
        OR ID_REFERENCIA = :item
        OR DESCRIPCION LIKE :descripcion
        OR ID_CODBAR = :item)
        AND (no_req <> :no_req
        OR no_req IS NULL);";

        $stmt= $this->link->prepare($sql);
        $descripcion="%".$item."%";
        $stmt->bindParam(":item",$item,PDO::PARAM_STR);
        $stmt->bindParam(":descripcion",$descripcion,PDO::PARAM_STR);
        $stmt->bindParam(":no_req",$no_req,PDO::PARAM_STR);

        $stmt->execute();

        return $stmt;

        // cierra la conexion
        $stmt=null;

    }

    public function mdlAgregarIE($items)
    {
        // guarda datos de la requisicion
        $no_req=$this->req[0];$persona=$this->req[1];
        $datos="";
        for($i=0;$i<count($items);$i++) {
            $datos.="(:item$i,:no_req,1,'----',:pedido$i,:pedido$i,0,0),";
        }

        $datos=substr($datos, 0, -1).';';

        $sql='INSERT INTO pedido VALUES'. $datos;

        $stmt= $this->link->prepare($sql);

        $i=0;
        foreach ($items as $row) {
            
            $stmt->bindParam(":item$i",$row['iditem'],PDO::PARAM_STR);
            $stmt->bindParam(":pedido$i",$row['pedido'],PDO::PARAM_INT);
            $i++;
        }

        $stmt->bindParam(":no_req",$no_req,PDO::PARAM_STR);
        
        
        $res= $stmt->execute();
        
        // libera conexion para hace otra sentencia
        $stmt->closeCursor();
        return ($res); 
    }
    //muestra el numero de la caja correspondiente a la requisicion
    public function mdlMostrarNumCaja()
    {   
        
        $alistador=$this->req[1];
         
         $stmt= $this->link->prepare("SELECT numerocaja(:alistador) AS numcaja");
 
         $stmt->bindParam(":alistador",$alistador,PDO::PARAM_INT);
 
         $res=$stmt->execute();
 
         
         return $stmt;
 
         // cierra la conexion
         $stmt=null;
 
    }

}