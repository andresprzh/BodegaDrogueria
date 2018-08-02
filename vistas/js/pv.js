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
                
                // SE MUESTRAN LAS reqUISICIONES EN EL MENU DE SELECCION
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
    
        //EVENTO AL CAMBIAR ENTRADA reqUERIDOS
        $(".requeridos").change(function (e) {
            // oculta los datos de la requisicion
            $( "#infreq" ).addClass( "hide" );
            // muestra la entrada se seleccion de cajas
            $( ".SelectCaja" ).removeClass( "hide" ); 
            // oculta el input donde se ingresa el codigo de barras
            $( ".input_barras" ).addClass( "hide" );
            $( "#Registrar" ).addClass( "hide" );
            //consigue el numero de requerido
            var requeridos=$(".requeridos").val();
            //id usuario es obtenida de las variables de sesion
            var req=[requeridos,id_usuario];
            
            $('#cajas').html('<option value="" disabled selected>Seleccionar</option>');
            
            $.ajax({
                url: 'ajax/pv.cajas.ajax.php',//url de la funcion
                type: 'post',//metodo post para mandar datos
                data: {"req":req},//datos que se enviaran
                dataType: "JSON",
                success: function (res) {
                    
                    if (res!==false) {
                        // SE MUESTRAN LAS CAJAS EN EL MENU DE SELECCION
                        
                        var cajas= res['cajas'];
                        // si el resultado es un array
                        
                        if (cajas.constructor === Array) {
                            for (var i in cajas) {
                                $("#cajas").append($('<option value="'+cajas[i]+'">Caja '+cajas[i]+'</option>'));
                            }   
                        }else{
                            $("#cajas").append($('<option value="'+cajas+'">Caja '+cajas+'</option>'));
                        }

                        var requisicion=res['requisicion']['contenido'];
                        // se muestra el origen y destino de la requisicion
                        $( "#infreq" ).removeClass( "hide" );
                        $('#origen').html(requisicion['origen']);
                        $('#destino').html(requisicion['destino']);
                        
                    }
                    
        
                    // INICIA MENU DE SELECCION
                    $('select').formSelect();
                }
                
              }); 
            
    
        });
        
        // EVENTO AL CAMBIAR LA ENTRADA DE CAJAS
        $("#cajas").change(function (e) { 
            $( ".input_barras" ).removeClass( "hide" ); 
            $( "#Registrar" ).addClass( "hide" );
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


        // EVENTO CUANDO SE MODIFICA UNA CELDA DE LA TABLA
        $('#tablaeditable').on( 'change', 'td', function () {

            // guarda el valor dle input en datatable
            var tabla=$('#tabla').DataTable();
            
            
            var cantr = $(this).find("input").val();
            var fila = tabla.row(this);

            // si la tabla es responsive
            if(fila.data() == undefined) {
    
                var fila = $(this).parents('tr');
                if (fila.hasClass('child')) {
                    fila = fila.prev();
                }
                
                tabla.row(fila).cell(fila,3).data('<input type="number" class="validate" value="'+cantr+'">').draw()
    
            } else {
                
                tabla.cell(this).data('<input type="number" class="validate" value="'+cantr+'">').draw()
            }
            
            
            
            
        } );
    
        
        // EVENTO SI SE PRESIONA EL BOTON DE AGREGAR CODIGO DE BARRAS(+)
        $("#agregar").click(function (e) {
            
            // busca el codigo y lo agrega a la tabla 
            BuscarCodBar();
    
        });

        // EVENTO SI SE PRESIONA 1 BOTON EN LA TABLA EDITABLE(ELIMINAR ITEM)
        $('#tablaeditable').on('click', 'button', function (e) {
            

            var dt = $.fn.dataTable.tables();
            var tabla = $(dt).DataTable();
            

            celda = table.cell(this);

            var fila = table.row(this)
            
            // si la tabla es responsive
            if (fila.data() == undefined) {

                fila = $(this).parents('tr');
                if (fila.hasClass('child')) {
                    fila = fila.prev();
                }
            } else {
                fila=this;
            }

            
            tabla.row(fila).remove().draw('false');
            

            
        });

        // EVENTO SI SE PRESIONA EL BOTON DE GENERAR
        $('#Registrar').click(function (e) { 

            swal({
                title: "¿Registrar items?",
                icon: "warning",
                buttons: ['Cancelar','Resgitrar']
                
            })
            .then((registrar) => {
    
                //si se le da click en Resgitrar procede a generar e reporte
                if (registrar) {
                    
                    //consigue el codigo de barras
                    var caja= $('#cajas').val();
                    
                    //consigue el numero de requerido
                    var requeridos=$(".requeridos").val();
                    //id usuario es obtenida de las variables de sesion
                    var req=[requeridos,id_usuario];

                    // console.log(req);
                    var tabla = $('table.tablas').DataTable();
                    // se obtienen todos los datos de la tabla en una matriz
                    var datos=tabla.data().toArray();   
                    
                    // se guerda en un arreglo los datos de codigo de Baras y la cantidad recibido                    
                    var items=new Array();
                    for (var i in datos) {
                        items[i]={
                            "codbarras":datos[i][0],
                            "recibidos": $(datos[i][3]).val()
                        }                
                    }
                    

                    $.ajax({   
                        url: "ajax/pv.registrar.ajax.php",
                        type: 'post',//metodo post para mandar datos
                        data: {"caja":caja,"req":req,"items":items},//datos que se enviaran
                        dataType: "JSON",
                        success: function (res) {
                            
                            
                            if (res['estado']==true && res['contenido']) {
                                swal({
                                    title: "¡Items registrados!",
                                    icon: "success",
                                }).then((OK) => {
                                    
                                        var numcaja=$('#cajas').val();
                                        // obtiene los 3 ultimos caracteres de la requisicion
                                        var no_req=req[0].substr(req[0].length - 3);

                                        // crea el nombre del documento a partir de la requisicion y la caja
                                        var nomdoc='RQ'+no_req+'C'+numcaja+'.TR1';

                                        var element = document.createElement('a');
                                        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(res['contenido']));
                                        element.setAttribute('download', nomdoc);

                                        element.style.display = 'none';
                                        document.body.appendChild(element);

                                        element.click();

                                        document.body.removeChild(element);
   
   
                                });
                            }else{
                                swal({
                                    title: "¡Error!",
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
        var codigo= $('#codbarras').val();
        //consigue el numero de requerido
        var requeridos=$(".requeridos").val();
        //id usuario es obtenida de las variables de sesion
        var req=[requeridos,id_usuario];
        
        // ajax para ejecutar un script php mandando los datos
        return $.ajax({
          url: 'ajax/pv.items.ajax.php',//url de la funcion
          type: 'post',//metodo post para mandar datos
          data: {"codigo":codigo,"req":req},//datos que se enviaran
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
                    cantr+=parseInt($(datos[pos][3]).val());
                    
                    tabla.cell(pos,3).data('<input type="number" class="validate" value="'+cantr+'">').draw();
                // si no encuentra el item en la tabla lo agrega a esta    
                }else{
                
                    var item=res['contenido'];
                    
                    // agrega datos en la tabla
                    tabla.row.add( [
                        item['codigo'],
                        item['referencia'],
                        item['descripcion'],
                        // cantr
                        '<input type="number" class="validate" value="'+cantr+'">',
                        "<button  title='Eliminar Item' class='btn-floating btn-small waves-effect waves-light red darken-3 ' >" +
                            "<i class='fas fa-times'></i>" +
                        "</button>"
                    ] ).draw(false);
                }
                $( "#Registrar" ).removeClass( "hide" ); 
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