<?php
include "cajas.controlador.php";


class ControladorPV extends ControladorCajas{
    /* ============================================================================================================================
                                                        ATRIBUTOS   
    ============================================================================================================================*/
    
    private $modelo;

    /* ============================================================================================================================
                                                        CONSTRUCTOR   
    ============================================================================================================================*/
    function __construct($req) {
        
        parent::__construct($req);
        $this->modelo=new ModeloPV($req);

    }

    /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/

    // busca el item en la requisicion
    public function ctrBuscarItemPV($cod_bar){
        $busqueda=$this->modelo->mdlMostrarItemPV($cod_bar);

        if ($busqueda->rowCount() > 0) {

            $row = $busqueda->fetch();
            $item=["estado"=>"encontrado",
            "contenido"=> ["codigo"=>$row["ID_CODBAR"],
                            "referencia"=>$row["ID_REFERENCIA"],
                            "descripcion"=>$row["DESCRIPCION"],
                          ]
            ];

        }else {
            $item['estado']='error';
            $item['contenido']='Item no encontrado';
        }

        return $item;
    }


    // busca cajas visibles para el punto de venta
    public function ctrBuscarCajaPV($numcaja){
        $busqueda=$this->modelo->mdlMostrarCajaPV($numcaja);
        
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

    // modifca los valores de los items en la tabla y luego genera el documento
    public function ctrRegistrarItems($items,$numcaja){   
        $i=0;
        // busca el id del item usando el codigo de barras de cada item
        foreach ($items as $row) {

            $busqueda=$this->modelo->mdlMostrarItemPV($row['codbarras']);
            $iditem = $busqueda->fetch();
            $items[$i]['item']=$iditem['ID_ITEM'];
            $i++;
        }
        // libera conexion para hace otra sentencia
        $busqueda->closeCursor();

        // agrega los datos en la datbla de recibidos
        $resultado['estado']=$this->modelo->mdlRegistrarItems($items,$numcaja);

        
        //si registra los items, modifica la tabla de pedido para que la caja aperezca como recibida
        if ($resultado==true) {
            $resultado['estado']=$this->modelo->mdlModCaja($numcaja);
            if($resultado['estado']==true){
                $resultado['contenido']=$this->ctrDocumentoR($numcaja);
            }
        }
        
        return $resultado;
        
    }

    // busca la srequisiciones
    public function ctrBuscarReq(){
        
        $busqueda=$this->modelo->mdlMostrarReq();

        if ($busqueda->rowCount() > 0) {

            $row = $busqueda->fetch();
            $requisicion=["estado"=>"encontrado",
            "contenido"=> ["no_req"=>$row["no_req"],
                            "creada"=>$row["creada"],
                            "origen"=>$row["lo_origen"],
                            "destino"=>$row["lo_destino"],
                          ]
            ];

        }else {
            $requisicion['estado']='error';
            $requisicion['contenido']='requisicion no encontrada';
        }
        // libera conexion para hace otra sentencia
        $busqueda->closeCursor();
        return $requisicion;
    }

    // crea archivo plano de la caja recibida
    private function ctrDocumentoR($numcaja){
        $busqueda=$this->modelo->mdlMostrarItemsRec($numcaja);

        
        if ($busqueda->rowCount()>0) {
            $recibidos=$busqueda->fetchAll();
            $i=0;
            $string='';
            foreach ($recibidos as $row) {

                $origen=str_replace('BD','',$row["lo_origen"]);
                $destino=str_replace('VE','',$row["lo_destino"]);
                $origen=$origen.substr($destino,1,-1);
                $localicacion=str_replace('-','',$origen.$row["lo_destino"].'C');
                $localicacion=str_pad($localicacion,11+15," ",STR_PAD_RIGHT);
                $codigo=str_pad($row["ID_CODBAR"],13+15," ",STR_PAD_RIGHT);
                $num=$row["recibidos"]*1000;
                $alistado=str_pad($num,12,'0',STR_PAD_LEFT);
                $alistado=str_pad($alistado,12+32," ",STR_PAD_RIGHT);
                

                switch ($row['estado']) {
                    case 0:
                        
                        if ($row['recibidos']==0) {
                            $mensaje='Item no Recibido';
                        }else {
                            $mensaje='Menos items';
                        }
                        break;

                    case 1:
                        $mensaje='Mas items';
                        break;

                    case 2:
                        $mensaje='Caja diferente';
                        break;

                    case 3:
                        $mensaje='Req diferente';
                        break;
                    
                    default:
                        $mensaje='Ok';
                        break;
                }
                
                $string.=($localicacion.$codigo.$alistado.$mensaje."\n");
            }
        }else {
            $string=false;
        }

        return $string;
    }
    
    
}