<?php
include "../controladores/tareas.controlador.php";

// llibreria conectar impresora 
require __DIR__ . '/../lib/impresora_mike42/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class ControladorAlistar extends ControladorTareas{
    /* ============================================================================================================================
                                                        ATRIBUTOS   
    ============================================================================================================================*/
    protected $req;
    private $modelo;
    private $numcaja;//se guarda el numero de la ultima caja cerrada
    private $tipo_inventario=[
        1=>"PRIMA",
        2=>"QUIMICO",
        3=>"ETICO"
    ];

    /* ============================================================================================================================
                                                        CONSTRUCTOR   
    ============================================================================================================================*/
    function __construct($req=null) {
        
        parent::__construct();
        $this->req=$req;
        $this->modelo=new ModeloAlistar($req);

    }

    /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/

    // busca los o el item en la tabla de alistado
    public function ctrBuscarItem($cod_barras)
    {
        
        // busca el numero de la ultima acaja abierta por el usuario
        $busqueda = $this->modelo->mdlMostrarNumCaja();
        $numcaja = ($busqueda->fetch());
        $numcaja = $numcaja['numcaja'];
        // libera conexion para hace otra sentencia
        $busqueda->closeCursor();

        $busqueda=$this->modelo->mdlMostrarItems($cod_barras);

        if ($busqueda->rowCount() > 0) {

                        
            while($row = $busqueda->fetch()){

                //solo muestra los items que no estan alistados
                $itembus["estado"]=$row['estado'];                        
                $itembus["contenido"]=["codigo"=>$row["ID_CODBAR"],
                                        "iditem"=>$row["item"],  
                                        "referencia"=>$row["ID_REFERENCIA"],
                                        "descripcion"=>$row["DESCRIPCION"],
                                        "disponibilidad"=>$row["disp"],
                                        "pedido"=>$row["pedido"],
                                        "pendientes"=>$row["pendientes"],
                                        "alistados"=>$row["alistado"],
                                        "caja"=>$row["no_caja"],
                                        "alistador"=>$row["nombre"],
                                        "ubicacion"=>$row["ubicacion"],
                                        "origen"=>$row["lo_origen"],
                                        "destino"=>$row["lo_destino"]
                                        ];
                
                
                // comprueba el estado del item pedido
                switch ($itembus["estado"]) {

                    //0 si encontro algun resultaod en la consulta
                    case 0:
                        $itembus["estado"]="encontrado";
                        break;

                    // 1 si el item ya esta siendo alistado pro alguien
                    case 1:
                        $itembus["estado"]="error1";
                        $itembus["contenido"]="El item ya fue Alistado";
                        return $itembus;
                        break;

                }
                // si el item ya esta en la caja del alistador se regresa un mensaje informando 
                if ($numcaja==$row["no_caja"]) {
                    $itembus["estado"]="error2";
                    $itembus["contenido"]="Item ya esta en la caja";
                    return $itembus;
                }
                
            }
            
            
            //retorna el item a la funcion
            return $itembus;

        //si no encuentra resultados devuelve "error"
        }else{

            return ['estado'=>"error",
                    'contenido'=>"Item no encontrado en la base de datos!"];

        }
    }

    // busca todos los items de la tabla pedido de una requisicion
    public function ctrBuscarItemsReq($estado=null)
    {   
        $alistador=$this->req[1];
        // busca ubicaciones
        $ubicaciones=$this->ctrBuscarUbicaciones($alistador);
        
        $busqueda=$this->modelo->mdlMostrarItems('%%');

        if ($busqueda->rowCount() > 0) {

            $itembus["estado"]="encontrado";

            $cont=0;

            // muestra todos los items de la requisicion
            if ($estado!=null) {

                  while($row = $busqueda->fetch()){
                    
                        
                    // se usa el id del item como el index en el arreglo
                    // si se encuentra 2 veces el mismo item este se remplaza
                    $itembus["contenido"][$row["item"]]=["codigo"=>$row["ID_CODBAR"],
                                        "referencia"=>$row["ID_REFERENCIA"],
                                        "descripcion"=>$row["DESCRIPCION"],
                                        "disponibilidad"=>$row["disp"],
                                        "pendientes"=>$row["pendientes"],
                                        'ubicacion'=>$row["ubicacion"]
                                        ];
                    
                    $cont++;
                    $itembus["ubicaciones"][$cont]=$row["ubicacion"];                          
                    

                }
            //solo muestra los items que no estan alistados
            }else {
                // si el alistador tiene ubiaciones asignadas
                if ($ubicaciones) {
                    // $itembus["ubicaciones"]=$ubicaciones;
                    while($row = $busqueda->fetch()){
                    
                        if($row["estado"]==0 && in_array(trim($row['ubicacion']),$ubicaciones)){
                            
                            // se usa el id del item como el index en el arreglo
                            $itembus["contenido"][$row["item"]]=["codigo"=>$row["ID_CODBAR"],
                            "referencia"=>$row["ID_REFERENCIA"],
                            "descripcion"=>$row["DESCRIPCION"],
                            "disponibilidad"=>$row["disp"],
                            "pendientes"=>$row["pendientes"],
                            'ubicacion'=>$row["ubicacion"]
                            ];

                            // almacena ubicaciones
                            $cont++;
                            $itembus["ubicaciones"][$cont]=$row["ubicacion"];   
                                                    
                        }
                    }
                }else {
                    while($row = $busqueda->fetch()){

                        if($row["estado"]==0 ){

                            $itembus["contenido"][$row["item"]]=["codigo"=>$row["ID_CODBAR"],
                            "referencia"=>$row["ID_REFERENCIA"],
                            "descripcion"=>$row["DESCRIPCION"],
                            "disponibilidad"=>$row["disp"],
                            "pendientes"=>$row["pendientes"],
                            'ubicacion'=>$row["ubicacion"]
                            ];

                            // almacena ubicaciones
                            $cont++;
                            $itembus["ubicaciones"][$cont]=$row["ubicacion"];                          
                        }
                    }
                }
                    
                    
            }
            if ($cont>0) {
                $itembus["ubicaciones"]=array_unique($itembus["ubicaciones"]);
            }
            
            return $itembus;


        //si no encuentra resultados devuelve "error"
        }else{

            return ['estado'=>"error",
                    'contenido'=>"Item no encontrado en la base de datos!"];

        }
    }

    // busca items por fuera de la requisicion
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

    // agrega un item extra a la requisicion
    public function ctrAgregarIE($items)
    {
        $resultado=$this->modelo->mdlAgregarIE($items);
        return $resultado;
    }

    //crea una caja si no existe
    public function ctrCrearCaja()
    {
        
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

    // busca los items de 1 caja
    public function ctrBuscarItemCaja($numcaja)
    {
        
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
                $itembus["contenido"][]= $row;                 

            }

            
            
            return $itembus;

        //si no encuentra resultados devuelve "error"
        }else{

            return ['estado'=>"error",
                    'contenido'=>"Caja sin Items!"];

        }

    }
    
    //cierra la caja
    public function ctrCerrarCaja($items,$tipocaja,$pesocaja,$numcaja=null)
    {
        
        // busca el numero de la ultima acaja abierta por el usuario
        if ($numcaja==null) {
            $busqueda = $this->modelo->mdlMostrarNumCaja();
            $numcaja = ($busqueda->fetch());
            $numcaja = $numcaja['numcaja'];
            $this->numcaja = $numcaja;
            $busqueda->closeCursor(); 
        }
                
        for ($i=0; $i <count($items) ; $i++) { 
            $resultado=$this->modelo->mdlAlistarItem($items[$i],$numcaja);
        }
        
        if ($resultado) {
            $resultado=$this->modelo->mdlCerrarCaja($tipocaja,$pesocaja,$numcaja);
        }

        // if ($resultado) {
        //     $resultado=$this->ctrDocList($numcaja);
        //     return $resultado['estado'];
        // }

        return $resultado;
    }

    // elimina 1 item de una caja
    public function ctrEliminarItemCaja($cod_barras,$numcaja=null)
    {
        
        if ($numcaja==null) {
            $busqueda=$this->modelo->mdlMostrarNumCaja();
            $row=$busqueda->fetch();
            $numcaja=$row['numcaja'];
        }

        return $this->modelo->mdlEliminarItemCaja($cod_barras,$numcaja);

    }

    // alista 1 solo item en la caja
    public function ctrAlistarItem($item)
    {
        // busca el numero de la ultima acaja abierta por el usuario
        $busqueda = $this->modelo->mdlMostrarNumCaja();
        $numcaja = ($busqueda->fetch());
        $numcaja = $numcaja['numcaja'];
        $busqueda->closeCursor();        

        $resultado=$this->modelo->mdlAlistarItem($item,$numcaja);
        // if ($resultado) {
        //     return $numcaja;
        // }
        return $resultado;   

    }

    public function ctrTerminarreq($req)
    {
        $resultado=$this->modelo->mdlTerminarreq($req);
        return $resultado;
    }
    // CREA LISTA DE ITEMS Y LO MANDA A IMPRIMIR
    public function ctrDocList($numcaja=null){
            
        if ($numcaja==null) {
            // $busqueda = $this->modelo->mdlMostrarNumCaja();
            // $numcaja = ($busqueda->fetch());
            // $numcaja = $numcaja["numcaja"];
            // libera conexion para hace otra sentencia
            // $busqueda->closeCursor();
            $numcaja = $this->numcaja;
        }
        $busqueda=$this->modelo->mdlMostrarDocList($numcaja);
        $datos=$busqueda->fetchAll();
        // obtiene el numero de caja
        $caja0=str_pad($datos[0]["no_caja"], 3, "0", STR_PAD_LEFT);
        $caja=$datos[0]["no_caja"];
        // se obtiene el numero de requisicion
        $req=substr($datos[0]["no_req"],-6);
        //se obtiene el destino de la requisicion
        $destino=$datos[0]["lo_destino"];
        $destinodes=trim($datos[0]["sede"]," \t\n\r");
        // se obtiene fecha y hora en que se cerro la caja
        $fecha=$datos[0]["fecha"];
        $hora=$datos[0]["hora"];
        // se obtiene el nombre de alistador
        $alistador=substr($datos[0]["nombre"],0,27);
        // se obtiene el peso de la caja
        $peso=str_pad($datos[0]["peso"],8," ",STR_PAD_LEFT);
        // se obtiene el tipo de inventario de la requisicion
        $observacion=$this->tipo_inventario[$datos[0]["tip_inventario"]];
        
        // total de items
        $total=0;
        //se obtiene la lista de items de la caja
        $item="";
        foreach ($datos as $value) {

            $iditem=$value["item"];
            $descripcion=substr(trim($value["descripcion"]," \t\n\r"),0,22);
            $um=$value["um"];
            $cantidad=str_pad($value["alistado"], 4, " ", STR_PAD_LEFT);
            $total+=$value["alistado"];

            $item.=str_pad($value["item"], 9, " ", STR_PAD_RIGHT) . str_pad($descripcion, 22, " ", STR_PAD_RIGHT) . str_pad("", 4, " ", STR_PAD_RIGHT) . str_pad($um, 4, " ", STR_PAD_RIGHT)."\r\n";
            $item.=str_pad("Cantidad: $cantidad", 20, " ", STR_PAD_RIGHT) . str_pad("", 20, " ", STR_PAD_RIGHT)."\r\n";

        }


        // cabecera del recibo 
        $imprimir="\r\n";
        $imprimir.=str_repeat("\n",6 );
        
        $imprimir.=str_pad("FARMACIA DROGUERIA SAN JORGE LTDA DROGUE", 40, " ", STR_PAD_BOTH)."\r\n";
        $imprimir.=str_pad("NIT.: 805002583-1", 40, " ", STR_PAD_BOTH)."\r\n";
        $imprimir.=str_pad("CR 2 14 34", 40, " ", STR_PAD_BOTH)."\r\n";
        $imprimir.=str_pad("Tel: 8801216 Fax: 8801216 EXT 11", 40, " ", STR_PAD_BOTH)."\r\n";
        $imprimir.=str_pad("CALI - COLOMBIA", 40, " ", STR_PAD_BOTH)."\r\n";
        $imprimir.=str_pad("REGIMEN COMUN", 40, " ", STR_PAD_BOTH)."\r\n\r\n";

        $imprimir.=str_pad("PEDIDO DE DISTRIBUCION SALIDA DESDE BOD", 40, " ", STR_PAD_BOTH)."\r\n\r\n";

        //datos de la caja
        $imprimir.=str_pad("C.O      : $caja0", 40, " ", STR_PAD_RIGHT)."\r\n";
        $imprimir.=str_pad("Doc. Alt#: CAJA#$caja", 20, " ", STR_PAD_RIGHT) . str_pad("Fecha : $fecha", 20, " ", STR_PAD_RIGHT)."\r\n";
        $imprimir.=str_pad("Req.    #: $req", 20, " ", STR_PAD_RIGHT) . str_pad("Hora  : $hora", 20, " ", STR_PAD_RIGHT)."\r\n";
        $imprimir.=str_pad("Loc. Ori.:       ", 20, " ", STR_PAD_RIGHT) . str_pad("Loc. Desct.: $destino", 20, " ", STR_PAD_RIGHT)."\r\n";
        
        $imprimir.=str_repeat("-",40 )."\r\n";

        // lista de items en la caja
        $imprimir.=str_pad("Ref.", 9, " ", STR_PAD_RIGHT) . str_pad("Descripcion", 23, " ", STR_PAD_RIGHT) . str_pad("IVA", 4, " ", STR_PAD_RIGHT) . str_pad("UM", 4, " ", STR_PAD_RIGHT)."\r\n";
        $imprimir.=$item."\r\n";            
        $imprimir.=str_repeat("-",40 )."\r\n";
        $imprimir.=str_pad("TOTAL ......", 20, " ", STR_PAD_RIGHT) . str_pad("$total", 20, " ", STR_PAD_LEFT)."\r\n";
        $imprimir.=str_repeat("-",40 )."\r\n";

        // datos generales de la caja
        $imprimir.=str_pad("USUARIO ING. $alistador", 40, " ", STR_PAD_RIGHT)."\r\n\r\n";
        $imprimir.=str_pad("Obervacion: PEDIDO $observacion CAJA#$caja ", 40, " ", STR_PAD_RIGHT)."\r\n";
        $imprimir.=str_pad("          : $destinodes", 40, " ", STR_PAD_RIGHT)."\r\n\r\n";
        $imprimir.=str_pad("RECIBI CONFORME: ", 40, "_", STR_PAD_RIGHT)."\r\n";


        // parte 2 de l resibo de items
        $imprimir.=str_repeat("\r\n",10 );

        $imprimir.=str_repeat("-",40 )."\r\n";

        $imprimir.=str_repeat("\r\n",3 );

        $imprimir.=str_pad("PEDIDO   #: $req", 40, " ", STR_PAD_BOTH)."\r\n";
        $imprimir.=str_pad("EMPAQUE  #: CAJA#$caja", 40, " ", STR_PAD_BOTH)."\r\n\r\n";

        $imprimir.=str_pad("FECHA   : $fecha", 40, " ", STR_PAD_RIGHT)."\r\n";
        $imprimir.=str_pad("ORIGEN  :            ", 40, " ", STR_PAD_RIGHT)."\r\n";
        $imprimir.=str_pad("DESTINO : $destinodes", 40, " ", STR_PAD_RIGHT)."\r\n\r\n";

        $imprimir.=str_pad("PESO TOTAL: $peso kg", 40, " ", STR_PAD_RIGHT)."\r\n";

        $imprimir.=str_repeat("\r\n",5 );
        
        
        // try
        // {
        //     $fp=fsockopen("192.168.0.41", 9100);
        //     fwrite($fp, $imprimir);
        //     fclose($fp);

        //     echo 'Successfully Printed';
        // }
        // catch (Exception $e) 
        // {
        //     echo 'Caught exception: ',  $e->getMessage(), "\r\n";
        // }
        
        // $resultado["contenido"]=$imprimir;
        // imprime string en la impresora
        $resultado["contenido"]=$imprimir;
        
        try {
            $connector = new WindowsPrintConnector("epsonliza_contab");
            $connector = new WindowsPrintConnector("hpljp1102");
            $printer = new Printer($connector);
            $printer -> text($imprimir);
            $printer -> cut();
            /* Close printer */
            $printer -> close();
            $resultado["estadoimp"]=true;
        } catch (Exception $e) {
            $resultado["estadoimp"]=false;
        }
        


        // $resultado["estado"]=true;
        return $resultado;
        
    }
}   
