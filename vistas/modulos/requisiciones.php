<h3 class="header center ">Requisiciones</h3>

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
    <ul class="collapsible col s12" id="listreq">
    </ul>
</div>


<!-- ============================================================================================================================
                                                MODAL VER ITEMS REQUISICION
============================================================================================================================ -->


<div id="itemsreq" class="modal grey lighten-3">

    <div class="modal-content grey lighten-3">

        <div class="modal-header">

            <a href="#!" class="modal-close waves-effect waves-green btn-flat right"><i class='fas fa-times'></i></a>
            <h4 class="center" >Requisicion <span id="requeridos"></span></h4>
        </div>
        <table class="centered no-border" >
            <thead>
                <tr>
                    <th>Solicitante: <span id="solicitante"></span></th>
                    <th>Tipo inventario: <span id="tipoinv"></span></th>
                </tr>
                <tr>
                    <th>Origen: <span id="origen">001-BD CENTRO</span></th>
                    <th>destino: <span id="destino"></span></th>
                </tr>
            </thead>
        </table>

        <div class="row " id="contenido"  >
            
            
            <!-- ==============================================================
                        TABLA EDITABLE    
            ============================================================== -->
            <div class="col s12" id="TablaE" >


                <table class="tabla centered " id="TablaM" style="width:100%">
                
                    <thead>

                    <tr class="white-text green darken-3">

                        <th>Descripción</th>
                        <th>Disponibles</th>
                        <th>Pendientes</th>
                        <th>Ubicacion</th>
                        
                    </tr>

                    </thead>

                    <tbody id="tablamodal"></tbody>
                    <!-- ==================================
                        INPUT PARA CERRAR REQUISICION 
                    ================================== -->
                    
                    <div class="input-field col s4 m3 l3">

                        <button id="cerrar" class="btn waves-effect red darken-4 " >
                            Cerrar
                        </button>
                        
                    </div>  

                    
                </table> 
                  
            </div>

        </div>
    </div>
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

.modal{
    width:100%;
}


</style>
<!-- GUARDA EL NOMBRE DEL USUARIO DE LA SESION EN UNA VARIABLE DE JS -->
<script type="text/javascript">
    var id_usuario='<?php echo $_SESSION["usuario"]["id"];?>';
</script>

<!-- JS QUE MANEJA LOS EVENTOS DE LA PAGINA -->
<script src="vistas/js/requisiciones.js">



</script>