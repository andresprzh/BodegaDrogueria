
<div class="navbar-fixed ">
    <nav class="green darken-2" role="navigation" >

        <div class="nav-wrapper">
            <ul class="sidenav-trigger"  data-target="desplegable">
                <li><a href="#"><i class="fas fa-bars"></i></a></li>
            </ul>

            <a id="logo-container" href="inicio" class="brand-logo center">
                <span>BodegaSJ</span>
            </a>
        </div>

        <!-- <a href="#" data-target="slide-out" class="sidenav-trigger"><i class="material-icons">menu</i></a> -->
    </nav>
</div>


<!-- ==========================================
                MENU DESPLEGABLE
=============================================== -->
<ul id="desplegable" class="sidenav ">
        
        <li>

            <div class="user-view">

                <div class="background green darken-2">
                </div>

                <a href="#user"><img class="circle white-text" src="vistas/imagenes/usuarios/default/anonymous.png"></a>
                <?php
                    echo '<a href="#name"><span class="name white-text">'.$_SESSION["usuario"]["usuario"].'</span></a>';
                    echo '<a href="#name"><span class="name white-text">'.$_SESSION["usuario"]["nombre"].'</span></a>';
                ?>
                

            </div>

        </li>
        <?php

            if (in_array($_SESSION["usuario"]["perfil"],[1])) {
                
                echo '<li><a class="subheader">Requisiciones</a></li>';
                echo '<li><a href="requerir" ><i class="fas fa-file-upload"></i>Subir archivo requisicion</a></li>';
                echo '<li><a href="requisiciones" ><i class="fas fa-list"></i>Requisiciones</a></li>';
                echo '<li><a href="alistar" ><i class="fas fa-clipboard-list"></i>Alistar items</a></li>';
                echo '<li><a href="cajas" ><i class="fas fa-boxes"></i>Ver cajas alistadas</a></li>';
                echo '<li><a href="Nitem" ><i class="fas fa-plus-square"></i><span class="truncate">Agregar items a requisición<span></a></li>';
                echo '<li><a href="pv" ><i class="fas fa-clipboard-check"></i>Registrar cajas</a></li>';
                
                echo '<li><a href="pvcajas" ><i class="fas fa-boxes"></i>Ver cajas registradas</a></li>';
                echo '<li><a href="tareas" ><i class="fas fa-tasks"></i>Tareas</a></li>';
                echo '<li><a href="usuarios" ><i class="fas fa-users"></i>Administrar usuarios</a></li>';
                echo '<li><a href="transportador" ><i class="fas fa-truck"></i>Despachos</a></li>';
                
                echo '<li><div class="divider  "></div></li>';

                echo '<li><a class="subheader">Remisiones</a></li>';
                echo '<li><a href="remisiones" ><i class="fas fa-upload"></i>Unificar Remisiones</a></li>';
                echo '<li><a href="franquicia" ><i class="fas fa-clipboard-check"></i>Validar llegada Remision</a></li>';
                echo '<li><a href="franquiciaremision" ><i class="fas fa-clipboard-list"></i>Re-validar</a></li>';
            }
            if ($_SESSION["usuario"]["perfil"]==2) {
                echo '<li><a href="requerir" ><i class="fas fa-upload"></i>Subir archivo requisicion</a></li>';
                echo '<li><a href="requisiciones" ><i class="fas fa-list"></i>Requisiciones</a></li>';
                echo '<li><a href="cajas" ><i class="fas fa-boxes"></i>Ver cajas alistadas</a></li>';
                echo '<li><a href="Nitem" ><i class="fas fa-plus-square"></i><span class="truncate">Agregar items a requisición<span></a></li>';
                echo '<li><a href="tareas" ><i class="fas fa-tasks"></i>Tareas</a></li>';
                echo '<li><a href="usuarios" ><i class="fas fa-users"></i>Administrar usuarios</a></li>';
                // echo '<li><a href="tareas" ><i class="fas fa-tasks"></i>Tareas</a></li>';
            }
            if ($_SESSION["usuario"]["perfil"]==5) {
                echo '<li><a href="requerir" ><i class="fas fa-upload"></i>Subir archivo requisicion</a></li>';
                echo '<li><a href="requisiciones" ><i class="fas fa-list"></i>Requisiciones</a></li>';
                echo '<li><a href="Nitem" ><i class="fas fa-plus-square"></i><span class="truncate">Agregar items a requisición<span></a></li>';
                echo '<li><a href="cajas" ><i class="fas fa-boxes"></i>Ver cajas alistadas</a></li>';
            }
            if ($_SESSION["usuario"]["perfil"]==3) {
                echo '<li><a href="alistar" >Alistar items</a></li>';
            }
            if ($_SESSION["usuario"]["perfil"]==4) {
                echo '<li><a href="pv"><i class="fas fa-clipboard-check"></i>Registrar cajas</a></li>';
                echo '<li><a href="pvcajas" ><i class="fas fa-boxes"></i>Ver cajas registradas</a></li>';
            }
            if ($_SESSION["usuario"]["perfil"]==6) {
                echo '<li><a href="transportador" ><i class="fas fa-truck"></i>Despachos</a></li>';
            }
            
            if ($_SESSION["usuario"]["perfil"]==7   ) {
                echo '<li><a href="franquicia" ><i class="fas fa-clipboard-check"></i>Validar llegada Remision</a></li>';
                echo '<li><a href="franquiciaremision" ><i class="fas fa-clipboard-list"></i>Re-validar</a></li>';
            }
            if ($_SESSION["usuario"]["perfil"]==8   ) {
                echo '<li><a href="remisiones" ><i class="fas fa-upload"></i>Unificar Remisiones</a></li>';
            }
            

        ?>
        <li><div class="divider  "></div></li>                
        <li class="">
            <a href="salir" class="waves-effect "><i class="fas fa-sign-out-alt"></i>Salir</a>
        </li>

        

</ul>