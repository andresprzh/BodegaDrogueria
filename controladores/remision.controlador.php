<?php

class ControladorRemision{

    private $modelo;
    private $documentos;
    private $itemsarray;
    private $ubicacion;
    private $no_rem;

    function __construct($documentos=null){
      $this->modelo=new ModeloRemision();
      $this->documentos=$documentos;

    }


    // funcion que asigna la cabecera
    private function ctrSetCabecera()
    {

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
            //    return ($linea[0]);
                //busca si la linea tiene datos de los items
                // es un item si la linea no tiene | de primer caracter, no tiene una sucecion de -, no tiene : y si no es salto de linea(ascii=10)
                if( !empty($linea))
                    if(!in_array($linea[0],["|","+","-"]) 
                       && strpos($linea, "REMISIONES")===false 
                       && strpos($linea, "DROGUERIA SAN")===false){
                                        
                        // // $item["linea"]=$linea;
                        // $item["item"]=trim(substr($linea,0,6)); 
                        // $item["descripcicon"]=trim(substr($linea,6,37)); 
                        // $item["local"]=trim(substr($linea,44,6));
                        // $item["cantidad"]=trim(str_replace(",","",substr($linea,55,5)));
                        // $item["unidad"]=trim(str_replace(",","",substr($linea,63,3)));
                        // $item["valor"]=trim(str_replace(",","",substr($linea,70,9)));
                        // $item["descuento"]=trim(str_replace(",","",substr($linea,85,6)));
                        // $item["impuesto"]=trim(str_replace(",","",substr($linea,94,9)));
                        // $item["total"]=trim(str_replace(",","",substr($linea,107,9)));
                        // $item["costo"]=trim(str_replace(",","",substr($linea,119,9)));
                        // $item["rent"]=trim((substr($linea,129,6)));
                        
                    
                        // if (!(is_numeric($item["item"]) && strlen($item["item"])==6)) {
                            
                        //     //se busca el id de los item usando la referencia en el documento subido
                        //     $modelo=new ModeloRequierir();
                        //     // $item="ID_REFERENCIA";
                        //     $valor=$item["item"];
                            
                        //     $id_item=$modelo->mdlMostrarItem("ID_REFERENCIA",$valor);
                        //     $id_item=$id_item->fetch(); 
                                    
                        //     //se reemplaza la referencia del item por su id
                        //     $item["item"]=$id_item["ID_ITEM"];
                        
                        // }
                    
                        
                        // $this->itemsarray[$i][]=$item;
                        
                        $iditem=trim(substr($linea,0,6)); 
                        if (!(is_numeric($iditem) && strlen($iditem)==6)) {
                            
                            //se busca el id de los item usando la referencia en el documento subido
                            $modelo=new ModeloRequierir();
                            // $item="ID_REFERENCIA";
                            $valor=$iditem;
                            
                            $id_item=$modelo->mdlMostrarItem("ID_REFERENCIA",$valor);
                            $id_item=$id_item->fetch(); 
                                    
                            //se reemplaza la referencia del item por su id
                            $iditem=$id_item["ID_ITEM"];
                        
                        }
                        if (!empty($this->itemsarray[$iditem])) {
                            $this->itemsarray[$iditem]["cantidad"]+=trim(str_replace(",","",substr($linea,55,5)));
                        }else {
                            $this->itemsarray[$iditem]["cantidad"]=trim(str_replace(",","",substr($linea,55,5)));
                        }
                        $this->itemsarray[$iditem]["item"]=$iditem; 
                        $this->itemsarray[$iditem]["descripcicon"]=trim(substr($linea,6,37)); 
                        $this->itemsarray[$iditem]["local"]=trim(substr($linea,44,6));
                        $this->ubicacion=trim(substr($linea,44,6));
                        $this->itemsarray[$iditem]["unidad"]=trim(str_replace(",","",substr($linea,63,3)));
                        $this->itemsarray[$iditem]["valor"]=trim(str_replace(",","",substr($linea,70,9)));
                        $this->itemsarray[$iditem]["descuento"]=trim(str_replace(",","",substr($linea,85,6)));
                        $this->itemsarray[$iditem]["impuesto"]=trim(str_replace(",","",substr($linea,94,9)));
                        $this->itemsarray[$iditem]["total"]=trim(str_replace(",","",substr($linea,107,9)));
                        $this->itemsarray[$iditem]["costo"]=trim(str_replace(",","",substr($linea,119,9)));
                        $this->itemsarray[$iditem]["rent"]=trim((substr($linea,129,6)));
                        
                    
                        
                        // $this->itemsarray[$iditem]=$item;|
                        

                }
            }
        }

        return $this->itemsarray;

    }

    // funcion que sube los items
    public function ctrSubirRem()
    {
        
        // foreach ($this->itemsarray as $array) {
        //     // return $array;
        //     $folder=substr($folder,0,5);
        //     $resultado=$this->modelo->mdlSubirRem($folder);
            
        //     if ($resultado) {
        //         $no_rem2=$this->modelo->mdlMostrarRem($folder);
        //         // $no_rem2=$no_rem2->fetch()["no_rem2"];
                
        //         $no_rem2=$no_rem2->fetch()["no_rem2"];
                
                
        //         foreach ($array as  $item) {
        //             // return $item;
        //             // $resultado2[]=$this->modelo->mdlSubirItem($item,$folder,$no_rem2);
        //             $resultado=$this->modelo->mdlSubirItem($item,$folder,$no_rem2);
        //             if (!$resultado) {
        //                 return false;
        //             }
        //         }

        //     }else {
        //         // $resultadoel=$modelo->mdlEliRem($this->cabecera[0]);
                
        //         return $resultado;
        //     }
        // }

        // $folder=substr($folder,0,5);
        
        $resultado=$this->modelo->mdlSubirRem($this->ubicacion);
        
        foreach ($this->itemsarray as $item) {
            // return $array;
            
            if ($resultado) {
                $no_rem=$this->modelo->mdlMostrarRem();
                
                $this->no_rem=$no_rem->fetch()["no_rem"];
                $resultado=$this->modelo->mdlSubirItem($item,$this->no_rem);

            }else {
                // $resultadoel=$modelo->mdlEliRem($this->cabecera[0]);
                
                return $resultado;
            }
        }

        return $resultado;
    }

    public function ctrDocRem()
    {   

        // $busqueda=$this->modelo->mdlMostrarRemDoc($this->no_rem);
        $busqueda=$this->modelo->mdlMostrarRemDoc(1);
        $string="";
        // print json_encode($busqueda->fetchAll());
        // return 0;
        while($row = $busqueda->fetch()){
            $pordesc=$row["descuento"]/$row["valor"]*100;
            $pordesc=round($pordesc,2);
            // $string.=str_pad(str_replace(".","",$pordesc), 4, "0", STR_PAD_RIGHT);
            // return $string;
            $no_rem=str_pad("$row[no_rem]",3, "0", STR_PAD_LEFT);

            $string.=str_pad("OC$no_rem", 10, " ", STR_PAD_RIGHT)."|";
            $string.="2"."|";
            $string.=str_repeat(" ",20)."|";
            $string.="805002583    "."|";
            $string.="00"."|";
            $string.=str_replace("-","",substr($row["creada"],0,10))."|";
            $string.=str_replace("-","",$row["ubicacion"])."|";
            $string.="I"."|";
            $string.=str_repeat(" ",15)."|";
            $string.=str_pad($row["item"], 15, " ", STR_PAD_RIGHT)."|";
            $string.="  "."|";
            $string.=$row["unidad"]."|";
            $string.=str_pad(str_replace(".","",$row["valor"]), 12, "0", STR_PAD_RIGHT)."+"."|";
            $string.="000000000000+"."|";
            $string.="1"."|";
            $string.="13"."|";
            $string.="  "."|";
            $string.=str_pad(str_replace(".","",$row["valor"]), 12, "0", STR_PAD_RIGHT)."+"."|";
            $string.=str_repeat(" ",22)."|";
            $string.=str_pad(str_replace(".","",$row["total"]), 12, "0", STR_PAD_RIGHT)."+"."|";
            $string.="000000000000+"."|";
            $string.=str_pad(str_replace(".","",$pordesc), 4, "0", STR_PAD_RIGHT)."|";
            $string.=str_pad(str_replace(".","",$row["descuento"]), 12, "0", STR_PAD_RIGHT)."|";
            $string.="0000";
            $string.="000000000000";

            
            


            $string.="\r\n";
        }
        return $string;
    }

}