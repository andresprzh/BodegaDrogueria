$(document).ready(function () {

    /* ============================================================================================================================
                                                        INICIALIZACION   
    ============================================================================================================================*/
    // INICIA DATATABLE
    // table = iniciar_tabla();

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
        $("#TablaV").addClass("hide");
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
        $("#TablaV").addClass("hide");
        $("#codbarras").focus();
    });

    // EVENTO INPUT  CODIGO DE BARRAS
    $("#codbarras").keypress(function (e) {

        //si se presiona enter busca el item y lo pone en la pagina
        if (e.which == 13) {

            BuscarCodBar()
        }

    });

    // EVENTO SI SE PRESIONA EL BOTON DE AGREGAR CODIGO DE BARRAS(+)
    $("#agregar").click(function (e) {

        // busca el codigo y lo agrega a la tabla 
        BuscarCodBar();

    });

    // EVENTO SI SE PRESIONA 1 BOTON EN LA TABLA EDITABLE(ELIMINAR ITEM)
    $("#tablaeditable").on("click", "button", function (e) {

        $(this).closest('tr').remove();
        
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

                    // Busca los datos en la tabla
                    let table = document.getElementById("tabla");
                    let tr = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');;
                    let items = new Array;

                    for (let i = 0; i < tr.length; i++) {

                        items[i] = {
                            'iditem': tr[i].id,
                            'codbarras': tr[i].getElementsByTagName('td')[1].innerHTML.replace(/(^\s+|\s+$)/g, ''),
                            'recibidos': $(tr[i]).find('input').val(),
                        };
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


    // EVENTO SI SE ELIMINA UN ELEMENTO DE LA TABLA    
    $('table tbody').bind("DOMNodeRemoved", function(e)
    {
        // let table = document.getElementById("tabla");
        let tr = this.parentNode.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        if (tr.length<2) {
            this.parentNode.parentNode.classList.add("hide");
        }

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
            if (res) {
            
                filter = codigo.toUpperCase();
                table = document.getElementById("tabla");
                tr = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');;
                
                if (res["estado"] == 'encontrado') {
                    var cantr = 1;
                    let pos=-1;
                    for (let i = 0; i < tr.length; i++) {
                        let td = tr[i].getElementsByTagName("td");
                        
                        if (td) {
                            for (let j = 0; j < td.length; j++) {
                                pos=td[j].innerHTML.toUpperCase().indexOf(filter);
                                if (pos > -1) {break;}
                            }
                        }
                        if (pos > -1) {
                            // incrementa la cantidad
                            td[4].getElementsByTagName('input')[0].value=cantr+parseInt(td[4].getElementsByTagName('input')[0].value);
                            break;
                        }
                    }
                    //si no encuentra el item en la tabla agrga el item a la tabla
                    if (pos == -1)  {

                        var item = res['contenido'];
                        $("#tabla tbody").prepend($(`
                            <tr id=${item["iditem"]}><td >
                                ${item["descripcion"]}</td><td>
                                ${item["codigo"]}</td><td >
                                ${item["iditem"]}</td><td >
                                ${item["referencia"]}</td><td>
                                <input type= 'number' class="validate" value="${cantr}"> </td><td>
                                <button  title='Eliminar Item' class='btn-floating btn-small waves-effect waves-light red darken-3 ' > 
                                    <i class='fas fa-times'></i>
                                </button>
                            </tr>`)
                        );
                    }
                    $("#TablaV").removeClass("hide");
                    $("#codbarras").focus();

                }else{

                    swal({
                        title: res['contenido'],
                        icon: "error"
                    });
                }
            }
        }

    });
}
