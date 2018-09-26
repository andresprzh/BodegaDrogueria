<h3 class="header center ">Transporte de cajas</h3>

<!-- ============================================================================================================================
                                                INPUT SELECCIONAR DESPACHOS   
============================================================================================================================ -->
<div class="row">

    <div class="input-field col s9 m10 l11 " >

        <select   list="destino" name="destino" class="destino browser-default col s12 " id="destino">
            <option value="" disabled selected>Destino</option>
        </select>
        

    </div>
    <div class="input-field col s3 m1 l1  input_refresh">

        <button id="refresh" title="Recargar" disabled onclick="recargarCajas()" class="btn waves-effect waves-light green darken-3 col s12 m12 l8" >
            <i class="fas fa-sync"></i>
        </button>
        
    </div>
    
</div>
  
<div class="divider green darken-4"></div>

<div class="row">
    <ul class="collapsible col s12" id="pedidos">
        <li>
            <div class="collapsible-header">
                <i class="fas fa-truck collapsible-primary" ></i>First
                <button class="collapsible-secondary not-collapse btn green">Entregar</button>
            </div>
            <div class="collapsible-body">
                <ul>
                    <li>algo1</li>
                    <li>algo2</li>
                    <li>algo3</li>
                </ul>
            </div>
            
        </li>
</div>

<style>
.collapsible-secondary { 
    position: absolute; 
    right: 0; 
    height:60%;
    /* padding-bottom:10px; */
}
.collapsible-header{
    position: relative;
    /* padding:0; */
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