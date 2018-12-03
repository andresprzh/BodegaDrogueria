<?php

class ControladorPV extends ControladorCajas{
    /* ============================================================================================================================
                                                        ATRIBUTOS   
    ============================================================================================================================*/
    
    private $modelo;

    /* ============================================================================================================================
                                                        CONSTRUCTOR   
    ============================================================================================================================*/
    function __construct($req=null) {
        
        parent::__construct($req);
        $this->modelo=new ModeloPV($req);

    }

    /* ============================================================================================================================
                                                        FUNCIONES   
    ============================================================================================================================*/

    // busca el item en la requisicion
    public function ctrBuscarItemPV($cod_bar){
        $busqueda=$this->modelo->mdlMostrarItemPV($cod_bar);

        if ($busqueda->rowCount() > 0) {

            $row = $busqueda->fetch();
            $item=["estado"=>"encontrado",
            "contenido"=> ["codigo"=>$row["ID_CODBAR"],
                            "iditem"=>$row["ID_ITEM"],
                            "referencia"=>$row["ID_REFERENCIA"],
                            "descripcion"=>$row["DESCRIPCION"],
                          ]
            ];

        }else {
            $item["estado"]="error";
            $item["contenido"]="Item no encontrado";
        }

        return $item;
    }

    // busca cajas visibles para el punto de venta
    public function ctrBuscarCajaPV($numcaja){
        $busqueda=$this->modelo->mdlMostrarCajaPV($numcaja);
        
        if ($busqueda->rowCount() > 0) {

            $cajabus["estado"]="encontrado";

            $cont=0;

            while($row = $busqueda->fetch()){

                //Muestra todas las cajas
                $cajabus["contenido"][]=["no_caja"=>$row["no_caja"],
                                                "num_caja"=>$row["num_caja"],
                                                "alistador"=>$row["nombre"],
                                                "tipocaja"=>$row["tipo_caja"],
                                                "abrir"=>$row["abrir"],
                                                "cerrar"=>$row["cerrar"],
                                                "recibido"=>$row["recibido"],
                                            ];

                
                $cont++;

            }

        //si no encuentra resultados devuelve "error"
        }else{

            $cajabus= ["estado"=>"error",
                    "contenido"=>"Caja no encontrado en la base de datos!"];

        }
        // libera conexion para hace otra sentencia
        $busqueda->closeCursor();
        return $cajabus;
    }

    // modifca los valores de los items en la tabla y luego genera el documento
    public function ctrRegistrarItems($items,$numcaja){   
        
        
        // agrega los datos en la tabla de recibidos
        $resultado["estado"]=$this->modelo->mdlRegistrarItems($items,$numcaja);
        
        //si registra los items, modifica la tabla de pedido para que la caja aperezca como recibida
        if ($resultado["estado"]==true) {
            $resultado["estado"]=$this->modelo->mdlModCaja($numcaja);
            if($resultado["estado"]==true){
                
                $resultado["contenido"]=$this->ctrVerificarRegistro($numcaja);
                //si hay errores en los items recibido en la caja se cambia el estado de la caja
                if ($resultado["contenido"]["estado"]!="ok" ) {
                    $resultado["estado"]=$this->modelo->mdlModCaja($numcaja,5);
                }else {
                    $resultado["contenido"]=$this->ctrDocumentoR($numcaja);
                }
            }
        }
        
        return $resultado;
        
    }

    // busca la srequisiciones
    public function ctrBuscarReq(){
        
        $busqueda=$this->modelo->mdlMostrarReq();

        if ($busqueda->rowCount() > 0) {

            $row = $busqueda->fetch();
            $requisicion=["estado"=>"encontrado",
            "contenido"=> ["no_req"=>$row["no_req"],
                            "creada"=>$row["creada"],
                            "origen"=>$row["lo_origen"],
                            "destino"=>$row["lo_destino"],
                          ]
            ];

        }else {
            $requisicion['estado']='error';
            $requisicion['contenido']='requisicion no encontrada';
        }
        // libera conexion para hace otra sentencia
        $busqueda->closeCursor();
        return $requisicion;
    }

