$(document).ready(function(){

    /* ============================================================================================================================
                                                        INICIALIZACION   
    ============================================================================================================================*/
        // INICIA DATATABLE
        table=iniciar_tabla();
        
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
            // muestra la entrada se seleccion de cajas
            $( ".SelectCaja" ).removeClass( "hide" ); 
            // oculta el input donde se ingresa el codigo de barras
            $( ".input_barras" ).addClass( "hide" );
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
                        // SE MUESTRAN LAS CAJAS EN EL MENU DE SELECCION
                        for (var i in res) {
            
                            $("#cajas").append($('<option value="'+res[i]+'">Caja '+res[i]+'</option>'));
                            
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
                    
                BuscarCodBar()
            }
    
        });
    
        
        // EVENTO SI SE PRESIONA EL BOTON DE AGREGAR CODIGO DE BARRAS(+)
        $("#agregar").click(function (e) {
            
            // busca el codigo y lo agrega a la tabla 
            BuscarCodBar();
    
        });

        // EVENTO SI SE PRESIONA EL BOTON DE GENERAR
        $('#Registrar').click(function (e) { 

            swal({
                title: "¿Registrar items?",
                icon: "warning",
                buttons: ['Cancelar','Resgitrar']
                
            })
            .then((Registrar) => {
    
                //si se le da click en Resgitrar procede a generar e reporte
                if (Registrar) {
                    
                    //consigue el codigo de barras
                    var Caja= $('#cajas').val();
                    
                    //consigue el numero de requerido
                    var requeridos=$(".requeridos").val();
                    //id usuario es obtenida de las variables de sesion
                    var Req=[requeridos,id_usuario];

                    // console.log(Req);
                    var tabla = $('table.tablas').DataTable();
                    // se obtienen todos los datos de la tabla en una matriz
                    var datos=tabla.data().toArray();   
                    
                    // se guerda en un arreglo los datos de codigo de Baras y la cantidad recibido                    
                    var Items=new Array();
                    for (var i in datos) {
                        Items[i]={
                            "codbarras":datos[i][0],
                            "recibidos": datos[i][3]
                        }                
                    }
                    // console.log(Items);

                    $.ajax({
                        
                        url: "ajax/pv.registrar.ajax.php",
                        type: 'post',//metodo post para mandar datos
                        data: {"Caja":Caja,"Req":Req,"Items":Items},//datos que se enviaran
                        dataType: "JSON",
                        success: function (res) {
                            console.log(res);  
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
        var codigo= $('#codbarras').val();
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
          success: function (res) {

                var tabla = $('table.tablas').DataTable();
                var datos=tabla.data().toArray()
                var codbarras=datos.map(function(value,index) { return value[0]; });
                
            if (res["estado"]=='encontrado') {
                var cantr=1;
                
                //busca si el item ya esta n la tabla
                var pos = codbarras.indexOf(codigo);
                
                //si encuentra el item en la tabla acumula el item en la columna de recibido
                if (pos>=0) {
                    
                    // se acumula la cantidad recibida
                    cantr+=datos[pos][3];

                    tabla.cell(pos,3).data(cantr).draw();
                // si o encuentra el item en la tabla lo agrega a esta    
                }else{
                
                    var item=res['contenido'];
                    var barras=document.getElementsByClassName('barras');
                    // agrega datos en la tabla
                    tabla.row.add( [
                        item['codigo'],
                        item['referencia'],
                        item['descripcion'],
                        cantr
                    ] ).draw(false);
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