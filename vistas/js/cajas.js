$(document).ready(function () {
    /* ============================================================================================================================
                                                    INICIAN  COMPONENTE DE LA PAGINA
    ============================================================================================================================*/

    //INICIA EL MODAL
    $('.modal').modal();

    // INICIA DATATABLE
    table = iniciarTabla('#TablaC');

    // pone items en el input select
    $.ajax({
        url: "api/alistar/requisiciones",
        method: "GET",
        data: { 'valor': 3 },
        dataType: "json",
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
        $.when(mostrarCajas()).done(function () {

            table = iniciarTabla('#TablaC');

        });

    });

    // EVENTO CUANDO SE MODIFICA UNA CELDA DE LA TABLA
    $('#tablamodal').on('change', 'td', function () {

        var tabla = $('#TablaM').DataTable();

        var mensaje = $(this).find("input").val();
        var fila = table.row(this);

        // si la tabla es responsive
        if (fila.data() == undefined) {

            var fila = $(this).parents('tr');
            if (fila.hasClass('child')) {
                fila = fila.prev();
            }
            tabla.row(fila).cell(fila, 8).data('<input  type="text" placeholder="texto de maximo 20 caracteres" class="mensajes validate" maxlength="20" value="' + mensaje + '">').draw()

        } else {

            table.cell(this).data('<input  type="text" placeholder="texto de maximo 20 caracteres" class="mensajes validate" maxlength="20" value="' + mensaje + '">').draw()
        }


    });

    // EVENTO SI SE DA CLICK EN EL BOTON DE GENERAR DOCUMENTO
    $('#Documento').click(function (e) {
        //consigue el numero de requerido
        var requeridos = $('.requeridos').val();
        //id usuario es obtenida de las variables de sesion
        var req = [requeridos, id_usuario];

        var numcaja = $('.NumeroCaja').html();

        var datos = $('#TablaM').DataTable().data().toArray();

        var items = new Array();

        for (var i in datos) {
            items[i] = {
                'id': datos[i][1],
                'alistados': datos[i][6],
                'mensajes': $(datos[i][8]).val(),
                'origen': $('#origen').html(),
                'destino': $('#destino').html()
            }
        }


        $.ajax({

            url: 'api/cajas/documento',
            method: 'POST',
            data: { 'req': req, 'items': items, 'numcaja': numcaja },
            dataType: 'JSON',
            success: function (res) {

                // var numcaja = $('#NumeroCaja').html();
                // obtiene los 3 ultimos caracteres de la requisicion
                var no_res = req[0].substr(req[0].length - 3);
                numcaja = ('00' + numcaja).slice(-2);
                // crea el nombre del documento a partir de la requisicion y la caja
                var nomdoc = 'C' + numcaja + 'DS' + no_res + '.TR1';
                // si hay un error al buscar los archivos no genera el documento
                if (!res) {
                    swal({
                        title: '!Error al generar el documento¡',
                        type: 'error',
                    });

                    // si no hay error genera le documento y lo manda a decargar
                } else {

                    var element = document.createElement('a');
                    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(res));
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
                        };
                    }


                    //guarda el tipo de caja en una variable
                    var numcaja = $('.NumeroCaja').html();


                    $.ajax({
                        url: 'api/cajas/modificar',//url de la funcion
                        method: 'post',//metodo post para mandar datos
                        data: { 'req': req, 'numcaja': numcaja, 'items': items },//datos que se enviaran          
                        dataType: 'JSON',
                        success: function (res) {
                            
                            if (res) {

                                swal({
                                    title:'¡Caja Modificada exitosamente!',
                                    type: 'success',
                                })
                                    .then(function (result) {

                                        $('.modal').modal('close');
                                        recargarCajas();

                                    });

                            } else {

                                swal({
                                    title:'¡Error al modificar la caja!',
                                    type: 'error',
                                });

                            }

                        }
                    });
                }
            });

    });

    $('#eliminar').click(function (e) {
        //consigue el numero de requerido
        let requeridos = $('.requeridos').val();
        //id usuario es obtenida de las variables de sesion
        let req = [requeridos, id_usuario];
        // se consigue el numero de la caja
        let caja = $('.NumeroCaja').html();

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
                                    title: `Caja ${caja} cancelada`,
                                    type: 'success',
                                })
                                    .then(() => {
                                        $('.modal').modal('close');
                                        recargarCajas();
                                    });
                            } else {
                                swal({
                                    title: `No se pudo cancelar la caja ${caja} `,
                                    type: 'error',
                                })
                            }
                        }
                    });

                }

            });
    });

    $('#despachar').click(function (e) {

        var datos = $("#TablaC").DataTable().data().toArray();


        var cajas = new Array();
        for (var i in datos) {

            cajas[i] = datos[i][0];

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
                                    reject('You need to select a Tier');
                                }
                            });
                        }
                    }).then(function (result) {
                        if (result.value) {
                            transportador = result.value;
                            // ajax('api/cajas/despachar', 'POST').done(function (res) {
                            //     console.log(res);
                            // });
                            $.ajax({
                                url: 'api/cajas/despachar',
                                method: "POST",
                                data: { 'cajas': cajas, 'transportador': transportador },
                                // dataType: 'JSON',
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


        // console.log(resultado);
        return 0;

    });

});

/* ============================================================================================================================
                                                   FUNCIONES   
============================================================================================================================*/

// FUNCION QUE PONE LOS ITEMS  EN LA TABLA
function mostrarCajas() {
    //refresca la tabla, para volver a cargar los datos
    var dt = $.fn.dataTable.tables();
    $('#tablacajas').html("");
    $(dt).DataTable().clear();
    $(dt).DataTable().destroy();
    //consigue el numero de requerido
    var requeridos = $(".requeridos").val();
    //id usuario es obtenida de las variables de sesion
    var req = [requeridos, id_usuario];

    return $.ajax({

        url: "api/cajas/cajas",
        method: "POST",
        data: { "req": req },
        dataType: "JSON",
        success: function (res) {
            let estado_despacho = true;
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
                    // si la caja ya fue enviada da la opcion de corregirla
                    if (caja["estado"] == '5') {
                        modal_target = "EditarCaja2";
                    }

                    $("#tablacajas").append($(`<tr>
                                        <td class='numcaja'>${caja["no_caja"]}</td>
                                        <td class="alistadores">${caja["alistador"]}</td>
                                        <td class="tipocajas">${caja["tipocaja"]}</td>
                                        <td>${caja["abrir"]}</td>
                                        <td class="cierres">${caja["cerrar"]}</td><td>
                                        <button  
                                        onclick="mostrarItemsCaja(0,${caja["estado"]})"  
                                        title="Revisar"  
                                        data-target="${modal_target}"
                                        class="btn modal-trigger waves-effect waves-light ${color[caja["estado"]]}  darken-3">
                                            <i class="fas fa-${logo[caja["estado"]]}"></i>
                                        </button></td>+
                                        </tr>`));
                } else {

                    for (var i in caja) {
                        // console.log([0,2,3,5].includes(parseInt(caja[i]["estado"])));
                        if ([0,2,3,5].includes(parseInt(caja[i]["estado"]))){
                        // if (caja[i]["estado"] ==0 || caja[i]["estado"]==2 || caja[i]["estado"]==3 || caja[i]["estado"]==5) {
                            
                            estado_despacho = false;
                            
                            
                        }
                        
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
                        // si la caja ya fue enviada da la opcion de corregirla
                        if (caja[i]["estado"] == '5') {
                            modal_target = "EditarCaja2";
                        } else {
                            modal_target = "EditarCaja";
                        }
                        $("#tablacajas").append($(`<tr>
                                            <td class="numcaja">${caja[i]["no_caja"]}</td>
                                            <td class="alistadores">${caja[i]["alistador"]}</td>
                                            <td class="tipocajas">${caja[i]["tipocaja"]}</td>
                                            <td>${caja[i]["abrir"]}</td>
                                            <td class="cierres">${caja[i]["cerrar"]}</td><td>
                                            <button  
                                            onclick="mostrarItemsCaja(${i},${caja[i]["estado"]})"  
                                            title="Revisar"  
                                            data-target="${modal_target}" 
                                            class="btn modal-trigger waves-effect waves-light ${color[caja[i]["estado"]]} darken-3">
                                                <i class="fas fa-${logo[caja[i]["estado"]]}"></i>
                                            </button></td>
                                            </tr>`));



                    }

                    // si todas las cajas estan en estado 1(cerradas), se activa boton de despachar pedido

                    if (estado_despacho) {

                        $("#despachar").removeAttr("disabled", "disabled");
                    } else {

                        $("#despachar").attr("disabled", "disabled");
                    }
                }

                $("#TablaCajas").removeClass("hide");


            }

        }

    });

}

