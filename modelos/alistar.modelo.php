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
    
    //busca items de la tabla pedido usando el codigo de barras
    public function mdlMostrarItems($Cod_barras){
        
        $no_req=$this->req[0];$alistador=$this->req[1];
        $stmt= $this->link->prepare("CALL buscarcod(:Cod_barras,:no_req,:alistador,'%%');");

        $stmt->bindParam(":Cod_barras",$Cod_barras,PDO::PARAM_STR);
        $stmt->bindParam(":no_req",$no_req,PDO::PARAM_STR);
        $stmt->bindParam(":alistador",$alistador,PDO::PARAM_INT);

        $stmt->execute();

        $res=$stmt;

        return $stmt;

        // cierra la conexion
        $stmt=null;

    }

    //muestra el numero de la caja correspondiente a la requisicion
    public function mdlMostrarNumCaja(){   
        
        $alistador=$this->req[1];
        $stmt= $this->link->prepare("SELECT numerocaja(:alistador) as numcaja");

        $stmt->bindParam(":alistador",$alistador,PDO::PARAM_INT);

        $res=$stmt->execute();

        
        return $stmt;

        // cierra la conexion
        $stmt=null;

    }

    //crea una caja nueva correspoendiente a la requisicion  
    public function mdlCrearCaja(){

        //obtiene el codigo del alistador
        $alistador=$this->req[1];
        // se agregan los datos a la tabla       
        $stmt= $this->link->prepare('INSERT INTO caja(Persona) VALUES(:alistador)');
        
        $stmt->bindParam(":alistador",$alistador,PDO::PARAM_INT);
        
        $res=$stmt->execute();
        
        // retorna el resultado de la sentencia
        return $res;

        // cierra la conexion
        $stmt=null;
    }

    //funcion que asigna los la cantidad alistada a cada item en la caja
    public function mdlAlistarItem($Items,$TipoCaja){

        // obtiene numero de rquisicion y el nombre del alistador
        $no_req=$this->req[0];$alistador=$this->req[1];
        $Cod_barras=$Items["codigo"];
        $alistados=$Items["alistados"];
        // $sql='CALL empacar("'.$Cod_barras.'",'.$alistados.','.$alistador.',"'.$TipoCaja.'","'.$no_req.'")';
        $stmt= $this->link->prepare('CALL empacar(:Cod_barras,:alistados,:alistador,:TipoCaja,:no_req)');
        // $stmt= $this->link->prepare($sql);

        $stmt->bindParam(":Cod_barras",$Cod_barras,PDO::PARAM_STR);
        $stmt->bindParam(":alistados",$alistados,PDO::PARAM_INT);
        $stmt->bindParam(":alistador",$alistador,PDO::PARAM_INT);
        $stmt->bindParam(":TipoCaja",$TipoCaja,PDO::PARAM_STR);
        $stmt->bindParam(":no_req",$no_req,PDO::PARAM_STR);

        $res=$stmt->execute();
        // $stmt->closeCursor();
        // retorna el resultado de la sentencia
        return $res;

        // cierra la conexion
        $stmt=null;
        
    }

}