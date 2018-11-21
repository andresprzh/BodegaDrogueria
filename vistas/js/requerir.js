$(document).ready(function () {

    // sube el archivo 
    $("#archivo").change(function (e) {

        $('#tabla').addClass('hide');
        $("#carga").removeClass("hide");
        $('#resultado').html('');

        var archivo = document.getElementById('archivo').files[0];
        var form_data = new FormData();
        form_data.append('archivo', archivo);

        // return 0;
        $.ajax({
            type: "POST",
            url: "api/requisicion/req",
            contentType: false,
            processData: false,
            data: form_data,
            dataType: "JSON",
            success: function (res) {

                // si sube los items
                $('#tabla tbody ').html('');
                $("#carga").addClass("hide");
                if (res['estado']) {
                    swal({
                        title: res['contenido'],
                        icon: "success"
                    });
                    $('#resultado').append(`<p class="green-text text-darken-5">${res['contenido']}</p> `);
                    let items = res['items'];
                    for (var i in items) {
                        $('#tabla tbody ').append(`
                        <tr>
                            <td>${items[i]["DESCRIPCION"]}</td>
                            <td>${items[i]["ID_CODBAR"]}</td>
                            <td>${items[i]["item"]}</td>
                            <td>${items[i]["ID_REFERENCIA"]}</td>
                            <td>${items[i]["disp"]}</td>
                            <td>${items[i]["pedido"]}</td>
                            <th class="black-text">${items[i]["ubicacion"]}</th>
                        </tr>`);
                    }
                    $('#tabla').removeClass('hide');
                } else {
                    swal({
                        title: res['contenido'],
                        icon: "error"
                    });

                    $('#resultado').append(`<p class="red-text text-darken-5">${res['contenido']}</p> `);

                }
                if (res['errores']) {
                    var nomdoc = 'errores.txt';

                    var element = document.createElement('a');
                    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(res['errores']));
                    element.setAttribute('download', nomdoc);

                    element.style.display = 'none';
                    document.body.appendChild(element);

                    element.click();

                    document.body.removeChild(element);
                }

            }
        });
    });

    // $("#urlarchivo").change(function (e) {
    //     $("#carga").removeClass("hide");
    // });

});