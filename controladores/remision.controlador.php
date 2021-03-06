<?php

class ControladorRemision extends ControladorLoginUsuario{

    /* ============================================================================================================================
                                                        ATRIBUTOS   
    ============================================================================================================================*/
    private $modelo;
    private $documentos;
    private $itemsarray;
    private $ubicacion;
    private $no_rem;
    private $franquicia;
    private $cabecera;

    /* ============================================================================================================================
                                                        CONSTRUCTOR   
    ============================================================================================================================*/
    function __construct($documentos=null,$franquicia=null){
      $this->modelo=new ModeloRemision();
      $this->documentos=$documentos;
      $this->franquicia=$franquicia;
    }

    /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/
    // funcion que asigna la cabecera
    public function ctrSetCabecera()
    {
        foreach($this->documentos as $i => $documento){
            foreach ($documento as  $row) {
                $linea=trim($row);
           
                
                if( !empty($linea))
                    if(in_array($linea[0],["|","+","-"])){
                    
                    //busca la fecha 
                    $pos=strpos($linea, 'FECHA :');
                    
                    if($pos){
                        $fecha=str_replace('/','-',substr($linea,$pos+8,10));	
                    }
                    
                    //busca la hora y minuto
                    $pos=strpos($linea, 'HORA  :');
                    if($pos){
                        $tiempo=substr($linea,$pos+8,8);	
                    }
        
                    // si ya tiene todos los datos de cabecera los ingresa a la base de datos
                    if(isset($tiempo) && isset($fecha)){
                        // se obtiene solo hora
                        $hora=substr($tiempo,0,2);
                        // CONVIERTE LA HORA A FORMATO DE 24 HORAS
                        // si es pm se suma 12 a la hora
                        if (!strcasecmp(substr($tiempo,-2),'pm')) {
                            if ($hora!=12) {
                                $hora+=12;
                            }
                        }elseif ($hora==12) {
                            $hora=0;
                        }
                        // guarda el tiempo en formato 24 horas
                        $tiempo=substr($hora.substr($tiempo,2),0,-3).':00';
                        // pone fecha y hora en 1 solo string
                        $fecha.=' '.$tiempo;
                        
                        // almacena los datos de cabecera en un vector
                        $cabecera=["fecha"=>$fecha];;
                        
                        
                        //asigna los dtaos de cabecera al parametro de la clase
                        $this->cabecera=$cabecera;
                        
                        // si ya encontro todos los datos decabecera se sale del ciclo
                        return $cabecera;
                        
                    }

                }
            }
        }
    }

