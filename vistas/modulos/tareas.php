<!-- USA SWEETALERT2 -->
<script src="vistas/plugins/sweetalert2/sweetalert2.all.js"></script>
<h3 class="header center ">Asignar tareas</h3>
<!-- ============================================================================================================================
                                                    FORMAULARIO    
============================================================================================================================ -->
<div class=" fixed" style="padding:15px;" >

    <table class="" id="TablaU">
        
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
            <h4 class="center " >Asignar Ubiaciones Usuario: <span id="iduser"></span></h4>

            <table class="centered">
                <thead>
                    <tr>
                        <th id="nombre"></th>
                        <th id="cedula"></th>
                    </tr>
                </thead>
            </table>

        </div>

        <div class="row modal_principal" >

        <div class="input-field col s12 " >

            <ul class="collection with-header" id="listtareas">
                <li class="collection-header"><h4 class="center-align">Ubicaciones</h4></li>
                <div class="progress hide green lighten-4">
                    <div class="indeterminate green"></div>
                </div>
                <li 
                 id="agregarubicacion"  
                 class="collection-item center-align green btn waves-effect waves-light col s12" 
                 style="padding:0; ">
                    Agregar Ubicacion<i class="fas fa-plus" style="font-style: oblique;"></i>
                </li>
                <div id="ubicaciones"></div>
            </ul>

        </div>
        <button class="btn red waves-effect waves-light right" onclick="eliminarub()">
            Eliminar todas ubicaciones
        </button>
        </div>

    </div>

</div>

<!-- ============================================================================================================================
                                                    MODAL SELECCIONAR UBICACIONES
============================================================================================================================ -->
<div id="seleubic" class="modal  ">

    <div class="modal-content ">

        <h5 class="center ">Ubicaciones</h5>
        <div class="progress hide green lighten-4">
            <div class="indeterminate green"></div>
        </div>
        <form class="row">

        <label class="col s2 offset-s8 offset-m10">
            <input id="check-todos" type="checkbox"/>
            <span class="black-text" style="font-weight: bold;">Todos</span>
        </label>

        <div  id="ubic" class="col s12">
            
        </div>
        <button class="btn green waves-effect waves-light left" type="submit" name="action">
            Asignar
        </button>
        <button class="modal-close btn red waves-effect waves-light right">
            Cancelar
        </button>
        </form>
    </div>

</div>
<!-- ============================================================================================================================
                                                        ESTILOS 
============================================================================================================================ -->
<style>
#ubicaciones
{
    max-height:300px; 
    width:100%;
}
#ubicaciones 
{
    overflow:hidden; 
    overflow-y:scroll;
}

#seleubic
{       
    min-height: 380px;
    height: 380px;
    min-width: 350px;
    max-width:600px;
    
}
#seleubic #ubic .card{
    min-height:200px;
    height:200px;
    overflow-y:auto;
    
}
.modal-content{
    overflow-y:none;
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