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
    $.ajax({
        url: "api/alistar/requisiciones",
        method: "GET",
        data: { 'valor': 3 },
        dataType: "JSON",
        success: function (res) {

            // SE MUESTRAN LAS REQUISICIONES EN EL MENU DE SELECCION
            for (var i in res) {

                $("#requeridos").append($('<option value="' + res[i]["no_req"] + '">' + res[i]["no_req"] + '</option>'));

            }

            // INICIA MENU DE SELECCION
            $('select').formSelect();

        }
    });

    /* ============================================================================================================================
                                                EVENTOS   
    ============================================================================================================================*/

    //EVENTO AL CAMBIAR ENTRADA REQUERIDOS
    $(".requeridos").change(function (e) {

        //muestra la tabla y la reinicia
        $("#Cajas").removeClass("hide");


        //espera a que la funcion termine para reiniciar las tablas
        mostrarCajas();

    });

    // evento si se da click en generar documento
    $("#Documento").click(function (e) {
        let numcaja = $('.modal .NumeroCaja').attr('name');

        documento(numcaja);
    });

});

/* ============================================================================================================================
                                                   FUNCIONES   
============================================================================================================================*/

// FUNCION QUE PONE LOS ITEMS  EN LA TABLA
function mostrarCajas() {
    //refresca la tabla, para volver a cargar los datos
    $('#cajas').html("");
    //consigue el numero de requerido
    var requeridos = $(".requeridos").val();
    //id usuario es obtenida de las variables de sesion
    var req = [requeridos, id_usuario];

    return $.ajax({

        url: 'api/cajas/pvcajas',
        method: 'POST',
        data: { 'req': req },
        dataType: 'JSON',
        success: function (res) {
            console.log(res);
            var caja = res['contenido'];

            //si no encuentra la caja muestra en pantalla que no se encontro
            if (res['estado'] == 'error') {
                $('#contenido').addClass('hide');
                $('#refresh').prop('disabled', true);
            }
            //en caso de contrar el item mostrarlo en la tabla
            else {

                $('#contenido').removeClass('hide');
                $('#refresh').prop('disabled', false);
                var caja = res['contenido'];

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

                for (var i in caja) {

                    botonestado = '';

                    // reemplaza varoles nul por ---
                    if (caja[i]['tipocaja'] === null) {
                        caja[i]['tipocaja'] = '---'
                    }


                    $('#cajas').append($(`<li
                                              class="collection-item avatar" 
                                              id="${caja[i]["no_caja"]}"
                                              >
                                                <i
                                                onclick="mostrarItemsCaja(${caja[i]["no_caja"]},${caja[i]["num_caja"]},${caja[i]["estado"]})"  
                                                class="fas fa-box circle modal-trigger ${color[caja[i]["estado"]]}" 
                                                data-target="VerCaja"
                                                >
                                                </i>
                                                <span class="title">Caja No ${caja[i]["num_caja"]}</span>
                                                <p>
                                                Tipo de Caja: ${caja[i]["tipocaja"]}
                                                <br>
                                                ${caja[i]["recibido"]}
                                                </p>
                                                <button 
                                                onclick="documento(${caja[i]["no_caja"]})"
                                                title="GenerarDocumento" 
                                                ${botonestado}
                                                class="btn-floating  secondary-content waves-effect green darken-4 " >
                                                    <i class="fas fa-file-alt"></i>
                                                </button>
                                            </li>`));

                }
                $('#TablaCajas').removeClass('hide');

            }

        }

    });

}

function mostrarItemsCaja(no_caja, num_caja, estado) {

    $(".NumeroCaja").html(num_caja);
    $(".NumeroCaja").attr('name', no_caja);
    if (estado == 4) {
        // $("#Documento").removeAttr("disabled");
        $("#TablaM thead tr").addClass("green darken-3");
        $("#TablaM thead tr").removeClass("red darken-3");
    } else {
        // $("#Documento").attr("disabled", "disabled");
        $("#TablaM thead tr").addClass("red darken-3");
        $("#TablaM thead tr").removeClass("green darken-3");
    }
    mostrarItems(no_caja, estado);
}

function mostrarItems(no_caja, estado = null) {

    //consigue el numero de requerido
    var requeridos = $(".requeridos").val();
    //id usuario es obtenida de las variables de sesion
    var req = [requeridos, id_usuario];
    
    
    return $.ajax({
        url: "api/cajas/pvcajas",
        method: "POST",
        data: { "req": req, "numcaja": no_caja, "estado": estado },
        dataType: "JSON",
        success: function (res) {
            
            $("#TablaM tbody").html("");

            var item = res["contenido"];

            //si no encuentra el item muestra en pantalla que no se encontro
            if (res["estado"] == "error") {

            }
            //en caso de contrar el item mostrarlo en la tabla
            else {

                

                var item = res["contenido"];
                
                if (estado==5) {
                    $('#mensaje').html('Error');
                    for (var i in item) {
                        $("#TablaM tbody").append($(`<tr id="${item[i]['iditem']}"><td>
                            ${item[i]['descripcion']} </td><td>    
                            ${item[i]['iditem']} </td><td>
                            ${item[i]["mensaje"]}</td>
                            </tr>`));
                    }
                }else{
                    $('#mensaje').html('Recibidos');
                    for (var i in item) {
                        $("#TablaM tbody").append($(`<tr id="${item[i]['iditem']}"><td>
                            ${item[i]['descripcion']} </td><td>    
                            ${item[i]['iditem']} </td><td>
                            ${item[i]["recibidos"]}</td>
                            </tr>`));
                    }
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

                
                // numcaja = ('00' + numcaja).slice(-2);
                let no_req=req[0].substr(1,2)+req[0].substr(req[0].length-6);
                // crea el nombre del documento a partir de la requisicion y la caja
                let nomdoc = no_req[0] + '.TR1';

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

