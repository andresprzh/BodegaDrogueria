<h3 class="header center ">Remisiones recibidas</h3>

<div class="row">

    
    <div class="input-field col s3 m1 l1  right input_refresh">

        <button id="refresh" title="Recargar" disabled onclick="mostrarRemisiones()" class="btn waves-effect waves-light green darken-3 col s12 m12 l8" >
            <i class="fas fa-sync"></i>
        </button>
        
    </div>
    
</div>

<div id="contenido" class="hide">
    <ul class="collection" id="remisiones">
        
    </ul>
</div>
<!-- ============================================================================================================================
                                                    MODAL VER remision
============================================================================================================================ -->
<div id="VerRemisiones" class="modal grey lighten-3">

    <div class="modal-content grey lighten-3">

        <div class="modal-header">

            <a href="#!" class="modal-close waves-effect waves-green btn-flat right"><i class='fas fa-times'></i></a>
            <h4 class="center" >Remision No OC<span class="NumeroRemision"></span></h4>

        </div>

        <div class="modal-footer grey lighten-3">
            <button id="Documento" title="GenerarDocumento" class="btn right waves-effect green darken-4 col s12 m12 l8" >
                <i class="fas fa-file-alt"></i>
            </button>
        </div>

        <table class="tabla centered " id="TablaR"  >
                
                    <thead>

                    <tr  class="white-text green darken-3" >
                        <th>Descripción</th>
                        <th>ID item</th>
                        <th>Recibidos</th>
                    </tr>

                    </thead>

                    <tbody id="tablamodal"></tbody>

        </table> 
        
        
        
    </div>
</div>

<style >
    #cajas .modal-trigger:hover{
        cursor: pointer;
    }
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
<!-- ============================================================================================================================
                                                    SCRIPTS JAVASCRIPT   
============================================================================================================================ -->

<!-- GUARDA EL NOMBRE DEL USUARIO DE LA SESION EN UNA VARIABLE DE JS -->
<script type="text/javascript">
    var id_usuario='<?php echo $_SESSION["usuario"]["id"];?>';
    var franquicia='<?php echo $_SESSION["usuario"]["franquicia"];?>';
</script>

<!-- JS QUE MANEJA LOS EVENTOS DE LA PAGINA -->
<script src="vistas/js/franquiciaremision.js">



</script>