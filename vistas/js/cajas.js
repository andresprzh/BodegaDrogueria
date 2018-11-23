$(document).ready(function () {
    /* ============================================================================================================================
                                                    INICIAN  COMPONENTE DE LA PAGINA
    ============================================================================================================================*/

    //INICIA EL MODAL
    $('.modal').modal();

    // INICIAR TABS
    $('.tabs').tabs({
        'swipeable': true
    });

    // INICIA DATATABLE
    table = iniciarTabla('.datatable');

    // pone items en el input select
    $.ajax({
        url: "api/alistar/requisiciones",
        method: "GET",
        data: { 'valor': 3 },
        dataType: "json",
        success: function (res) {

            // SE MUESTRAN LAS REQUISICIONES EN EL MENU DE SELECCION
            for (var i in res) {

                $("#requeridos").append($(`<option 
                                            value='${res[i]['no_req']}'
                                            name='${res[i]['descripcion']}'>` +
                    res[i]['no_req'].substr(4) + res[i]['descripcion'] +
                    '</option>'));

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
        let requeridos = $(this).val();
        let destino = $(this).find('option:selected').attr("name");
        $("#destino").html(requeridos.substr(0, 4) + "VE " + destino);
        //muestra la tabla y la reinicia
        $("#Cajas").removeClass("hide");

        // desabilita los botones de generar documento
        $(".Documento").attr("disabled", "disabled");
        //espera a que la funcion termine para reiniciar las tablas
        $.when(mostrarCajas()).done(function () {

            table[0] = iniciarTabla('#TablaC');
            table[1] = iniciarTabla('#TablaCE');
            // table = iniciarTabla('.datatable');

        });

    });

    // EVENTO SI SE DA CLICK EN EL BOTON DE GENERAR DOCUMENTO
    $('.Documento').click(function (e) {
        // consigue la id de la tabla seleccionada
        const tabla = $($(this).parent().parent().find('table')[0]).attr('id');
        
        //consigue el numero de requerido
        var requeridos = $('.requeridos').val();
        //id usuario es obtenida de las variables de sesion
        var req = [requeridos, id_usuario];


        
        var datos = $(`#${tabla}`).DataTable();
        
        // obtiene los numeros de las cajas de la tabla seleccionada
        var numcaja = new Array();
        for (var i in datos.data().toArray()) {
            numcaja[i] = datos.row(i).id();
            
        }
        
        
        $.ajax({

            url: 'api/cajas/documento',
            method: 'GET',
            data: { 'req': req, 'numcaja': numcaja },
            dataType: 'JSON',
            success: function (res) {
                // console.log(res);
                // return 0;
                // si hay un error al buscar los archivos no genera el documento
                if (!res) {
                    swal({
                        title: '!Error al generar el documento¡',
                        type: 'error',
                    });

                    // si no hay error genera le documento y lo manda a decargar
                } else {
                    // // OBTIENE LOS 3 ULTIMOS CARACTERES DE LA REQUISICION
                    // var no_res = req[0].substr(req[0].length - 3);
                    // let numerodoc = ('00' + res['no_documento']).slice(-2);
                    // // CREA EL NOMBRE DEL DOCUMENTO A PARTIR DE LA REQUISICION Y LA CAJA
                    // var nomdoc = 'DS' + no_res + 'D' + numerodoc + '.TR1';
                    var nomdoc = req[0] + '.TR' + res['no_documento'];

                    var element = document.createElement('a');
                    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(res['documento']));
                    element.setAttribute('download', nomdoc);

                    element.style.display = 'none';
                    document.body.appendChild(element);

                    element.click();

                    document.body.removeChild(element);

                    $('.modal').modal('close');
                    recargarCajas();

                }
            }
        });

    });

    // EVENTO SI SE PRESIONA EL BOTON MODIFICAR
    $('#modificar').on('click', function (e) {

        //consigue el numero de requerido
        let requeridos = $('.requeridos').val();
        //id usuario es obtenida de las variables de sesion
        let req = [requeridos, id_usuario];

        //si se presiona aceptar se continua con el proceso

        swal({
            title: '¿Modificar caja?',
            type: 'warning',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Modificar',
            confirmButtonClass: 'green darken-3',
        })
            .then(function (result) {

                //si se le da click en cerrar procede a pasar los items a la caja y a cerrarla
                if (result.value) {

                    // Busca los datos en la tabla
                    let table = document.getElementById('tablaerror');
                    let tr = table.getElementsByTagName('tr');
                    let items = new Array;

                    for (let i = 0; i < tr.length; i++) {

                        items[i] = {
                            'iditem': tr[i].id,
                            'alistados': $(tr[i]).find('input').val(),
                            'estado': $(tr[i]).attr('name'),
                            'cajae': $(tr[i]).find(':nth-child(2)').text().replace(/(^\s+|\s+$)/g, ''),
                            'cajar': $(tr[i]).find(':nth-child(3)').text().replace(/(^\s+|\s+$)/g, '')
                        };
                    }

                    //guarda el tipo de caja en una variable
                    var numcaja = $('.NumeroCaja').attr('name');


                    $.ajax({
                        url: 'api/cajas/modificar',//url de la funcion
                        method: 'post',//metodo post para mandar datos
                        data: { 'req': req, 'numcaja': numcaja, 'items': items },//datos que se enviaran          
                        dataType: 'JSON',
                        success: function (res) {
                            
                            if (res) {

                                swal({
                                    title: '¡Caja Modificada exitosamente!',
                                    type: 'success',
                                })
                                    .then(function (result) {

                                        $('.modal').modal('close');
                                        recargarCajas();

                                    });

                            } else {

                                swal({
                                    title: '¡Error al modificar la caja!',
                                    type: 'error',
                                });

                            }

                        }
                    });
                }
            });

    });

    // ELIMINA LA CAJA SI HAY SELECCIONADA
    $('#eliminar').click(function (e) {
        //consigue el numero de requerido
        let requeridos = $('.requeridos').val();
        //id usuario es obtenida de las variables de sesion
        let req = [requeridos, id_usuario];
        // se consigue el numero de la caja
        let caja = $('.NumeroCaja').attr('name');
        
        swal({
            title: `¿Esta seguro de eliminr la caja ${caja}?`,
            type: 'warning',
            showCancelButton: true,
            cancelButtonText: 'No',
            confirmButtonText: 'Si',
            confirmButtonClass: 'red darken-3',

        })
            .then((result) => {

                if (result.value) {

                    $.ajax({
                        type: 'POST',
                        url: 'api/cajas/eliminar',
                        data: { 'numcaja': caja, 'req': req },
                        dataType: 'JSON',
                        success: function (res) {

                            if (res) {
                                swal({
                                    title: `Caja ${caja} eliminada`,
                                    type: 'success',
                                })
                                    .then(() => {
                                        $('.modal').modal('close');
                                        recargarCajas();
                                    });
                            } else {
                                swal({
                                    title: `No se pudo eliminar la caja ${caja} `,
                                    type: 'error',
                                })
                            }
                        }
                    });

                }

            });
    });

    // ASIGNA LAS CAJAS A UN TRANSPORTADOR PARA SER ENVIADAS AL PUNTO
    $('#despachar').click(function (e) {

        var datos = $("#TablaC").DataTable();
        
        // obtiene los numeros de las cajas de la tabla seleccionada
        var numcaja = new Array();
        for (var i in datos.data().toArray()) {
            numcaja[i] = datos.row(i).id();
            
        }


        let resultado;
        ajax('api/cajas/conductor', 'GET').done(function (res) {

            if (res) {
                if (res['estado']) {

                    let opciones = res['contenido'];

                    swal({
                        title: 'Seleccionar Transportador',
                        input: 'select',
                        inputOptions: opciones,
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar',
                        confirmButtonText: 'Asignar',
                        confirmButtonClass: 'green darken-3',
                        inputValidator: function (value) {
                            return new Promise(function (resolve, reject) {
                                if (value !== '') {
                                    resolve();
                                } else {
                                    reject('Por favor seleccione un tranportador');
                                }
                            });
                        }
                    }).then(function (result) {
                        if (result.value) {
                            transportador = result.value;
                            $.ajax({
                                url: 'api/cajas/despachar',
                                method: "POST",
                                data: { 'cajas': numcaja, 'transportador': transportador },
                                dataType: 'JSON',
                                success: function (res) {
                                    if (res) {
                                        swal({
                                            type: 'success',
                                            html: 'Cajas asignadas  para despachar'
                                        }).then(function (res) {
                                            recargarCajas();

                                        });

                                    } else {
                                        swal({
                                            type: 'error',
                                            html: 'No se puedo asignar cajas para despachar'
                                        });
                                    }
                                }
                            });

                        }
                    });

                }
            }
        })

        return 0;

    });

    // IMPRIMI LISTA DE ITEMS PARA PEGAR EN LA CAJA Al ENVIAR
    $('#imprimir').click(function (e) {

        //consigue el numero de requerido
        let requeridos = $(".requeridos").val();
        //id usuario es obtenida de las variables de sesion
        let req = [requeridos, id_usuario];

        // numero de la caja
        let numcaja = $(".NumeroCaja").html();

        return $.ajax({
            type: 'GET',
            url: 'api/alistar/listadoc',
            data: { 'numcaja': numcaja, 'req': req },
            dataType: 'JSON',
            success: function (res) {

                // si hay un error al buscar los archivos no genera el documento
                if (!res) {
                    swal({
                        title: '!Error al generar el documento¡',
                        type: 'error',
                    });

                    // si no hay error genera le documento y lo manda a decargar
                } else {

                    var nomdoc = numcaja + '.txt';
                    var element = document.createElement('a');
                    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(res['contenido']));
                    element.setAttribute('download', nomdoc);

                    element.style.display = 'none';
                    document.body.appendChild(element);

                    element.click();

                    document.body.removeChild(element);

                    $('.modal').modal('close');
                    recargarCajas();

                }
            },
            error: function (res) {
                if (!res) {
                    swal({
                        title: '!Error al generar el documento¡',
                        type: 'error',
                    });

                    // si no hay error genera le documento y lo manda a decargar
                } else {

                    var nomdoc = numcaja + '.txt';
                    var element = document.createElement('a');
                    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(res['contenido']));
                    element.setAttribute('download', nomdoc);

                    element.style.display = 'none';
                    document.body.appendChild(element);

                    element.click();

                    document.body.removeChild(element);

                    $('.modal').modal('close');
                    recargarCajas();

                }
            }
        });

    });

    // EVENTO SI SE PRESIONA 1 TECLA EN LA TABLA EDITABLE (EVITA ENVIAR FORMULARIO CON ENTER)
    $('#tablamodal').on('keydown', 'input', function (e) {
        //previene enviar formulario si se presiona enter
        if (e.which == 13) {
            e.preventDefault();
        }
    });

    // EVENTO PARA CERRAR  1 CAJA
    $('#formmodal').submit(function (e) {
        e.preventDefault();

        //consigue el numero de requerido
        let requeridos = $('.requeridos').val();
        //id usuario es obtenida de las variables de sesion
        let req = [requeridos, id_usuario];


        //si se presiona aceptar se continua con el proceso
        swal({
            title: '¿Cerrar caja?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4caf50',
            confirmButtonText: 'Cerrar',
            cancelButtonText: 'Cancelar'
        }).then((res) => {

            //si se le da click en cerrar procede a pasar los items a la caja y a cerrarla
            if (res.value) {

                // Busca los datos en la tabla
                let table = document.getElementById('tablamodal');
                let tr = table.getElementsByTagName('tr');
                let items = new Array;

                for (let i = 0; i < tr.length; i++) {

                    items[i] = {
                        'iditem': tr[i].id.substr(1),
                        'alistados': $(tr[i]).find('input').val(),
                    };
                }

                //guarda el datos de la caja

                let caja = new Array;
                caja = {
                    'no_caja': $('.NumeroCaja').attr('name'),
                    'tipocaja': $('#caja').val(),
                    'pesocaja': $('#peso').val(),
                }


                $.ajax({
                    url: 'api/cajas/cerrar',//url de la funcion
                    method: 'post',//metodo post para mandar datos
                    data: { 'req': req, 'caja': caja, 'items': items },//datos que se enviaran 
                    dataType: 'JSON',
                    success: function (res) {
                        

                        if (res) {

                            swal({
                                title: '¡Caja cerrada exitosamente!',
                                type: 'success',
                            }).then((res) => {

                                $('.modal').modal('close');
                                recargarCajas();
                            });

                        } else {

                            swal({
                                title: "¡Error al cerrar la caja!",
                                type: "error",
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

// FUNCION QUE PONE LOS ITEMS  EN LA TABLA
function mostrarCajas() {

    // borra y limpia tabla de cajas alistadas
    $('#TablaC tbody').html('');
    $("#TablaC").DataTable().clear();
    $("#TablaC").DataTable().destroy();

    // borra y limpia tabla de cajas enviadas
    $('#TablaCR tbody').html('');
    $("#TablaCE").DataTable().clear();
    $("#TablaCE").DataTable().destroy();

    //consigue el numero de requerido
    let requeridos = $(".requeridos").val();
    //id usuario es obtenida de las variables de sesion
    let req = [requeridos, id_usuario];

    return $.ajax({

        url: "api/cajas/cajas",
        method: "POST",
        data: { "req": req },
        dataType: "JSON",
        success: function (res) {
            
            let estado_despacho = false;
            let caja = res["contenido"];

            //si no encuentra la caja muestra en pantalla que no se encontro
            if (res["estado"] == "error") {
                $("#refresh").prop("disabled", true);
            }
            //en caso de contrar el item mostrarlo en la tabla
            else {
                $("#refresh").prop("disabled", false);
                caja = res["contenido"];
                let color = {
                    0: "yellow",
                    1: "green",
                    2: "orange",
                    3: "green",
                    4: "green",
                    5: "red",
                    9: "black"
                };
                let logo = {
                    0: "box-open",
                    1: "box",
                    2: "truck",
                    3: "calendar-check",
                    4: "check-double",
                    5: "check-double",
                    9: "ban"
                };
                // modal a abirl al precionar boton de caja
                let modal_target = "EditarCaja";
                let tablatarget;

                // si solo hay 1 resultado no hace el ciclo for
                if (caja[0] === undefined) {
                    // reemplaza varoles nul por ---
                    if (caja["tipocaja"] === null) {
                        caja["tipocaja"] = "---"
                    }
                    if (caja["abrir"] === null) {
                        caja["abrir"] = "---"
                    }
                    if (caja["cerrar"] === null) {
                        caja["cerrar"] = "---"
                    }

                    if (caja["estado"] < 2) {
                        estado_despacho = true;
                        tablatarget = "#TablaC";

                        if (caja["estado"] != 1) {
                            estado_despacho = false;
                        }
                        modal_target = "EditarCaja";
                    } else {
                        tablatarget = "#TablaCE";

                        // si la caja ya fue enviada y presenta errores da la opcion de corregirla
                        if (caja["estado"] == '5') {
                            modal_target = "EditarCaja2";
                        } else {
                            modal_target = "EditarCaja";
                        }
                    }

                    $(tablatarget + " tbody").append($(`<tr id="${caja["no_caja"]}">
                                        <td class="numcaja">${caja["num_caja"]}</td>
                                        <td class="alistadores">${caja["alistador"]}</td>
                                        <td class="tipocajas">${caja["tipocaja"]}</td>
                                        <td>${caja["abrir"]}</td>
                                        <td class="cierres">${caja["cerrar"]}</td><td>
                                        <button  
                                        onclick="mostrarItemsCaja(0,${caja["estado"]},'${tablatarget}')"  
                                        title="Revisar"  
                                        data-target="${modal_target}"
                                        class="btn modal-trigger waves-effect waves-light ${color[caja["estado"]]}  darken-3">
                                            <i class="fas fa-${logo[caja["estado"]]}"></i>
                                        </button></td>+
                                        </tr>`));
                } else {
                    // cuenta ls posiciones en las tablas c y ce
                    let cont_tablac = 0;
                    let cont_tablace = 0;
                    let cont;
                    for (var i in caja) {


                        // reemplaza varoles nul por ---
                        if (caja[i]["tipocaja"] === null) {
                            caja[i]["tipocaja"] = "---"
                        }
                        if (caja[i]["abrir"] === null) {
                            caja[i]["abrir"] = "---"
                        }
                        if (caja[i]["cerrar"] === null) {
                            caja[i]["cerrar"] = "---"
                        }



                        if (caja[i]["estado"] < 2) {

                            estado_despacho = true;

                            tablatarget = "#TablaC";

                            cont = cont_tablac;
                            cont_tablac++;

                            if (caja[i]["estado"] != 1) {
                                estado_despacho = false;
                            }
                            modal_target = "EditarCaja";
                        } else {


                            tablatarget = "#TablaCE";
                            // si hay items en la tabla de enviados se activa el boton de generar documento para esta tabla
                            $("#Documento2").removeAttr("disabled", "disabled");

                            cont = cont_tablace;
                            cont_tablace++;

                            // si la caja ya fue enviada y presenta errores da la opcion de corregirla
                            if (caja[i]["estado"] == 5) {
                                modal_target = "EditarCaja2";
                            } else {
                                modal_target = "EditarCaja";
                            }
                        }
                        $(tablatarget + ' tbody').append($(`<tr id='${caja[i]["no_caja"]}'>
                                            <td class="numcaja">${caja[i]["num_caja"]}</td>
                                            <td class="alistadores">${caja[i]["alistador"]}</td>
                                            <td class="tipocajas">${caja[i]["tipocaja"]}</td>
                                            <td>${caja[i]["abrir"]}</td>
                                            <td class="cierres">${caja[i]["cerrar"]}</td><td>
                                            <button  
                                            onclick="mostrarItemsCaja(${cont},${caja[i]["estado"]},'${tablatarget}')"  
                                            title="Revisar"  
                                            data-target="${modal_target}" 
                                            class="btn modal-trigger waves-effect waves-light ${color[caja[i]["estado"]]} darken-3">
                                                <i class="fas fa-${logo[caja[i]["estado"]]}"></i>
                                            </button></td>
                                            </tr>`));




                    }

                    // si todas las cajas estan en estado 1(cerradas), se activa boton de despachar pedido


                }

                if (estado_despacho) {

                    $("#despachar").removeAttr("disabled", "disabled");
                    $("#Documento").removeAttr("disabled", "disabled");
                } else {
                    $("#despachar").attr("disabled", "disabled");
                    $("#Documento").attr("disabled", "disabled");
                }

                // $("#TablaCajas").removeClass("hide");


            }

        }

    });

}

// RECARGA LOS DATOS DE  LAS TABLAS 
function recargarCajas() {
    //espera a que la funcion termine para reiniciar las tablas
    $.when(mostrarCajas()).done(function () {

        table[0] = iniciarTabla('#TablaC');
        table[1] = iniciarTabla('#TablaCE');

    });
}
//FUNCION SI SE DA CLICK EN BOTON DOCUMENTO(MUESTRA ITEMS DE 1 CAJA ESPECIFICA)
function mostrarItemsCaja(e, estado, nombre_tabla) {


    let tabla = $(nombre_tabla).DataTable();

    //obtienen los datos de la caja para pasarlo al modal
    var datos = tabla.row(e).data();

    var id_caja = tabla.row(e).id();
    var numcaja = datos[0];
    var alistador = datos[1];
    var tipocaja = datos[2];
    var cierre = datos[4];

    // se muestran los datos generales de la caja
    $(".NumeroCaja").html(numcaja);
    $(".NumeroCaja").attr("name", id_caja);
    $("#alistador").html(alistador);
    $("#tipocaja").html(tipocaja);
    $("#cierre").html(cierre);

    // solo se permite crear doumento cajas recibidas sin errores
    // solo se pueden eliminar cajas que no se han recibido en el pv

    switch (estado) {

        case 0:
            $("#eliminar").removeAttr("disabled");
            $("#imprimir").attr("disabled", "disabled");
            $("#inputcerrar").removeClass("hide");
            break;
        case 1:
            $("#imprimir").removeAttr("disabled");
            $("#eliminar").removeAttr("disabled");
            $("#inputcerrar").addClass("hide");
            break;
        case 2:
            $("#imprimit").removeAttr("disabled");
            $("#eliminar").attr("disabled", "disabled");
            $("#inputcerrar").addClass("hide");
            break;
        case 3:
            $("#imprimit").attr("disabled", "disabled");
            $("#eliminar").attr("disabled", "disabled");
            $("#inputcerrar").addClass("hide");
            break;
        case 4:
            $("#imprimir").attr("disabled", "disabled");
            $("#eliminar").attr("disabled", "disabled");
            $("#inputcerrar").addClass("hide");
            break;

        default:
            break;
    }

    // destruye la datatable (tabla del modal)
    $('#TablaM tbody').html('');
    $('#TablaEr tbody').html('');


    //espera a que la funcion termine para reiniciar las tablas
    $.when(mostrarItems(id_caja, estado)).done(function () {
        //Reinicia Tabla
        // table[2] = iniciarTabla("#TablaM");
    });
}

// FUNCION QUE PONE LOS ITEMS  EN LA TABLA
function mostrarItems(numcaja, estado = null) {

    //consigue el numero de requerido
    var requeridos = $(".requeridos").val();
    //id usuario es obtenida de las variables de sesion
    var req = [requeridos, id_usuario];
    console.log(estado);
    return $.ajax({
        url: "api/cajas/cajas",
        method: "POST",
        data: { "req": req, "numcaja": numcaja, "estado": estado },
        dataType: "JSON",
        success: function (res) {

            origen = res["contenido"][0]["origen"];
            destino = res["contenido"][0]["destino"];

            $("#origen").html(origen);
            $("#destino").html(destino);

            var item = res["contenido"];

            //si no encuentra el item muestra en pantalla que no se encontro
            if (res["estado"] == "error") {

            }
            //en caso de econtrar el item mostrarlo en la tabla
            else {

                var item = res["contenido"];

                // muestra items con errores o eventualidades
                switch (estado) {

                    case 0:
                        for (var i in item) {
                            // consigue el valor maximo en decenas que puede valer la cantidad de alistados
                            // EJ: entre 0 y 10 maximo valor=100, entre 11 y 100 maximo valor 1000
                            let a = 0;
                            let pot = item[i]['pedido'];
                            do {
                                a++;
                                pot = pot / 10;
                            } while (pot > 1);
                            maxvalue = Math.pow(10, a);

                            $("#tablamodal").append($(`<tr id='M${item[i]['iditem']}'><td>
                                ${item[i]['codigo']}</td><td>
                                ${item[i]['iditem']}</td><td>
                                ${item[i]['referencia']}</td><td>
                                ${item[i]['descripcion']}</td><td>
                                ${item[i]['disp']}</td><td>
                                ${item[i]['pedido']}</td><td>
                                <input type= 'number' min='1' class='alistados ' min=1 max=${maxvalue * 10} required value='${item[i]['alistado']}'></td><td>
                                ${item[i]['ubicacion']}</td></tr>`
                            ));
                        }

                        break;

                    case 5:
                        for (var i in item) {
                            if (item[i]['no_caja'] == 0) {
                                item[i]['no_caja'] = '---';
                            }
                            // guarda el id del item en el id de la fila y el estaod en el nombre de la fila
                            $("#tablaerror").append($(`<tr id='${item[i]['iditem']}' name='${item[i]['estado']}'><td> 
                            ${item[i]['descripcion']}</td><td> 
                            ${item[i]['no_caja']}</td><td> 
                            ${item[i]['no_cajaR']}</td><td> 
                            <input type= 'number' min='1' class='alistados eliminaritem' value='${item[i]['alistados']}'></td><td> 
                            ${item[i]['recibidos']}</td><td '> 
                            ${item[i]['problema']}</td>
                            </tr>`
                            ));

                        }
                        break;

                    default:
                        for (var i in item) {
                            $("#tablamodal").append($(`<tr id='M${item[i]['iditem']}'><td>  
                                ${item[i]['codigo']}</td><td>
                                ${item[i]['iditem']}</td><td>
                                ${item[i]['referencia']}</td><td>
                                ${item[i]['descripcion']}</td><td>
                                ${item[i]['disp']}</td><td>
                                ${item[i]['pedido']}</td><td>
                                ${item[i]['alistado']}</td><td>
                                ${item[i]['ubicacion']}</td></tr>`
                            ));
                        }
                        break;
                }
            }

        }

    });

}

// FUNCION QUE INICIA DATATABLE
function iniciarTabla(tab) {
    let hight = "600px";
    if (tab === "#TablaM") {
        hight = "350px";
    }

    tabla = $(tab).DataTable({

        responsive: true,

        "bLengthChange": false,
        "bFilter": true,
        "sDom": '<"top">t<"bottom"irp><"clear">',

        "language": {
            "sProcessing": "Procesando...",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "_TOTAL_ Items",
            "sInfoEmpty": "",
            "sInfoFiltered": "(filtrado _MAX_ registros)",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
        },
        // scrollY: hight,
        scrollCollapse: true,
        paging: false,


    });

    return tabla;

}

function ajax(url, method) {

    return $.ajax({
        url: 'api/cajas/conductor',
        method: 'GET',
        dataType: 'JSON'

    });
}