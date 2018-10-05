$(document).ready(function () {


    switch (parseInt(usuario["perfil"])) {

        // Para Admins
        case 1:
            $("#contenido-inicio").append($('<h2 class="header center">Admin</h2>'));
            tablarequeridos();

            break;
        // jefe de bodeg
        case 2:
            $("#contenido-inicio").append($('<h2 class="header center">Jefe de bodega</h2>'));
            tablarequeridos();
            break;
        case 3:
            $("#contenido-inicio").append($('<h2 class="header center">Alistador</h2>'));
            break;
        case 4:
            $("#contenido-inicio").append($('<h2 class="header center">Encargado punto de venta</h2>'));
            break;
        case 5:
            $("#contenido-inicio").append($('<h2 class="header center">Jefe delegado</h2>'));
            tablarequeridos();
            break;
        default:
            break;
    }
});


function tablarequeridos() {

    $("#contenido-inicio").append($(`<ul class="collection with-header collapsible expandable" id="listreq">
                <li class="collection-header"><h4>Requisiciones</h4></li>
            </ul>`));
    // pone requisiciones en el input select
    $.ajax({
        url: "api/alistar/requisiciones",
        method: "GET",
        data: { 'valor': 3 },
        dataType: "json",
        success: function (res) {

            let color = {
                0: "grey",
                1: "orange",
                2: "green"
            };

            // SE MUESTRAN LAS reqUISICIONES EN EL MENU DE SELECCION  
            for (var i in res) {
                $("#listreq").append($(`<li class="white ">
                <div class="collapsible-header">
                    <i class="fas fa-check-square ${color[res[i]['estado']]}-text"></i>
                    <span style="font-size:150%" class="title green-text " >${res[i]['no_req']}</span>
                </div>
                <div class="collapsible-body ">
                    <table class="centered black-text">
                        <thead>
                            <tr>
                                <th>Bodega origen: ${res[i]["lo_origen"]}</th>
                                <th>Destino: ${(res[i]["lo_destino"])} ${(res[i]["descripcion"])}</th>
                            </tr>
                            <tr>
                                <th>Fecha de subida:`+ ((res[i]["creada"] == null) ? "---" : res[i]["creada"]) + `</th>
                                <th>Recibido: `+ ((res[i]["recibido"] == null) ? "---" : res[i]["recibido"]) + `</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                </li>`));
            }

            $('.collapsible').collapsible({ accordion: false });


            // INICIA MENU DE SELECCION
            $('select').formSelect();

        }
    });
}