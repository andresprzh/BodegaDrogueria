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
        $.when(mostrarCajas()).done(function () {



        });

    });

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
function mostrarCajas() {
    //refresca la tabla, para volver a cargar los datos
    $('#cajas').html("");
    //consigue el numero de requerido
    var requeridos = $(".requeridos").val();
    //id usuario es obtenida de las variables de sesion
    var req = [requeridos, id_usuario];

    return $.ajax({

        url: "api/cajas/pvcajas",
        method: "POST",
        data: { "req": req },
        dataType: "JSON",
        success: function (res) {

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
                    3: "orange",
                    4: "green",
                    5: "red",
                    9: "black"
                };

                let botonestado = '';

                // si solo hay 1 resultado no hace el ciclo for
                if (caja[0] === undefined) {

                    
                    // if (caja["estado"] != 4) {   
                    //     botonestado = 'disabled';
                    // }

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
                                            onclick="documento(${caja["no_caja"]})"
                                            title="GenerarDocumento"
                                            ${botonestado} 
                                            class="btn-floating  secondary-content waves-effect green darken-4 " 
                                            >
                                                <i class="fas fa-file-alt"></i>
                                            </button>
                                        </li>`));


                } else {
                    for (var i in caja) {

                        botonestado = '';
                        // if (caja[i]["estado"] != 4) {
                        // if (caja[i]["estado"]) {
                        //     botonestado = 'disabled';
                        // }

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
                                                onclick="documento(${caja[i]["no_caja"]})"
                                                title="GenerarDocumento" 
                                                ${botonestado}
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

    if (estado == 4) {
        // $("#Documento").removeAttr("disabled");
        $("#TablaM thead tr").addClass("green darken-3");
        $("#TablaM thead tr").removeClass("red darken-3");
    } else {
        // $("#Documento").attr("disabled", "disabled");
        $("#TablaM thead tr").addClass("red darken-3");
        $("#TablaM thead tr").removeClass("green darken-3");
    }
    mostrarItems(numcaja, estado);
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
        url: "api/cajas/documento",
        method: "POST",
        data: { "req": req, "numcaja": numcaja },
        // dataType: "JSON",
        success: function (res) {
            console.log(res);
            return 0;
            if (res["estado"] == true) {

                // let numcaja = $("#cajas").val();
                // obtiene los 3 ultimos caracteres de la requisicion
                let no_req = req[0].substr(req[0].length - 3);
                numcaja = ("00" + numcaja).slice(-2);

                // crea el nombre del documento a partir de la requisicion y la caja
                let nomdoc = "C" + numcaja + "DE" + no_req + ".TR1";

                let element = document.createElement("a");
                element.setAttribute("href", "data:text/plain;charset=utf-8," + encodeURIComponent(res["string"]));
                element.setAttribute("download", nomdoc);

                element.style.display = "none";
                document.body.appendChild(element);

                element.click();

                document.body.removeChild(element);

            }
        }

    });

}

