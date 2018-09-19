<h2 class="header center ">Pedido</h2>
<!-- ============================================================================================================================
                                                INPUT SELECCIONAR REQUISICION   
============================================================================================================================ -->
<div class="row">

    <div class="input-field col s9 m10 l11 " >

        <select   list="requeridos" name="requeridos" class="requeridos" id="requeridos">
            <option value="" disabled selected>Seleccionar</option>
        </select>
        <label  style="font-size:12px;">Número requisicion</label>

    </div>
    <div class="input-field col s3 m1 l1  input_refresh">

        <button id="refresh" title="Recargar" disabled onclick="recargarCajas()" class="btn waves-effect waves-light green darken-3 col s12 m12 l8" >
            <i class="fas fa-sync"></i>
        </button>
        
    </div>
    
</div>

<div class="divider green darken-4"></div>


<div class="row hide " id="Cajas">

<!-- ============================================================================================================================
                                                Tabla que lista todas las cajas  
============================================================================================================================ -->
    <h5 class="header center ">Cajas</h5>

    <div class="col s12 m12 l12 ">

        <table class="datatable centered " id="TablaC" >

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
            <h4 class="center" >Caja No <span class="NumeroCaja"></span></h4>
            
            <table class="centered no-border" >
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

        <table class="datatable centered " id="TablaM"  >
                
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
                        <th>Texto</th>

                    </tr>

                    </thead>

                    <tbody id="tablamodal"></tbody>

        </table> 
        
        <div class="modal-footer grey lighten-3">
            <button id="Documento" title="GenerarDocumento" class="btn right waves-effect green darken-4 col s12 m12 l8" >
                <i class="fas fa-file-alt"></i>
            </button>
            <button id="eliminar" title="Cancelar Caja" class="btn left waves-effect red darken-4 col s12 m12 l8" >
                <i class="fas fa-ban"></i>
            </button>
        </div>
        
    </div>

</div>
<!-- ============================================================================================================================
                                                    MODAL EDITAR CAJA2 
============================================================================================================================ -->
<div id="EditarCaja2" class="modal grey lighten-3">

    <div class="modal-content grey lighten-3">

        <div class="modal-header">

            <a href="#!" class="modal-close waves-effect waves-green btn-flat right"><i class='fas fa-times'></i></a>
            <h4 class="center" >Caja No <span class="NumeroCaja"></span></h4>

        </div>
        <!-- ============================================================================================================================
                                                            FORMAULARIO    
        ============================================================================================================================ -->
        <div class="container fixed" style="padding-left:15px;" >

            <!-- INPUT PARA AGREGAR ITEMS -->
            <div class="row ">      
                <div class="input-field center col s12 m10 l10  input_barras">

                    <input  id="codbarras" type="text" class="validate">
                    <label for="codbarras" class="right">Item</label>

                </div>  
                <div class="input-field col hide-on-small-only m1 l2  input_barras">

                    <button id="agregar" title="Buscar Item" class="btn waves-effect waves-light green darken-3 col s12 m12 l8" >
                        <i class="fas fa-plus"></i>
                    </button>
                    
                </div>
            </div>

        </div>
        <!--============================================================================================================================
        ============================================================================================================================
                                                TABLAS
        ============================================================================================================================
        ============================================================================================================================-->

        <div class="row " id="contenido"  >
            
            
            <!-- ==============================================================
                        TABLA EDITABLE    
            ============================================================== -->
            <div class="col s12" id="TablaE" >


                <table class="tabla centered " id="TablaEr" style="width:100%">
                
                    <thead>

                    <tr  class="white-text green darken-3" >

                        <th>Descripcion</th>
                        <th>ID Item</th>
                        <th>Alistados</th>
                        <th>Recibidos</th>
                        <th>Problema</th>
                        <th data-priority="2" class='black-text'>Eliminar</th>

                    </tr>

                    </thead>

                    <tbody id="tablaerror"></tbody>
                    <!-- ==================================
                        INPUT PARA CERRAR CAJA  
                    ================================== -->
                    <div class="col s12  " id="input_cerrar">
                    
                        <div class="input-field col s4 m3 l3">

                            <button id="cerrar" class="btn waves-effect green darken-4 col s12 m12 l8" >
                                Modificar
                            </button>
                            
                        </div>  

                    </div>
                </table> 
                  
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




