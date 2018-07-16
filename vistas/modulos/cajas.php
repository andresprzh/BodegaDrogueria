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


    <div class="row hide" id="TablaCajas">

<!-- ============================================================================================================================
                                                    Tabla que lista todas las cajas  
============================================================================================================================ -->
        
        <h5 class="header center ">Cajas</h5>

        <div class="col s12 m12 l12 ">

            <table class="tablas centered "  >

                    <thead>
                    
                    <tr class="white-text green darken-3 ">

                        <th># Caja</th>
                        <th>Alistador</th>
                        <th>Tipo de caja</th>
                        <th>Abierta</th>
                        <th>Cerrada</th>
                        <th>Editar</th>
                        
                    </tr>

                    </thead>

                    <tbody id="tablacajas"></tbody>
                    
                </table>  
                    
        </div>
        
    </div>
</div>
<!-- Modal Editar caja -->
<div id="EdicarCaja" class="modal ">

<div class="modal-content grey lighten-3">

<<<<<<< HEAD
    <div class="modal-header grey lighten-3">

    <a href="#!" class="modal-close waves-effect waves-green btn-flat right"><i class='fas fa-times red-text'></i></a>

    </div>
=======
    <div class="modal-footer">

        <a href="#!" class="modal-close waves-effect waves-green btn-flat right"><i class='fas fa-times'></i></a>

    </div>

>>>>>>> caja
    <h4 class="center">Editar caja</h4>
    
    <table class="tablas "  >
            
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

                <tbody id="tablavista"></tbody>

    </table> 
    
    <div>

        <button id="Guardar" title="Guardar" class="btn waves-effect green darken-4 col s12 m12 l8" >
            <i class="fas fa-save"></i>
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




