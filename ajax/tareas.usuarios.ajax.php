<?php

include "../controladores/usuarios.controlador.php";

require "../modelos/conexion.php";

require "../modelos/usuarios.modelo.php";

$controlador=new ControladorUsuarios();

$resultado=$controlador->ctrBuscarUsuarios("perfil",3);
// if ($resultado['estado']=='encontrado') {
    echo json_encode($resultado);
// }


