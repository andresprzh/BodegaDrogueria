$(document).ready(function () {

    /* ============================================================================================================================
                                                INICIAN  COMPONENTE DE LA PAGINA
    ============================================================================================================================*/

    //INICIA EL MODAL
    $('.modal').modal({
        onOpenStart: function () {
            // console.log("hola");
        }
    });

    // pone items en el input select
    mostrarRemisiones();

    /* ============================================================================================================================
                                                EVENTOS   
    ============================================================================================================================*/


    // evento si se da click en generar documento
    $("#Documento").click(function (e) {
        let numcaja = $('.modal .NumeroCaja').html();

        documento(numcaja);
    });

});

/* ============================================================================================================================
                                                   FUNCIONES   
============================================================================================================================*/

// FUNCION QUE PONE LOS ITEMS  EN LA TABLA
function mostrarRemisiones() {
    //refresca la tabla, para volver a cargar los datos
    $('#cajas').html("");
    //consigue el numero de requerido
    var requeridos = $(".requeridos").val();
    //id usuario es obtenida de las variables de sesion
    var req = [requeridos, id_usuario];

    return $.ajax({
    url: "api/remisiones/remisiones",
    method: "GET",
    data: { 'valor': 2 },
    dataType: "JSON",
    success: function (res) {
        console.log(res);
        // return 0;
        var remisiones = res;

        
        if (res['estado'] == 'error') {
            $('#contenido').addClass('hide');
            $('#refresh').prop('disabled', true);
        }
        //en caso de contrar el item mostrarlo en la tabla
        else {
            $('#remisiones').html("");
            $('#contenido').removeClass('hide');
            $('#refresh').prop('disabled', false);  
            var remisiones = res;

            let color = {
                0: 'yellow',
                1: 'green',
                2: 'orange',
                3: 'orange',
                4: 'green',
                5: 'red',
                9: 'black'
            };

            let botonestado = '';
            
                for (var i in remisiones) {

                    botonestado = '';
                    // if (remisiones[i]["estado"] != 4) {
                    if (remisiones[i]["estado"]) {
                        botonestado = 'disabled';
                    }

                    let numrem = 'OC'+('00' + remisiones[i]["no_rem"]).slice(-2);
                    $('#remisiones').append($(`<li
                                            class="collection-item avatar" 
                                            id="${remisiones[i]["no_rem"]}"
                                            >
                                            <i
                                            onclick="mostrarItemsRem(${remisiones[i]["no_rem"]},${remisiones[i]["estado"]})"  
                                            class="fas fa-box circle modal-trigger ${color[remisiones[i]["estado"]]}" 
                                            data-target="VerRemisiones"
                                            >
                                            </i>
                                            <span class="title">Remision No ${numrem}</span>
                                            <p>
                                            <br>
                                            ${remisiones[i]["creada"]}
                                            </p>
                                            <button 
                                            onclick="documento(${remisiones[i]["no_rem"]})"
                                            title="GenerarDocumento" 
                                            ${botonestado}
                                            class="btn-floating  secondary-content waves-effect green darken-4 " >
                                                <i class="fas fa-file-alt"></i>
                                            </button>
                                        </li>`));

                }
            // }

        }

    }

    });

}

function mostrarItemsRem(numrem, estado) {
    // return 0;
    
    $(".NumeroRemision").html('OC'+('00' + numrem).slice(-2));

    if (estado == 4) {
        // $("#Documento").removeAttr("disabled");
        $("#TablaR thead tr").addClass("green darken-3");
        $("#TablaR thead tr").removeClass("red darken-3");
    } else {
        // $("#Documento").attr("disabled", "disabled");
        $("#TablaR thead tr").addClass("red darken-3");
        $("#TablaR thead tr").removeClass("green darken-3");
    }
    // mostrarItems(numrem, estado);
}

function mostrarItems(numcaja, estado = null) {

    //consigue el numero de requerido
    var requeridos = $(".requeridos").val();
    //id usuario es obtenida de las variables de sesion
    var req = [requeridos, id_usuario];

    return $.ajax({
        url: "api/cajas/pvcajas",
        method: "POST",
        data: { "req": req, "numcaja": numcaja, "estado": estado },
        dataType: "JSON",
        success: function (res) {


            $("#TablaM tbody").html("");


            var item = res["contenido"];

            //si no encuentra el item muestra en pantalla que no se encontro
            if (res["estado"] == "error") {

            }
            //en caso de contrar el item mostrarlo en la tabla
            else {

                // $("#Rerror").hide();

                var item = res["contenido"];


                for (var i in item) {
                    $("#TablaM tbody").append($(`<tr id="${item[i]['iditem']}"><td>
                        ${item[i]['descripcion']} </td><td>    
                        ${item[i]['iditem']} </td><td>
                        ${item[i]['recibidos']}</td>
                        </tr>`));


                }

            }

        }

    });

}

function documento(numcaja) {

    //consigue el numero de requerido
    var requeridos = $(".requeridos").val();
    //id usuario es obtenida de las variables de sesion
    var req = [requeridos, id_usuario];

    return $.ajax({
        url: 'api/cajas/documento',
        method: 'POST',
        data: { 'req': req, 'numcaja': numcaja, 'recibido': true },
        dataType: 'JSON',
        success: function (res) {
            // console.log(res);
            if (res['estado'] == true) {

                // let numcaja = $('#cajas').val();
                // obtiene los 3 ultimos caracteres de la requisicion
                let no_req = req[0].substr(req[0].length - 3);
                numcaja = ('00' + numcaja).slice(-2);

                // crea el nombre del documento a partir de la requisicion y la caja
                let nomdoc = 'C' + numcaja + 'DE' + no_req + '.TR1';

                let element = document.createElement('a');
                element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(res['string']));
                element.setAttribute('download', nomdoc);

                element.style.display = 'none';
                document.body.appendChild(element);

                element.click();

                document.body.removeChild(element);

            }
        }

    });

}

function documentoAll() {

    //consigue el numero de requerido
    var requeridos = $(".requeridos").val();
    //id usuario es obtenida de las variables de sesion
    var req = [requeridos, id_usuario];
    // Busca los datos en la tabla

    let lista = document.getElementById('cajas');
    let li = lista.getElementsByTagName('li');
    let numcaja = new Array;

    for (let i = 0; i < li.length; i++) {

        numcaja[i] = li[i].id;
    }
    // console.log(numcaja);
    // return 0;

    return $.ajax({
        url: 'api/cajas/documento',
        method: 'POST',
        data: { 'req': req, 'numcaja': numcaja, 'recibido': true },
        dataType: 'JSON',
        success: function (res) {
            // console.log(res);
            if (res['estado'] == true) {

                // let numcaja = $('#cajas').val();
                // obtiene los 3 ultimos caracteres de la requisicion
                // let no_req = req[0].substr(req[0].length - 3);
                // numcaja = ('00' + numcaja).slice(-2);

                // crea el nombre del documento a partir de la requisicion y la caja
                let nomdoc = req[0] + '.TR1';

                let element = document.createElement('a');
                element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(res['string']));
                element.setAttribute('download', nomdoc);

                element.style.display = 'none';
                document.body.appendChild(element);

                element.click();

                document.body.removeChild(element);

            }
        }

    });

}

