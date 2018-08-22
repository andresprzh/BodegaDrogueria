<?php

class ControladorRequerir{

    private $doc_req;
    private $cabecera;
    private $items;
    public $Estado;

   function __construct($doc_req){
        //se asigna el documento a la variable doc_req
        $this->doc_req=$doc_req;
        
        //busca los datos de cabecera y los guarda en el parametro Cabecera
        $this->ctrSetCabecera();
    
        //busca si ya existe la requisicion en la base de datos
        $modelo=new ModeloRequierir();
        $item='No_Req';
        $valor=$this->cabecera[0];


        //si no encuentra el numero de la requisicion no la sube a la base de datos
        if ($valor===null) {
            echo ('<script>
                swal({
                    title: "¡Requisicion no encontrada",
                    icon: "error",
                });
            </script>');  
            
        }else{

            $no_req=$modelo->mdlMostrarReq($item,$valor);
            $no_req=$no_req->fetch();
            
            //si la requisicion no existe busca los items y sube el archivo a la base de datos
            if($no_req["no_req"]==$valor){
                
                echo '<script>
                        swal({
                            title: "¡Requisicion ya subida¡",
                            icon: "error",
                        });
                    </script>';       
                
                echo '<div class="col s11 m10 l6 offset-l3 offset-m1">
                    <p class="red-text text-darken-2">Error: Requisicion '.$this->cabecera[0].' ya subida</p> 
                </div>';

                
                
            }
            else{

                //busca los datos de los items y los guarda en items
                $this->ctrSetItems();
                
                
                $this->ctrSubirReq();
                // echo $this->Items;
            
            } 
        }
       
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

        foreach($this->doc_req as $linea){

            $linea=($linea.'<br>');

            //busca si la linea tiene datos de los items
            // es un item si la linea no tiene | de primer caracter, no tiene una sucecion de -, no tiene : y si no es salto de linea(ascii=10)
            if($linea[0]!='|' &&  $linea[2]!='-' && strripos($linea,':')==false && ord($linea)!=10 && $linea[0]==' '){
                
                //obtienen los datos de cada item por linea
                $items[0]=str_replace(' ','',substr($linea,1,11)); //numero referencia o id del item
                $items[1]=$this->cabecera[0];//se obtiene el numero de requisicion
                //se cambian las comas del dato por espacios en blanco
                $items[2]=substr($linea,109,6);//ubiacion item
                $items[3]=str_replace(',','',substr($linea,71,5));//cantidad items disponibles
                $items[4]=substr($linea,86,5);//cantidad items pedidos
                $items[5]=intval(str_replace('_','',substr($linea,95,8)));//cantidad items alistados
               
                if (!(is_numeric($items[0]) && strlen($items[0])==6)) {
                    
                     //se busca el id de los item usando la referencia en el documento subido
                    $modelo=new ModeloRequierir();
                    $item='ID_REFERENCIA';
                    $valor=$items[0];
                    $id_item=$modelo->mdlMostrarItem($item,$valor);
                    $id_item=$id_item->fetch();           
                    //se reemplaza la referencia del item por su id
                    $items[0]=$id_item["ID_ITEM"];
                    
                }
               
                
                // echo ($items[0]."  ".$items[1]."  ".$items[2]."  ".$items[3]." ".$items[4]."  ".$items[5]."<br>");
                
                //pone los datos del item en un String
                $stringItem.='(';
                for ($i=0; $i <count($items) ; $i++) { 
                    $stringItem.='"'.$items[$i].'",';
                }
                $stringItem=substr($stringItem, 0, -1).'),';
                
             
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

        $resultado=$modelo->mdlSubirReq($this->cabecera,$this->items);
        
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
            $resultado=$modelo->mdlEliReq($this->cabecera[0]);

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
    }

}