function recargarCajas() {
    //espera a que la funcion termine para reiniciar las tablas
    $.when(mostrarCajas()).done(function () {

        table = iniciarTabla("#TablaC");

    });
}
//FUNCION SI SE DA CLICK EN BOTON DOCUMENTO(MUESTRA ITEMS DE 1 CAJA ESPECIFICA)
function mostrarItemsCaja(e, estado) {

    //obtienen los datos de la caja para pasarlo al modal
    var datos = table.row(e).data();
    var numcaja = datos[0];
    var alistador = datos[1];
    var tipocaja = datos[2];
    var cierre = datos[4];

    // se muestran los datos generales de la caja
    $(".NumeroCaja").html(numcaja);
    $("#alistador").html(alistador);
    $("#tipocaja").html(tipocaja);
    $("#cierre").html(cierre);

    // solo se permite crear doumento cajas recibidas sin errores
    // solo se pueden eliminar cajas que no se han recibido en el pv
    if (estado == 4) {
        $("#Documento").removeAttr("disabled");
    } else {
        $("#Documento").attr("disabled", "disabled");
        if ([0, 1].includes(estado)) {
            $("#eliminar").removeAttr("disabled", "disabled");
        } else {
            $("#eliminar").attr("disabled", "disabled");
        }
    }

    // destruye la datatable 2(tabla del modal)
    var dt = $.fn.dataTable.tables()[1];
    $("#tablamodal").html("");
    $(dt).DataTable().clear();
    $(dt).DataTable().destroy();


    //espera a que la funcion termine para reiniciar las tablas
    $.when(mostrarItems(numcaja, estado)).done(function () {
        //Reinicia Tabla
        table[1] = iniciarTabla("#TablaM");
    });
}

