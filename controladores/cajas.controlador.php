<?php

class ControladorCajas extends ControladorAlistar {
    /* ============================================================================================================================
                                                        ATRIBUTOS   
    ============================================================================================================================*/
    
    private $modelo;

    /* ============================================================================================================================
                                                        CONSTRUCTOR   
    ============================================================================================================================*/
    function __construct($req=null) {

        parent::__construct($req);
        $this->modelo=new ModeloCaja($req);

    }

    /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/
    // busca cajas en la requisicion
    public function ctrBuscarCaja($numcaja,$estado=null)
    {

        $busqueda=$this->modelo->mdlMostrarCaja($numcaja,$estado);
        
        if ($busqueda->rowCount() > 0) {

             $cajabus["estado"]="encontrado";

                while($row = $busqueda->fetch()){

                    //Muestra todas las cajas
                    $cajabus["contenido"][]=["no_caja"=>$row["no_caja"],
                                                    "num_caja"=>$row["num_caja"],
                                                    "alistador"=>$row["nombre"],
                                                    "tipocaja"=>$row["tipo_caja"],
                                                    "abrir"=>$row["abrir"],
                                                    "cerrar"=>$row["cerrar"],
                                                    "recibido"=>$row["recibido"],
                                                    "estado"=>$row["estado"]
                                                ];

                }
                
        //si no encuentra resultados devuelve "error"
        }else{

            $cajabus= ["estado"=>"error",
                    "contenido"=>"Caja no encontrado en la base de datos!"];

        }
        // libera conexion para hace otra sentencia
        $busqueda->closeCursor();
        return $cajabus;

    }
    public function ctrBuscarUsuarioSinCaja()
    {
        $busqueda=$this->modelo->mdlMostrarUsuarioSinCaja();
        
        if ($busqueda->rowCount() > 0) {

             $resultado["estado"]="encontrado";

                while($row = $busqueda->fetch()){

                    //Muestra todas las cajas
                    $resultado["contenido"][$row["id_usuario"]]=$row["nombre"];

                }
                $busqueda->closeCursor();
        //si no encuentra resultados devuelve "error"
        }else{

            $resultado= ["estado"=>false,
                    "contenido"=>"no hay alistadores disponibles!"];

        }
        // libera conexion para hace otra sentencia
        $busqueda->closeCursor();
        return $resultado;
    }
    // crea documento de texto
    public function ctrDocumento($numcaja)
    {
        $res=false;
        if ($this->modelo->mdlRequisicionDoc()) {
                    
            $busqueda=$this->modelo->mdlMostrarDocumento($numcaja );
            
            $documento="";
            
            while($row = $busqueda->fetch()){
                $Mensaje=str_pad($row["num_caja"],19,"0",STR_PAD_LEFT);
                
                // $origen=str_replace("BD","",$row["lo_origen"]);
                // $destino=str_replace("VE","",$row["lo_destino"]);
                // $destino=$origen.substr($destino,1,-1);
                $localicacion=str_replace("-","",$row["lo_origen"]."BD".$row["lo_destino"]."VEI");
                $localicacion=str_pad($localicacion,11+15," ",STR_PAD_RIGHT);
                $item=str_pad($row["iditem"],6+12," ",STR_PAD_RIGHT);
                $num=$row["alistado"]*1000;
                $alistado=str_pad($num,12,"0",STR_PAD_LEFT);
                $alistado=str_pad($alistado,12+32," ",STR_PAD_RIGHT);
                
                $documento.=($localicacion.$item.$alistado.$Mensaje."\r\n");
                $res["no_documento"]=$row["documentos"];
            }
            $res["documento"]=$documento;
            

        }

        return $res;

    }

    //elimina la caja
    public function ctrEliminar($numcaja){
        // cambia items de la caja a no alistados
        $res = $this->modelo->mdlEliminarItems($numcaja);
        
        // elimina los registros de items resibidos de dicha caja
        if($res){
            $res = $this->modelo->mdlEliminarRecibidos($numcaja);
        }
        // elimina los registros de errores de dicha caja
        if($res){
            $res = $this->modelo->mdlEliminarErrores($numcaja);
        }
        // elimina la caja
        if($res){
            $res = $this->modelo->mdlEliminarCaja($numcaja);
        }
        return $res;
        
    }

    //busca items con error al recibirse
    public function ctrBuscarItemError($numcaja)
    {
        $busqueda = $this->modelo->mdlMostrarItemError($numcaja);
        
        if ($busqueda->rowCount() > 0) {

            $itembus["estado"]="encontrado";

           
            
            while($row = $busqueda->fetch()){

                switch ($row["estado"]) {
                    
                    case 0:
                        
                        if ($row["recibidos"]==0) {
                            $mensaje="Item no recibido";
                            $mensajeitem="item no recibido";
                        }else {
                            $mensaje="Menos items";
                            $mensajeitem="Se recibieron menos items, recibidos: ".$row["recibidos"]." alistados: ".$row["alistado"];
                        }
                        break;

                    case 1:
                        $mensaje="Mas items";
                        $mensajeitem="Se recibieron mas items, recibidos: ".$row["recibidos"]." alistados: ".$row["alistado"];                        
                        break;

                    case 2:
                        $mensaje="Req diferente";
                        $mensajeitem="El item  recibido no estaba en la requisicion";
                        break;

                    case 3:
                        $mensaje="Caja diferente";
                        if ($row["cajap"]==0) {
                            $mensajeitem="El item recibido no está alistado en niguna caja";
                        }else{
                            $mensajeitem="El item recibido fue alistado en la caja ".$row["cajap"]." y recibido en la caja ".$row["cajar"];
                        }
                        break;
                    
                    default:
                        $mensaje="Ok";
                        break;
                }
                if ($row["estado"] != 4) {
                    $itemdat=$row;
                    $itemdat["mensaje"]=$mensajeitem;
                    $itembus["estado"]="error0";
                    $itembus["contenido"][]=$itemdat;
                }      
 
            }

            // return $itembus;

        //si no encuentra resultados devuelve "error"
        }else{

            $itembus= ['estado'=>"error",
                    'contenido'=>"Items no encontrados!"];

        }
        
        $busqueda->closeCursor();
        return $itembus;        

    }

    //modifica la caja despues de ser resivida en el PV
    public function ctrModificarCaja($numcaja,$items)
    {   
        

        for ($i=0; $i <count($items) ; $i++) {
            //modifica los items

            // si el se alistan 0 se saca el item de la caja
            if ($items[$i]["alistados"]==0) {
                $resultado=$this->ctrEliminarItemCaja($items[$i]["iditem"],$numcaja);
                // si se modifica la caja elimina el item de la tabla de pedidos
                // return $resultado;
                if ($resultado) {
                    $resultado=$this->modelo->mdlEliminarItemPedido($items[$i]["iditem"],$numcaja);
                }
    
            }else{
                $resultado=$this->modelo->mdlModificarItem($items[$i],$numcaja);
            }
        }

        if ($resultado) {

            $resultado =$this->modelo->mdlVerificarCaja($numcaja);
            
           
        }
      
        return $resultado;
    }
   
    // asigna cajas  a un transportador para ser enviada
    public function ctrDespacharCajas($cajas,$transportador)
    {   
        $resultado=true;
        for ($i=0; $i <count($cajas) ; $i++) { 
            $resultado=$this->modelo->mdlDespachar($cajas[$i],$transportador);
        }
        return $resultado;
    }

}
