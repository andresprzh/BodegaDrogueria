
<h2 class="header center ">Remisiones</h3>

<!-- <form role="form" method="post" enctype="multipart/form-data" style="padding-left:15px;" id="subir"> -->
<!-- ============================================================================================================================
                                                INPUTS   
============================================================================================================================ -->
<div class="row container" >

    <form class="file-upload" id="remisiones">
        <div class="col s12" id="entradas">
            <select   list="franquicias" name="franquicias" class="franquicias  " id="franquicias" required>
                
            </select>
        </div>
        <div class="input-field col s12" id="divfactura">
          <input id="factura" type="text" class="validate" required>
          <label for="factura">Número Factura</label>
        </div>
        <div class="file-upload-main " >
            <input type="file" id="archivos" webkitdirectory mozdirectory msdirectory odirectory directory multiple required/>           
            <div class="file-upload-res" id='contenido'>
                <p class=""><i class="fas fa-upload"></i> Subir</p>
            </div>
            
        </div>
        <button class="btn green col s12" type="submit" id="submitbutton">Subir</button>
    </form>
</div>
    
<!-- ============================================================================================================================
                                                    MODAL VER remision
============================================================================================================================ -->
<div id="VerRemisiones" class="modal grey lighten-3">

    <div class="modal-content grey lighten-3">

        <div class="modal-header">

            <a href="#!" class="modal-close waves-effect waves-green btn-flat right"><i class='fas fa-times'></i></a>
            <h4 class="center" >Items con lote remision <span class="remision"></span></h4>

        </div>
        <form id="formlotes" >
        <div class="modal-footer grey lighten-3 ">
            <button id="Documento" title="GenerarDocumento" type="submit" class="btn right waves-effect green darken-4 col s12 m12 l8" >
                <i class="fas fa-file-alt"></i>
            </button>
        </div>

        <table class="tabla centered " id="TablaL"  >
                
                    <thead>

                    <tr  class="white-text green darken-3" >
                        <th>Descripción</th>
                        <th>ID item</th>
                        <th>cantidad</th>
                        <th>Lote</th>
                        <th>Vencimiento</th>
                    </tr>

                    </thead>

                    <tbody id="tablamodal"></tbody>

        </table>  
        </form>
    </div>
</div>

<style>

.file-upload .file-upload-main{
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -webkit-align-items: center;
      -ms-flex-align: center;
          align-items: center;
  position: relative;
  min-height: 200px;
  width:100%;
  border: 2px dashed green;
  
}
.file-upload .file-upload-main:hover p{
    font-size:20px;
}
.file-upload .file-upload-main:hover p{
    font-size:25px;
}

.file-upload input[type=file]{
    
  position: absolute;
  left: 0;
  top: 0;
  opacity: 0;
  width: 100%;
  height: 100%;
  cursor: pointer;
}
.file-upload .file-upload-res{
    width: 100%;
    text-align: center;
    color:gray;
}

.modal{
    width:100%;
    height:80%;
}
.tabla  td:first-child, .tabla  th:first-child{
    width:30%;
    text-align: center;
}
.tabla  td:last-child, .tabla  th:last-child{
    width:30%;
    text-align: center;
}

.tabla input[type='date'] {
    -moz-appearance:textfield;
}

.tabla input::-webkit-outer-spin-button,
.tabla input::-webkit-inner-spin-button {
    -webkit-appearance: none;
}
</style>
<!-- ============================================================================================================================
                                                        EVENTOS PAGINA REQUERIR    
    ============================================================================================================================ -->
<!-- GUARDA EL NOMBRE DEL USUARIO DE LA SESION EN UNA VARIABLE DE JS -->
<script type="text/javascript">
    var id_usuario='<?php echo $_SESSION["usuario"]["id"];?>';
</script>
<script src="vistas/js/remisiones.js"></script>


