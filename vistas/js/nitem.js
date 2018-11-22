$(document).ready(function () {

    /* ============================================================================================================================
                                                        INICIALIZACION   
    ============================================================================================================================*/
    // INICIAR MODAL
    $('.modal').modal();
    // pone items en el input select
    $.ajax({
        url: 'api/alistar/requisiciones',
        method: "POST",
        data: '',
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (res) {
            
            // SE MUESTRAN LAS reqUISICIONES EN EL MENU DE SELECCION
            for (var i in res) {

                $('#requeridos').append($('<option value="' + res[i]["no_req"] + '">' + res[i]["no_req"] + '</option>'));

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

        $(".input_item").removeClass("hide");
        $("#TablaI tbody").html("");
        $("#DivTabla").addClass("hide");

    });

    $('#formitem').submit(function (e) {

        e.preventDefault();
        
        let item = $('#item').val();
        //consigue el numero de requerido
        var requeridos = $(".requeridos").val();
        //id usuario es obtenida de las variables de sesion
        var req = [requeridos, id_usuario];
        if (item.replace(/^\s+/g, '').length) {
            buscarItem(item, req);
        }
        // buscarItem(item,req);

    });

    // agrega item a la tabla de requisicion
    $('#TablaM tbody').on('click', '.agregar', function (e) {

        let item = {
            "iditem": $(this).closest('tr').attr('id'),
            "codigo": $('td:eq(0)', $(this).parents('tr')).text().replace(/(^\s+|\s+$)/g, ''),
            "referencia": $('td:eq(2)', $(this).parents('tr')).text().replace(/(^\s+|\s+$)/g, ''),
            "descripcion": $('td:eq(3)', $(this).parents('tr')).text().replace(/(^\s+|\s+$)/g, '')
        };
        insertarItem(item);
        $(`#TablaM  #${item['iditem']}`).remove();
    });

    // agregar los items de la tabla de requisicion a la requisicion
    $("#agitems").click(function (e) {
        e.preventDefault();
        
        //consigue el numero de requerido
        var requeridos = $(".requeridos").val();
        //id usuario es obtenida de las variables de sesion
        var req = [requeridos, id_usuario];


        swal({
            title: `¿Esta seguro de agregar los items a la requisición ${requeridos}?`,
            icon: "warning",
            buttons: ['Cancelar', 'Aceptar']
        })
            .then((Cancelar) => {

                if (Cancelar) {

                    let tabla = document.getElementById("tablaitems");
                    let tr = tabla.getElementsByTagName("tr");
                    let items = new Array;

                    for (let i = 0; i < tr.length; i++) {

                        items[i] = {
                            "iditem": tr[i].id,
                            "pedido": $(tr[i]).find("input").val(),
                        };
                    }

                    $.ajax({
                        type: "POST",
                        url: "api/nitem/agregar",
                        data: { "items": items, "req": req },
                        dataType: "JSON",
                        success: function (res) {
                                                        
                            if (res) {
                                swal({
                                    title: `Items agregados exitosamente a la requisición ${requeridos}`,
                                    icon: "success",
                                })
                                    .then((ok) => {
                                        location.reload();
                                    });

                            } else {
                                swal({
                                    title: `Error al agregar los items a la requisición ${requeridos}`,
                                    icon: "error",
                                });
                            }
                        }
                    });
                }

            });

    });

    // eliminar item de la tabla de requisicion
    $('#TablaI tbody').on('click', '.eliminar', function (e) {

        $(this).closest('tr').remove();

    });

    // input para buscar en la tabla del modal
    $("#buscar").keyup(function (e) {
        let input, filter, table, tr;
        let datos = new Array;

        filter = this.value.toUpperCase();
        table = document.getElementById("TablaM");
        tr = table.getElementsByTagName("tr");

        for (let i = 0; i < tr.length; i++) {
            datos[0] = tr[i].getElementsByTagName("td")[0];
            datos[1] = tr[i].getElementsByTagName("td")[1];
            datos[2] = tr[i].getElementsByTagName("td")[2];
            datos[3] = tr[i].getElementsByTagName("td")[3];
            if (datos[1] && datos[2]) {
                if ((datos[0].innerHTML.toUpperCase().indexOf(filter) > -1) || (datos[1].innerHTML.toUpperCase().indexOf(filter) > -1) ||
                    (datos[2].innerHTML.toUpperCase().indexOf(filter) > -1) || (datos[3].innerHTML.toUpperCase().indexOf(filter) > -1)) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    });

});
/* ============================================================================================================================
                                                FUNCIONES   
============================================================================================================================*/
function buscarItem(item, req) {

    $.ajax({
        type: "post",
        url: "api/nitem/items",
        data: { "item": item, "req": req },
        dataType: "JSON",
        success: function (res) {
            
            if (res["estado"] == "encontrado") {
                items = res["contenido"];
                $("#TablaM tbody").html("");
                if (items.constructor === Array) {
                    $(".modal").modal("open");
                    for (var i in items) {
                        $("#TablaM tbody").append($(`
                                <tr id=${items[i]["iditem"]}><td >
                                    ${items[i]["codigo"]}</td><td >
                                    ${items[i]["iditem"]}</td><td >
                                    ${items[i]["referencia"]}</td><td>
                                    ${items[i]["descripcion"]}</td><td>
                                    <button  title='Agregar' class='agregar btn-floating btn-small waves-effect waves-light green darken-1 ' > 
                                        <i class='fas fa-plus'></i> 
                                    </button>
                                </tr>`)
                        );
                    }
                } else {
                    insertarItem(items);
                }

            } else {
                swal({
                    title: `No se encontraron los Items`,
                    icon: "error",
                });
            }
        }
    });
}

function insertarItem(item) {

    let ids = $.makeArray($('#TablaI tbody tr[id]').map(function () {
        return this.id;
    }));

    if (!ids.includes(item["iditem"])) {
        $("#TablaI tbody").append($(`
            <tr id=${item["iditem"]}><td >
                ${item["codigo"]}</td><td >
                ${item["iditem"]}</td><td >
                ${item["referencia"]}</td><td>
                ${item["descripcion"]}</td><td>
                <input type= 'number' min='1' class='cantidad eliminaritem' value="1"> </td><td>
                <button  title='Eliminar' class='eliminar btn-floating btn-small waves-effect waves-light red darken-1 '  > 
                    <i class='fas fa-times'></i> 
                </button>
            </tr>`)
        );
        $("#DivTabla").removeClass("hide");
        return true;
    } else {
        M.toast({ html: 'Item ya agregado', classes: 'red' });
        return false;
    }
}