    // crea archivo plano de la caja recibida
    private function ctrVerificarRegistro($numcaja){
        
        $resultado=false;
        $estado=$this->modelo->mdlVerificarCaja($numcaja);
        
        if ($estado) {

            if ($estado["estado"]==1) {

                $resultado["estado"]="ok";

            }else {

                $resultado=$this->ctrBuscarItemError($numcaja);
                
            }
            
        }
        return $resultado;
        
    }

    private function ctrVerificarRemision($no_rem)
    {

        $resultado=false;

        $resultado['documento']=str_repeat("-",92 )."\r\n";
        $resultado['documento'].="|".str_pad("*ERRORES*",90," ",STR_PAD_BOTH)."|\r\n";
        $resultado['documento'].=str_repeat("-",92 )."\r\n";

        $descripcion=str_pad("DESCRIPCION ITEM",37," ",STR_PAD_RIGHT);        
        $item=str_pad("IDITEM",6+2," ",STR_PAD_RIGHT);
        $enviados=str_pad("ENVIADOS",8+2," ",STR_PAD_RIGHT);
        $recibidos=str_pad("RECIBIDOS",8+2," ",STR_PAD_RIGHT);
        $mensaje=str_pad("ERROR",27," ",STR_PAD_LEFT);
        $resultado['documento'].=($descripcion.$item.$enviados.$recibidos.$mensaje."\r\n");
        $resultado['documento'].=str_repeat("-",92 )."\r\n";

        if ($this->modelo->mdlVerificarRemision($no_rem)) {

            $busqueda=$this->modelo->mdlMostrarItemsRem($no_rem);

            $resultado["estado"]="ok";

            if ($busqueda->rowCount()>0) {
                $recibidos=$busqueda->fetchAll();
                $i=0;
                foreach ($recibidos as $row) {
                    if ($row["cantidad"]===null) {
                        $row["cantidad"]=0;
                    }
                    switch ($row["rem_estado"]) {
                        
                        case 0:
                            
                            if ($row["recibidos"]==0) {

                                $mensajeitem="item no recibido";
                            }else {
                                
                                $mensajeitem="Se recibieron menos items";
                            }
                            break;

                        case 1:
                            
                            $mensajeitem="Se recibieron mas items";                        
                            break;

                        case 2:
                            
                            $mensajeitem="Item fuera de la remision";
                            break;

                        case 3:
                            
                           
                            $mensajeitem="Item fuera de la remision";
                            break;
                        
                        default:
                           
                            break;
                    }
                    if ($row["rem_estado"] != 4) {
                        $resultado["estado"]="error0";
                        $resultado["item"][$i]=$row;
                        $resultado["item"][$i]["mensaje"]=$mensajeitem;
                        
                        $descripcion=str_pad(substr($row["descripcion"],0,35),35+2," ",STR_PAD_RIGHT);
                        $item=str_pad($row["item"],6+2," ",STR_PAD_RIGHT);
                        $enviados=str_pad($row["cantidad"],8,"0",STR_PAD_LEFT)."  ";
                        $recibidos=str_pad($row["recibidos"],8,"0",STR_PAD_LEFT)."  ";
                        $mensaje=str_pad($mensajeitem,27," ",STR_PAD_LEFT);
                        $resultado['documento'].=($descripcion.$item.$enviados.$recibidos.$mensaje."\r\n");
                        $i++;
                    }
                    
                }
            }else {
                $resultado["estado"]=false;

            }

        }

        $resultado['documento'].=str_repeat(str_repeat("-",92 )."\r\n",2);
        return $resultado;
    }
    // busca items de una requisicion
    public function ctrBuscarItemrec($numcaja)
    {   
        // return $this->ctrVerificarRegistro($numcaja);
         $busqueda=$this->modelo->mdlMostrarItemsRec($numcaja);
          
        if ($busqueda->rowCount() > 0) {

            $itembus["estado"]="encontrado";

            $itembus["contenido"]=$busqueda->fetchAll();
                       
            
            return $itembus;

        //si no encuentra resultados devuelve "error"
        }else{

            return ['estado'=>"error",
                    'contenido'=>"Caja sin Items!"];

        }
    }
    
