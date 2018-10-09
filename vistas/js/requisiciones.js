$(document).ready(function () {
    /* ============================================================================================================================
                                                    INICIAN  COMPONENTE DE LA PAGINA
    ============================================================================================================================*/
    $('.collapsible').collapsible();
    $('.modal').modal();

    mostrarReq();

    /* ============================================================================================================================
                                                   EVENTOS   
   ============================================================================================================================*/

   $("#cerrar").click(function (e) { 
       let req=$("#requeridos").html();
       
       swal({
        title: '¿Terminar rquisición?',
        icon: 'warning',
        buttons: ['Cancelar', 'Terminar']
    }).then((Cerrar) => {

        //si se le da click en cerrar procede a pasar los items a la caja y a cerrarla
        if (Cerrar) {
            
            $.ajax({
                url: "api/alistar/terminarreq",
                method: "POST",
                data: { 'req': req },
                dataType: "JSON",
                success: function (res) {
                    
                    if (res) {

                        swal('¡Requisición terminada!', {
                            icon: 'success',
                        }).then((event) => {
                            
                            mostrarReq();
                            $('.modal').modal('close');

                        });

                    } else {

                        swal("¡Error al modificar la requisición!", {
                            icon: "error",
                        });

                    }
        
                }
            });

        }
    });
   });
    

});

/* ============================================================================================================================
                                                FUNCIONES   
============================================================================================================================*/

function mostrarItemsReq(requisicion) {

    //id usuario es obtenida de las variables de sesion
    let req = [requisicion[0], id_usuario];

    

    $("#requeridos").html(requisicion[0]);
    $("#solicitante").html(requisicion[3]);
    $("#tipoinv").html(requisicion[4]);
    $("#destino").html(requisicion[2] + " " + requisicion[1]);
    return $.ajax({

        url: 'api/alistar/items',//url de la funcion
        method: 'GET',
        data: { 'req': req, 'estado': 'all' },
        dataType: 'JSON',
        success: function (res) {


            //si encuentra el item mostrarlo en la tabla
            if (res['estado'] != 'error') {


                let items = res['contenido'];

                $('#TablaM tbody').html('');


                for (let i in items) {

                    // se guarda el id del item en el id de la fila
                    $('#TablaM tbody').append($(`<tr id='V${i}'>
                                            <td>${items[i]['descripcion']}</td>
                                            <td>${items[i]['disponibilidad']}</td>
                                            <td>${items[i]['pendientes']}</td>
                                            <td>${items[i]['ubicacion']}</td>
                                        </tr>`));

                }

                // se carga el menu seleccion de ubicaciones
                let ubicaciones = res['ubicaciones'];
                $('#ubicacion').html('');
                $('#ubicacion').append($(`<option value=''  selected>Ubicacion</option>`));
                for (let i in ubicaciones) {

                    $('#ubicacion').append($(`<option value="${ubicaciones[i]}"> ${ubicaciones[i]}</option>`));

                }
                $('.entradas').removeClass('hide');

            } else {
                //oculta las entradas
                $('.entradas').addClass('hide');
            }

        }

    });

}

function mostrarReq(){

    $('#listreq').html('');

    return $.ajax({
        url: 'api/alistar/requisiciones',
        method: 'GET',
        data: { 'valor': 3 },
        dataType: 'JSON',
        success: function (res) {

            let color = {
                0: 'grey',
                1: 'orange',
                2: 'green'
            };

            // SE MUESTRAN LAS reqUISICIONES EN EL MENU DE SELECCION  
            for (var i in res) {
                const req = '["' + [
                    res[i]['no_req'],
                    res[i]['descripcion'],
                    res[i]['lo_destino'],
                    res[i]['solicitante'],
                    res[i]['tip_inventario'],
                ].join('","') + '"]'

                $("#listreq").append($(`<li class='white '>
                <div class='collapsible-header'>
                
                    <i class='fas fa-check-square ${color[res[i]['estado']]}-text'></i>
                    <span style='font-size:150%' class='title green-text ' >${res[i]['no_req'].substr(4)} ${res[i]['descripcion']}</span>
                    <button
                    class='modal-trigger collapsible-secondary not-collapse btn green'
                    data-target='itemsreq'
                    onclick='mostrarItemsReq(${req})'
                    >Ver</button>
                    
                </div>
                <div class='collapsible-body '>
                    <table class='centered black-text'>
                        <thead>
                            <tr>
                                <th>Bodega origen: ${res[i]['lo_origen']}</th>
                                <th>Destino: ${(res[i]['lo_destino'])} ${(res[i]['descripcion'])}</th>
                            </tr>
                            <tr>
                                <th>Fecha de subida:`+ ((res[i]['creada'] == null) ? '---' : res[i]['creada']) + `</th>
                                <th>Recibido: `+ ((res[i]['recibido'] == null) ? '---' : res[i]['recibido']) + `</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                </li>`));
            }

        }
    });
}
