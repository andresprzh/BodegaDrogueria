<?php

class ControladorAlistar {
    /* ============================================================================================================================
                                                        ATRIBUTOS   
    ============================================================================================================================*/
    protected $req;
    private $modelo;

    /* ============================================================================================================================
                                                        CONSTRUCTOR   
    ============================================================================================================================*/
    function __construct($req=null) {
        
        $this->req=$req;
        $this->modelo=new ModeloAlistar($req);

    }

    /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/

    public function ctrBuscarItems($cod_barras){
        
        $busqueda=$this->modelo->mdlMostrarItems($cod_barras);
        
        
        if ($busqueda->rowCount() > 0) {

            if($busqueda->rowCount() == 1){

                $row = $busqueda->fetch();

                //guarda los resultados en un arreglo
                $itembus=["estado"=>$row['estado'],
                           "contenido"=> ["codigo"=>$row["ID_CODBAR"],
                                           "iditem"=>$row["item"],  
                                           "referencia"=>$row["ID_REFERENCIA"],
                                           "descripcion"=>$row["DESCRIPCION"],
                                           "disponibilidad"=>$row["disp"],
                                           "pedidos"=>$row["pedido"],
                                           "alistados"=>$row["alistado"],
                                           "caja"=>$row["no_caja"],
                                           "alistador"=>$row["nombre"],
                                           "ubicacion"=>$row["ubicacion"],
                                           "origen"=>$row["lo_origen"],
                                           "destino"=>$row["lo_destino"]
                                         ]
                        ];
                
                // en el arreglo se guarda el estado de la consulta         
                switch ($itembus["estado"]) {

                    //0 si encontro algun resultaod en la consulta
                    case 0:
                        $itembus["estado"]='encontrado';
                        break;

                    // 1 si el item ya esta siendo alistado por alguien
                    case 1:
                        $itembus["estado"]='error1';
                        $itembus["contenido"]='Item en alistamiento por '.$itembus['contenido']['alistador'];
                        break;

                    // 2 si el item ya fue alistado en la caja
                    case 2:
                        $itembus["estado"]='error1';
                        $itembus["contenido"]='Item ya alistado en la caja '.$itembus['contenido']['caja'].' por '.$itembus['contenido']['alistador'];
                        break;
                    case 3:
                        $itembus["estado"]='error1';
                        $itembus["contenido"]='Item ya alistado en la caja '.$itembus['contenido']['caja'].' por '.$itembus['contenido']['alistador'];
                        break;
                    case 4:
                        $itembus["estado"]='error1';
                        $itembus["contenido"]='Item ya fue alistado y recibido en la caja '.$itembus['contenido']['caja'].' por '.$itembus['contenido']['alistador'];
                        break;
                }
                //retorna el item a la funcion
                return $itembus;

            }else {

                $itembus["estado"]=["encontrado"];

                $cont=0;
                
                while($row = $busqueda->fetch()){

                    //solo muestra los items que no estan alistados
                    if($row['estado']==0){
                        
                        $itembus["contenido"][$cont]=["codigo"=>$row["ID_CODBAR"],
                                           "iditem"=>$row["item"],  
                                           "referencia"=>$row["ID_REFERENCIA"],
                                           "descripcion"=>$row["DESCRIPCION"],
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

    public function ctrBuscarIE($item)
    {
        $busqueda=$this->modelo->mdlMostrarIE($item);
        
        // return $busqueda;
        if ($busqueda->rowCount() > 0) {

            if($busqueda->rowCount() == 1){

                $row = $busqueda->fetch();

                //guarda los resultados en un arreglo
                $itembus=["estado"=>"encontrado",
                           "contenido"=> ["codigo"=>$row["ID_CODBAR"],
                                           "iditem"=>$row["ID_ITEM"],  
                                           "referencia"=>$row["ID_REFERENCIA"],
                                           "descripcion"=>$row["DESCRIPCION"],
                                         ]
                         ];
                
               
                return $itembus;

            }else {

                $itembus["estado"]=["encontrado"];

                $cont=0;

                while($row = $busqueda->fetch()){
                        
                    $itembus["contenido"][$cont]=["codigo"=>$row["ID_CODBAR"],
                                        "iditem"=>$row["ID_ITEM"],  
                                        "referencia"=>$row["ID_REFERENCIA"],
                                        "descripcion"=>$row["DESCRIPCION"],
                                        ];
                    $cont++;

                }

                return $itembus;

            }

        //si no encuentra resultados devuelve "error"
        }else{

            return ['estado'=>"error",
                    'contenido'=>"Item no encontrado!"];

        }
    }

    public function ctrAgregarIE($items){
        $resultado=$this->modelo->mdlAgregarIE($items);
        return $resultado;
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

    public function ctrBuscarItemCaja($numcaja){
        
        $busqueda=$this->modelo->mdlMostrarItemsCaja($numcaja);

        if ($busqueda->rowCount() > 0) {

            $itembus["estado"]="encontrado";

            $cont=0;

            while($row = $busqueda->fetch()){
                
                // si hay cajas sin cerrar en otra requisicion
                if ($row['no_req']!=$this->req[0]) {
                    $itembus=['estado'=>"error2",
                    'contenido'=>$row['no_req']];
                    return $itembus;
                    break;
                }
                $itembus["contenido"][$cont]=["codigo"=>$row["ID_CODBAR"],
                                    "iditem"=>$row["item"],    
                                    "referencia"=>$row["ID_REFERENCIA"],
                                    "descripcion"=>$row["DESCRIPCION"],
                                    "disponibilidad"=>$row["disp"],
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
                    'contenido'=>"Caja sin Items!"];

        }

    }

    public function ctrCerrarCaja($tipocaja,$items,$req){
        $busqueda = $this->modelo->mdlMostrarNumCaja();
        $numcaja = ($busqueda->fetch());
        $numcaja = $numcaja['numcaja'];
        for ($i=0; $i <count($items) ; $i++) { 
            $cod_barras=$items[$i]["codigo"];
            $alistados=$items[$i]["alistados"];
            $resultado=$this->modelo->mdlAlistarItem($items[$i],$numcaja);
        }
        if ($resultado) {
            $resultado=$this->modelo->mdlCerrarCaja($tipocaja,$numcaja);
        }

        return $resultado;
    }

    public function ctrEliminarItemCaja($cod_barras,$no_caja=null){
        
        if ($no_caja==null) {
            $busqueda=$this->modelo->mdlMostrarNumCaja();
            $row=$busqueda->fetch();
            $no_caja=$row['numcaja'];
        }

        return $this->modelo->mdlEliminarItemCaja($cod_barras,$no_caja);

    }
}
