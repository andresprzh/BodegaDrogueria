<h3 class="header center ">Asignar tareas</h3>
<!-- ============================================================================================================================
                                                    FORMAULARIO    
============================================================================================================================ -->
<div class=" fixed" style="padding:15px;" >

    <table class="" id="TablaU"  >
        
        <div class="input-field col s6">
            <input id="buscar" type="text" class="">
            <label for="buscar">Buscar</label>
        </div>

        <thead>
        
            <tr  class="white-text green darken-3" >

                <th>Nombre</th>
                <th>Cedula</th>
                <th>Tarea</th>

            </tr>

        </thead>

        <tbody></tbody>

    </table> 

</div>

<!-- ============================================================================================================================
                                                    MODAL TAREAS DE USUARIOS
============================================================================================================================ -->
<div id="informacion" class="modal grey lighten-3">

    <div class="modal-content grey lighten-3">

        <div class="modal-header ">
        
            <a href="#!" class="modal-close waves-effect waves-green btn-flat right"><i class='fas fa-times'></i></a>
            <h4 class="center " >Asignar tareas</span></h4>

            <table class="centered">
                <thead>
                    <tr>
                        <th id="nombre"></th>
                        <th id="cedula"></th>
                    </tr>
                </thead>
            </table>

        </div>

        <div class="row">

        <div class="input-field col s12 " >

            <select   list="requeridos" name="requeridos" class="requeridos" id="requeridos">
                <option value="" disabled selected>Seleccionar</option>
            </select>
            <label  style="font-size:12px;">Seleccionar requisicion</label>

        </div>

        </div>

    </div>

</div>
<!-- ============================================================================================================================
                                                    ESTILOS  
============================================================================================================================ -->
<style scoped>
#TablaU tbody {
  display:block;
  height:380px;
  overflow-y:auto;
  
  }
  #TablaU  thead,#TablaU tbody tr {
  display:table;
  width:100%;
  table-layout:fixed;
  }
  #TablaU  td:last-child, #TablaU  th:last-child{
      width:60px;
  }
  #TablaU {
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
  }

#TablaU td, #TablaU th {
    border: 1px solid #ddd;
    padding: 8px;
}

#TablaU tr:nth-child(even){background-color: #f2f2f2;}

#TablaU th {
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
<script src="vistas/js/tareas.js"></script>