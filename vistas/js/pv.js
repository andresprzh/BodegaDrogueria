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
           
            //consigue el numero de requerido
            var requeridos=$(".requeridos").val();
            //id usuario es obtenida de las variables de sesion
            var Req=[requeridos,id_usuario];
            $('#cajas').html('<option value="" disabled selected>Seleccionar</option>');
            
            $.ajax({
                url: 'ajax/pv.cajas.ajax.php',//url de la funcion
                type: 'post',//metodo post para mandar datos
                data: {"Req":Req},//datos que se enviaran
                dataType: "JSON",
                success: function (res) {
                    
                    if (res!==false) {
                        // SE MUESTRAN LAS REQUISICIONES EN EL MENU DE SELECCION
                        for (var i in res) {
            
                            $("#cajas").append($('<option value="'+res[i]+'">'+res[i]+'</option>'));
                            
                        }
                    }
                    
        
                    // INICIA MENU DE SELECCION
                    $('select').formSelect();
                }
                
              }); 
            
    
        });
        
        // EVENTO AL CAMBIAR LA ENTRADA DE CAJAS
        $("#cajas").change(function (e) { 
            $( ".input_barras" ).removeClass( "hide" ); 
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
                    
                        table=iniciar_tabla();
                        
                    
    
                });
            }
    
        });
    
        
        // EVENTO SI SE PRESIONA EL BOTON DE AGREGAR CODIGO DE BARRAS(+)
        $("#agregar").click(function (e) {
            
            //muestra el item buscado en la tabla editable y vuelve a cargar los items en la caja
            table.destroy();
            //espera a que las 2 funciones terminen para reiniciar las tablas
            
            $.when(BuscarCodBar()).done(function(){
                
                    table=iniciar_tabla();
                    
                
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
          url: 'ajax/pv.items.ajax.php',//url de la funcion
          type: 'post',//metodo post para mandar datos
          data: {"codigo":codigo,"Req":Req},//datos que se enviaran
          dataType: 'json',
          error: function (xhr, status) {
    
            alert(status);
    
            },
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