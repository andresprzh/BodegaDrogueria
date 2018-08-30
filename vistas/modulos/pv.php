<h3 class="header center ">Punto de Venta</h3>
<!-- ============================================================================================================================
                                                    FORMAULARIO    
============================================================================================================================ -->
<div class="container fixed" style="padding-left:15px;" >

    <div class="row">

        <div class="input-field col s6 " >

            <select   list="requeridos" name="requeridos" class="requeridos" id="requeridos">
                <option value="" disabled selected>Seleccionar</option>
            </select>
            <label  style="font-size:12px;">Número requisicion</label>

        </div>
        <div class="input-field col s6 hide SelectCaja" >

            <select   list="cajas" name="cajas" class="cajas" id="cajas">
                
            </select>
            <label  style="font-size:12px;">Número Caja</label>

        </div>
        
    </div>

    <div class="row ">

        <div class="input-field col s12 m10 l11 hide  input_barras">

            <input  id="codbarras" type="text" class="validate">
            <label for="codbarras" >Item</label>

        </div>  
        
        <div class="input-field col s12 m1 l1 hide input_barras">

            <button id="agregar" title="Buscar Item extra" class="btn waves-effect waves-light green darken-3 col s12 m12 l8" >
                <i class="fas fa-plus"></i>
            </button>
            
        </div>    

    </div>

    <div>
        <table class="centered hide" id="infreq">
            <thead>
                <tr>
                    <th>Origen Requisición: <span id="origen"></span></th>
                    <th>Destino Requisición: <span id="destino"></span></th>
            </thead>
        </table>
    </div>

</div>

<!--============================================================================================================================
============================================================================================================================
                                        TABLAS
============================================================================================================================
============================================================================================================================-->

<div class="col s12" style="padding: 20px;"id="TablaV" >

    <h4 class="header center " >Items</h4>     

    <table class="tablas centered "  id="tabla">

        <thead>
        
        <tr class="white-text green darken-3 ">

            <th>Codigo de barras</th>
            <th>ID item</th>
            <th>Referencia</th>
            <th>Descripción</th>
            <th>Cantidad</th>
            <th data-priority="2" class='black-text'>Eliminar</th>
            
        </tr>

        </thead>

        <tbody id="tablaeditable"></tbody>
        
    </table> 

    <div class="input-field" >

        <button id="Registrar" class="btn waves-effect red darken-4 col s12 m12 l8 hide" >
            Registrar
        </button>

        <button id="documento" title="Generar Documento" class="btn waves-effect green darken-4 col s12 m12 l8 hide " >
            <i class="far fa-file-alt"></i>
        </button>
       
</div>

<!-- ============================================================================================================================
                                                    MODAL REGISTRO DE ITEMS
============================================================================================================================ -->
<div id="informacion" class="modal grey lighten-3">

    <div class="modal-content grey lighten-3">

        <div class="modal-header ">

            <a href="#!" class="modal-close waves-effect waves-green btn-flat right"><i class='fas fa-times'></i></a>
            <h4 class="center red-text darken-3 " >Información</span></h4>

        </div>

        <table class="" id="TablaM"  >
                
                    <thead>

                    <tr  class="white-text red darken-3" >

                        <th>Item</th>
                        <th>ID item</th>
                        <th>Descripcion</th>

                    </tr>

                    </thead>

                    <tbody id="tablamodal"></tbody>

        </table> 

    </div>

</div>
<!-- ============================================================================================================================
                                                    ESTILOS  
============================================================================================================================ -->
<style scoped>
#TablaM tbody {
  display:block;
  height:380px;
  overflow-y:auto;
  
  }
#TablaM  thead,#TablaM tbody tr {
  display:table;
  width:100%;
  table-layout:fixed;
  }
/* #TablaM  thead {
  width: calc( 100% - 1em )
  } */
  #TablaM {
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
  }

#TablaM td, #TablaM th {
    border: 1px solid #ddd;
    padding: 8px;
}

#TablaM tr:nth-child(even){background-color: #f2f2f2;}

#TablaM tr:hover {background-color: #ddd;}

#TablaM th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    color: white;
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
<script src="vistas/js/pv.js"></script>