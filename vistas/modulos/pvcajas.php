<h3 class="header center ">Cajas recibidas</h3>

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

<div>
    <ul class="collection" id="cajas">
        <li class="collection-item avatar">
            <i class="fas fa-box circle"></i>
            <span class="title">Title</span>
            <p>First Line <br>
                Second Line
            </p>
            <button id="Documento" title="GenerarDocumento" class="btn-floating  secondary-content waves-effect green darken-4 " >
                <i class="fas fa-file-alt"></i>
            </button>
        </li>
        <li class="collection-item avatar">
            <i class="fas fa-box circle red" ></i>
            <span class="title">Title</span>
            <p>First Line <br>
                Second Line
            </p>
            <button id="Documento" title="GenerarDocumento" class="btn-floating  secondary-content waves-effect green darken-4 " >
                <i class="fas fa-file-alt"></i>
            </button>
        </li>
        <li class="collection-item avatar">
            <i class="fas fa-box circle green" ></i>
            <span class="title">Title</span>
            <p>First Line <br>
                Second Line
            </p>
            <button id="Documento" title="GenerarDocumento" class="btn-floating  secondary-content waves-effect green darken-4 " >
                <i class="fas fa-file-alt"></i>
            </button>
        </li>
        <li class="collection-item avatar">
            <i class="fas fa-box circle blue" ></i>
            <span class="title">Title</span>
            <p>First Line <br>
                Second Line
            </p>
            <button id="Documento" title="GenerarDocumento" class="btn-floating  secondary-content waves-effect green darken-4 " >
                <i class="fas fa-file-alt"></i>
            </button>
        </li>
    </ul>
</div>
<!-- ============================================================================================================================
                                                    MODAL VER CAJA
============================================================================================================================ -->
<div id="VerCaja" class="modal grey lighten-3">

    <div class="modal-content grey lighten-3">

        <div class="modal-header">

            <a href="#!" class="modal-close waves-effect waves-green btn-flat right"><i class='fas fa-times'></i></a>
            <h4 class="center" >Caja No <span class="NumeroCaja"></span></h4>

        </div>

        <div class="modal-footer grey lighten-3">
            <button id="Documento" title="GenerarDocumento" class="btn right waves-effect green darken-4 col s12 m12 l8" >
                <i class="fas fa-file-alt"></i>
            </button>
        </div>

        <table class="datatable centered " id="TablaM"  >
                
                    <thead>

                    <tr  class="white-text green darken-3" >
                        <th>Descripción</th>
                        <th>Codigo de barras</th>
                        <th>Recibidos</th>
                    </tr>

                    </thead>

                    <tbody id="tablamodal"></tbody>

        </table> 
        
        
        
    </div>
</div>

<style scope>
    #cajas .modal-trigger:hover{
        cursor: pointer;
    }
</style>
<!-- ============================================================================================================================
                                                    SCRIPTS JAVASCRIPT   
============================================================================================================================ -->

<!-- GUARDA EL NOMBRE DEL USUARIO DE LA SESION EN UNA VARIABLE DE JS -->
<script type="text/javascript">
    var id_usuario='<?php echo $_SESSION["usuario"]["id"];?>';
</script>

<!-- JS QUE MANEJA LOS EVENTOS DE LA PAGINA -->
<script src="vistas/js/pvcajas.js">



</script>