<?php

class ControladorRemision{

    private $modelo;
    private $documentos;
    private $itemsarray;

    function __construct($documentos){

      $this->documentos=$documentos;

    }


    // funcion que asigna la cabecera
    private function ctrSetCabecera(){

        foreach($this->doc_req as $linea){
            
            $linea=($linea.'<br>');

            //se buscan los datos de cabecera en cada linea
            if(!($linea[0]!='|' &&  $linea[2]!='-' && strripos($linea,':')==false && ord($linea)!=10)){
                
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
                
                //busca la localizacion deorigen
                $pos=strpos($linea, 'Local. Origen  :');
                if($pos){
                    $lo=substr($linea,$pos+17,6);	
                }

                //busca la localzacion de destino
                $pos=strpos($linea, 'Local. Destino :');
                if($pos){
                    $ld=substr($linea,$pos+17,6);	
                }

                //busca el tipo de inventario
                $pos=strpos($linea, 'Tipo Inventario:');
                if($pos){
                    $tipInv=substr($linea,$pos+17,7);	
                }

                //busca el numero de requerido
                $pos=strpos($linea, 'Nro Req:');
                if($pos){
                    $req=substr($linea,$pos+9,10);	
                }

                //busca el nombre solicitante
                $pos=strpos($linea, 'Solicit:');
                if($pos){
                    $sol=substr($linea,$pos+9,27);	
                }

                // si ya tiene todos los datos de cabecera los ingresa a la base de datos
                if(isset($tiempo) && isset($lo) && isset($ld) &&isset($tipInv) && isset($req) && isset($sol)){
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
                    $cabecera=[$req,$fecha,$lo,$ld,$tipInv,$sol];
                    
                    //asigna los dtaos de cabecera al parametro de la clase
                    $this->cabecera=$cabecera;
                    
                    // si ya encontro todos los datos decabecera se sale del ciclo
                    break;
                    
                }

            }

        }

    }

    //funcion que asigna los items
    public function ctrSetItems(){

        $stringItem="";
        $contador=0;
        foreach($this->documentos as $i => $documento){
            foreach ($documento as  $row) {
                
                // $linea=($linea."<br>");
                $linea=trim($row);
            //    return ($linea[0]);
                //busca si la linea tiene datos de los items
                // es un item si la linea no tiene | de primer caracter, no tiene una sucecion de -, no tiene : y si no es salto de linea(ascii=10)
                if( !empty($linea))
                    if(!in_array($linea[0],["|","+","-"]) 
                       && strpos($linea, 'REMISIONES')===false 
                       && strpos($linea, 'DROGUERIA SAN')===false){
                                        
                        // $item["linea"]=$linea;
                        $item["item"]=trim(substr($linea,0,6)); 
                        $item["descripcicon"]=trim(substr($linea,7,37)); 
                        $item["local"]=trim(substr($linea,44,6));
                        $item["cantidad"]=trim(str_replace(",","",substr($linea,55,5)));
                        $item["unidad"]=trim(str_replace(",","",substr($linea,63,3)));
                        $item["valor"]=trim(str_replace(",","",substr($linea,70,9)));
                        $item["descuento"]=trim(str_replace(",","",substr($linea,85,6)));
                        $item["impuesto"]=trim(str_replace(",","",substr($linea,94,9)));
                        $item["total"]=trim(str_replace(",","",substr($linea,107,9)));
                        $item["costo"]=trim(str_replace(",","",substr($linea,119,9)));
                        $item["rent"]=trim((substr($linea,129,6)));
                        
                        
                    
                        if (!(is_numeric($item["item"]) && strlen($item["item"])==6)) {
                            
                            //se busca el id de los item usando la referencia en el documento subido
                            $modelo=new ModeloRequierir();
                            // $item="ID_REFERENCIA";
                            $valor=$item["item"];
                            
                            $id_item=$modelo->mdlMostrarItem("ID_REFERENCIA",$valor);
                            $id_item=$id_item->fetch(); 
                                    
                            //se reemplaza la referencia del item por su id
                            $item["item"]=$id_item["ID_ITEM"];
                        
                        }
                    
                        
                        // echo ($item[0]."  ".$item[1]."  ".$item[2]."  ".$item[3]." ".$item[4]."  ".$item[5]."<br>");

                        //pone los datos del item en un String
                        $stringItem.="(";
                        foreach ($item as $value) {
                            $stringItem.="'$value',";
                        }
                        $stringItem=substr($stringItem, 0, -1)."),";
                        
                        $this->itemsarray[$i][]=$item;
                
                //busca el numero de requisicion solo si no se ha encontrado
                }
            }
        }
        
        $stringItem = substr($stringItem, 0, -1).';';


        //asigna al parametro de Items todos lo  items encontrados
        
        // $this->items=$stringItem; 
        return $this->itemsarray;

    }

    // funcion que sube los items
    public function ctrSubirRem($folder){
        $modelo=new ModeloRemision();
        // return $this->itemsarray;
        foreach ($this->itemsarray as $array) {
            // return $array;
            $folder=substr($folder,0,5);
            $resultado=$modelo->mdlSubirRem($folder);
            
            if ($resultado) {
                $no_rem2=$modelo->mdlMostrarRem($folder);
                // $no_rem2=$no_rem2->fetch()["no_rem2"];
                
                $no_rem2=$no_rem2->fetch()["no_rem2"];
                
                
                foreach ($array as  $item) {
                    // return $item;
                    // $resultado2[]=$modelo->mdlSubirItem($item,$folder,$no_rem2);
                    $resultado=$modelo->mdlSubirItem($item,$folder,$no_rem2);
                    if (!$resultado) {
                        return false;
                    }
                }

            }else {
                // $resultadoel=$modelo->mdlEliRem($this->cabecera[0]);
                
                return $resultado;
            }
        }
        return $resultado;
    }

}