<?php

class ControladorRequerir{

    private $doc_req;
    private $cabecera;
    public $Estado;
    private $items;
    private $items_error;

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
            
            //si la requisicion no existe no busca los items y sube el archivo a la base de datos  
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
                
                $resultado=$this->ctrSubirReq();
                
                
                if ($resultado) {
                    
                    $busqueda=$modelo->mdlMostrarItems($this->cabecera[0]);
                   
                    if ($busqueda->rowCount() > 0) {
                        
                        echo '<table class="tabla "  >

                        <thead>
                        
                        <tr class="white-text green darken-3 ">
            
                            <th>Descripción</th>
                            <th>Codigo de barras</th>
                            <th>ID Item</th>
                            <th>Referencia</th> 
                            <th>Disponibilidad</th>
                            <th>Solicitados</th>              
                            <th>Ubicacion</th>
                            
                        </tr>
                        </thead>';
            
                        echo '<tbody >';
                        
                        while($row = $busqueda->fetch()){                          

                                echo '<tr>
                                    <td>'.$row["DESCRIPCION"].'</td>
                                    <td>'.$row["ID_CODBAR"].'</td>
                                    <td>'.$row["item"].'</td>
                                    <td>'.$row["ID_REFERENCIA"].'</td>
                                    <td>'.$row["disp"].'</td>
                                    <td>'.$row["pedido"].'</td>
                                    <th class="black-text">'.$row["ubicacion"].'</th>
                                </tr>';

                            }

                        
                        echo '</table> ';
                    }
                }
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
                    $lo=substr($linea,$pos+17,3);	
                }

                //busca la localzacion de destino
                $pos=strpos($linea, 'Local. Destino :');
                if($pos){
                    $ld=substr($linea,$pos+17,3);	
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
                $iditem=str_replace(' ','',substr($linea,1,11)); //numero referencia o id del item
               
                // busca la exitencia del item
                $modelo=new ModeloRequierir();
                
                $valor=$iditem;
                
                $busqueda=$modelo->mdlMostrarItem($valor);

                // solo agrega items que esten en la base de datos
                if ($busqueda->rowCount()>0) {
                    // $busqueda_res=$busqueda->fetch();

                    //obtienen los datos de cada item por linea
                    $item["iditem"]=$busqueda->fetch()["ID_ITEM"]; //numero referencia o id del item
                    $item["no_req"]=$this->cabecera[0];//se obtiene el numero de requisicion
                    //se cambian las comas del dato por espacios en blanco
                    $item["ubicacion"]=substr($linea,109,6);//ubiacion item
                    $item["disp"]=str_replace(',','',substr($linea,71,5));//cantidad item disponibles
                    $item["pedido"]=substr($linea,86,5);//cantidad item pedidos
                    $this->items[]=$item;
                }else {
                    //obtienen los datos de cada item por linea
                    $item_error["iditem"]=$iditem; //numero referencia o id del item
                    $item_error["no_req"]=$this->cabecera[0];//se obtiene el numero de requisicion
                    $item_error["descripcion"]=substr($linea,20,40);//ubiacion item
                    $item_error["ubicacion"]=substr($linea,109,6);//ubiacion item
                    $item_error["disp"]=str_replace(',','',substr($linea,71,5));//cantidad item disponibles
                    $item_error["pedido"]=substr($linea,86,5);//cantidad item pedidos
                    $this->items_error[]=$item_error;
                    
                    
                }
                
                $busqueda->closeCursor();
                
            }
            
        }

    }

    // funcion que sube los items
    private function ctrSubirReq(){
        $modelo=new ModeloRequierir();
        
        $resultado=$modelo->mdlSubirReq($this->cabecera);

        if ($resultado==true) {
            $resultado=$modelo->mdlSubirItems($this->items);
            
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
            if (!empty($this->items_error)) {
                $hoy = getdate();
                $fecha=$hoy["year"]."/".$hoy["month"]."/".$hoy["mday"]." ".$hoy["hours"].":".$hoy["minutes"];
                $documento=str_repeat("=",72 )."\r\n";
                $documento.="|".str_pad("Fecha: $fecha" ,70," ",STR_PAD_BOTH)."|\r\n";
                $documento.="|".str_pad("**ITEMS NO ENCONTRADOS EN LA BASE DE DATOS**" ,70," ",STR_PAD_BOTH)."|\r\n";
                $documento.=str_repeat("=",72 )."\r\n";
                $documento.=str_pad("ID/REF",25," ",STR_PAD_RIGHT).str_pad("DESCRIPCION",40," ",STR_PAD_RIGHT).
                str_pad("PEDIDOS",7," ",STR_PAD_LEFT)."\r\n";
                $documento.=str_repeat("=",72 )."\r\n";
                foreach ($this->items_error as $error) {
                    $documento.=str_pad($error["iditem"],25,"-",STR_PAD_RIGHT);
                    $documento.=str_pad(trim($error["descripcion"]),40,"-",STR_PAD_RIGHT);
                    $documento.=str_pad(trim($error["pedido"]),7,"-",STR_PAD_LEFT);
                    $documento.="\r\n";
                }
                $filename="errores.txt";
                $fp=fopen($filename, "w");
                fwrite($fp,$documento);
                // header("Content-disposition: attachment;filename=$filename");
                // readfile($filename);
                // unlink($filename);
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