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
    function __construct($req) {

        parent::__construct($req);
        $this->modelo=new ModeloCaja($req);

    }

    /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/
    public function ctrBuscarCaja($numcaja){

        $busqueda=$this->modelo->mdlMostrarCaja($numcaja);

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
    public function ctrDocumento($items,$numcaja){
        
        $res = $this->modelo->mdlModificarCaja($numcaja);
        // $items=$this->ctrBuscarItemCaja($numcaja);
        if ($res) {          
            $documento='';
            foreach($items as $row){
                $origen=str_replace('BD','',$row["origen"]);
                $destino=str_replace('VE','',$row["destino"]);
                $destino=$origen.substr($destino,1,-1);
                $localicacion=str_replace('-','',$row["origen"].$destino.'I');
                $localicacion=str_pad($localicacion,11+15," ",STR_PAD_RIGHT);
                $item=str_pad($row["id"],6+15," ",STR_PAD_RIGHT);
                $num=$row["alistados"]*1000;
                $alistado=str_pad($num,12,'0',STR_PAD_LEFT);
                $alistado=str_pad($alistado,12+32," ",STR_PAD_RIGHT);
                $Mensaje=$row['mensajes'];
                
                $documento.=($localicacion.$item.$alistado.$Mensaje."\r\n");

            }
            $res=$documento;
        }

        return $res;

    }

    public function ctrCancelar($numcaja){
        $res = $this->modelo->mdlCancelarItems($numcaja);
        
        if($res){
            $res = $this->modelo->mdlCancelarRecibidos($numcaja);
        }
        if($res){
            $res = $this->modelo->mdlCancelarCaja($numcaja);
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
                
                                
                $itembus["contenido"][$cont]=["codigo"=>$row["ID_CODBAR"],
                                    "iditem"=>$row["item"],    
                                    "referencia"=>$row["ID_REFERENCIA"],
                                    "descripcion"=>$row["DESCRIPCION"],
                                    'ubicacion'=>$row["ubicacion"],
                                    "recibidos"=>$row["recibidos"],
                                    "alistados"=>$row["alistado"],
                                    "estado"=>$row["estado"],
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

   
}
