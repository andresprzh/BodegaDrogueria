$(document).ready(function () {
    
    /* ============================================================================================================================
                                                        INICIALIZACION   
    ============================================================================================================================*/
    // INICIA DATATABLE
    table=iniciar_tabla();

    //inicia modal
    $('.modal').modal();
    
     // CARGA DATO AL MENU DE SELECCION DE PERFILES
    $.when(CargarPerfiles()).done(function () {
        
        // espera a que termine de cargar los perfiles
        // INICIA TABLA USUARIOS
        CargarUsuarios();
    });
        

    /* ============================================================================================================================
                                                        EVENTOS   
    ============================================================================================================================*/
    // si se da click en agregar o editar usuario
    $(document).on('click', '.modal-trigger', function () {
        
        var tabla=$('table.tablas').DataTable();
        // si se da click en agregar usuario
        if (this.id=='addusuario') {

            //obtiene el numero de id mayor
            var id=Math.max(...tabla.column(0).data().toArray());
            id++;//id para nuevo usuario
            
            //cambia titulo modal
            $('#iduser').html(id);
        
            $('#nombre').val("");
            $('#cedula').val("");
            $('#usuario').val("");
            $('#perfil').val(3);
            // INICIA MENU DE SELECCION
            $('select').formSelect();

            // Muestra el boton agregar y oculta el boton de modificar
            $('#agregar').removeClass('hide');
            $('#modificar').addClass('hide');

        // si se da clicken editar usuario
        }else{
            // guarda el valor dle input en datatable
            
            
            var celda=$(this).parents('td');
            var fila = tabla.row(celda);
            
            // si la tabla es responsive
            if(fila.data() == undefined) {
                var fila = $(this).parents('tr');
                if (fila.hasClass('child')) {
                    fila = fila.prev();
                    
                }
            } 
            var datos=tabla.row(fila).data();

            //obtiene el numero de perfil
            var perfil = $(fila).find(".perfiles").attr('name');

            // cambia titulo modal
            $('#iduser').html(datos[0]);
            
            $('#nombre').val(datos[2]);
            $('#cedula').val(datos[3]);
            $('#usuario').val(datos[1]);
            
            $('#perfil').val(perfil);            
            // INICIA MENU DE SELECCION
            $('select').formSelect();

            // Muestra el boton modificar y oculta el boton de agregar
            $('#modificar').removeClass('hide');
            $('#agregar').addClass('hide');

        }
    } );

    // si se agrga o modifica usuarios
    $('.modal').on('submit', 'form', function (event) {
        var buttonid = $(this).find("button:focus").attr('id');
        
        
        event.preventDefault();
        swal({
            title: "¿Agregar Usuario?",
            icon: "warning",
            buttons: ['Cancelar', 'Agregar']
        })
            .then((Cerrar) => {
                var datosusuario = new Array()
                datosusuario = {
                    'id': $('#iduser').html(),
                    'nombre': $('#nombre').val(),
                    'cedula': $('#cedula').val(),
                    'usuario': $('#usuario').val(),
                    'password': $('#password').val(),
                    'perfil': $('#perfil').val()
                };
                var tabla = $('table.tablas').DataTable();                               
                

                $.ajax({
                    type: "POST",
                    url: "ajax/usuarios.modificar.ajax.php",
                    data: {"datosusuario":datosusuario,"button":buttonid},
                    dataType: "JSON",
                    success: function (res) {
                        
                        $('#iduser').html(""),
                        $('#nombre').val(""),
                        $('#cedula').val(""),
                        $('#usuario').val(""),
                        $('#password').val(""),
                        $('#perfil').val("")
                        // tabla
                        var tabla = $('table.tablas').DataTable();
                        var perfiles = $("#perfil>option").map(function() { return $(this).html(); });
                        var perfil="<span class='perfiles' name='"+datosusuario['perfil']+"'>"+perfiles[datosusuario['perfil']]+"</span>";
                        if (res) {
                            if (buttonid=="agregar") {
                                swal({
                                    title: "Usuario Agregado",
                                    icon: "success",
                                })
                                .then(()=>{
                                    
                                    // muestra el nuevo usuario en la tabla
                                    tabla.row.add( [
                                        datosusuario['id'],
                                        datosusuario['usuario'],
                                        datosusuario['nombre'],
                                        datosusuario['cedula'],
                                        perfil,
                                        "<button  title='Editar Usuario' data-target='editarusuario' class='editar modal-trigger btn-small waves-effect waves-light tea darken-1 ' >" +
                                            "<i class='fas fa-user-edit'></i>" +
                                        "</button>"
                                    ] ).draw(false);

                                }) 
                            }else{
                                swal({
                                    title: "Usuario modificado",
                                    icon: "success",
                                }).then(()=>{
                                    console.log(datosusuario);
                                    // muestra nuevos datos en la tabla
                                    
                                    tabla.row(datosusuario['id']-1).data( [
                                        datosusuario['id'],
                                        datosusuario['usuario'],
                                        datosusuario['nombre'],
                                        datosusuario['cedula'],
                                        perfil,
                                        "<button  title='Editar Usuario' data-target='editarusuario' class='editar modal-trigger btn-small waves-effect waves-light tea darken-1 ' >" +
                                            "<i class='fas fa-user-edit'></i>" +
                                        "</button>"
                                    ] ).draw(false);

                                }) ;
                            }
                            // cierra el modal
                            $('.modal').modal('close'); 


                        }else{
                            swal({
                                title: "No se pudo agregar o modificar el usuario",
                                icon: "error",
                            });
                        }
                    }
                });
                
            });
    });

    
    
});
/* ============================================================================================================================
                                                    FUNCIONES   
    ============================================================================================================================*/
    