    //funcion que asigna los items
    public function ctrSetItems()
    {

        $stringItem="";
        $contador=0;
        foreach($this->documentos as $i => $documento){
            foreach ($documento as  $row) {
                
                // $linea=($linea."<br>");
                $linea=trim($row);
           
                
                if( !empty($linea))
                    if(!in_array($linea[0],["|","+","-"]) 
                       && strpos($linea, "REMISIONES")===false 
                       && strpos($linea, "DROGUERIA SAN")===false){
                        
                        $iditem=trim(substr($linea,0,6)); 
                    
                            
                        // busca la exitencia del item
                        $modelo=new ModeloRequierir();
                        
                        $valor=$iditem;
                        
                        $busqueda=$modelo->mdlMostrarItem($valor);
                        $iditem=$busqueda->fetch(); 
                        $busqueda->closeCursor();
                        $iditem=$iditem["ID_ITEM"];

                        
                        if ($iditem!==null) {

                            if (!empty($this->itemsarray[$iditem])) {
                                $this->itemsarray[$iditem]["cantidad"]+=trim(str_replace(",","",substr($linea,55,5)));
                                $this->itemsarray[$iditem]["valor"]+=trim(str_replace(",","",substr($linea,70,9)));
                                $this->itemsarray[$iditem]["descuento"]+=trim(str_replace(",","",substr($linea,85,6)));
                                $this->itemsarray[$iditem]["impuesto"]+=trim(str_replace(",","",substr($linea,94,9)));
                                $this->itemsarray[$iditem]["total"]+=trim(str_replace(",","",substr($linea,107,9)));
                                $this->itemsarray[$iditem]["costo"]+=trim(str_replace(",","",substr($linea,119,9)));
                            }else {
                                $this->itemsarray[$iditem]["cantidad"]=trim(str_replace(",","",substr($linea,55,5)));
                                $this->itemsarray[$iditem]["valor"]=trim(str_replace(",","",substr($linea,70,9)));
                                $this->itemsarray[$iditem]["valor"]=trim(str_replace(",","",substr($linea,70,9)));
                                $this->itemsarray[$iditem]["descuento"]=trim(str_replace(",","",substr($linea,85,6)));
                                $this->itemsarray[$iditem]["impuesto"]=trim(str_replace(",","",substr($linea,94,9)));
                                $this->itemsarray[$iditem]["total"]=trim(str_replace(",","",substr($linea,107,9)));
                                $this->itemsarray[$iditem]["costo"]=trim(str_replace(",","",substr($linea,119,9)));
                            }
                            $this->itemsarray[$iditem]["item"]=$iditem; 
                            // $this->itemsarray[$iditem]["descripcicon"]=trim(substr($linea,6,37)); 
                            $this->itemsarray[$iditem]["local"]=trim(substr($linea,44,6));
                            $this->ubicacion=trim(substr($linea,44,6));
                            $this->itemsarray[$iditem]["unidad"]=trim(str_replace(",","",substr($linea,63,3)));
                            $this->itemsarray[$iditem]["rent"]=trim((substr($linea,129,6)));
                        
                        }    
                }
            }
        }

        return $this->itemsarray;

    }

    // funcion que signa lote a  los items
    public function ctrAsignarLote($items,$no_rem)
    {
        foreach ($items as $item) {
            $resultado=$this->modelo->mdlAsignarLote($item,$no_rem);
        }
        
        return  $resultado;
    }

    // funcion que sube los items
    public function ctrSubirRem($usuario)
    {
        $resultado=false;
        
        $this->no_rem=$this->modelo->mdlSubirRem($usuario,$this->franquicia,$this->cabecera["fecha"]);
        if ($this->no_rem!==false) {

            $resultado=$this->modelo->mdlSubirItems($this->itemsarray,$this->no_rem);

        }
        

        return $resultado;
    }

