
<div class="navbar-fixed ">
    <nav class="green darken-2" role="navigation" >

        <div class="nav-wrapper">
            <ul class="sidenav-trigger"  data-target="desplegable">
                <li><a href="#"><i class="fas fa-bars"></i></a></li>
            </ul>

            <a id="logo-container" href="inicio" class="brand-logo center">
                <span>Empacar</span>
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
                echo '<li><a href="requerir" ><i class="fas fa-upload"></i>Subir archivo requisicion</a></li>';
                echo '<li><a href="alistar" ><i class="fas fa-clipboard-list"></i>Alistar</a></li>';
                echo '<li><a href="cajas" ><i class="fas fa-boxes"></i>Cajas</a></li>';
                echo '<li><a href="pv" ><i class="fas fa-clipboard-check"></i>Pventa</a></li>';
                echo '<li><a href="tareas" ><i class="fas fa-tasks"></i>Tareas</a></li>';
                echo '<li><a href="usuarios" ><i class="fas fa-users"></i>Usuario</a></li>';
            }
            if ($_SESSION["usuario"]["perfil"]==2) {
                echo '<li><a href="requerir" ><i class="fas fa-upload"></i>Subir archivo requisicion</a></li>';
                echo '<li><a href="cajas" ><i class="fas fa-boxes"></i>Cajas</a></li>';
                echo '<li><a href="usuarios" ><i class="fas fa-users"></i>Usuario</a></li>';
                // echo '<li><a href="tareas" ><i class="fas fa-tasks"></i>Tareas</a></li>';
            }
            if ($_SESSION["usuario"]["perfil"]==3) {
                echo '<li><a href="alistar" >Alistar</a></li>';
            }
            if ($_SESSION["usuario"]["perfil"]==4) {
                echo '<li><a href="PV" >Pventa</a></li>';
            }
            

        ?>
        <li><div class="divider  "></div></li>                
        <li class="">
            <a href="salir" class="waves-effect "><i class="fas fa-sign-out-alt"></i>Salir</a>
        </li>

        

</ul>