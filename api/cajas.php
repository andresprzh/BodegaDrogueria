<?php

// include "../controladores/alistar.controlador.php";
include "../controladores/cajas.controlador.php";


require "../modelos/conexion.php";
require "../modelos/alistar.modelo.php";
require "../modelos/requerir.modelo.php";
require "../modelos/cajas.modelo.php";

if (isset($_GET['ruta'])) {
    

    switch ($_GET['ruta']) {
        
        case "conductor":
            $modelo=new Conexion();
            $busqueda=$modelo->buscaritem('usuario','perfil',6);
            
            $cont=0;
            
            if ($busqueda->rowCount()>0) {
                $resultado["estado"]="encontrado";
            
                while($row = $busqueda->fetch()){
                    
                    
                    $resultado["contenido"][$row["id_usuario"]]=$row["nombre"];
                    $cont++;

                }
            }else {
                $resultado["estado"]=false;
                $resultado["contenido"]="No se encontraron usuarios";
            }
            print json_encode($resultado);
            break;
        
        case "despachar":
            $transportador=$_POST['transportador'];
            $cajas=$_POST['cajas'];
            
            $controlador=new ControladorCajas();
            $busqueda=$controlador->ctrDespacharCajas($cajas,$transportador);
            print json_encode($busqueda);
            return 0;
            break;


    }
    
}