// FUNCION QUE PONE LOS ITEMS  EN LA TABLA
function mostrarItems(numcaja, estado = null) {

    //consigue el numero de requerido
    var requeridos = $(".requeridos").val();
    //id usuario es obtenida de las variables de sesion
    var req = [requeridos, id_usuario];

    return $.ajax({
        url: "api/cajas/cajas",
        method: "POST",
        data: { "req": req, "numcaja": numcaja, "estado": estado },
        dataType: "JSON",
        success: function (res) {

            var dt = $.fn.dataTable.tables()[1];
            $("#tablamodal").html("");
            $("#tablaerror").html("");
            $(dt).DataTable().clear();
            $(dt).DataTable().destroy();

            origen = res["contenido"][0]["origen"];
            destino = res["contenido"][0]["destino"];

            $("#origen").html(origen);
            $("#destino").html(destino);

            var item = res["contenido"];

            //si no encuentra el item muestra en pantalla que no se encontro
            if (res["estado"] == "error") {

            }
            //en caso de contrar el item mostrarlo en la tabla
            else {

                // $("#Rerror").hide();

                var item = res["contenido"];
                let cajar;
                if (estado == 5) {
                    for (var i in item) {
                        if (item[i]['no_caja']==1) {
                            item[i]['no_caja']='---';
                        }
                        $("#tablaerror").append($(`<tr id='${item[i]['iditem']}'><td> 
                            ${item[i]['descripcion']}</td><td> 
                            ${item[i]['no_caja']}</td><td> 
                            ${item[i]['no_cajaR']}</td><td> 
                            <input type= 'number' min='1' class='alistados eliminaritem' value='${item[i]['alistados']}'></td><td> 
                            ${item[i]['recibidos']}</td><td> 
                            ${item[i]['problema']}</td>
                            </tr>`
                        ));

                    }
                } else {
                    for (var i in item) {
                        $("#tablamodal").append($("<tr><td>" +
                            item[i]['codigo'] + "</td><td>" +
                            item[i]['iditem'] + "</td><td>" +
                            item[i]['referencia'] + "</td><td>" +
                            item[i]['descripcion'] + "</td><td>" +
                            item[i]['disponibilidad'] + "</td><td>" +
                            item[i]['pedidos'] + "</td><td>" +
                            item[i]['alistados'] + "</td><td>" +
                            item[i]['ubicacion'] + "</td><td>" +
                            '<input  type="text" placeholder="texto de maximo 20 caracteres" class="mensajes validate" maxlength="20"></input></td></tr>'));

                    }
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
        scrollY: hight,
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