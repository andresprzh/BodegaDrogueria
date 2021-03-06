$(document).ready(function () {

    /* ============================================================================================================================
                                                        INICIALIZACION   
    ============================================================================================================================*/
    // INICIA DATATABLE
    table = iniciar_tabla();

    // INICIAR MODAL
    $('.modal').modal({
        onCloseEnd: function () { location.reload(); }
    });
    // pone items en el input select
    $.ajax({
        url: "api/alistar/requisiciones",
        method: "GET",
        data: { 'valor': 2 },
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
        // oculta los datos de la requisicion
        $("#infreq").addClass("hide");
        // muestra la entrada se seleccion de cajas
        $(".SelectCaja").removeClass("hide");
        // oculta el input donde se ingresa el codigo de barras
        $(".input_barras").addClass("hide");
        $("#Registrar").addClass("hide");
        //consigue el numero de requerido
        var requeridos = $(".requeridos").val();
        //id usuario es obtenida de las variables de sesion
        var req = [requeridos, id_usuario];

        $('#cajas').html('<option value="" disabled selected>Seleccionar</option>');

        $.ajax({
            url: 'api/pv/cajas',//url de la funcion
            type: 'post',//metodo post para mandar datos
            data: { "req": req },//datos que se enviaran
            dataType: "JSON",
            success: function (res) {

                if (res !== false) {
                    // SE MUESTRAN LAS CAJAS EN EL MENU DE SELECCION

                    var cajas = res['cajas'];
                    // si el resultado es un array

                    if (cajas.constructor === Array) {
                        for (var i in cajas) {
                            $("#cajas").append($('<option value="' + cajas[i]['no_caja'] + '">Caja ' + cajas[i]['num_caja'] + '</option>'));
                        }
                    }

                    var requisicion = res['requisicion']['contenido'];
                    // se muestra el origen y destino de la requisicion
                    $("#infreq").removeClass("hide");
                    $('#origen').html(requisicion['origen']);
                    $('#destino').html(requisicion['destino']);

                }


                // INICIA MENU DE SELECCION
                $('select').formSelect();
            }

        });


    });

    // EVENTO AL CAMBIAR LA ENTRADA DE CAJAS
    $("#cajas").change(function (e) {
        $(".input_barras").removeClass("hide");
        $("#Registrar").addClass("hide");
        $("#codbarras").focus();
    });

    // EVENTO INPUT  CODIGO DE BARRAS
    $("#codbarras").keypress(function (e) {

        //si se presiona enter busca el item y lo pone en la pagina
        if (e.which == 13) {

            BuscarCodBar()
        }

    });


    // EVENTO CUANDO SE MODIFICA UNA CELDA DE LA TABLA
    $('#tablaeditable').on('change', 'td', function () {

        // guarda el valor dle input en datatable
        var tabla = $('#tabla').DataTable();


        var cantr = $(this).find("input").val();
        var fila = tabla.row(this);

        // si la tabla es responsive
        if (fila.data() == undefined) {

            var fila = $(this).parents('tr');
            if (fila.hasClass('child')) {
                fila = fila.prev();
            }

            tabla.row(fila).cell(fila, 4).data('<input type="number" class="validate" value="' + cantr + '">').draw()

        } else {

            tabla.cell(this).data('<input type="number" class="validate" value="' + cantr + '">').draw()
        }




    });


    // EVENTO SI SE PRESIONA EL BOTON DE AGREGAR CODIGO DE BARRAS(+)
    $("#agregar").click(function (e) {

        // busca el codigo y lo agrega a la tabla 
        BuscarCodBar();

    });

    // EVENTO SI SE PRESIONA 1 BOTON EN LA TABLA EDITABLE(ELIMINAR ITEM)
    $("#tablaeditable").on("click", "button", function (e) {


        var dt = $.fn.dataTable.tables();
        var tabla = $(dt).DataTable();


        celda = table.cell(this);

        var fila = table.row(this)

        // si la tabla es responsive
        if (fila.data() == undefined) {

            fila = $(this).parents("tr");
            if (fila.hasClass("child")) {
                fila = fila.prev();
            }
        } else {
            fila = this;
        }


        tabla.row(fila).remove().draw("false");



    });

    // EVENTO SI SE PRESIONA EL BOTON DE REGISTRAR
    $("#Registrar").click(function (e) {

        swal({
            title: "¿Registrar items?",
            icon: "warning",
            buttons: ['Cancelar', 'Resgitrar']

        })
            .then((registrar) => {

                //si se le da click en Resgitrar procede a generar e reporte
                if (registrar) {

                    //consigue el numeor de la caja
                    var caja = $('#cajas').val();

                    //consigue el numero de requerido
                    var requeridos = $(".requeridos").val();
                    //id usuario es obtenida de las variables de sesion
                    var req = [requeridos, id_usuario];


                    var tabla = $('table.tablas').DataTable();
                    // se obtienen todos los datos de la tabla en una matriz
                    var datos = tabla.data().toArray();

                    // se guerda en un arreglo los datos de codigo de Baras y la cantidad recibido                    
                    var items = new Array();
                    for (var i in datos) {
                        items[i] = {
                            "codbarras": datos[i][1],
                            "item": datos[i][2],
                            "recibidos": $(datos[i][4]).val()
                        }
                    }

                    $.ajax({
                        url: "api/pv/registrar",
                        type: "POST",//metodo post para mandar datos
                        data: { "caja": caja, "req": req, "items": items },//datos que se enviaran
                        dataType: "JSON",
                        success: function (res) {

                            if (res["estado"] == true) {
                                // crea el documento si no hay errores en los items recibidos
                                if (res["contenido"]["estado"] == true) {
                                    swal({
                                        title: "¡Items registrados!",
                                        icon: "success",
                                    }).then((ok) => {
                                        location.reload();
                                        let numcaja = $("#cajas").val();
                                        // obtiene los 3 ultimos caracteres de la requisicion
                                        let no_req = req[0].substr(req[0].length - 3);
                                        numcaja = ("00" + numcaja).slice(-2);

                                        // crea el nombre del documento a partir de la requisicion y la caja
                                        let nomdoc = "C" + numcaja + "DE" + no_req + ".TR1";

                                        let element = document.createElement("a");
                                        element.setAttribute("href", "data:text/plain;charset=utf-8," + encodeURIComponent(res["contenido"]["string"]));
                                        element.setAttribute("download", nomdoc);

                                        element.style.display = "none";
                                        document.body.appendChild(element);

                                        element.click();

                                        document.body.removeChild(element);
                                    });
                                } else if (res["contenido"]["estado"] == "error0") {
                                    $('.modal').modal('open');
                                    let item = res["contenido"]["contenido"];
                                    $("#tablamodal").html("");
                                    for (var i in item) {
                                        $("#tablamodal").append($(`<tr><td>
                                                        ${item[i]["descripcion"]}</td><td>
                                                        ${item[i]["iditem"]}</td><td>
                                                        ${item[i]["mensaje"]}</td>
                                                        </tr>`));
                                    }
                                } else {
                                    swal({
                                        title: "¡Error!",
                                        text: "Error",
                                        icon: "error",
                                    })
                                        .then((ok) => {
                                            location.reload();
                                        });
                                }

                            } else {
                                swal({
                                    title: "¡Error!",
                                    text: "Error",
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

// FUNCION QUE BUSCA EL CODIGO DE BARRAS
function BuscarCodBar() {

    //consigue el codigo de barras
    var codigo = $('#codbarras').val();
    // pone en blanco input 
    $('#codbarras').val("");
    //consigue el numero de requerido
    var requeridos = $(".requeridos").val();
    //id usuario es obtenida de las variables de sesion
    var req = [requeridos, id_usuario];

    // ajax para ejecutar un script php mandando los datos
    return $.ajax({
        url: 'api/pv/items',//url de la funcion
        type: 'post',//metodo post para mandar datos
        data: { "codigo": codigo, "req": req },//datos que se enviaran
        dataType: 'json',
        success: function (res) {

            var tabla = $('table.tablas').DataTable();
            var datos = tabla.data().toArray()
            var items = new Array;
            items[0] = datos.map(function (value, index) { return value[0]; });
            items[1] = datos.map(function (value, index) { return value[1]; });
            items[2] = datos.map(function (value, index) { return value[2]; });
            items[3] = datos.map(function (value, index) { return value[3]; });
            if (res["estado"] == 'encontrado') {
                var cantr = 1;

                //busca si el item ya esta n la tabla

                for (let index = 0; index < 4; index++) {
                    var pos = items[index].indexOf(codigo);
                    if (pos >= 0) {
                        break;
                    }
                }
                //si encuentra el item en la tabla acumula el item en la columna de recibido
                if (pos >= 0) {

                    // se acumula la cantidad recibida
                    cantr += parseInt($(datos[pos][4]).val());

                    tabla.cell(pos, 4).data('<input type="number" class="validate" value="' + cantr + '">').draw();
                    // si no encuentra el item en la tabla lo agrega a esta    
                } else {

                    var item = res['contenido'];

                    // agrega datos en la tabla
                    tabla.row.add([
                        item['descripcion'],
                        item['codigo'],
                        item['iditem'],
                        item['referencia'],
                        // cantr
                        '<input type="number" class="validate" value="' + cantr + '">',
                        "<button  title='Eliminar Item' class='btn-floating btn-small waves-effect waves-light red darken-3 ' >" +
                        "<i class='fas fa-times'></i>" +
                        "</button>"
                    ]).draw(false);
                }
                $("#Registrar").removeClass("hide");
                $("#codbarras").focus();
            }

        }

    });
}

// FUNCION QUE INICIA DATATABLE
function iniciar_tabla(tabla) {

    if (!tabla) {
        tabla = "table.datatable";
    }

    var tabla = $(tabla).DataTable({

        "bLengthChange": false,
        "bFilter": true,
        "sDom": '<"top">t<"bottom"irp><"clear">',
        "pageLength": 5,
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
        scrollY: "600px",
        scrollCollapse: true,
        paging: false

    });

    return tabla;

}