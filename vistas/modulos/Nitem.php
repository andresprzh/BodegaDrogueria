<h3 class="header center ">Agregar items</h3>
<!-- ============================================================================================================================
                                                    FORMAULARIO    
============================================================================================================================ -->
<div class="container fixed" style="padding-left:15px;" >
    <form class="row " id='formitem'>
    

        <div class="input-field col s6 " >

            <select   list="requeridos" name="requeridos" class="requeridos" id="requeridos">
                <option value="" disabled selected>Seleccionar</option>
            </select>
            <label  style="font-size:12px;">Número requisicion</label>

        </div>
        
    
        <div class="input-field col s12 m10 l11  hide input_item">

            <input  id="item" type="text"   pattern=".{3,}" minlength="1"  title="minimo 3 caracteres" required>
            <label for="codbarras" >Item</label>

        </div>  
        
        <div class="input-field col s12 m1 l1 hide input_item">

            <button type="submit" id="agregar" title="Buscar Item extra"  class="btn waves-effect waves-light green darken-3 col s12 " required >
                <i class="fas fa-plus"></i>
            </button>
            
        </div>    

    </form>

    

</div>

<!--============================================================================================================================
============================================================================================================================
                                        TABLAS
============================================================================================================================
============================================================================================================================-->

<div class="col s12 hide" style="padding: 20px;" id="DivTabla">

    <h4 class="header center " >Items</h4>   

    <table class="tabla centered "  id="TablaI">
        
        <thead>
        
        <tr class="white-text green darken-3 ">

            <th>Codigo de barras</th>
            <th>ID item</th>
            <th>Referencia</th>
            <th>Descripción</th>
            <th><span class="truncate">Cantidad</span></th>
            <th><span class="black-text truncate">Eliminar</span></th>
            
        </tr>

        </thead>

        <tbody id="tablaitems"></tbody>
        
    </table> 

    <div class="input-field" >

        <button id="agitems" class="btn waves-effect green darken-4 col s12 m12 l8" >
            Agregar
        </button>
    </div>
       
</div>

<!-- ============================================================================================================================
                                                    MODAL REGISTRO DE ITEMS
============================================================================================================================ -->
<div id="informacion row" class="modal grey lighten-3">

    <div class="modal-content grey lighten-3">

        <div class="modal-header ">

            <a href="#!" class="modal-close waves-effect waves-green btn-flat right"><i class='fas fa-times'></i></a>
            <h4 class="center green-text darken-3 " >Información</span></h4>

        </div>

        <table class="tabla" id="TablaM"  >
            
            <div class="input-field col s12">
                <input id="buscar" type="text" class="">
                <label for="buscar">Buscar</label>
            </div>

            <thead>

            <tr  class="white-text green darken-3" >
                
                <th>Codigo de barras</th>
                <th>Item</th>
                <th>Referencia</th>
                <th>Descripción</th>
                <th>agregar</th>
                

            </tr>

            </thead>

            <tbody id="tablamodal"></tbody>

        </table> 

    </div>

</div>
<style>

@media(max-width:630px){
    
    table  td:nth-child(1), th:nth-child(1),td:nth-child(2), th:nth-child(2),td:nth-child(3), th:nth-child(3) {
        display: none;
    }

    table#TablaI  td:nth-child(4), th:nth-child(4){
        width: 50%;
    }
    table#TablaI  td:nth-child(5), th:nth-child(5),td:nth-child(6), th:nth-child(6){
        width: 20%;
    }
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
<script src="vistas/js/nitem.js"></script>