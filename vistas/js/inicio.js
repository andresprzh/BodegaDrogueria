$(document).ready(function () {
    
    
   switch (parseInt(usuario["perfil"])) {

        // Para Admins
       case 1:
            $("#contenido-inicio").append($('<h2 class="header center">Admin</h2>'));
            $("#contenido-inicio").append($(`<ul class="collection with-header collapsible expandable" id="listreq">
                        <li class="collection-header"><h4>Requisiciones</h4></li>
                    </ul>`));
            // pone requisiciones en el input select
            $.ajax({
                url: "ajax/alistar.requisicion.ajax.php",
                method: "POST",
                data: {'valor':3},
                dataType: "json",
                success: function (res) {
                    var icon;
                    
                    // SE MUESTRAN LAS reqUISICIONES EN EL MENU DE SELECCION  
                    for (var i in res) {
                        if (res[i]["estado"]!=0) {
                            icon='<i class="fas fa-check-square green-text "></i>';
                        }else{
                            icon='<i class="fas fa-check-square grey-text"></i>';
                        }
                        $("#listreq").append($(`<li class="collection-item avatar white">
                        <div class="collapsible-header">
                            ${icon}
                            <span style="font-size:150%" class="title green-text " >`+res[i]['no_req']+`</span>
                            <span class="secondary-content"><i class="fas fa-caret-down green-text"></i></span>
                        </div>
                        <div class="collapsible-body ">
                            <table class="centered black-text">
                                <thead>
                                    <tr>
                                        <th>Bodega origen:`+(res[i]["lo_origen"])+`</th>
                                        <th>Destino: `+(res[i]["lo_destino"])+`</th>
                                    </tr>
                                    <tr>
                                        <th>Fecha de subida:`+((res[i]["enviado"]==null) ? "---":res[i]["enviado"])+`</th>
                                        <th>Recibido: `+((res[i]["recibido"]==null) ? "---":res[i]["recibido"])+`</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        </li>`));
                    }
  
                    $('.collapsible').collapsible();
  
                    
                    // INICIA MENU DE SELECCION
                    $('select').formSelect();

                }
            });
            break;
        // jefe de bodeg
        case 2:
            $("#contenido-inicio").append($('<h2 class="header center">Admin</h2>'));
            $("#contenido-inicio").append($(`<ul class="collection with-header collapsible expandable" id="listreq">
                        <li class="collection-header"><h4>Requisiciones</h4></li>
                    </ul>`));
            // pone requisiciones en el input select
            $.ajax({
                url: "ajax/alistar.requisicion.ajax.php",
                method: "POST",
                data: {'valor':3},
                dataType: "json",
                success: function (res) {
                    var icon;
                    
                    // SE MUESTRAN LAS reqUISICIONES EN EL MENU DE SELECCION  
                    for (var i in res) {
                        if (res[i]["estado"]!=0) {
                            icon='<i class="fas fa-check-square green-text "></i>';
                        }else{
                            icon='<i class="fas fa-check-square grey-text"></i>';
                        }
                        $("#listreq").append($(`<li class="collection-item avatar white">
                        <div class="collapsible-header">
                            ${icon}
                            <span style="font-size:150%" class="title green-text " >`+res[i]['no_req']+`</span>
                            <span class="secondary-content"><i class="fas fa-caret-down green-text"></i></span>
                        </div>
                        <div class="collapsible-body ">
                            <table class="centered black-text">
                                <thead>
                                    <tr>
                                        <th>Bodega origen:`+(res[i]["lo_origen"])+`</th>
                                        <th>Destino: `+(res[i]["lo_destino"])+`</th>
                                    </tr>
                                    <tr>
                                        <th>Fecha de subida:`+((res[i]["enviado"]==null) ? "---":res[i]["enviado"])+`</th>
                                        <th>Recibido: `+((res[i]["recibido"]==null) ? "---":res[i]["recibido"])+`</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        </li>`));
                    }

                    $('.collapsible').collapsible();

                    
                    // INICIA MENU DE SELECCION
                    $('select').formSelect();

                }
            });
            break;
        case 3:
            $("#contenido-inicio").append($('<h2 class="header center">Alistador</h2>'));
            break;
        case 4:
            $("#contenido-inicio").append($('<h2 class="header center">Encargado punto de venta</h2>'));
            break;
        case 5:
            $("#contenido-inicio").append($('<h2 class="header center">Admin</h2>'));
            $("#contenido-inicio").append($(`<ul class="collection with-header collapsible expandable" id="listreq">
                        <li class="collection-header"><h4>Requisiciones</h4></li>
                    </ul>`));
            // pone requisiciones en el input select
            $.ajax({
                url: "ajax/alistar.requisicion.ajax.php",
                method: "POST",
                data: {'valor':3},
                dataType: "json",
                success: function (res) {
                    var icon;
                    
                    // SE MUESTRAN LAS reqUISICIONES EN EL MENU DE SELECCION  
                    for (var i in res) {
                        if (res[i]["estado"]!=0) {
                            icon='<i class="fas fa-check-square green-text "></i>';
                        }else{
                            icon='<i class="fas fa-check-square grey-text"></i>';
                        }
                        $("#listreq").append($(`<li class="collection-item avatar white">
                        <div class="collapsible-header">
                            ${icon}
                            <span style="font-size:150%" class="title green-text " >`+res[i]['no_req']+`</span>
                            <span class="secondary-content"><i class="fas fa-caret-down green-text"></i></span>
                        </div>
                        <div class="collapsible-body ">
                            <table class="centered black-text">
                                <thead>
                                    <tr>
                                        <th>Bodega origen:`+(res[i]["lo_origen"])+`</th>
                                        <th>Destino: `+(res[i]["lo_destino"])+`</th>
                                    </tr>
                                    <tr>
                                        <th>Fecha de subida:`+((res[i]["enviado"]==null) ? "---":res[i]["enviado"])+`</th>
                                        <th>Recibido: `+((res[i]["recibido"]==null) ? "---":res[i]["recibido"])+`</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        </li>`));
                    }

                    $('.collapsible').collapsible();

                    
                    // INICIA MENU DE SELECCION
                    $('select').formSelect();

                }
            });
            break;
            default:
            break;
   }
});