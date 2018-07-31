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
       
        //destruye datatabel para reiniciarla
        table.destroy();
        //espera a que la funcion termine para reiniciar las tablas
        $.when(MostrarItems(),MostrarCaja()).done(function(){
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
            $.when(BuscarCodBar()).done(function(){
                $.when(MostrarItems()).done(function(){
                    table=iniciar_tabla();
                });

            });
        }

    });

    
    // EVENTO SI SE PRESIONA EL BOTON DE AGREGAR CODIGO DE BARRAS(+)
    $("#agregar").click(function (e) {
        
        //muestra el item buscado en la tabla editable y vuelve a cargar los items en la caja
        table.destroy();
        //espera a que las 2 funciones terminen para reiniciar las tablas
        $.when(BuscarCodBar()).done(function(){
            $.when(MostrarItems()).done(function(){
                table=iniciar_tabla();
            });
        });

    });

    // EVENTO CUANDO SE MODIFICA UNA CELDA DE LA TABLA
    $('#tablaeditable').on( 'change', 'td', function () {
        
        var dt = $.fn.dataTable.tables()[1];
        var tabla=$(dt).DataTable();

        //se obtiene el valor de la variable y se le asigna a datatable para que quede guardado
        celda=table.cell(this);
        // var nuevovalor = $(this).find("input").val();
        // celda.data("<input  type= 'number' min='0' class='alistados'  value='"+nuevovalor+"'></input>");

        var nuevovalor = $(this).find("input").val();
        var fila = table.row(this)

        // si la tabla es responsive
        if(fila.data() == undefined) {

            var fila = $(this).parents('tr');
            if (fila.hasClass('child')) {
                fila = fila.prev();
            }
            tabla.row(fila).cell(fila,5).data("<input  type= 'number' min='0' class='alistados'  value='"+nuevovalor+"'></input>");

        } else {
            
            tabla.cell(this).data("<input  type= 'number' min='0' class='alistados'  value='"+nuevovalor+"'></input>");
        }
        
    } );


    
    // EVENTO SI SE PRESIONA EL BOTON CERRAR
    $("#cerrar").on('click',function (e) {  
        
        
        //consigue el numero de requerido
        var requeridos=$(".requeridos").val();
        //id usuario es obtenida de las variables de sesion
        var Req=[requeridos,id_usuario];
        
        //si se presiona aceptar se continua con el proceso
        swal({
            title: "¿Cerrar caja?",
            icon: "warning",
            buttons: ['Cancelar','Cerrar']
          })
          .then((Cerrar) => {

            //si se le da click en cerrar procede a pasar los items a la caja y a cerrarla
            if (Cerrar) {
                
                var dt = $.fn.dataTable.tables()[1];
                
                
                var datos=$(dt).DataTable().data().toArray();
                
                // console.log();
                var Items=new Array();
                for (var i in datos) {

                        Items[i]={
                            "codigo":datos[i][0],
                            "alistados": $(datos[i][5]).val()
                        }                
                    
                }
                console.log(Items);
                

                //guarda el tipo de caja en una variable
                var TipoCaja=$("#caja").val();
                
                 
                $.ajax({
                    url: 'ajax/alistar.empacar.ajax.php',//url de la funcion
                    method: 'post',//metodo post para mandar datos
                    data: { 'Req':Req,"TipoCaja":TipoCaja,"Items":Items},//datos que se enviaran          
                    success: function (res) {


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
    
    // ajax para ejecutar un script php mandando los datos
    return $.ajax({
      url: 'ajax/alistar.items.ajax.php',//url de la funcion
      type: 'post',//metodo post para mandar datos
      data: {"codigo":codigo,"Req":Req},//datos que se enviaran
      dataType: 'json',
      success: function (res) {
        // console.log(res);
        AgregarItem(res);
      }
      
    }); 


}


//FUNCION QUE AGREGA ITEM A LA TABLA EDITABLE
function AgregarItem(res){
    //busca el estado de del resultado
     //si encontro el codigo de barras muestar el contenido de la busqueda
    if (res['estado']=='encontrado') {

        var items=res['contenido'];
       
        $('#tablaeditable').append($("<tr><td class='barras'>"+
                            items['codigo']+"</td><td>"+
                            items['referencia']+"</td><td>"+
                            items['descripcion']+"</td><td>"+
                            items['disponibilidad']+"</td><td>"+
                            items['pedidos']+"</td><td> <input type= 'number' min='0'; class='alistados' value="+
                            items['alistados']+"></input></td><td>"+
                            items['ubicacion']+"</td></tr>"));                  
        
        $( "#TablaE" ).removeClass( "hide" );

        // se muestra un mensaje con el item agregado
        var toastHTML = '<p class="truncate">Agregado Item <span class="yellow-text">'+items['descripcion']+'</span></p>';
        M.toast({html: toastHTML, classes:"light-green darken-4 rounded"});
       
    //si no encontro el item regresa el contenido del error(razon por la que no lo encontro)
   }else{
        swal(res['contenido'], {
            icon: "warning",
        })
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
            
            //refresca las tablas, para volver a cargar los datos
            $('#tablavista').html("");
            table.clear();
            //si ecnuentra el item mostrarlo en la tabla
            if (res['estado']!="error"){
                // $('#Rerror').hide(); 
                var items=res['contenido'];
                
                for (var i in items) {
                    $('#tablavista').append($("<tr><td>"+
                                        items[i]['codigo']+"</td><td>"+
                                        items[i]['referencia']+"</td><td>"+
                                        items[i]['descripcion']+"</td><td>"+
                                        items[i]['disponibilidad']+"</td><td>"+
                                        items[i]['pedidos']+"</td><td>"+
                                        items[i]['alistados']+"</td><td>"+
                                        items[i]['ubicacion']+"</td></tr>"));  
                    
                } 
                
            }

        }

    }); 

} 


// FUNCION QUE CREA O MUESTRA UNA CAJA
function MostrarCaja() {

    var item;
    //consigue el numero de requerido
    var requeridos=$(".requeridos").val();
    //id usuario es obtenida de las variables de sesion
    var Req=[requeridos,id_usuario];
    
    return $.ajax({

        url:"ajax/alistar.cajas.ajax.php",
        method:"POST",
        data: {"Req":Req},
        dataType: "JSON",
        success: function (res) {          
            console.log(res);

            //refresca las tablas, para volver a cargar los datos
            $('#tablaeditable').html("");
            table.clear();
            // si la caja ya esta creada muestra los items en la tabla de alistar
            if (res['estadocaja']=='yacreada') {

                    //si encontro el codigo de barras muestar el contenido de la busqueda
                    if (res['estado']=='encontrado') {
                        var items=res['contenido'];

                        //refresca las tablas, para volver a cargar los datos
                        $('#tablaeditable').html("");
                        table.clear();
                        
                        for (var i in items) {                
                                    $('#tablaeditable').append($("<tr><td class='barras'>"+
                                                    items[i]['codigo']+"</td><td>"+
                                                    items[i]['referencia']+"</td><td>"+
                                                    items[i]['descripcion']+"</td><td>"+
                                                    items[i]['disponibilidad']+"</td><td>"+
                                                    items[i]['pedidos']+"</td><td > <input type= 'number' min='0' class='alistados' value="+
                                                    items[i]['alistados']+"></input></td><td>"+
                                                    items[i]['ubicacion']+"</td></tr>"));             
                        }
                        // muestra la tabla
                        $( "#TablaE" ).removeClass( "hide" );
                    // si hay una caja sin cerrar en otra requisicion muestra mensaje adventencia y recarga la pagina          
                    }else if (res['estado']=='error2') {
                        swal({
                            title: "!No se puede generar caja¡",
                            text:res['contenido'],
                            icon: "warning",
                          })
                          .then((ok) => {
                            location.reload();
                          }); 
                    }

            }

        }

    }); 
} 



// FUNCION QUE INICIA DATATABLE
function iniciar_tabla(){
    
    
     var tabla= $("table.tablas").DataTable({
                            
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