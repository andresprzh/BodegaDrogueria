<?php
  session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>

    
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bodega</title>
    <link rel="icon" href="vistas/imagenes/plantilla/logo.png">

<!-- ============================================================================================================================
                            ESTILOS
  ============================================================================================================================ -->
<!-- Font Awesome -->
<link rel="stylesheet" href="vistas\lib\font-awesome\css\all.css">


<!-- Estilos -->
<link rel="stylesheet" href="vistas/css/style.css">

<!-- Materialize -->
<link rel="stylesheet" href="vistas/lib/materialize/css/materialize.css">

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="vistas/lib/DataTables/datatables.css"/>
 


<!-- ============================================================================================================================
                        PLUGINS JAVASCRIPT
============================================================================================================================= -->
<!-- jQuery 3 -->
<script src="vistas/lib/jquery/dist/jquery.min.js"></script>

<!-- FastClick -->
<script src="vistas/lib/fastclick/lib/fastclick.js"></script>

<!-- DataTables -->
<script type="text/javascript" src="vistas/lib/DataTables/datatables.min.js"></script>


<!-- SweetAlert 2 -->
<script src="vistas/plugins/sweetalert2/sweetalert.min.js"></script>


<!-- Materialize -->
<script src="vistas/lib/materialize/js/materialize.js"></script>
<script src="vistas/lib/materialize/js/init.js"></script>

<!-- JS Principal -->
<script src="vistas/js/principal.js"></script>


</head>
<!-- ============================================================================================================================
                            CUERPO DOCUMENTO
  ============================================================================================================================= -->
<body class="" >

  <?php
  
  if(isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"]=="ok"){
    
    /* ============================================================================================================================
                BARRA DE NAVEGACION
    ============================================================================================================================== */
    include "modulos/navbar.php";
    
    
    // Posibles vistas en el contenido
    $pages=[
        "inicio",
        "remisiones",
        "requerir",
        "alistar",
        "cajas",
        "pv",
        "tareas",
        "usuarios",
        "Nitem",
        "salir",
        "pvcajas",
        "transportador",
        "requisiciones",
        "franquicia"
        ];
    $jefe=[
      "inicio",
      "requerir",
      "cajas",
      "usuarios",
      "Nitem",
      "tareas",
      "requisiciones",
      "salir"
    ];
    
    $alistador=[
      "inicio",
      "alistar",
      "salir"
    ];
    $pv=[
      "inicio",
      "pv",
      "salir",
      "pvcajas"
    ];
    $franquicia=[
      "inicio",
      "franquicia",
      "salir"
    ];
    $jefed=[
      "inicio",
      "requerir",
      "cajas",
      "Nitem",
      "requisiciones",
      "salir"
    ];

    $transportador=[
      "inicio",
      "transportador",
      "salir"
    ];
    echo "<main>";

    if (isset($_GET["ruta"])) {
      
      /* =================================================================================================================================
                             CONTENIDO
        =================================================================================================================================*/
        
      if ($_SESSION["usuario"]["perfil"]==1 && in_array($_GET["ruta"],$pages)) {
        
        include "modulos/".$_GET["ruta"].".php";

      }elseif ($_SESSION["usuario"]["perfil"]==2 && in_array($_GET["ruta"],$jefe)) {
        
        include "modulos/".$_GET["ruta"].".php";
        
      }elseif ($_SESSION["usuario"]["perfil"]==3 && in_array($_GET["ruta"],$alistador)) {
        
        include "modulos/".$_GET["ruta"].".php";

      }elseif ($_SESSION["usuario"]["perfil"]==4 && in_array($_GET["ruta"],$pv)) {
        
        include "modulos/".$_GET["ruta"].".php";

      }elseif ($_SESSION["usuario"]["perfil"]==5 && in_array($_GET["ruta"],$jefed)) {
        
        include "modulos/".$_GET["ruta"].".php";

      }elseif ($_SESSION["usuario"]["perfil"]==6 && in_array($_GET["ruta"],$transportador)) {
        
        include "modulos/".$_GET["ruta"].".php";

      }elseif ($_SESSION["usuario"]["perfil"]==7 && in_array($_GET["ruta"],$franquicia)) {
        
        include "modulos/".$_GET["ruta"].".php";

      }else{

        include "modulos/404.php";

      }

    }else{

        include "modulos/inicio.php";

    }
    echo "</main>";

    /*============================================================================================================================
                    FOOTER
    ==============================================================================================================================*/
    
    if ($_SESSION["usuario"]["perfil"]!=3 && $_SESSION["usuario"]["perfil"]!=6) {
      include "modulos/footer.php";
    }
  } else {
    include "modulos/login.php";
  } 
    
  ?>

</body>

</html>