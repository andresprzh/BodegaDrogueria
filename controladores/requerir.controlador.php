<?php

class ControladorRequerir{

    private $Doc_Req;
    private $Cabecera;
    private $Items;
    public $Estado;

   function __construct($Doc_Req) {

        //se asigna el documento a la variable Doc_Req
        $this->Doc_Req=$Doc_Req;
        
        //busca los datos de cabecera y los guarda en el parametro Cabecera
        $this->ctrSetCabecera();
    
        //busca si ya existe la requisicion en la base de datos
        $modelo=new ModeloRequierir();
        $item='No_Req';
        $valor=$this->Cabecera[0];


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
            if($no_req["No_Req"]==$valor){
                
                echo '<script>
                        swal({
                            title: "¡Requisicion ya subida¡",
                            icon: "error",
                        });
                    </script>';       
                
                echo '<div class="col s11 m10 l6 offset-l3 offset-m1">
                    <p class="red-text text-darken-2">Error: Requisicion '.$this->Cabecera[0].' ya subida</p> 
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

        foreach($this->Doc_Req as $linea){
            
            $linea=($linea.'<br>');

            //se buscan los datos de cabecera en cada linea
            if(!($linea[0]!='|' &&  $linea[2]!='-' && strripos($linea,':')==false && ord($linea)!=10)){
                
                //busca la fecha 
                $pos=strpos($linea, 'FECHA :');
                
		        if($pos){
			    $fecha=substr($linea,$pos+8,10);	
                }
                
                //busca la hora
                $pos=strpos($linea, 'HORA  :');
                if($pos){
                    $hora=substr($linea,$pos+8,8);	
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
                if(isset($fecha) && isset($hora) && isset($lo) && isset($ld) &&isset($tipInv) && isset($req) && isset($sol)){

                    // almacena los datos de cabecera en un vector
                    $cabecera=[$req,$fecha,$hora,$lo,$ld,$tipInv,$sol,0];

                    //asigna los dtaos de cabecera al parametro de la clase
                    $this->Cabecera=$cabecera;
                    
                    // si ya encontro todos los datos decabecera se sale del ciclo
                    break;
                    
                }

            }

        }

    }

     //funcion que asigna los items
     private function ctrSetItems(){

        $StringItem='';

        foreach($this->Doc_Req as $linea){

            $linea=($linea.'<br>');

            //busca si la linea tiene datos de los items
            // es un item si la linea no tiene | de primer caracter, no tiene una sucecion de -, no tiene : y si no es salto de linea(ascii=10)
            if($linea[0]!='|' &&  $linea[2]!='-' && strripos($linea,':')==false && ord($linea)!=10){
                
                //obtienen los datosd e cada item por linea
                $items[0]=substr($linea,1,11); //numero referencia item
                $items[1]=$req;//se obtiene el numero de requisicion
                $items[2]=1;//se manda la caja como un 1(caja no asignada)
                //se cambian las comas del dato por espacios en blanco
                $items[3]=substr($linea,109,6);//ubiacion item
                $items[4]=str_replace(',','',substr($linea,71,5));//cantidad items disponibles
                $items[5]=substr($linea,86,5);//cantidad items pedidos
                $items[6]=intval(str_replace('_','',substr($linea,95,8)));//cantidad items alistados
                $items[7]=0;//cantidad de items recibidad
                $items[8]=0;//estado alistamiento del item(predeterminado 0 no alistado)
                $items[9]=0;//estado item recibido (predeterminado 0 no recibido)
                
                
                //se busca el id de los item usando la referencia en el documento subido
                $modelo=new ModeloRequierir();
                $item='ID_REFERENCIA';
                $valor=$items[0];
                $id_item=$modelo->mdlMostrarItem($item,$valor);
                $id_item=$id_item->fetch();           
                
                
		        //se reemplaza la referencia del item por su id
                $items[0]=$id_item["ID_ITEM"];
                
                //pone los datos del item en un String
                $StringItem.='(';
                for ($i=0; $i <count($items) ; $i++) { 
                    $StringItem.='"'.$items[$i].'",';
                }
                $StringItem=substr($StringItem, 0, -1).'),';
                
             
            //busca el numero de requerido solo si no se ha encontrado
            }elseif(!isset($req)){

                //busca el numero de requerido
                $pos=strpos($linea, 'Nro Req:');
                if($pos){
                    $req=substr($linea,$pos+9,10);	
                }
            }
        }
        
        $StringItem = substr($StringItem, 0, -1).';';

        //asigna al parametro de Items todos lo  items encontrados
        
        $this->Items=$StringItem; 
    }

    // funcion que sube los items
    private function ctrSubirReq(){
        $modelo=new ModeloRequierir();

        $resultado=$modelo->mdlSubirReq($this->Cabecera,$this->Items);
        
        if ($resultado==true) {
            echo '<script>
                            swal({
                                title: "¡Archivo Subido exitosamente¡",
                                icon: "success"
                            });
                    </script>';

            echo '<div class="col s11 m10 l6 offset-l3 offset-m1">
                    <p class="green-text text-darken-5">Requisicion '.$this->Cabecera[0].' subida Exitosamente</p> 
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
    }

}