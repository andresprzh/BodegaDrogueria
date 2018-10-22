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
                let usuarios=res['contenido'];

                // si el resultado es n array
                if (usuarios.constructor===Array) {
                    for (var i in usuarios) {
                        $("#TablaU tbody").append($(`<tr id=${usuarios[i]["id_usuario"]}><td>
                        ${usuarios[i]["nombre"]}</td><td>
                        ${usuarios[i]["cedula"]}</td><td>
                        <a class="tareanueva btn-floating btn-small waves-effect waves-light white" title="Ver Ubicaciones"><i class="grey-text fas fa-wrench"></i></a></td>
                        </tr>`));  
                    } 
                // si solo hay 1 dato en usuarios
                }else{
                    
                    $("#TablaU tbody").append($(`<tr id=${usuarios["id"]}><td>
                        ${usuarios["nombre"]}</td><td>
                        ${usuarios["cedula"]}</td><td>
                        <a class="tareanueva"><i class="fas fa-wrench"></i></a></td>
                        </tr>`));  
                }
                  
            }

        }
    });

    // guarda las ubicaciones en variable global
    $.ajax({
        type: 'GET',
        url: 'api/tareas/ubicaciones',
        data: 'data',
        dataType: 'JSON',
        success: function (res) {
            if (res) {
                
                ubicaciones=res;

            }
        }
    });

/* ============================================================================================================================
                                                    EVENTOS   
============================================================================================================================*/

    $("#buscar").keyup(function (e) { 
        let input, filter, table, tr, nombres,cedulas;

        filter = this.value.toUpperCase();
        table = document.getElementById("TablaU");
        tr = table.getElementsByTagName("tr");
        for (let i = 0; i < tr.length; i++) {
            nombres = tr[i].getElementsByTagName("td")[0];
            cedulas =tr[i].getElementsByTagName("td")[1];
            if (nombres && cedulas) {
                if ((nombres.innerHTML.toUpperCase().indexOf(filter) > -1)||(cedulas.innerHTML.toUpperCase().indexOf(filter) > -1)) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    });

    // si se da click en el boton de ver o asignar ubicacion
    $('#TablaU tbody').on('click','a', function () {
        // se consigue el id del item y el nombre
        let iduser = $(this).closest('tr').attr('id');
        const nombre =  $('td:eq(0)', $(this).parents('tr')).text().replace(/(^\s+|\s+$)/g, '');
        const cedula =  $('td:eq(1)', $(this).parents('tr')).text().replace(/(^\s+|\s+$)/g, '');
        $('#iduser').html(iduser);
        $('#nombre').html(nombre);
        $('#cedula').html(cedula);

        agregarUbicaciones(iduser);
        
    });

    // si se da click en el boton de ver o asignar ubicacion
    $('#agregarubicacion').on('click', function () {

        swal({
            title: 'Seleccionar ubicacion',
            input: 'select',
            inputOptions: ubicaciones,
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
                
                const ubicacion = result.value;
                const iduser=$('#iduser').html();
                // return 0;
                $.ajax({
                    type: 'POST',
                    url: 'api/tareas/dettarea',
                    data: {'usuario':iduser,'ubicacion':ubicacion},
                    // dataType: 'JSON',
                    success: function (res) {
                        console.log(res);
                        if (res) {
                            
                            agregarUbicaciones(iduser);

                        } else {
                            swal({
                                type: 'error',
                                html: 'No se pudo agregar ubicacion'
                            });
                        }
                    }
                });
            }
        });
        // buscaubicaciones().done(function (res) {
            
        // });
        


        // agregarUbicaciones(iduser);
        
    });
    
});

/* ============================================================================================================================
                                                FUNCIONES   
============================================================================================================================*/
function agregarUbicaciones(iduser) {

    $.ajax({
        type: 'GET',
        url: 'api/tareas/dettarea',
        data: {'usuario':iduser},
        dataType: 'JSON',
        success: function (res) {

            // refresca ubicaciones
            $('#ubicaciones').html('');

            if (res) {

                for (let i in res) {
                    $('#ubicaciones').append(`<li class="collection-item">${res[i]}</li>`);
                }
                
            }else{
                
                $('#ubicaciones').html(`<li class="collection-item">No hay ubicaciones asignadas</li>`);
            }

            $('.modal').modal('open');
        }
    });

}

function buscaubicaciones(){

    return $.ajax({
        type: 'GET',
        url: 'api/tareas/ubicaciones',
        data: 'data',
        dataType: 'JSON'
    });
}