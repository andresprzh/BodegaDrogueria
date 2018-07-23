$(document).ready(function(){
/* ============================================================================================================================
                                                INICIAN  COMPONENTE DE LA PAGINA
============================================================================================================================*/

    //INICIA EL MODAL
    $('.modal').modal();
    
    // INICIA DATATABLE
    table=iniciar_tabla('.tablas');
     
    // pone items en el input select
    $.ajax({
        url:"ajax/alistar.requisicion.ajax.php",
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
       
               
       
        //muestra la tabla y la reinicia
        $( "#Cajas" ).removeClass( "hide" );
        //refresca la tabla, para volver a cargar los datos
        $('#tablacajas').html("");
        table.clear();
        table.destroy();


        //espera a que la funcion termine para reiniciar las tablas
        $.when(MostrarCajas()).done(function(){
            
            table=iniciar_tabla("#TablaC");

        });
            
    });

    $("#Documento").click(function (e) {
        //consigue el numero de requerido
        var requeridos=$(".requeridos").val();
        //id usuario es obtenida de las variables de sesion
        var Req=[requeridos,id_usuario];
        var NumCaja=$('#NumeroCaja').html();

        $.ajax({
                
            url:"ajax/cajas.documento.ajax.php",
            method:"POST",
            data: {"Req":Req,"NumCaja":NumCaja,"Mensaje":"PRUEBA DE CARGA PLA"},
            
            success: function (res) {

                // si hay un error al buscar los archivos no genera el documento
                if (res=="error") {
                    swal({
                        title: "!Error al generar el documento¡",
                        icon: "error",
                        buttons: true,
                        dangerMode: true,
                      });

                // si no hay error genera le documento y lo manda a decargar
                }else{
                
                    var element = document.createElement('a');
                    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(res));
                    element.setAttribute('download', "texto.P01");

                    element.style.display = 'none';
                    document.body.appendChild(element);

                    element.click();

                    document.body.removeChild(element);

                }                     
            }
        });

    });

});

/* ============================================================================================================================
                                                   FUNCIONES   
============================================================================================================================*/

// FUNCION QUE PONE LOS ITEMS  EN LA TABLA
function MostrarCajas(){
        
    var item;
    //consigue el numero de requerido
    var requeridos=$(".requeridos").val();
    //id usuario es obtenida de las variables de sesion
    var Req=[requeridos,id_usuario];

    return $.ajax({

        url:"ajax/cajas.cajas.ajax.php",
        method:"POST",
        data: {"Req":Req},
        dataType: "JSON",
        success: function (res) {
            
            var caja=res['contenido'];
            
            
            
            //si no encuentra la caja muestra en pantalla que no se encontro
            if (res['estado']=="error"){
                
            }
            //en caso de contrar el item mostrarlo en la tabla
            else{
                
                var caja=res['contenido'];
                
                for (var i in caja) {
                    
                    $('#tablacajas').append($("<tr>"+
                                        "<td class='NumCaja'>"+caja[i]['no_caja']+"</td>"+
                                        "<td class='alistadores'>"+caja[i]['alistador']+"</td>"+
                                        "<td class='tipocajas'>"+caja[i]['tipocaja']+"</td>"+
                                        "<td>"+caja[i]['abrir']+"</td>"+
                                        "<td class='cierres'>"+caja[i]['cerrar']+"</td><td>"+
                                        "<button  onclick='MostrarItemsCaja("+i+")'  title='Revisar'  data-target='EditarCaja' class='btn modal-trigger waves-effect waves-light green darken-3'>"+
                                            "<i class='fas fa-file-alt'></i>"+
                                        "</button></td>"+
                                        "</tr>"));  

                    $( "#TablaCajas" ).removeClass( "hide" );

                }                  
                
            }

        }

  }); 

} 

function MostrarItemsCaja(e) {
    

    //obtienen los datos de la caja para pasarlo al modal
    var datos=table.row(e).data();
    
    var NumCaja=datos[0];
    var alistador=datos[1];
    var tipocaja=datos[2];
    var cierre=datos[4];

    
    // se muestran los datos generales de la caja
    $('#NumeroCaja').html(NumCaja);
    $('#alistador').html(alistador);
    $('#tipocaja').html(tipocaja);
    $('#cierre').html(cierre);
    
    // destruye la datatable 2(tabla del modal)
    var dt = $.fn.dataTable.tables()[1];
    $('#tablamodal').html("");
    $(dt).DataTable().clear();
    $(dt).DataTable().destroy();
    
    //espera a que la funcion termine para reiniciar las tablas
    $.when(MostrarItems(NumCaja)).done(function(){
        //Reinicia Tabla
        table[1]=iniciar_tabla("#TablaM");
    });
}

// FUNCION QUE PONE LOS ITEMS  EN LA TABLA
function MostrarItems(NumCaja){

    var item;
    //consigue el numero de requerido
    var requeridos=$(".requeridos").val();
    //id usuario es obtenida de las variables de sesion
    var Req=[requeridos,id_usuario];

    return $.ajax({

        url:"ajax/cajas.cajas.ajax.php",
        method:"POST",
        data: {"Req":Req, "NumCaja":NumCaja},
        dataType: "JSON",
        success: function (res) {
            
            var item=res['contenido'];
 
            //si no encuentra el item muestra en pantalla que no se encontro
            if (res['estado']=="error"){
                
            }
            //en caso de contrar el item mostrarlo en la tabla
            else{
                
                // $('#Rerror').hide();
                
                var item=res['contenido'];
                
                for (var i in item) {
                    $('#tablamodal').append($("<tr><td>"+
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

// FUNCION QUE INICIA DATATABLE
function iniciar_tabla(tab){
    
    tabla= $(tab).DataTable({
                        
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