    // crea documento de texto de los items recibidos
    public function ctrDocumentoR($numcaja)
    {
        $busqueda=$this->modelo->mdlMostrarrecibidos($numcaja);

        $resultado["estado"]=true;
        if ($busqueda->rowCount()>0) {
            $recibidos=$busqueda->fetchAll();
            $i=0;
            $resultado["string"]="";
            foreach ($recibidos as $row) {
                $mensaje=str_pad($row["num_caja"],19,"0",STR_PAD_LEFT);

                
                // $localicacion=str_replace("-","",$row["lo_origen"]."BD".$row["lo_destino"]."VEI");
                // $localicacion=str_pad($localicacion,11+15," ",STR_PAD_RIGHT);
                // $item=str_pad($row["iditem"],13+12," ",STR_PAD_RIGHT);
                // $num=$row["recibidos"]*1000;
                // $alistado=str_pad($num,12,"0",STR_PAD_LEFT);
                // $alistado=str_pad($alistado,12+32," ",STR_PAD_RIGHT);
                $localicacion=str_replace("-","",$row["lo_origen"]."BD".$row["lo_destino"]."VEI");
                $localicacion=str_pad($localicacion,11+15," ",STR_PAD_RIGHT);
                $item=str_pad($row["iditem"],6+12," ",STR_PAD_RIGHT);
                $num=$row["recibidos"]*1000;
                $alistado=str_pad($num,12,"0",STR_PAD_LEFT);
                $alistado=str_pad($alistado,12+32," ",STR_PAD_RIGHT);
                
                // $busqueda=$this->modelo->buscaritem('usuario','id_usuario',$this->req[1]);
                // $busqueda=$busqueda->fetch();
                // $mensaje=substr($busqueda['nombre'],0,19);
                
                $resultado["string"].=($localicacion.$item.$alistado.$mensaje."\r\n");
            }
        }else {
            $resultado["estado"]=false;
        }
        
        return $resultado;
    }

    // crea documento de la remision
    public function ctrDocumentoRemision($franquicia,$rem)
    {      
        // busca la descripcion dela franquicia
        $busqueda=$this->modelo->mdlMostrarUbicacion($franquicia);
        $franquiciadesc=$busqueda->fetch()[0];
        $busqueda->closeCursor();

        // busca los items
        $busqueda=$this->modelo->mdlMostraRecibidoRemision($rem);
        $items=$busqueda->fetchAll();
        

        $hoy = getdate();
        $fecha=$hoy["year"]."/".$hoy["month"]."/".$hoy["mday"]." ".$hoy["hours"].":".$hoy["minutes"];
        
        $franquicia=$franquiciadesc;
    
        $documento="";

        $documento.=str_repeat("-",92 )."\r\n";
        $documento.="|".str_pad("Fecha: $fecha" ,90/2," ",STR_PAD_RIGHT).str_pad("franquicia: $franquicia" ,90/2," ",STR_PAD_RIGHT)."|\r\n";
        $documento.=str_repeat("-",92 )."\r\n";

        
        $documento.=str_repeat("-",92 )."\r\n";
        $documento.="|".str_pad("*ITEM RECIBIDO*",90," ",STR_PAD_BOTH)."|\r\n";
        $documento.=str_repeat("-",92 )."\r\n";

        $descripcion=str_pad("DESCRIPCION ITEM",40+2," ",STR_PAD_RIGHT);
        $item=str_pad("IDITEM",6+2," ",STR_PAD_RIGHT);
        $ref=str_pad("REFERENCIA",15+2," ",STR_PAD_RIGHT);
        $cod_bar=str_pad("CODIGO BARRAS",15+2," ",STR_PAD_RIGHT);
        $recibidos=str_pad("CANTIDAD",8," ",STR_PAD_RIGHT);
        $documento.=($descripcion.$item.$ref.$cod_bar.$recibidos."\r\n");
        $documento.=str_repeat("-",92 )."\r\n";

        $total=0;
        foreach ($items as $row) {

            $descripcion=str_pad($row["descripcion"],40+2," ",STR_PAD_RIGHT);

            $item=str_pad($row["item"],6+2," ",STR_PAD_RIGHT);
            $ref=str_pad($row["referencia"],15+2," ",STR_PAD_RIGHT);
            $cod_bar=str_pad($row["codbarras"],15+2," ",STR_PAD_RIGHT);
            $num=$row["recibidos"];
            $recibidos=str_pad($num,8,"0",STR_PAD_LEFT);
            $total+=$num;
            $documento.=($descripcion.$item.$ref.$cod_bar.$recibidos."\r\n");
        }
        $documento.=str_repeat("-",92 )."\r\n";
        
        
        $documento.=str_pad("TOTAL:",92-12 ," ",STR_PAD_LEFT)."    ";
        $documento.=str_pad($total,8,"0",STR_PAD_LEFT)."\r\n";
        $documento.=str_repeat(str_repeat("-",92 )."\r\n",2);

        
        
        // valida la remision
        $resultado=$this->ctrVerificarRemision($rem);
        
        // hay problema en la recepcion de la remision
        if ($resultado["estado"]=="error0") {
            $resultado["documento"]=$documento.$resultado["documento"];
        }else {
            $resultado["documento"]=$documento;
        }
        
        return $resultado;
    }

