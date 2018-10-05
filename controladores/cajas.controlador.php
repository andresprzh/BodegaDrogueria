<?php
include "alistar.controlador.php";

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

            if($busqueda->rowCount() == 1){

                $row = $busqueda->fetch();

                //guarda los resultados en un arreglo
                $cajabus=["estado"=>"encontrado",
                           "contenido"=> ["no_caja"=>$row["no_caja"],
                                           "alistador"=>$row["nombre"],
                                           "tipocaja"=>$row["tipo_caja"],
                                           "abrir"=>$row["abrir"],
                                           "cerrar"=>$row["cerrar"],
                                           "recibido"=>$row["recibido"],
                                           "estado"=>$row["estado"]
                                         ]
                         ];

                
                //retorna el item a la funcion
                return $cajabus;

            }else {

                $cajabus["estado"]="encontrado";

                $cont=0;

                while($row = $busqueda->fetch()){

                    //Muestra todas las cajas
                    $cajabus["contenido"][$cont]=["no_caja"=>$row["no_caja"],
                                                    "alistador"=>$row["nombre"],
                                                    "tipocaja"=>$row["tipo_caja"],
                                                    "abrir"=>$row["abrir"],
                                                    "cerrar"=>$row["cerrar"],
                                                    "recibido"=>$row["recibido"],
                                                    "estado"=>$row["estado"]
                                                ];

                    
                    $cont++;

                }
                

            }

        //si no encuentra resultados devuelve "error"
        }else{

            $cajabus= ['estado'=>"error",
                    'contenido'=>"Caja no encontrado en la base de datos!"];

        }
        // libera conexion para hace otra sentencia
        $busqueda->closeCursor();
        return $cajabus;

    }

    // crea documento de texto
    // public function ctrDocumento($items,$numcaja)
    public function ctrDocumento($numcaja)
    {
        
        $busqueda=$this->modelo->mdlMostrarDocumento($numcaja );
        // return $busqueda->fetchAll();
        $documento="";
        while($row = $busqueda->fetch()){
            $Mensaje=str_pad($row["no_caja"],19,"0",STR_PAD_LEFT);
            
            $origen=str_replace("BD","",$row["lo_origen"]);
            $destino=str_replace("VE","",$row["lo_destino"]);
            $destino=$origen.substr($destino,1,-1);
            $localicacion=str_replace("-","",$row["lo_origen"].$destino."I");
            $localicacion=str_pad($localicacion,11+15," ",STR_PAD_RIGHT);
            $item=str_pad($row["iditem"],6+12," ",STR_PAD_RIGHT);
            $num=$row["alistado"]*1000;
            $alistado=str_pad($num,12,"0",STR_PAD_LEFT);
            $alistado=str_pad($alistado,12+32," ",STR_PAD_RIGHT);
            
            $documento.=($localicacion.$item.$alistado.$Mensaje."\r\n");
        }
        $res=$documento;
        

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

            $cont=0;
            
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
                        if ($row["no_caja"]==1) {
                            $mensajeitem="El item recibido no está alistado en niguna caja";
                        }else{
                            $mensajeitem="El item recibido fue alistado en la caja ".$row["no_caja"]." y recibido en la caja ".$numcaja;
                        }
                        break;
                    
                    default:
                        $mensaje="Ok";
                        break;
                }
                                
                $itembus["contenido"][$cont]=["codigo"=>$row["ID_CODBAR"],
                                    "iditem"=>$row["item"],    
                                    "no_caja"=>$row["no_caja"],
                                    "no_cajaR"=>$numcaja,
                                    "referencia"=>$row["ID_REFERENCIA"],
                                    "descripcion"=>$row["DESCRIPCION"],
                                    'ubicacion'=>$row["ubicacion"],
                                    "recibidos"=>$row["recibidos"],
                                    "alistados"=>$row["alistado"],
                                    "estado"=>$row["estado"],
                                    "problema"=>$mensajeitem
                                    ];
                $cont++;

            }

            return $itembus;

        //si no encuentra resultados devuelve "error"
        }else{

            return ['estado'=>"error",
                    'contenido'=>"Items no encontrados!"];

        }

    }

    //modifica la caja despues de ser resivida en el PV
    public function ctrModificarCaja($numcaja,$items)
    {   
        // $busqueda=$this->modelo->mdlVerificarCaja($numcaja);
        // $row = $busqueda->fetch();
        // return $row['cantidad'];

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
