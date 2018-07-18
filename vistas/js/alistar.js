$(document).ready(function(){

/* ============================================================================================================================
                                                    INICIALIZACION   
============================================================================================================================*/
    // INICIA DATATABLE
    table=iniciar_tabla();
    // INICIAR TABS
    $('.tabs').tabs({ 'swipeable': true });
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
       
        
        //muestra los items en la tabla
        table.destroy();
        //espera a que la funcion termine para reiniciar las tablas
        $.when(MostrarItems()).done(function(){
            //muestra la tabla y la reinicia
            $( "#contenido" ).removeClass( "hide" );
            
            $( ".input_barras" ).removeClass( "hide" ); 
            $('.tabs').tabs({ 'swipeable': true });//reinicia el tabs
            
            table=iniciar_tabla();
            
        });

    });


    // EVENTO INPUT  CODIGO DE BARRAS
 	$("#codbarras").keydown(function (e) {
        // permite: spacio, eliminar , tab, escape, enter y  .
       if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // permite: Ctrl+letra, Command+letra
           ((e.keyCode >=0 ) && (e.ctrlKey === true || e.metaKey === true)) || 
            // permite: home, fin, izquierda, derecha, abajo, arriba
           (e.keyCode >= 35 && e.keyCode <= 40)) {
                // no hace nada si cumple la condicion
                return;
       }
       // solo acepta numeros
       if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
           //previene mandar los datos al input
           e.preventDefault();
       }		
    });
    

    // EVENTO INPUT  CODIGO DE BARRAS
    $("#codbarras").keypress(function (e) {
    
    //si se presiona enter busca el item y lo pone en la pagina
    if (e.which == 13  ) {
            
            //muestra el item buscado en la tabla editable y vuelve a cargar los items en la caja
            table.destroy();
            //espera a que las 2 funciones terminen para reiniciar las tablas
            $.when(MostrarItems(),BuscarCodBar()).done(function(){
                
                // reinicia tabla y muestra la tabla editable
                table=iniciar_tabla();
                $( "#tabsI2" ).removeClass( "disabled" );
                $('.tabs').tabs('select','TablaE');  

            });
        }

    });

    
    // EVENTO SI SE PRESIONA EL BOTON DE AGREGAR CODIGO DE BARRAS(+)
    $("#agregar").click(function (e) {
        
        //muestra el item buscado en la tabla editable y vuelve a cargar los items en la caja
        table.destroy();
        //espera a que las 2 funciones terminen para reiniciar las tablas
        $.when(MostrarItems(),BuscarCodBar()).done(function(){
            
            table=iniciar_tabla();
            $( "#tabsI2" ).removeClass( "disabled" );
            $('.tabs').tabs('select','TablaE');

        });

    });


    // EVENTO SI SE PRESIONA EL BOTON CERRAR
    $("#cerrar").click(function (e) {  
        
        //consigue el numero de requerido
        var requeridos=$(".requeridos").val();
        //id usuario es obtenida de las variables de sesion
        var Req=[requeridos,id_usuario];
        
        //si se presiona aceptar se continua con el proceso
        swal({
            title: "¿Cerrar caja?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((cerrar) => {

            //si se le da click en cerrar procede a pasar los items a la caja y a cerrarla
            if (cerrar) {

                var alistados = $(".alistados");//arreglo cantidad de alistados de todos los items cargados
                var barras = $(".barras");//arreglo codigo de barra de todos lo items cargados.
                //guarda en un vector todos los datos de la caja
                var TipoCaja=$("#caja").val();
                var Items=new Array();
                
                for(var i = 0; i < alistados.length; i++){

                    //guarda en una matriiz los datos de los items(# alistados y codigode barras)        
                    
                    Items[i]={
                        "codigo":$(barras[i]).html(),
                        "alistados": $(alistados[i]).val()
                    }    
                    
                }
                 
                $.ajax({
                    url: 'ajax/alistar.empacar.ajax.php',//url de la funcion
                    method: 'post',//metodo post para mandar datos
                    data: { 'Req':Req,"TipoCaja":TipoCaja,"Items":Items},//datos que se enviaran          
                    success: function (res) {
                    console.log(res);

                        if (res) {

                            swal("¡Caja cerrada exitosamente!", {
                                    icon: "success",
                                  })
                                  .then((event)=>{

                                    location.reload(true);

                                  });
                                  
                        }else{

                            swal("¡Error al cerrar la caja!", {
                                icon: "error",
                              });

                        }
                        // location.reload(true);
                    }
                }); 
            }
          });

    });

});




/* ============================================================================================================================
                                                FUNCIONES   
============================================================================================================================*/

// FUNCION QUE BUSCA EL CODIGO DE BARRAS
function BuscarCodBar(){
    
    //consigue el codigo de barras
    codigo= $('#codbarras').val();
    //consigue el numero de requerido
    var requeridos=$(".requeridos").val();
    //id usuario es obtenida de las variables de sesion
    var Req=[requeridos,id_usuario];
    
    //ajax para ejecutar un script php mandando los datos
    return $.ajax({
      url: 'ajax/alistar.items.ajax.php',//url de la funcion
      type: 'post',//metodo post para mandar datos
      data: {"codigo":codigo,"Req":Req},//datos que se enviaran
      dataType: 'json',
      error: function (xhr, status) {

        alert(status);

        },
      success: function (res) {
        AgregarItem(res);
      }
      
    }); 


}


//FUNCION QUE AGREGA ITEM A LA TABLA EDITABLE
function AgregarItem(res){
    //busca el estado de del resultado
    switch (res['estado']) {
     //si encontro el codigo de barras muestar el contenido de la busqueda
     case 'encontrado':
       
       var item=res['contenido'];

       $('#tablaeditable').append($("<tr><td class='barras'>"+
                           item['codigo']+"</td><td>"+
                           item['referencia']+"</td><td>"+
                           item['descripcion']+"</td><td>"+
                           item['disponibilidad']+"</td><td>"+
                           item['pedidos']+"</td><td> <input type= 'number' class='alistados' value="+
                           item['alistados']+"></input></td><td>"+
                           item['ubicacion']+"</td></tr>"));                  
        // muestra la tabla y los input de cerrar caja
                
       break;
       

     //si no encontro el item regresa el contenido del error(razon por la que no lo encontro)
     default:
        swal(res['contenido'], {
            icon: "warning",
        })
    //    $('#error').show();
    //    $("#error").html(res['contenido']);
       break;
   }
 }


// FUNCION QUE PONE LOS ITEMS  EN LA TABLA
function MostrarItems(){
        
    var item;
    //consigue el numero de requerido
    var requeridos=$(".requeridos").val();
    //id usuario es obtenida de las variables de sesion
    var Req=[requeridos,id_usuario];

    return $.ajax({

        url:"ajax/alistar.items.ajax.php",
        method:"POST",
        data: {"Req":Req},
        dataType: "JSON",
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