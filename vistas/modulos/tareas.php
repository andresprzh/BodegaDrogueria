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
<div id="informacion" class="modal ">

    <div class="modal-content ">

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

            <ul class="collection with-header" id="listtareas">
                <li class="collection-header"><h4>tareas asignadas</h4></li>
                <li class="collection-item " style="padding:0;">
                    <button  class="btn green col s12" style="font-style: oblique;">Agregar tarea <i class="fas fa-plus" style="font-style: oblique;"></i></button   >
                </li>
                <li class="collection-item">Tarea1 
                    <span class="green-text">Completada</span>
                    <span >Fech asignacion: 28/08/2018</span>
                    <span >Fech terminacion: 30/08/2018</span>
                </li>
                <li class="collection-item">tarea2 
                    <span class="grey-text darken-4">Pendiente</span>
                    <span >Fech asignacion: 29/08/2018</span>
                    <span >Fech terminacion: "-/-/-"</span>
                </li>
                <li class="collection-item">tarea3
                    <span class="grey-text darken-4">Pendiente</span>
                    <span >Fech asignacion: 30/08/2018</span>
                    <span >Fech terminacion: "-/-/-"</span>
                </li>
            </ul>

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