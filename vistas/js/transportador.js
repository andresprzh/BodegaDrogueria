$(document).ready(function () {

    $('.collapsible').collapsible();
    // pone items en el input select
    $.ajax({
        url: 'api/transporte/destinos',
        method: 'GET',
        data: { 'usuario': 6 },
        dataType: 'JSON',
        success: function (res) {
            console.log(res);
            if (res['estado']) {

                let destinos = res['contenido'];
                // SE MUESTRAN LAS REQUISICIONES EN EL MENU DE SELECCION
                for (var i in destinos) {

                    $("#destino").append($('<option value="' + destinos[i]["descripcion"] + '">' + destinos[i]["descripcion"] + '</option>'));
                    $("#pedidos").append($(` <li>
                                                <div class="collapsible-header">
                                                    <i class="fas fa-truck collapsible-primary" ></i>${destinos[i]["descripcion"]}
                                                    <button class="collapsible-secondary not-collapse btn green">Entregar</button>
                                                </div>
                                                <div class="collapsible-body"><span>Lorem ipsum dolor sit amet.</span></div>
                                                
                                            </li>`
                    ));
                }

                // INICIA MENU DE SELECCION
                $('select').formSelect();
            }

        }
    });



    $(".not-collapse").on("click", function (e) {

        e.stopPropagation();
    });

});