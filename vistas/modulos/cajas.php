<div>
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
</div>
<!-- ============================================================================================================================
                                                    MODAL EDITAR CAJA 
============================================================================================================================ -->
<div id="EditarCaja" class="modal">

<div class="modal-content">

        <div class="modal-header">

            <a href="#!" class="modal-close waves-effect waves-green btn-flat right"><i class='fas fa-times'></i></a>
            <h4 class="center" >Caja No <span class="NumeroCaja"></span></h4>
            
            <table class="centered no-border" >
                <thead>
                    <tr>
                        <th>Alistador: <span id="alistador"></span></th>
                        <th>Tipo Caja: <span id="tipocaja"></span></th>
                        <th>Fecha cierre: <span id="cierre"></span></th>
                    </tr>
                    <tr>
                        <th>Origen: <span id="origen">001-BD CENTRO</span></th>
                        <th>destino: <span id="destino"></span></th>
                    </tr>
                </thead>
            </table>

        </div>

        <div class="modal-footer grey lighten-3 row">
            <button id="eliminar" title="Eliminar Caja" class="btn  waves-effect red darken-4 col s2 m2 l1 left" >
                <i class="fas fa-ban"></i>
            </button>
            <button id="cambiar" title="Reasignar caja" class="btn waves-effect green darken-4 col s2 m2 l1 center" >
                <i class="fas fa-user"></i>
            </button>
            <button id="imprimir" title="Impriir Lista de items" class="btn waves-effect green darken-4 col s2 m2 l1 right" >
                <i class="fas fa-print"></i>
            </button>
            
        </div>
        <form id="formmodal">
            <table class="tablascroll centered " id="TablaM"  >
                    
                        <thead>

                        <tr  class="white-text green darken-3" >

                            <th>Codigo de barras</th>
                            <th>ID item</th>
                            <th>Referencia</th>
                            <th>Descripción</th>
                            <th>Disponibilidad</th>
                            <th>Pedidos</th>
                            <th>Alistados</th>
                            <th>Ubicacion</th>

                        </tr>

                        </thead>

        <a href="#!" class="modal-close waves-effect waves-green btn-flat right"><i class='fas fa-times'></i></a>
        <h4 class="center" id="TituloCaja"></h4>
        
        <table class="centered green-text">
            <thead>
                <th>Alistador: <span id="alistador"></span></th>
                <th>Tipo Caja: <span id="tipocaja"></span></th>
                <th>Fecha cierre: <span id="cierre"></span></th>
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

                </tr>

                </thead>

                <tbody id="tablamodal"></tbody>

    </table> 

</div>

<div class="modal-footer">

    <button id="Documento" title="GenerarDocumento" class="btn left waves-effect green darken-4 col s12 m12 l8" >
        <i class="fas fa-file-alt"></i>
    </button>

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