    // funcion que genera el archivo plano de la remision unificada
    public function ctrDocRem($no_rem=null)
    {   
        if (!isset($no_rem)) {
            $no_rem=$this->no_rem;
        }
        $busqueda=$this->modelo->mdlMostrarRemDoc($no_rem);
        $resultado["documento"]="";
        $resultado["no_rem"]=$no_rem;        
        
        while($row = $busqueda->fetch()){
            
            if ($row["eslote"]=="NO" || ($row['lote']!=null && $row['vencimiento']!=null )) {
                $pordesc=$row["descuento"]/$row["valor"]*100;
                $pordesc=round($pordesc,2);
                
                $no_rem=str_pad("$row[no_rem]",3, "0", STR_PAD_LEFT);
                // $resultado['nomdoc']="REMIS$no_rem.RM0";

                
                $resultado["documento"].=str_pad("OC$no_rem", 10, " ", STR_PAD_RIGHT);
                $resultado["documento"].="2";
                $resultado["documento"].=str_repeat(" ",20);
                $resultado["documento"].=str_pad($row["nit"], 13, " ", STR_PAD_RIGHT);
                $resultado["documento"].=$row["cod_sucursal"];
                $resultado["documento"].=str_replace("-","",substr($row["creada"],0,10));
                $resultado["documento"].=str_replace("-","",$row["ubicacion"]);
                $resultado["documento"].="I";
                $resultado["documento"].=str_repeat(" ",15);
                $resultado["documento"].=str_pad($row["item"], 15, " ", STR_PAD_RIGHT);
                $resultado["documento"].="   ";
                $resultado["documento"].=$row["unidad"];
                $resultado["documento"].=str_pad(str_replace(".","",$row["cantidad"]), 12, "0", STR_PAD_LEFT)."+";
                $resultado["documento"].="000000000000+";
                $resultado["documento"].="1";
                $resultado["documento"].="UNI";
                $resultado["documento"].="03";
                // $resultado["documento"].=str_pad(str_replace(".","",$row["valor"]), 11, "0", STR_PAD_LEFT)."+";
                $resultado["documento"].=str_repeat("0",11)."+";
                $resultado["documento"].="  ";
                $resultado["documento"].="02";
                $resultado["documento"].=str_repeat(" ",8);
                $resultado["documento"].=str_repeat(" ",10);
                // $resultado["documento"].=str_pad(str_replace(".","",$row["total"]), 14, "0", STR_PAD_LEFT)."+";
                $resultado["documento"].=str_repeat("0",14)."+";
                $resultado["documento"].=str_repeat(" ",6);
                $resultado["documento"].="0000000000000+";
                // $resultado["documento"].=str_pad(str_replace(".","",$pordesc), 4, "0", STR_PAD_RIGHT);
                // $resultado["documento"].=str_pad(str_replace(".","",$row["descuento"]), 11, "0", STR_PAD_LEFT);
                $resultado["documento"].=str_repeat("0",4);
                $resultado["documento"].=str_repeat("0",11);
                $resultado["documento"].=str_repeat("0",4);
                $resultado["documento"].=str_repeat("0",11);
                $resultado["documento"].="1";
                $resultado["documento"].=str_repeat(" ",20);

                $resultado["documento"].=str_repeat(" ",2);
                $resultado["documento"].=str_repeat(" ",3);
                $resultado["documento"].=str_repeat(" ",2);//ERROR ¿realmente son 3 columnas?
                $resultado["documento"].=str_repeat(" ",6);
                $resultado["documento"].=str_repeat(" ",60);
                
                $resultado["documento"].=str_pad($row["lote"], 12, " ", STR_PAD_RIGHT);
                $resultado["documento"].=str_replace("-","",$row["vencimiento"]);

                


                $resultado["documento"].="\r\n";
            }else {
                $resultado["lotes"][]=["item"=>$row["item"],
                                     "descripcion"=>$row["DESCRIPCION"],
                                     "cantidad"=>$row["cantidad"],
                                     "descuento"=>$row["descuento"],
                                     "total"=>$row["total"]
                                     ];
            }
        }
        return $resultado;
    }

    public function ctrDocRemCopi($factura=null,$no_rem=null)
    {
        if (!isset($no_rem)) {
            $no_rem=$this->no_rem;
        }
        $busqueda=$this->modelo->mdlMostrarRemDoc($no_rem,$factura);
        
        $resultado["documento"]="";
        $resultado["no_rem"]=$no_rem;        
        
        while($row = $busqueda->fetch()){
            $cantidad=round($row["cantidad"]);
            $valor_desc=abs(round($row["valor"]-$row["descuento"]));
            
            $pordesc=$row["descuento"]/$row["valor"]*100;// % descuento
            // $pordesc=round($pordesc,2);
            $pordesc=number_format($pordesc, 2);
            $resultado["documento"].="13803".","; //codigo drogueria
            $resultado["documento"].=str_replace("-","",substr($row["creada"],0,10)).",";
            $resultado["documento"].=str_pad($factura, 20, " ", STR_PAD_RIGHT).",";//factura
            $resultado["documento"].=str_pad($row["ID_REFERENCIA"], 15, " ", STR_PAD_RIGHT).",";//referencia copi
            $resultado["documento"].=str_pad($row["DESCRIPCION"], 40, " ", STR_PAD_RIGHT).",";
            $resultado["documento"].=str_pad($cantidad, 7, "0", STR_PAD_LEFT).",";
            $resultado["documento"].=str_pad($valor_desc, 10, "0", STR_PAD_LEFT).",";
            $resultado["documento"].=str_pad(round($row["valor"]), 10, "0", STR_PAD_LEFT).",";
            $resultado["documento"].=str_pad($row["IVA"]/100, 5, "0", STR_PAD_LEFT).",";
            $resultado["documento"].=str_pad($pordesc, 5, "0", STR_PAD_LEFT).",";
            $resultado["documento"].=str_pad("J28", 5, " ", STR_PAD_LEFT).",";// codigo de fabricante
            $resultado["documento"].="0000000000".",";
            $resultado["documento"].=str_pad(0, 5, " ", STR_PAD_LEFT).",";
            $resultado["documento"].=str_pad($row["unidad"], 3, " ", STR_PAD_RIGHT).",";
            $resultado["documento"].="2".",";// no se sabe
            $resultado["documento"].="01";// no se sabe

            $resultado["documento"].="\r\n";

        }
        return $resultado;
    }

