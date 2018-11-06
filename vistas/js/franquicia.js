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

    
    $.ajax({
        url: "api/remisiones/remisiones",
        method: "GET",
        data: { 'estado': 2, "franquicia":franquicia },
        dataType: "json",
        success: function (res) {
            console.log(res);
            // SE MUESTRAN LAS REQUISICIONES EN EL MENU DE SELECCION
            for (var i in res) {

                $("#remisiones").append($(`<option value="${res[i]["no_rem"]}"> OC${res[i]["no_rem"]}</option>`));
                
            }

            // INICIA MENU DE SELECCION
            $('select').formSelect();

        }
    });


    /* ============================================================================================================================
                                                        EVENTOS   
    ============================================================================================================================*/

    

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

                    var remision = $(".remisiones").val();
                    
                    var rem ={'no_rem':remision, 'id_usuario':id_usuario};
                    
                    var tabla = $('table.tablas').DataTable();
                    // se obtienen todos los datos de la tabla en una matriz
                    var datos = tabla.data().toArray();
                    
                    // se guerda en un arreglo los datos de codigo de Baras y la cantidad recibido                    
                    var items = new Array();
                    for (var i in datos) {
                        items[i] = {
                            "descripcion":datos[i][0], 
                            "codbarras": datos[i][1],
                            "item": datos[i][2],
                            "referencia": datos[i][3],
                            "recibidos": $(datos[i][4]).val()
                        }
                        
                    }
                    $.ajax({
                        url: 'api/pv/registrar',
                        type: 'POST',//metodo post para mandar datos
                        // type: 'GET',
                        data: { 'items': items,'franquicia':franquicia,'rem':rem },//datos que se enviaran
                        dataType: 'JSON',
                        success: function (res) {
                            // console.log(res);
                            // return 0;
                            if (res) {

                                // crea el nombre del documento a partir de la requisicion y la caja
                                let nomdoc = 'lista.txt';

                                let element = document.createElement('a');
                                element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(res['contenido']['documento']));
                                element.setAttribute('download', nomdoc);

                                element.style.display = 'none';
                                document.body.appendChild(element);

                                element.click();

                                document.body.removeChild(element);

                                enviarmail(res['contenido']['documento']);

                                location.reload();
                            }
                            

                        }
                    });

                }
            });
    });
    
     //EVENTO AL CAMBIAR ENTRADA REQUERIDOS
     $(".remisiones").change(function (e) {
        $(".input_barras").removeClass("hide");
        $("#Registrar").addClass("hide");
        $("#codbarras").focus();
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

    // ajax para ejecutar un script php mandando los datos
    return $.ajax({
        url: 'api/pv/items',//url de la funcion
        type: 'post',//metodo post para mandar datos
        data: { "codigo": codigo },//datos que se enviaran
        dataType: 'JSON',
        success: function (res) {
            
            var tabla = $('table.tablas').DataTable();
            var datos = tabla.data().toArray()
            var items = new Array;
            items[0] = datos.map(function (value, index) { return value[0]; });
            items[1] = datos.map(function (value, index) { return value[1]; });
            items[2] = datos.map(function (value, index) { return value[2]; });
            items[3] = datos.map(function (value, index) { return value[3]; });
            
            if (res['estado'] == 'encontrado') {
                var cantr = 1;

                //busca si el item ya esta en la tabla
                for (let index = 0; index < 4; index++) {
                    var pos = items[index].indexOf(codigo.toUpperCase());
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
                $('#Registrar').removeClass('hide');
                $('#codbarras').focus();
            }else{
                swal({
                    title: '¡Error!',
                    text: res['contenido'],
                    icon: 'error',
                });
            }

        }

    });
}

function enviarmail(data){
    $.ajax({
        type: 'POST',
        url: 'api/pv/mail',
        data: {'data':data}
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
        scrollY: "350px",
        scrollCollapse: true,
        paging: false

    });

    return tabla;

}