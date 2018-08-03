$(document).ready(function () {
    
    /* ============================================================================================================================
                                                        INICIALIZACION   
    ============================================================================================================================*/
    // INICIA DATATABLE
    table=iniciar_tabla();

    //inicia modal
    $('.modal').modal();

    $.ajax({
        type: "POST",
        url: "ajax/usuarios.usuarios.ajax.php",
        data: "data",
        dataType: "JSON",
        success: function (res) {
            
            if (res['estado'] == 'encontrado') {
                var usuarios=res['contenido'];
                
                var tabla = $('table.tablas').DataTable();
                for (var i in usuarios) {
                     
                    tabla.row.add( [
                        usuarios[i]['id'],
                        usuarios[i]['usuario'],
                        usuarios[i]['nombre'],
                        usuarios[i]['cedula'],
                        usuarios[i]['perfil'],
                        "<button  title='Editar Usuario' data-target='editarusuario' class='editar modal-trigger btn-small waves-effect waves-light green darken-3 ' >" +
                            "<i class='fas fa-user-edit'></i>" +
                        "</button>"+
                        "<button  title='Eliminar Usuario' class='eliminar  btn-small waves-effect waves-light red darken-3 ' >" +
                            "<i class='fas fa-user-times'></i>" +
                        "</button>"
                    ] ).draw(false);
                }   
            }
        }
    });

    /* ============================================================================================================================
                                                        EVENTOS   
    ============================================================================================================================*/
    $('#TablaU').on('click', 'button.editar', function (e) {
        
    } );
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
        "pageLength": 5,

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