<?php

class ControladorAlistar {
    /* ============================================================================================================================
                                                        ATRIBUTOS   
    ============================================================================================================================*/
    protected $Req;
    private $modelo;

    /* ============================================================================================================================
                                                        CONSTRUCTOR   
    ============================================================================================================================*/
    function __construct($Req) {
        
        $this->Req=$Req;
        $this->modelo=new ModeloAlistar($Req);

    }

    /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/

    public function ctrBuscarItems($Cod_barras){
        
        $busqueda=$this->modelo->mdlMostrarItems($Cod_barras);

        if ($busqueda->rowCount() > 0) {

            if($busqueda->rowCount() == 1){

                $row = $busqueda->fetch();

                //guarda los resultados en un arreglo
                $itembus=["estado"=>$row['alistamiento'],
                           "contenido"=> ["codigo"=>$row["ID_CODBAR"],
                                           "referencia"=>$row["id_referencia"],
                                           "descripcion"=>$row["descripcion"],
                                           "disponibilidad"=>$row["disp"],
                                           "pedidos"=>$row["pedido"],
                                           "alistados"=>$row["alistado"],
                                           "caja"=>$row["No_caja"],
                                           "alistador"=>$row["nombre"],
                                           "ubicacion"=>$row["ubicacion"]
                                         ]
                         ];
                // en el arreglo se guarda el estaod de la consulta         
                switch ($itembus["estado"]) {

                    //0 si encontro algun resultaod en la consulta
                    case 0:
                        $itembus["estado"]='encontrado';
                        break;

                    // 1 si el item ya esta siendo alistado pro alguien
                    case 1:
                        $itembus["estado"]='error1';
                        $itembus["contenido"]='Item en alistamiento por '.$itembus['contenido']['alistador'];
                        break;

                    // 2 si el item ya fue alistado en la caja
                    case 2:
                        $itembus["estado"]='error2';
                        $itembus["contenido"]='Item ya alistado en la caja '.$itembus['contenido']['caja'].' por '.$itembus['contenido']['alistador'];
                        break;
                }
                //retorna el item a la funcion
                return $itembus;

            }else {

                $itembus["estado"]=["encontrado"];

                $cont=0;

                while($row = $busqueda->fetch()){

                    //solo muestra los items que no estan alistados
                    if($row['alistamiento']==0){
                        
                        $itembus["contenido"][$cont]=["codigo"=>$row["ID_CODBAR"],
                                           "referencia"=>$row["id_referencia"],
                                           "descripcion"=>$row["descripcion"],
                                           "disponibilidad"=>$row["disp"],
                                           "pedidos"=>$row["pedido"],
                                           "alistados"=>$row["alistado"],
                                           'ubicacion'=>$row["ubicacion"]
                                            ];
                        
                        $cont++;

                    }

                }

                return $itembus;

            }

        //si no encuentra resultados devuelve "error"
        }else{

            return ['estado'=>"error",
                    'contenido'=>"Item no encontrado en la base de datos!"];

        }
    }

    public function ctrCrearCaja(){
        
        $busqueda=$this->modelo->mdlMostrarNumCaja();

        
        $row=$busqueda->fetch();
        
        

         //si tiene cajas sin cerrar no crea una nueva
         if ($row['numcaja']) {
            // libera conexion para hace otra sentencia
            $busqueda->closeCursor();
            //busca los items en la caja
            $resultado=$this->ctrBuscarItemCaja($row['numcaja']);
            $resultado["estadocaja"]="yacreada";
            return $resultado ;

         // si no tiene cajas sin cerrar crea otra caja
         }else{

            // libera conexion para hace otra sentencia
            $busqueda->closeCursor();

            
            // $res=$this->modelo->mdlCrearCaja();

            //crea una caja nueva
            if ($this->modelo->mdlCrearCaja()) {
                $resultado["estadocaja"]="creada";
                return $resultado;

            }else {
                $resultado["estadocaja"]="error";
                return $resultado;

            }
            

         }

    }

    public function ctrBuscarItemCaja($NumCaja){
        
        $busqueda=$this->modelo->mslMostrarItemsCaja($NumCaja);

        if ($busqueda->rowCount() > 0) {

            $itembus["estado"]="encontrado";

            $cont=0;

            while($row = $busqueda->fetch()){
              
                $itembus["contenido"][$cont]=["codigo"=>$row["ID_CODBAR"],
                                    "referencia"=>$row["id_referencia"],
                                    "descripcion"=>$row["descripcion"],
                                    "disponibilidad"=>$row["disp"],
                                    "pedidos"=>$row["pedido"],
                                    "alistados"=>$row["alistado"],
                                    'ubicacion'=>$row["ubicacion"],
                                    'origen'=>$row["Lo_Origen"],
                                    'destino'=>$row["Lo_Destino"]
                                    ];
                
                $cont++;

            }
            
            return $itembus;

        //si no encuentra resultados devuelve "error"
        }else{

            return ['estado'=>"error",
                    'contenido'=>"Caja sin Items!"];

        }

    }

    public function ctrCerrarCaja($TipoCaja,$Items,$Req){

        for ($i=0; $i <count($Items) ; $i++) { 

            $Cod_barras=$Items[$i]["codigo"];
            $alistados=$Items[$i]["alistados"];
            $resultado=$this->modelo->mdlAlistarItem($Items[$i],$TipoCaja);

        }

        return $resultado;
    }
}
