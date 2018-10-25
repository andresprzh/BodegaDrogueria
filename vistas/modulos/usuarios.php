<h3 class="header center "><i class="fas fa-users"></i> Usuarios</h3>


<div class="row">
    <div class="col s12 "  >

        <button  title='Añadir usuario'  data-target='editarusuario' id='addusuario' class='left modal-trigger btn-small waves-effect waves-light green darken-3 ' >
                <i class='fas fa-user-plus'></i>
        </button>
       
        <table class="tabla" id="TablaU"  >

            <div class="input-field col s12">
                <input id="buscar" type="text" class="">
                <label for="buscar">Buscar</label>
            </div>

            <thead>
            
            <tr class="white-text green darken-3 ">

                <th><i class="fas fa-users"></i> Usuario</th>
                <th><i class="fas fa-pen"></i> Nombre</th>
                <th><i class="fas fa-id-card"></i> Cedula</th>
                <th><i class="fas fa-users-cog"></i> Perfil</th>
                <th><i class="fas fa-user-edit"></i> Editar</th>
                
            </tr>

            </thead>

            <tbody id="tablausuarios"></tbody>
            
        </table>  
    </div>
</div>
<!-- ============================================================================================================================
                                                    MODAL EDITAR USUARIO 
============================================================================================================================ -->
<div id="editarusuario" class="modal grey lighten-3">
    
    <div class="modal-content grey lighten-3" id="modal">
        <form action="" id='formuser'>
            <div class="modal-header">
                <a href="#!" class="modal-close waves-effect waves-green btn-flat right"><i class='fas fa-times'></i></a>
                <h3 class='center' >Usuario <span id='iduser'></span> </h3>

            </div>

            <div class="modal-body container">

                <div class="row">
                    <i class="fas fa-pen"></i><label class="black-text" for="cedula"> Nombre</label>
                    <input id="nombre" required="required" type="text" class="validate">
                    

                </div>

                <div class="row">
                    
                    <i class="fas fa-id-card"></i><label class="black-text" for="cedula"> cedula</label>
                    <input id="cedula" required="required" type="text" class="validate" maxlength="10">
                    

                </div>

                <div class="row">
                    
                    <i class="fas fa-users"></i><label class="black-text" for="usuario"> Usuario</label>
                    <input id="usuario" required="required" type="text" class="validate">
                    

                </div>

                <div class="row">
                    
                    <i class="fas fa-lock"></i><label class="black-text" for="password"> Contraseña</label>
                    <input id="password"  required="required" type="password" class="validate">
                    

                </div>

                <div class="row">
                    <i class="fas fa-users-cog"></i><label class="black-text" for="perfil"> Perfil</label>
                    <select   list="perfil" name="perfil" class="perfil" id="perfil">
                    </select>
                </div>

                <div class="row hide" id='fran'>
                    <i class="fas fa-store-alt"></i><label class="black-text" for="franquicia"> Franquicia</label>
                    <select   list="franquicia" name="franquicia" class="franquicia" id="franquicia"></select>
                </div>

            </div>
            
            <div class=" grey lighten-3 row container">
                <button type='submit' id="agregar" title="Agregar usuario" class="btn right hide waves-effect green darken-4 col s12 m8 l5" >
                    <i class="fas fa-user-plus"></i>
                </button>
                
                <button id="modificar" title="Modificar usuario"  class="btn right hide waves-effect tea darken-4 col s12 m8 l5" >
                    <i class="fas fa-user-edit"></i>
                </button>
            </div>
        </form>
    </div>

</div>
<style scoped>
@media(max-width:630px){
    
    table#TablaU td:nth-child(1), td:nth-child(4),th:nth-child(1), th:nth-child(4) {
        display: none;
    }
}
@media(max-width:380px){
    
    table#TablaU td:nth-child(1),td:nth-child(2), td:nth-child(4),th:nth-child(1),th:nth-child(2), th:nth-child(4) {
        display: none;
    }
}
</style>
<!-- ============================================================================================================================
                                                    SCRIPTS JAVASCRIPT   
============================================================================================================================ -->
<!-- GUARDA EL NOMBRE DEL USUARIO DE LA SESION EN UNA VARIABLE DE JS -->
<script type="text/javascript">
    var id_usuario='<?php echo $_SESSION["usuario"]["id"]; ?>';
</script>

<!-- JS QUE MANEJA LOS EVENTOS DE LA PAGINA -->
<script src="vistas/js/usuarios.js">
</script>
