<h3 class="header center ">Alistar</h3>
<!-- ============================================================================================================================
                                                    FORMAULARIO    
============================================================================================================================ -->
<div class="container fixed" style="padding-left:15px;" >

    <div class="row">

        <div class="input-field col s10 m10 l12 " >

            <select   list="requeridos" name="requeridos" class="requeridos" id="requeridos">
                <option value="" disabled selected>Seleccionar</option>
            </select>
            <label  style="font-size:12px;">Número requisicion</label>

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

</div>

<!--============================================================================================================================
============================================================================================================================
                                        TABLAS
============================================================================================================================
============================================================================================================================-->

<div class="row hide" id="contenido"  >
    
    <div class="col s12" id="tabsmenu">
        
            <ul class="tabs ">
                <li class="tab col s6" id="tabsI1"><a class="black-text" href="#TablaV"><i class="fas fa-clipboard-list"></i> Items</a></li>
                <li class="tab col s6 " id="tabsI2"><a class="black-text" href="#TablaE"><i class="fas fa-box-open"></i> Caja</a></li>
            </ul>

    </div>
    <!-- ==============================================================
                            TABLA VISTA O MUESTRA    
    ============================================================== -->
    <div class="col s12 " id="TablaV" >

        <h4 class="header center " >Items</h4>     

        <table class="tablas centered responsive"  >

            <thead>
            
            <tr class="white-text green darken-3 ">

                <th>Codigo de barras</th>
                <th>ID Item</th>
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
                
    </div>


    <!-- ==============================================================
                TABLA EDITABLE    
    ============================================================== -->
    <div class="col s12 hide" id="TablaE" >

        <h4 class="header center green-text text-darken-3"  ><b>Caja</b></h4>

        <table class="tablas centered responsive"  >
        
            <thead>

            <tr  class="white-text green darken-3" >

                <th>Codigo de barras</th>
                <th>ID Item</th>
                <th>Referencia</th>
                <th>Descripción</th>
                <th>Disponibilidad</th>
                <th>Pedidos</th>
                <th>Alistados</th>
                <th>Ubicacion</th>
                <th data-priority="2" class='black-text'>Eliminar</th>

            </tr>

            </thead>

            <tbody id="tablaeditable"></tbody>

        </table> 
        
        <!-- ==================================
                INPUT PARA CERRAR CAJA  
        ================================== -->
        <div class="row  " id="input_cerrar">
    
            <div class="divider green darken-4"></div> 
                    
                <div class="fixed"  style="padding-left:15px;" >

                    <div class="row">

                        <div class="input-field col s8 m7 l4 " >

                            <select   name='caja'  class='carcaja' id='caja'>
                                
                                <option selected value='CRT'>Caja carton</option>
                                <option value='CPL'>Caja plastica</option>
                                <option value='CAP'>Canasta plastica</option>
                                <option value='GLN'>Galon</option>
                                <option value='GLA'>Galoneta</option>

                            </select>

                            <label  style="font-size:17px;">Caja</label>

                        </div>

                        <div class="input-field col s4 m2 l2">

                            <button id="cerrar" class="btn waves-effect tea darken-4 col s12 m12 l8" >
                                Cerrar
                            </button>
                            
                        </div>  
                        
                    </div>

                </div>

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
<script src="vistas/js/alistar.js">

</script>


