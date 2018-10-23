<div id="contenido-inicio" class="container">
    <!-- <h2 class="header center"  >Aplicación Bodega</h3> -->
</div>
<!-- ============================================================================================================================
                                                        ESTILOS 
============================================================================================================================ -->
<style>
#ubicaciones
{
    max-height:300px; 
    width:100%;
}
#ubicaciones 
{
    overflow:hidden; 
    overflow-y:scroll;
}
</style>
<!-- ============================================================================================================================
                                                        JS SCRIPTS
============================================================================================================================ -->
<!-- GUARDA EL NOMBRE DEL USUARIO DE LA SESION EN UNA VARIABLE DE JS -->
<script type="text/javascript">
    // var id_usuario='<?php echo $_SESSION["usuario"]["id"];?>';
    var usuario=JSON.parse('<?php print json_encode($_SESSION["usuario"]);?>');
    // console.log(usuario["nombre"]);
</script>

<!-- JS QUE MANEJA LOS EVENTOS DE LA PAGINA -->
<script src="vistas/js/inicio.js">

</script>
    