    public function ctrDocRemEA($factura=null,$no_rem=null)
    {
        if (!isset($no_rem)) {
            $no_rem=$this->no_rem;
        }
        $busqueda=$this->modelo->mdlMostrarRemDoc($no_rem);
        $resultado["documento"]="";
        $resultado["no_rem"]=$no_rem;        
        
        while($row = $busqueda->fetch()){
            $cantidad=round($row["cantidad"]);
            $valor_desc=abs(round($row["valor"]-$row["descuento"]));
            $pordesc=$row["descuento"]/$row["valor"]*100;// % descuento
            // $pordesc=round($pordesc,2);
            $pordesc=number_format($pordesc, 2);
            $resultado["documento"].=str_pad($factura, 8, " ", STR_PAD_RIGHT);//factura
            $resultado["documento"].=str_pad("805002583", 13, " ", STR_PAD_RIGHT);// nit compra
            $resultado["documento"].="01";
            $resultado["documento"].=str_replace("-","",substr($row["creada"],0,10));
            $resultado["documento"].=str_pad("0008", 4, "0", STR_PAD_RIGHT);//codigo de compra
            $resultado["documento"].=str_pad("002", 3, "0", STR_PAD_RIGHT);//sede
            $resultado["documento"].="VE";
            $resultado["documento"].="I";
            $resultado["documento"].=str_pad($row["codbar"],15, " ", STR_PAD_RIGHT);
            $resultado["documento"].=str_pad($row["item"],15, " ", STR_PAD_RIGHT);
            $resultado["documento"].="   ";
            $resultado["documento"].=str_pad(str_replace(".","",$row["FACTOR_EMPAQ"]), 9, "0", STR_PAD_LEFT);
            $resultado["documento"].=str_pad($row["unidad"], 3, " ", STR_PAD_RIGHT);
            $resultado["documento"].=str_pad(str_replace(".","",$row["valor"]), 12, "0", STR_PAD_LEFT)."+";
            $resultado["documento"].=str_pad("0", 12, "0", STR_PAD_LEFT)."+";//transaccion 2
            $resultado["documento"].=str_pad(str_replace(".","",$row["ULTIMO_COSTO_ED"]), 11, "0", STR_PAD_LEFT)."+";
            $resultado["documento"].=str_pad(str_replace(".","",$pordesc), 4, "0", STR_PAD_LEFT);
            $resultado["documento"].=str_pad(str_replace(".","",0), 4, "0", STR_PAD_LEFT);//descuento 2
            $resultado["documento"].=str_pad(str_replace(".","",$row["IVA"]), 4, "0", STR_PAD_LEFT);//iva
            $resultado["documento"].="01";//motivo de compra
            $resultado["documento"].="                       ";
            $resultado["documento"].="000000";
            $resultado["documento"].="                                          ";
            $resultado["documento"].="00000000";
            $resultado["documento"].="                    ";

            $resultado["documento"].="\r\n";

        }
        return $resultado;
    }
}