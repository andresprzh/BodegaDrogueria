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
    url: 'api/remisiones/remisiones',
    method: 'GET',
    data: { 'franquicia': franquicia },
    dataType: 'JSON',
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
                3: 'green',
                4: 'red',
                5: 'red',
                9: 'black'
            };

            
                for (var i in remisiones) {



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
    
    $(".NumeroRemision").html(('00' + numrem).slice(-2));

    if (estado == 3) {
        // $("#Documento").removeAttr("disabled");
        $("#TablaR thead tr").addClass("green darken-3");
        $("#TablaR thead tr").removeClass("red darken-3");
    } else {
        // $("#Documento").attr("disabled", "disabled");
        $("#TablaR thead tr").addClass("red darken-3");
        $("#TablaR thead tr").removeClass("green darken-3");
    }
    mostrarItems(numrem, estado);
}

function mostrarItems(numcaja, estado = null) {

    //consigue el numero de requerido
    let no_rem = $(".NumeroRemision").html();
    //id usuario es obtenida de las variables de sesion
    
    return $.ajax({
        url: "  api/pv/items",
        method: "POST",
        data: { "no_rem": no_rem, "estado": estado },
        dataType: "JSON",
        success: function (res) {

            $("#TablaR tbody").html("");

            //si no encuentra el item muestra en pantalla que no se encontro
            if (res){

                // $("#Rerror").hide();

                var item = res;

                
                for (var i in item) {
                    
                    $("#TablaR tbody").append($(`<tr id="${item[i]['item']}"><td>
                        ${item[i]['descripcion']} </td><td>    
                        ${item[i]['item']} </td><td>
                        ${item[i]['recibidos']}</td>
                        </tr>`));


                }

            }

        }

    });

}

function documento(no_rem) {

    if (!no_rem) {
        //consigue el numero de remision
        no_rem = $(".NumeroRemision").html();
    }

    return $.ajax({
        url: 'api/pv/documento',
        method: 'POST',
        data: { 'no_rem': no_rem,'franquicia': franquicia },
        dataType: 'JSON',
        success: function (res) {
            console.log(res);
            
            if (res) {

                // crea el nombre del documento a partir de la requisicion y la caja
                let nomdoc = 'lista.txt';

                let element = document.createElement('a');
                element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(res['documento']));
                element.setAttribute('download', nomdoc);

                element.style.display = 'none';
                document.body.appendChild(element);

                element.click();

                document.body.removeChild(element);

                // enviarmail(res);

                location.reload();
            }
        }

    });

}



