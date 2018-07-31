<h2 class="header center ">Pedido</h2>
<!-- ============================================================================================================================
                                                INPUT SELECCIONAR REQUISICION   
============================================================================================================================ -->
<div class="row">

    <div class="input-field col s10 m10 l12 " >

        <select   list="requeridos" name="requeridos" class="requeridos" id="requeridos">
            <option value="" disabled selected>Seleccionar</option>
        </select>
        <label  style="font-size:12px;">Número requisicion</label>

    </div>
    
</div>

<div class="divider green darken-4"></div>


<div class="row hide " id="Cajas">

<!-- ============================================================================================================================
                                                Tabla que lista todas las cajas  
============================================================================================================================ -->
    <h5 class="header center ">Cajas</h5>

    <div class="col s12 m12 l12 ">

        <table class="tablas centered " id="TablaC" >

                <thead>
                
                <tr class="white-text green darken-3 ">

                    <th># Caja</th>
                    <th>Alistador</th>
                    <th>Tipo de caja</th>
                    <th>Abierta</th>
                    <th>Cerrada</th>
                    <th>Ver</th>
                    
                </tr>

                </thead>

                <tbody id="tablacajas"></tbody>
                
        </table>  
                
    </div>
    
</div>

<!-- ============================================================================================================================
                                                    MODAL EDITAR CAJA 
============================================================================================================================ -->
<div id="EditarCaja" class="modal grey lighten-3">

    <div class="modal-content grey lighten-3">

        <div class="modal-header">

            <a href="#!" class="modal-close waves-effect waves-green btn-flat right"><i class='fas fa-times'></i></a>
            <h4 class="center" >Caja No <span id="NumeroCaja"></span></h4>
            
            <table class="centered">
                <thead>
                    <tr>
                        <th>Alistador: <span id="alistador"></span></th>
                        <th>Tipo Caja: <span id="tipocaja"></span></th>
                        <th>Fecha cierre: <span id="cierre"></span></th>
                    </tr>
                    <tr>
                        <th>Origen: <span id="origen"></span></th>
                        <th>destino: <span id="destino"></span></th>
                    </tr>
                </thead>
            </table>

        </div>

        <table class="tablas centered " id="TablaM"  >
                
                    <thead>

                    <tr  class="white-text green darken-3" >

                        <th>Codigo de barras</th>
                        <th>Referencia</th>
                        <th>Descripción</th>
                        <th>Disponibilidad</th>
                        <th>Pedidos</th>
                        <th>Alistados</th>
                        <th>Ubicacion</th>
                        <th>Texto</th>

                    </tr>

                    </thead>

                    <tbody id="tablamodal"></tbody>

        </table> 
        
        <div class="modal-footer grey lighten-3">
            <button id="Documento" title="GenerarDocumento" class="btn left waves-effect green darken-4 col s12 m12 l8" >
                <i class="fas fa-file-alt"></i>
            </button>
        </div>

    </div>

</div>

<!-- ============================================================================================================================
                                                    SCRIPTS JAVASCRIPT   
============================================================================================================================ -->
<!-- GUARDA EL NOMBRE DEL USUARIO DE LA SESION EN UNA VARIABLE DE JS -->
<script type="text/javascript">
    var id_usuario='<?php echo $_SESSION["usuario"]["id"];?>';
</script>

<!-- JS QUE MANEJA LOS EVENTOS DE LA PAGINA -->
<script src="vistas/js/cajas.js">



</script>