    // envia los archivos planos generados de registrar la remision al correo
    public function ctrEnviarMail($enviar)
    {
        $busqueda=$this->modelo->buscaritem('emails');
        // print json_encode($busqueda->fetchall());
        $to='';
        if ($busqueda->rowCount(0)) {
            // return $busqueda->fetchAll();
            foreach ($busqueda->fetchAll() as $row) {
                $to = $row['correo'];
                // return $to;
                // $to = 'andresprzh@gmail.com;correoautomatico.bodegasj@gmail.com;';
                
                //sender
                $from = 'correoautomatico.bodegasj@gmail.com';
                $fromName = 'correoautomatico';

                //email subject
                $subject = 'Lista de productos'; 

                //email body content
                $htmlContent = '<h1>Lista de productos</h1>
                    <p>Adjunta lista de items</p>';

                //header for sender info
                $headers = "From: $fromName"." <".$from.">";

                //boundary 
                $semi_rand = md5(time()); 
                $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 

                //headers for attachment 
                $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 

                //multipart boundary 
                $htmlContent="lista";
                $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
                "Content-Transfer-Encoding: 7bit\n\n" . $htmlContent . "\n\n"; 

                $data = chunk_split(base64_encode($enviar));
                $message .= "--{$mime_boundary}\n"; 
                $message .= "Content-Type: text/html; name=\"lista.txt\"\n" . 
                "Content-Description: lista.txt\n" .
                "Content-Disposition: attachment;\n" . " filename=\"lista.txt\";\n" . 
                "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";

                $message .= "--{$mime_boundary}--";
                $returnpath = "-f" . $from;
                
                $resultado[]["to"]=$to;
                //send email
                $mail = @mail($to, $subject, $message, $headers ,$returnpath); 

                $resultado[]["res"]=$mail?"enviado":"error";
                
            }
            return $resultado;
        }else {
            return "Error no destinatario";
        }
        
        
        
        
    }

    // registra los items recibidos de una remision
    public function ctrRegistrarRemision($items,$rem,$franquicia)
    {
        
        $resultado["estado"]=$this->modelo->mdlRegistrarRemision($items,$rem);

        if ($resultado==true) {
            $resultado["contenido"]=$this->ctrDocumentoRemision($franquicia,$rem['no_rem']);
            // $documento=$this->ctrDocumentoRemision($franquicia,$rem['no_rem']);
            // $resultado["contenido"]=$this->ctrVerificarRemision($rem['no_rem']);
            
            // if ($resultado["contenido"]["estado"]=="error0") {
            //     $resultado["contenido"]["documento"]=$documento.$resultado["contenido"]["documento"];
            // }else {
            //     $resultado["contenido"]["documento"]=$documento;
            // }
            
            
        }
        
        return $resultado;
    }
    
}