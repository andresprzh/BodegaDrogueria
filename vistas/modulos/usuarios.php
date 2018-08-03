<h3 class="header center "><i class="fas fa-users"></i> Usuarios</h3>

<div class="row">
    <div class="col s12 " id="TablaU" >
   

        <table class="tablas centered responsive table"  >

        <button  title='Añadir usuario'  data-target='editarusuario' id='addusuario' class='modal-trigger btn-small waves-effect waves-light green darken-3 ' >
            <i class='fas fa-user-plus'></i>
        </button>
            <thead>
            
            <tr class="white-text green darken-3 ">

                <th>#</th>
                <th><i class="fas fa-users"></i> Usuario</th>
                <th><i class="fas fa-book"></i> Nombre</th>
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

    <div class="modal-content grey lighten-3">

        <div class="modal-header">

            <h3>Usuario</h3>
        </div>
        
        <div class="modal-footer grey lighten-3">
            
        </div>

    </div>

</div>

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
