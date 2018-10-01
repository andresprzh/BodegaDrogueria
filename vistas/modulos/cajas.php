<script src="vistas/plugins/sweetalert2/sweetalert2.all.js"></script>
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


<div class="row " id="contenido">
    <div class="col s12" id="tabsmenu">
            
            <ul class="tabs ">
                <li class="tab col s6" id="tabsI1">
                    <a class="black-text"  href="#TablaA">
                        <i class="fas fa-box"></i>
                        <span style="font-size:100%">Cajas Alistadas</span>
                    </a>
                </li>
                <li class="tab col s6 " id="tabsI2">
                    <a class="black-text"  href="#TablaR">
                        <i class="fas fa-check"></i>
                        <span style="font-size:100%">Cajas Enviadas</span>
                    </a>
                </li>
            </ul>

    </div>
    <!-- ============================================================================================================================
                                                Tabla que lista todas las cajas Alistadas 
    ============================================================================================================================ -->
    <div class="col s12 " id="TablaA">

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
        <div class="row">

            <!-- <div class="right input-field col s4 m2 l2"> -->

                <button id="despachar" class="right btn waves-effect tea darken-4 col s12 m2 l2"  disabled>
                    Despachar cajas
                </button>
                
            <!-- </div>   -->
            
        </div>

    </div>

     <!-- ============================================================================================================================
                                                Tabla que lista todas las cajas Resibidas 
    ============================================================================================================================ -->
    <div class="col s12 " id="TablaR">

        <table class="datatable centered " id="TablaCE" >

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

        <div class="modal-footer grey lighten-3">
            <button id="Documento" title="GenerarDocumento" class="btn right waves-effect green darken-4 col s12 m12 l8" >
                <i class="fas fa-file-alt"></i>
            </button>
            <button id="imprimir" title="Impriir Lista de items" class="btn right waves-effect green darken-4 col s12 m12 l8" >
                <i class="fas fa-print"></i>
            </button>
            <button id="eliminar" title="Eliminar Caja" class="btn left waves-effect red darken-4 col s12 m12 l8" >
                <i class="fas fa-ban"></i>
            </button>
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
            <h5 class="red-text center">Problemas items recibidos</h5>
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

                    <tr  class="white-text red darken-3" >

                        <th>Descripcion</th>
                        <th>Caja enviada</th>
                        <th>Caja recibida</th>
                        <th>Alistados</th>
                        <th>Recibidos</th>
                        <th>Problema</th>

                    </tr>

                    </thead>

                    <tbody id="tablaerror"></tbody>
                    <!-- ==================================
                        INPUT PARA MODIFICAR CAJA  
                    ================================== -->
                    
                    <div class="input-field col s4 m3 l3">

                        <button id="modificar" class="btn waves-effect green darken-4 " >
                            Modificar
                        </button>
                        
                    </div>  

                    
                </table> 
                  
            </div>

        </div>
    </div>
</div>
<!-- ============================================================================================================================
                                                    SCRIPTS JAVASCRIPT   
============================================================================================================================ -->
<style scoped>
    /* #EditarCaja2{
        width:100%;
    } */
    .modal{
        width:100%;
    }
    .tabla  td:first-child, .tabla  th:first-child{
        width:30%;
        text-align: center;
    }
    .tabla  td:last-child, .tabla  th:last-child{
        width:30%;
        text-align: center;
    }
</style>
<!-- GUARDA EL NOMBRE DEL USUARIO DE LA SESION EN UNA VARIABLE DE JS -->
<script type="text/javascript">
    var id_usuario='<?php echo $_SESSION["usuario"]["id"];?>';
</script>

<!-- JS QUE MANEJA LOS EVENTOS DE LA PAGINA -->
<script src="vistas/js/cajas.js">



</script>




