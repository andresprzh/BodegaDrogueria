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
    public function ctrDocumento($items,$numcaja)
    {
        
        $res = $this->modelo->mdlModificarCaja($numcaja);
        // $items=$this->ctrBuscarItemCaja($numcaja);
        if ($res) {          
            $documento='';
            foreach($items as $row){
                $Mensaje=$row['mensajes'];
                if ($row['mensajes']=='' ){
                    $busqueda=$this->modelo->buscaritem('usuario','id_usuario',$this->req[1]);
                    $busqueda=$busqueda->fetch();
                    $Mensaje=substr($busqueda['nombre'],0,19);
                    
                }
                $origen=str_replace('BD','',$row["origen"]);
                $destino=str_replace('VE','',$row["destino"]);
                $destino=$origen.substr($destino,1,-1);
                $localicacion=str_replace('-','',$row["origen"].$destino.'I');
                $localicacion=str_pad($localicacion,11+15," ",STR_PAD_RIGHT);
                $item=str_pad($row["id"],6+12," ",STR_PAD_RIGHT);
                $num=$row["alistados"]*1000;
                $alistado=str_pad($num,12,'0',STR_PAD_LEFT);
                $alistado=str_pad($alistado,12+32," ",STR_PAD_RIGHT);
                
                $documento.=($localicacion.$item.$alistado.$Mensaje."\r\n");

            }
            $res=$documento;
        }

        return $res;

    }

    public function ctrCancelar($numcaja){
        // cambia items de la caja a no alistados
        $res = $this->modelo->mdlCancelarItems($numcaja);
        
        // elimina los registros de items resibidos de dicha caja
        if($res){
            $res = $this->modelo->mdlCancelarRecibidos($numcaja);
        }
        // elimina los registros de errores de dicha caja
        if($res){
            $res = $this->modelo->mdlCancelarErrores($numcaja);
        }
        // elimina la caja
        if($res){
            $res = $this->modelo->mdlEliminarCaja($numcaja);
        }
        return $res;
        
    }

    public function ctrBuscarItemCancelados($numcaja)
    {
        $busqueda = $this->modelo->mdlMostrarItemCancelados($numcaja);

        if ($busqueda->rowCount() > 0) {

            $itembus["estado"]="encontrado";

            $cont=0;

            while($row = $busqueda->fetch()){
                
                                
                $itembus["contenido"][$cont]=["codigo"=>$row["ID_CODBAR"],
                                    "iditem"=>$row["item"],    
                                    "referencia"=>$row["ID_REFERENCIA"],
                                    "descripcion"=>$row["DESCRIPCION"],
                                    "disponibilidad"=>"---",
                                    "pedidos"=>$row["pedido"],
                                    "alistados"=>$row["alistado"],
                                    'ubicacion'=>$row["ubicacion"],
                                    'origen'=>$row["lo_origen"],
                                    'destino'=>$row["lo_destino"]
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
                if ($resultado) {
                    $resultado=$this->modelo->mdlEliminarItemPedido($items[$i]["iditem"],$numcaja);
                }
    
            }else{
                $resultado=$this->modelo->mdlModificarItem($items[$i],$numcaja);
            }
        }

        if ($resultado) {
            // verifica el estado de  la caja
            $busqueda=$this->modelo->mdlVerificarCaja($numcaja);
            $row = $busqueda->fetch();
            // si todos los items recibidos coinciden con los enviados cambia el estaod de la caja a recibida
            if ($row['cantidad']==0) {
                $resultado=$this->modelo->mdlCerrarCaja($numcaja);
            }
           
        }
        // // si cambia el estado de la caja crea nuevamente el archivo plano
        // if ($resultado) {
        //     $resultado=$this->ctrDocumento($items,$numcaja);
        // }
        return $resultado;
    }

    public function ctrDespacharCajas($cajas,$transportador)
    {   
        $resultado=true;
        for ($i=0; $i <count($cajas) ; $i++) { 
            $resultado=$this->modelo->mdlDespachar($cajas[$i],$transportador);
        }
        return $resultado;
    }

    
   
}
