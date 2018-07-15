$(document).ready(function(){

    // INICIA DATATABLE
    table=iniciar_tabla();
    

    // pone items en el input select
    $.ajax({
        url:"ajax/requisicion.ajax.php",
        method:"POST",
        data: '',
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (res) {
            
            // SE MUESTRAN LAS REQUISICIONES EN EL MENU DE SELECCION
            for (var i in res) {

                $("#requeridos").append($('<option value="'+res[i]+'">'+res[i]+'</option>'));
                
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
       
        
        //muestra los items en la tabla
        table.destroy();
        //espera a que la funcion termine para reiniciar las tablas
        $.when(MostrarItems()).done(function(){
            //muestra la tabla y al reinicia
            $( "#TablaM" ).removeClass( "hide" );
            $( ".input_barras" ).removeClass( "hide" ); 
            table=iniciar_tabla();
        });

        // setInterval(function(){
        //     //muestra los items en la tabla
        // table.destroy();
        //     //espera a que la funcion termine para reiniciar las tablas
        //     $.when(MostrarItems()).done(function(){
        //         table=iniciar_tabla();
        //     });
        // }, 3000);  
            
    });

});

/* ============================================================================================================================
                                                   FUNCIONES   
============================================================================================================================*/
// FUNCION QUE INICIA DATATABLE
function iniciar_tabla(){
    
    
    var tabla= $(".tablas").DataTable({
                           
       responsive:true,
       
       "bLengthChange": false,
       "bFilter": true,
       "pageLength": 5,

       "language": {
           "sProcessing":     "Procesando...",
           "sZeroRecords":    "No se encontraron resultados",
           "sEmptyTable":     "Ningún dato disponible en esta tabla",
           "sInfo":           "Mostrando _START_ - _END_ de  _TOTAL_ registros",
           "sInfoEmpty":      "Mostrando 0 - 0 de 0 registros",
           "sInfoFiltered":   "(filtrado _MAX_ registros)",
           "sSearch":         "Buscar:",
           "sUrl":            "",
           "sInfoThousands":  ",",
           "sLoadingRecords": "Cargando...",
           "oPaginate": {
               "sFirst":    "Primero",
               "sLast":     "Último",
               "sNext":     "Siguiente",
               "sPrevious": "Anterior"
           },
           "oAria": {
               "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
               "sSortDescending": ": Activar para ordenar la columna de manera descendente"
           }
       }
   
   });

   return tabla;
   
}

// FUNCION QUE PONE LOS ITEMS  EN LA TABLA
function MostrarItems(){
        
    var item;
    //consigue el numero de requerido
    var requeridos=$(".requeridos").val();
    //id usuario es obtenida de las variables de sesion
    var Req=[requeridos,id_usuario];

    return $.ajax({

        url:"ajax/cajas.ajax.php",
        method:"POST",
        data: {"Req":Req},
        dataType: "JSON",
        error: function (xhr, status) {

            alert(status);

        },
        success: function (res) {

            var item=res['contenido'];
            
            //refresca la tabla, para volver a cargar los datos
           
            $('#tablavista').html("");
            table.clear();
            
            //si no encuentra el item muestra en pantalla que no se encontro
            if (res['estado']=="error"){
                
            }
            //en caso de contrar el item mostrarlo en la tabla
            else{
                
                // $('#Rerror').hide();
                
                var item=res['contenido'];
                
                for (var i in item) {
                    $('#tablavista').append($("<tr><td>"+
                                        item[i]['codigo']+"</td><td>"+
                                        item[i]['referencia']+"</td><td>"+
                                        item[i]['descripcion']+"</td><td>"+
                                        item[i]['disponibilidad']+"</td><td>"+
                                        item[i]['pedidos']+"</td><td>"+
                                        item[i]['alistados']+"</td><td>"+
                                        item[i]['ubicacion']+"</td></tr>"));  
                    
                }                  
                
            }

        }

  }); 

} 