<?php

class ControladorRemision{

    private $modelo;

    function __construct($doc_req){
      
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
    private function ctrSetItems(){

        $stringItem='';
        $contador=0;
        foreach($this->doc_req as $linea){

            $linea=($linea.'<br>');

            //busca si la linea tiene datos de los items
            // es un item si la linea no tiene | de primer caracter, no tiene una sucecion de -, no tiene : y si no es salto de linea(ascii=10)
            if($linea[0]!='|' &&  $linea[2]!='-' && strripos($linea,':')==false && ord($linea)!=10 && $linea[0]==' '){
                                
                //obtienen los datos de cada item por linea
                $item["iditem"]=str_replace(' ','',substr($linea,1,11)); //numero referencia o id del item
                $item["no_req"]=$this->cabecera[0];//se obtiene el numero de requisicion
                //se cambian las comas del dato por espacios en blanco
                $item["ubicacion"]=substr($linea,109,6);//ubiacion item
                $item["disp"]=str_replace(',','',substr($linea,71,5));//cantidad item disponibles
                $item["pedido"]=substr($linea,86,5);//cantidad item pedidos
                
               
                if (!(is_numeric($item["iditem"]) && strlen($item["iditem"])==6)) {
                    
                     //se busca el id de los item usando la referencia en el documento subido
                    $modelo=new ModeloRequierir();
                    // $item="ID_REFERENCIA";
                    $valor=$item["iditem"];
                    
                    $id_item=$modelo->mdlMostrarItem('ID_REFERENCIA',$valor);
                    $id_item=$id_item->fetch(); 
                              
                    //se reemplaza la referencia del item por su id
                    $item["iditem"]=$id_item["ID_ITEM"];
                   
                }
               
                
                // echo ($item[0]."  ".$item[1]."  ".$item[2]."  ".$item[3]." ".$item[4]."  ".$item[5]."<br>");

                //pone los datos del item en un String
                $stringItem.="(";
                foreach ($item as $value) {
                    $stringItem.="'$value',";
                }
                $stringItem=substr($stringItem, 0, -1).'),';
                
                $this->itemsarray[]=$item;
             
            //busca el numero de requisicion solo si no se ha encontrado
            }
            
        }
        
        $stringItem = substr($stringItem, 0, -1).';';


        //asigna al parametro de Items todos lo  items encontrados
        
        $this->items=$stringItem; 
    }

    // funcion que sube los items
    private function ctrSubirReq(){
        $modelo=new ModeloRequierir();

        // $resultado=$modelo->mdlSubirReq($this->cabecera,$this->items);
        $resultado=$modelo->mdlSubirReq($this->cabecera);
       
        if ($resultado==true) {
            foreach ($this->itemsarray as  $i=> $item) {
                
                $resultado=$modelo->mdlSubirItem($item);
                
            }
            if ($resultado==true) {
                echo '<script>
                            swal({
                                title: "¡Archivo Subido exitosamente¡",
                                icon: "success"
                            });
                    </script>';

                echo '<div class="col s11 m10 l6 offset-l3 offset-m1">
                        <p class="green-text text-darken-5">Requisicion '.$this->cabecera[0].' subida Exitosamente</p> 
                    </div>';
            }else {
                echo '<script>
                            swal({
                                title: "¡Error al subir el archivo¡",
                                icon: "error"
                            });
                    </script>';
                echo '<div class="col s11 m10 l6 offset-l3 offset-m1">
                        <p class="red-text text-darken-2">Error al subir la requisición</p> 
                    </div>';
            }
            
            return $resultado;

        }else {
            $resultadoel=$modelo->mdlEliReq($this->cabecera[0]);

            echo '<script>
                            swal({
                                title: "¡Error al subir el archivo¡",
                                icon: "error"
                            });
                    </script>';
            echo '<div class="col s11 m10 l6 offset-l3 offset-m1">
                    <p class="red-text text-darken-2">Error al subir la requisición</p> 
                </div>';
            
            return $resultado;
        }
    }

}