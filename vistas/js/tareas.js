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
        // Busca los datos en la tabla
        let lista = document.getElementById('ubicaciones');
        let li = lista.getElementsByTagName('li');
        let items = new Array;
        
        // var ubicacioneslista = li.map(function(x) {
        //     return x.id.substr(1)
        //  });
        let ubicacioneslista=new Array;
        for (let i = 0; i < li.length; i++) {
            ubicacioneslista[li[i].id] = li[i].id;
        }
        
        // solo muestra ubicaciones que no estan asignadas
        newubicaciones=diff(ubicaciones,ubicacioneslista);
        
        swal({
            title: 'Seleccionar ubicacion',
            input: 'select',
            inputOptions: newubicaciones,
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
                    dataType: 'JSON',
                    success: function (res) {
                        
                        if (res) {
                            
                            agregarUbicaciones(iduser);

                        } else {
                            var toastHTML = `<span class="truncate">No se pudo agregar ubicación</span>`;
                            M.toast({ html: toastHTML, 
                                classes: "red darken-4",
                                displayLength:1500 
                            });
                        }
                    }
                });
            }
        });
        
    });

    $('#listtareas').on('click','a', function () {
        let ubicacion = $(this).closest('li').attr('id');
        const iduser=$('#iduser').html();

        $.ajax({
            type: 'POST',
            url: 'api/tareas/dettarea',
            data: {'usuario':iduser,'ubicacion':ubicacion,'eliminar':true},
            dataType: 'JSON',
            success: function (res) {
                
                if (res) {
                    
                    agregarUbicaciones(iduser);

                } else {
                    
                    var toastHTML = `<span class="truncate">No se pudo eliminar ubicación</span>`;
                    M.toast({ html: toastHTML, 
                        classes: "red darken-4",
                        displayLength:1500 
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
        data: {'usuario':iduser},
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
                
            }else{
                
                $('#ubicaciones').html(`<li class="collection-item">No hay ubicaciones asignadas</li>`);
            }

            $('.modal').modal('open');
        }
    });

}


// obtiene conjunto diferencia entre 2 arreglos u objetos
function diff(a,b) {
    c=new Array();
    
    for (var i in a) {
        
        for(var j in b){
            var include=true;
            if (a[i]==b[j]) {
                include=false;
                break;
            }
        }
        if (include) {
            c[i]=a[i];
        }
    }
    return c;
}