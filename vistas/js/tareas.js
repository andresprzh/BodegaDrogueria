$(document).ready(function () {

    /* ============================================================================================================================
                                                        INICIALIZACION   
    ============================================================================================================================*/
    $('.modal').modal();


    // pone requisiciones en el input select
    $.ajax({
        url: "api/tareas/usuarios",
        method: "GET",
        contentType: false,
        processData: false,
        dataType: "JSON",
        success: function (res) {

            if (res['estado'] == 'encontrado') {
                let usuarios = res['contenido'];

                // si el resultado es n array
                if (usuarios.constructor === Array) {
                    for (var i in usuarios) {
                        $("#TablaU tbody").append($(`<tr id=${usuarios[i]["id_usuario"]}><td>
                        ${usuarios[i]["nombre"]}</td><td>
                        ${usuarios[i]["cedula"]}</td><td>
                        <a class="tareanueva btn-floating btn-small waves-effect waves-light white" title="Ver Ubicaciones"><i class="grey-text fas fa-wrench"></i></a></td>
                        </tr>`));
                    }
                    // si solo hay 1 dato en usuarios
                } else {

                    $("#TablaU tbody").append($(`<tr id=${usuarios["id"]}><td>
                        ${usuarios["nombre"]}</td><td>
                        ${usuarios["cedula"]}</td><td>
                        <a class="tareanueva"><i class="fas fa-wrench"></i></a></td>
                        </tr>`));
                }

            }

        }
    });
    asignarUbicaciones();

    /* ============================================================================================================================
                                                        EVENTOS   
    ============================================================================================================================*/

    $("#buscar").keyup(function (e) {
        let input, filter, table, tr, nombres, cedulas;

        filter = this.value.toUpperCase();
        table = document.getElementById("TablaU");
        tr = table.getElementsByTagName("tr");
        for (let i = 0; i < tr.length; i++) {
            nombres = tr[i].getElementsByTagName("td")[0];
            cedulas = tr[i].getElementsByTagName("td")[1];
            if (nombres && cedulas) {
                if ((nombres.innerHTML.toUpperCase().indexOf(filter) > -1) || (cedulas.innerHTML.toUpperCase().indexOf(filter) > -1)) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    });

    // si se da click en el boton de ver o asignar ubicacion
    $('#TablaU tbody').on('click', 'a', function () {
        // se consigue el id del item y el nombre
        let iduser = $(this).closest('tr').attr('id');
        const nombre = $('td:eq(0)', $(this).parents('tr')).text().replace(/(^\s+|\s+$)/g, '');
        const cedula = $('td:eq(1)', $(this).parents('tr')).text().replace(/(^\s+|\s+$)/g, '');
        $('#iduser').html(iduser);
        $('#nombre').html(nombre);
        $('#cedula').html(cedula);

        agregarUbicaciones(iduser);

    });

    // evita que el botton de cerrar modal haga otra accion
    $('.modal-close').click(function (e) {
        e.preventDefault();
    });

    // si se da click en el boton de ver o asignar ubicacion
    $('#agregarubicacion').on('click', function () {
        // Busca los datos en la tabla
        let lista = document.getElementById('ubicaciones');
        let li = lista.getElementsByTagName('li');
        let items = new Array;

        if (!ubicaciones) {
            swal({
                title: 'No hay ubicaciones',
                type: 'warning',
                confirmButtonColor: 'red',
            });

        } else {
            let ubicacioneslista = new Array;
            for (let i = 0; i < li.length; i++) {
                ubicacioneslista[li[i].id] = li[i].id;
            }

            // solo muestra ubicaciones que no estan asignadas
            newubicaciones = diff(ubicaciones, ubicacioneslista);
            let ub = new Array();

            for (let i in ubicaciones) {

                switch (ubicaciones[i]['tip_inventario']) {
                    case '2':
                        if (!ub[0]) {
                            ub[0] = '<p class="green-text center-align">Quimicos</p>';
                        }
                        ub[0] += `
                        <div class="col s12 ">
                        <label>
                            <input type="checkbox" name="ubicacion" value="${ubicaciones[i]['ubicacion']}"/>
                            <span>${ubicaciones[i]['ubicacion']}</span>
                        </label>
                        </div>`;
                        break;
                    case '3':
                        if (!ub[1]) {
                            ub[1] = '<p class="green-text center-align">Eticos</p>';
                        }
                        ub[1] += `
                        <div class="col s12">
                        <label>
                            <input type="checkbox" name="ubicacion" value="${ubicaciones[i]['ubicacion']}"/>
                            <span>${ubicaciones[i]['ubicacion']}</span>
                        </label>
                        </div>`;
                        break;
                    default:
                        break;
                }
            }
            $('#ubic').html('');

            let ubs = new Array();
            for (let i in ub) {
                if (ub) {
                    ubs.push(ub[i])
                }
            }
            let col = 12 / ubs.length;
            for (let i in ubs) {
                $('#ubic').append(`
                <div class="card  col s${col} ">
                    ${ubs[i]}
                </div>
                `);
            }

            $('#seleubic .card div').addClass(`m${Math.round(12 / (4 / ubs.length))}`);
            $('#seleubic').modal('open');

        }
    });

    // se asignan ubicaciones
    $('#seleubic').on('submit', 'form', function (e) {
        e.preventDefault();

        const iduser = $('#iduser').html();
        let arra_ubc = new Array;

        $.each($("#ubic input:checkbox:checked"), function () {
            arra_ubc.push($(this).val());
        });

        if (arra_ubc.length > 0) {

            $('#seleubic .btn').attr('disabled', true);
            $('#seleubic input:checkbox').attr('disabled', true);
            $('#seleubic .progress').removeClass('hide');

            $.ajax({
                type: 'POST',
                url: 'api/tareas/dettarea',
                data: { 'usuario': iduser, 'ubicacion': arra_ubc },
                dataType: 'JSON',
                success: function (res) {

                    $('#seleubic .btn').removeAttr('disabled');
                    $('#seleubic input:checkbox').removeAttr('disabled');
                    $('#seleubic .progress').addClass('hide');
                    if (res) {
                        asignarUbicaciones();
                        agregarUbicaciones(iduser);
                        $('#seleubic').modal('close');

                    } else {
                        var toastHTML = `<span class="truncate">No se pudo agregar ubicación</span>`;
                        M.toast({
                            html: toastHTML,
                            classes: "red darken-4",
                            displayLength: 1500
                        });
                    }

                }
            });
        }
    });

    $('#check-todos').change(function (e) {
        e.preventDefault();
        var checkbox = document.getElementById('check-todos');
        if (checkbox.checked) {
            $.each($("#ubic input:checkbox"), function () {
                $(this).prop('checked', true);
            });
        } else {
            $.each($("#ubic input:checkbox"), function () {
                $(this).prop('checked', false);
            });
        }

    });

    // elimina ubicacin
    $('#listtareas').on('click', 'a', function () {

        let ubicacion = new Array;
        ubicacion[0] = $(this).closest('li').attr('id');
        const iduser = $('#iduser').html();

        $.ajax({
            type: 'POST',
            url: 'api/tareas/dettarea',
            data: { 'usuario': iduser, 'ubicacion': ubicacion, 'eliminar': true },
            dataType: 'JSON',
            success: function (res) {

                if (res) {
                    asignarUbicaciones();
                    agregarUbicaciones(iduser);

                } else {

                    var toastHTML = `<span class="truncate">No se pudo eliminar ubicación</span>`;
                    M.toast({
                        html: toastHTML,
                        classes: "red darken-4",
                        displayLength: 1500
                    });
                }
            }
        });
    });


});

/* ============================================================================================================================
                                                FUNCIONES   
============================================================================================================================*/
function agregarUbicaciones(iduser) {

    $.ajax({
        type: 'GET',
        url: 'api/tareas/dettarea',
        data: { 'usuario': iduser },
        dataType: 'JSON',
        success: function (res) {

            // refresca ubicaciones
            $('#ubicaciones').html('');

            if (res) {

                for (let i in res) {

                    $('#ubicaciones').append(`
                    <li class="collection-item" id="${i}">
                        <div>${res[i]}<a href="#!" class="secondary-content red-text"><i class="fas fa-times"></i></a></div>
                    </li>`);
                }

            } else {

                $('#ubicaciones').html(`<li class="collection-item">No hay ubicaciones asignadas</li>`);
            }

            $('#informacion').modal('open');
        }
    });

}

// guarda las ubicaciones en variable global
function asignarUbicaciones() {
    $.ajax({
        type: 'GET',
        url: 'api/tareas/ubicaciones',
        data: 'data',
        dataType: 'JSON',
        success: function (res) {

            ubicaciones = res;

        }
    });
}

// obtiene conjunto diferencia entre 2 arreglos u objetos
function diff(a, b) {
    c = new Array();

    for (var i in a) {

        for (var j in b) {
            var include = true;
            if (a[i] == b[j]) {
                include = false;
                break;
            }
        }
        if (include) {
            c[i] = a[i];
        }
    }
    return c;
}

function eliminarub() {

    $('.modal_principal .btn').attr('disabled', true);
    $('.modal_principal a').attr('disabled', true);
    const iduser = $('#iduser').html();
    $('#listtareas .progress').removeClass('hide');

    let lista = document.getElementById('ubicaciones');
    let li = lista.getElementsByTagName('li');
    let ubicaciones = new Array;

    for (let i = 0; i < li.length; i++) {
        ubicaciones[i] = li[i].id
    }

    $.ajax({
        type: 'POST',
        url: 'api/tareas/dettarea',
        data: { 'usuario': iduser, 'ubicacion': ubicaciones, 'eliminar': true },
        dataType: 'JSON',
        success: function (res) {
            $('.modal_principal .btn').removeAttr('disabled', true);
            $('.modal_principal a').removeAttr('disabled');
            $('#listtareas .progress').addClass('hide');

            if (res) {
                asignarUbicaciones();
                agregarUbicaciones(iduser);

            } else {

                var toastHTML = `<span class="truncate">No se pudo eliminar ubicación</span>`;
                M.toast({
                    html: toastHTML,
                    classes: "red darken-4",
                    displayLength: 1500
                });
            }
        }
    });

}

