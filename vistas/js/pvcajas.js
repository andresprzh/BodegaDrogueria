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
        url: "ajax/alistar.requisicion.ajax.php",
        method: "POST",
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



        });

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

        url: "ajax/pvcajas.cajas.ajax.php",
        method: "POST",
        data: { "req": req },
        dataType: "JSON",
        success: function (res) {
            console.log(res);
            var caja = res["contenido"];

            //si no encuentra la caja muestra en pantalla que no se encontro
            if (res["estado"] == "error") {
                $("#refresh").prop("disabled", true);
            }
            //en caso de contrar el item mostrarlo en la tabla
            else {
                $("#refresh").prop("disabled", false);
                var caja = res["contenido"];
                // console.log(caja);
                // return 0;
                let color = {
                    0: "yellow",
                    1: "green",
                    2: "orange",
                    3: "green",
                    4: "red",
                    9: "black"
                };



                // si solo hay 1 resultado no hace el ciclo for
                if (caja[0] === undefined) {

                    // reemplaza varoles nul por ---
                    if (caja["tipocaja"] === null) {
                        caja["tipocaja"] = "---"
                    }

                    $("#cajas").append($(`<li 
                                          class="collection-item avatar"
                                          id="${caja["no_caja"]}"
                                          >
                                            <i 
                                            onclick="mostrarItemsCaja(${caja["no_caja"]},${caja["estado"]})" 
                                            class="fas fa-box circle modal-trigger ${color[caja["estado"]]}" 
                                            data-target="VerCaja" >
                                            </i>
                                            <span class="title">Caja No ${caja["no_caja"]}</span>
                                            <p>
                                            Tipo de Caja: ${caja["tipocaja"]}
                                            <br>
                                            ${caja["recibido"]}
                                            </p>
                                            <button 
                                            title="GenerarDocumento" 
                                            class="btn-floating  secondary-content waves-effect green darken-4 " 
                                            >
                                                <i class="fas fa-file-alt"></i>
                                            </button>
                                        </li>`));


                } else {
                    for (var i in caja) {

                        // reemplaza varoles nul por ---
                        if (caja[i]["tipocaja"] === null) {
                            caja[i]["tipocaja"] = "---"
                        }

                        $("#cajas").append($(`<li
                                              class="collection-item avatar" 
                                              id="${caja[i]["no_caja"]}"
                                              >
                                                <i
                                                onclick="mostrarItemsCaja(${caja[i]["no_caja"]},${caja[i]["estado"]})"  
                                                class="fas fa-box circle modal-trigger ${color[caja[i]["estado"]]}" 
                                                data-target="VerCaja"
                                                >
                                                </i>
                                                <span class="title">Caja No ${caja[i]["no_caja"]}</span>
                                                <p>
                                                Tipo de Caja: ${caja[i]["tipocaja"]}
                                                <br>
                                                ${caja[i]["recibido"]}
                                                </p>
                                                <button 
                                                title="GenerarDocumento" 
                                                class="btn-floating  secondary-content waves-effect green darken-4 " >
                                                    <i class="fas fa-file-alt"></i>
                                                </button>
                                            </li>`));

                    }
                }

                $("#TablaCajas").removeClass("hide");


            }

        }

    });

}

function mostrarItemsCaja(numcaja, estado, recibido) {

    $(".NumeroCaja").html(numcaja);

    // let numcaja = caja["no_caja"];
    // console.log(caja);
    // return 0;
    // //obtienen los datos de la caja para pasarlo al modal
    // var datos = table.row(e).data();
    // var numcaja = datos[0];
    // var alistador = datos[1];
    // var tipocaja = datos[2];
    // var cierre = datos[4];

    // // se muestran los datos generales de la caja
    // $(".NumeroCaja").html(numcaja);
    // $("#alistador").html(alistador);
    // $("#tipocaja").html(tipocaja);
    // $("#cierre").html(cierre);

    // // si la caja no esta cerrada, ya fue recibida en el punto de venta o 
    // // fue cancelada se desabilita la opcion de crear documento

    if (estado == 3) {
        $("#Documento").removeAttr("disabled");
    } else {
        $("#Documento").attr("disabled", "disabled");
    }

    // // destruye la datatable 2(tabla del modal)
    // var dt = $.fn.dataTable.tables()[1];
    // $("#tablamodal").html("");
    // $(dt).DataTable().clear();
    // $(dt).DataTable().destroy();


    // //espera a que la funcion termine para reiniciar las tablas
    // $.when(mostrarItems(numcaja, estado)).done(function () {
    //     //Reinicia Tabla
    //     table[1] = iniciarTabla("#TablaM");
    // });
}