// FUNCION QUE INICIA DATATABLE
function iniciar_tabla() {


    var tabla = $("table.tablas").DataTable({

        responsive: true,

        "bLengthChange": false,
        "bFilter": true,
        "pageLength": 10,

        "language": {
            "sProcessing": "Procesando...",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando _START_ - _END_ de  _TOTAL_ registros",
            "sInfoEmpty": "Mostrando 0 - 0 de 0 registros",
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
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }

    });

    return tabla;

}

function CargarPerfiles() {
    
    return $.ajax({
        type: "POST",
        url: "ajax/usuaios.perfiles.ajax.php",
        data: "data",
        dataType: "JSON",
        success: function (res) {
            if (res['estado'] == 'encontrado') {
                var perfiles=res['contenido'];
                                
                for (var i in perfiles) {
                    $("#perfil").append($('<option value="'+perfiles[i]['id']+'">'+perfiles[i]['perfil']+'</option>'));
                }   

                // INICIA MENU DE SELECCION
                $('select').formSelect();
            }
        }
    });
    
}

function CargarUsuarios() {
    
    return $.ajax({
        type: "POST",
        url: "ajax/usuarios.usuarios.ajax.php",
        data: "data",
        dataType: "JSON",
        success: function (res) {
            
            if (res['estado'] == 'encontrado') {
                var usuarios=res['contenido'];
                var perfil;//guarda el perfil del usuario
                var tabla = $('table.tablas').DataTable();

                var perfiles = $("#perfil>option").map(function() { return $(this).html(); });

                // si el resultado es n array
                if (usuarios.constructor===Array) {
                    for (var i in usuarios) {
                    
                        // obtiene el perfil del usuario dle menu de seleccion cargado anteriormente
                        perfil="<span class='perfiles' name='"+usuarios[i]['perfil']+"'>"+perfiles[usuarios[i]['perfil']]+"</span>";
                        
                        tabla.row.add( [
                            usuarios[i]['id'],
                            usuarios[i]['usuario'],
                            usuarios[i]['nombre'],
                            usuarios[i]['cedula'],
                            perfil,
                            "<button  title='Editar Usuario' data-target='editarusuario' class='editar modal-trigger btn-small waves-effect waves-light tea darken-1 ' >" +
                                "<i class='fas fa-user-edit'></i>" +
                            "</button>"
                        ] ).draw(false);
                    } 
                // si solo hay 1 dato en usuarios
                }else{
                    
                    // obtiene el perfil del usuario dle menu de seleccion cargado anteriormente
                    perfil="<span class='perfiles' name='"+usuarios['perfil']+"'>"+perfiles[usuarios['perfil']]+"</span>";
                    
                    tabla.row.add( [
                        usuarios['id'],
                        usuarios['usuario'],
                        usuarios['nombre'],
                        usuarios['cedula'],
                        perfil,
                        "<button  title='Editar Usuario' data-target='editarusuario' class='editar modal-trigger btn-small waves-effect waves-light tea darken-1 ' >" +
                            "<i class='fas fa-user-edit'></i>" +
                        "</button>"
                    ] ).draw(false);

                }
                  
            }
        }
    });
}