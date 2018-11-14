<!-- USA SWEETALERT2 -->
<script src="vistas/plugins/sweetalert2/sweetalert2.all.js"></script>
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

        <div class="row">

        <div class="input-field col s12 " >

            <ul class="collection with-header" id="listtareas">
                <li class="collection-header"><h4 class="center-align">Ubicaciones</h4></li>
                <li 
                 id="agregarubicacion"  
                 class="collection-item center-align green btn waves-effect waves-light col s12" 
                 style="padding:0; ">
                    Agregar Ubicacion<i class="fas fa-plus" style="font-style: oblique;"></i>
                </li>
                <div id="ubicaciones"></div>
            </ul>

        </div>

        </div>

    </div>

</div>

<!-- ============================================================================================================================
                                                    MODAL SELECCIONAR UBICACIONES
============================================================================================================================ -->
<div id="seleubic" class="modal  ">

    <div class="modal-content ">

        <div class="modal-header ">
        
            <p class="center " >Asignar Ubiaciones Usuario: <span id="iduser"></span></p>

        </div>

        <form action="#" id="ubic">
            <p>
            <label>
                <input type="checkbox" />
                <span>Red</span>
            </label>
            </p>
            <p>
            <label>
                <input type="checkbox" checked="checked" />
                <span>Yellow</span>
            </label>
            </p>
            <p>
            <label>
                <input type="checkbox" class="filled-in" checked="checked" />
                <span>Filled in</span>
            </label>
            </p>
            <p>
            <label>
                <input id="indeterminate-checkbox" type="checkbox" />
                <span>Indeterminate Style</span>
            </label>
            </p>
            <p>
            <label>
                <input type="checkbox" checked="checked" disabled="disabled" />
                <span>Green</span>
            </label>
            </p>
            <p>
            <label>
                <input type="checkbox" disabled="disabled" />
                <span>Brown</span>
            </label>
            </p><p>
            <label>
                <input type="checkbox" disabled="disabled" />
                <span>Brown</span>
            </label>
            </p><p>
            <label>
                <input type="checkbox" disabled="disabled" />
                <span>Brown</span>
            </label>
            </p><p>
            <label>
                <input type="checkbox" disabled="disabled" />
                <span>Brown</span>
            </label>
            </p><p>
            <label>
                <input type="checkbox" disabled="disabled" />
                <span>Brown</span>
            </label>
            </p>
        </form>

    </div>

</div>
<!-- ============================================================================================================================
                                                        ESTILOS 
============================================================================================================================ -->
<style>
#ubicaciones
{
    height:200px; 
    width:100%;
}
#ubicaciones 
{
    overflow:hidden; 
    overflow-y:scroll;
}

#seleubic
{       

  min-height: 300px;
  height: 300px;
  min-width: 100px;
}
#seleubic form{
    min-height:200px;
    height:200px;
    overflow-y:auto;
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