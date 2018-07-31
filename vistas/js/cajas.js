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

    // EVENTO CUANDO SE MODIFICA UNA CELDA DE LA TABLA
    $('#tablamodal').on( 'change', 'td', function () {
        var tabla=$('#TablaM').DataTable();
        
        
        var mensaje = $(this).find("input").val();
        var fila = table.row(this)

        // si la tabla es responsive
        if(fila.data() == undefined) {

            var fila = $(this).parents('tr');
            if (fila.hasClass('child')) {
                fila = fila.prev();
            }
            tabla.row(fila).cell(fila,7).data('<input  type="text" placeholder="texto de maximo 20 caracteres" class="mensajes validate" maxlength="20" value="'+mensaje+'"></input></td></tr>').draw()

        } else {
            
            table.cell(this).data('<input  type="text" placeholder="texto de maximo 20 caracteres" class="mensajes validate" maxlength="20" value="'+mensaje+'"></input></td></tr>').draw()
        }
        
        // celda.data('<input  type="text" placeholder="texto de maximo 20 caracteres" class="mensajes validate" maxlength="20"></input></td></tr>');
        
        
    } );

    // EVENTO SI SE DA CLICK EN EL BOTON DE GENERAR DOCUMENTO
    $("#Documento").click(function (e) {
        //consigue el numero de requerido
        var requeridos=$(".requeridos").val();
        //id usuario es obtenida de las variables de sesion
        var Req=[requeridos,id_usuario];
        
        var datos=$('#TablaM').DataTable().data().toArray();
        
        var Items=new Array();
        for (var i in datos) {
            Items[i]={
                "codigo":datos[i][0],
                "alistados": datos[i][5],
                "mensajes": $(datos[i][7]).val(),
                'origen' : $('#origen').html(),
                'destino' : $('#destino').html()
            }                
        }
        
       console.log(Items['origen'])
        $.ajax({
                
            url:"ajax/cajas.documento.ajax.php",
            method:"POST",
            data: {"Req":Req,"Items":Items},
            dataType: 'JSON',
            success: function (res) {
                var NumCaja=$('#NumeroCaja').html();
                // obtiene los 3 ultimos caracteres de la requisicion
                var no_res=Req[0].substr(Req[0].length - 3);

                // crea el nombre del documento a partir de la requisicion y la caja
                var nomdoc='RQ'+no_res+'C'+NumCaja+'.TR1';
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
                    element.setAttribute('download', nomdoc);

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
                
                
                // si solo hay 1 resultado no hace el ciclo for
                if (caja[0] === undefined) {

                    // reemplaza varoles nul por ---
                    if (caja['tipocaja']===null) {
                        caja['tipocaja']='---'
                    }
                    if (caja['abrir']===null) {
                        caja['abrir']='---'
                    }
                    if (caja['cerrar']===null) {
                        caja['cerrar']='---'
                    }

                    $('#tablacajas').append($("<tr>"+
                                        "<td class='NumCaja'>"+caja['no_caja']+"</td>"+
                                        "<td class='alistadores'>"+caja['alistador']+"</td>"+
                                        "<td class='tipocajas'>"+caja['tipocaja']+"</td>"+
                                        "<td>"+caja['abrir']+"</td>"+
                                        "<td class='cierres'>"+caja['cerrar']+"</td><td>"+
                                        "<button  onclick='MostrarItemsCaja("+0+")'  title='Revisar'  data-target='EditarCaja' class='btn modal-trigger waves-effect waves-light green darken-3'>"+
                                            "<i class='fas fa-file-alt'></i>"+
                                        "</button></td>"+
                                        "</tr>"));  
                }else {
                    for (var i in caja) {
                        
                        // reemplaza varoles nul por ---
                        if (caja[i]['tipocaja']===null) {
                            caja[i]['tipocaja']='---'
                        }
                        if (caja[i]['abrir']===null) {
                            caja[i]['abrir']='---'
                        }
                        if (caja[i]['cerrar']===null) {
                            caja[i]['cerrar']='---'
                        }

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

                    }   
                }
                $( "#TablaCajas" ).removeClass( "hide" );                  
                
            }

        }

  }); 

} 

//FUNCION SI SE DA CLICK EN BOTON DOCUMENTO(MUESTRA ITEMS DE 1 CAJA ESPECIFICA)
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
            origen=res['contenido'][0]['origen'];
            destino=res['contenido'][0]['destino'];
            
            $('#origen').html(origen);
            $('#destino').html(destino);

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
                                        item[i]['ubicacion']+"</td><td>"+
                                       '<input  type="text" placeholder="texto de maximo 20 caracteres" class="mensajes validate" maxlength="20"></input></td></tr>'));  
                    
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