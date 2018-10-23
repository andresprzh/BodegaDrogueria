<h3 class="header center ">Transporte de cajas</h3>

<!-- ============================================================================================================================
                                                INPUT SELECCIONAR DESPACHOS   
============================================================================================================================ -->
<div class="row">

    <div class="input-field col s3 m1 l1  input_refresh">

        <button id="refresh" title="Recargar"  onclick="cargarpedidos()" class="btn waves-effect waves-light green darken-3 col s12 m12 l8" >
            <i class="fas fa-sync"></i>
        </button>
        
    </div>
    
</div>
  
<div class="divider green darken-4"></div>

<div class="row">
    <ul class="collapsible col s12" id="pedidos">
    </ul>
</div>

<style>
.collapsible-secondary { 
    position: absolute; 
    right: 0; 
    /* height:60%; */
    height:30px;
    padding-bottom:0px;
}
.collapsible-header{
    position: relative;
    padding:15px;
}
.collapsible-primary{
    padding:0px;
}
</style>
<!-- GUARDA EL NOMBRE DEL USUARIO DE LA SESION EN UNA VARIABLE DE JS -->
<script type="text/javascript">
    var id_usuario='<?php echo $_SESSION["usuario"]["id"];?>';
</script>

<!-- JS QUE MANEJA LOS EVENTOS DE LA PAGINA -->
<script src="vistas/js/transportador.js">



</script>