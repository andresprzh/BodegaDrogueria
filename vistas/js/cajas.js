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
       
               
       
        // //muestra la tabla y la reinicia
        // $( "#Cajas" ).removeClass( "hide" );
        // //refresca la tabla, para volver a cargar los datos
        // $('#tablacajas').html("");
        // table.clear();
        // table.destroy();
        $("#Cajas").removeClass("hide");

        // desabilita los botones de generar documento
        $(".Documento").attr("disabled", "disabled");
        //espera a que la funcion termine para reiniciar las tablas
        $.when(mostrarCajas()).done(function () {

            table[0] = iniciarTabla('#TablaC');
            table[1] = iniciarTabla('#TablaCE');

        });

    });

    // EVENTO SI SE DA CLICK EN EL BOTON DE GENERAR DOCUMENTO
    $('.Documento').click(function (e) {
        // consigue la id de la tabla seleccionada
        const tabla = $($(this).parent().parent().find('table')[0]).attr('id');

        //consigue el numero de requerido
        var requeridos = $('.requeridos').val();
        //id usuario es obtenida de las variables de sesion
        var req = [requeridos, id_usuario];



        var datos = $(`#${tabla}`).DataTable();

        // obtiene los numeros de las cajas de la tabla seleccionada
        var numcaja = new Array();
        for (var i in datos.data().toArray()) {
            numcaja[i] = datos.row(i).id();

        }


        $.ajax({

            url: 'api/cajas/documento',
            method: 'GET',
            data: { 'req': req, 'numcaja': numcaja },
            dataType: 'JSON',
            success: function (res) {
                // si hay un error al buscar los archivos no genera el documento
                if (!res) {
                    swal({
                        title: '!Error al generar el documento¡',
                        type: 'error',
                    });

                    // si no hay error genera le documento y lo manda a decargar
                } else {
                    // // OBTIENE LOS 3 ULTIMOS CARACTERES DE LA REQUISICION
                    // var no_res = req[0].substr(req[0].length - 3);
                    // let numerodoc = ('00' + res['no_documento']).slice(-2);
                    // // CREA EL NOMBRE DEL DOCUMENTO A PARTIR DE LA REQUISICION Y LA CAJA
                    // var nomdoc = 'DS' + no_res + 'D' + numerodoc + '.TR1';
                    var nomdoc = req[0] + '.TR' + res['no_documento'];

                    var element = document.createElement('a');
                    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(res['documento']));
                    element.setAttribute('download', nomdoc);

                    element.style.display = 'none';
                    document.body.appendChild(element);

                    element.click();

                    document.body.removeChild(element);

                    $('.modal').modal('close');
                    recargarCajas();

                }
            }
        });

    });

    // EVENTO SI SE PRESIONA EL BOTON MODIFICAR
    $('#modificar').on('click', function (e) {

        //consigue el numero de requerido
        let requeridos = $('.requeridos').val();
        //id usuario es obtenida de las variables de sesion
        let req = [requeridos, id_usuario];

        //si se presiona aceptar se continua con el proceso

        swal({
            title: '¿Modificar caja?',
            type: 'warning',
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Modificar',
            confirmButtonClass: 'green darken-3',
        })
            .then(function (result) {

                //si se le da click en cerrar procede a pasar los items a la caja y a cerrarla
                if (result.value) {

                    // Busca los datos en la tabla
                    let table = document.getElementById('tablaerror');
                    let tr = table.getElementsByTagName('tr');
                    let items = new Array;

                    for (let i = 0; i < tr.length; i++) {

                        items[i] = {
                            'iditem': tr[i].id,
                            'alistados': $(tr[i]).find('input').val(),
                            'estado': $(tr[i]).attr('name'),
                            'cajae': $(tr[i]).find(':nth-child(2)').text().replace(/(^\s+|\s+$)/g, ''),
                            'cajar': $(tr[i]).find(':nth-child(3)').text().replace(/(^\s+|\s+$)/g, '')
                        };
                    }

                    //guarda el tipo de caja en una variable
                    var numcaja = $('.NumeroCaja').attr('name');


                    $.ajax({
                        url: 'api/cajas/modificar',//url de la funcion
                        method: 'post',//metodo post para mandar datos
                        data: { 'req': req, 'numcaja': numcaja, 'items': items },//datos que se enviaran          
                        dataType: 'JSON',
                        success: function (res) {

                            if (res) {

                                swal({
                                    title: '¡Caja Modificada exitosamente!',
                                    type: 'success',
                                })
                                    .then(function (result) {

                                        $('.modal').modal('close');
                                        recargarCajas();

                                    });

                            } else {

                                swal({
                                    title: '¡Error al modificar la caja!',
                                    type: 'error',
                                });

                            }

                        }
                    });
                }
            });

    });

    // ELIMINA LA CAJA SI HAY SELECCIONADA
    $('#eliminar').click(function (e) {
        //consigue el numero de requerido
        let requeridos = $('.requeridos').val();
        //id usuario es obtenida de las variables de sesion
        let req = [requeridos, id_usuario];
        // se consigue el numero de la caja
        let caja = $('.NumeroCaja').attr('name');

        swal({
            title: `¿Esta seguro de eliminr la caja ${caja}?`,
            type: 'warning',
            showCancelButton: true,
            cancelButtonText: 'No',
            confirmButtonText: 'Si',
            confirmButtonClass: 'red darken-3',

        })
            .then((result) => {

                if (result.value) {

                    $.ajax({
                        type: 'POST',
                        url: 'api/cajas/eliminar',
                        data: { 'numcaja': caja, 'req': req },
                        dataType: 'JSON',
                        success: function (res) {

                            if (res) {
                                swal({
                                    title: `Caja ${caja} eliminada`,
                                    type: 'success',
                                })
                                    .then(() => {
                                        $('.modal').modal('close');
                                        recargarCajas();
                                    });
                            } else {
                                swal({
                                    title: `No se pudo eliminar la caja ${caja} `,
                                    type: 'error',
                                })
                            }
                        }
                    });

                }

            });
    });

    // ASIGNA LAS CAJAS A UN TRANSPORTADOR PARA SER ENVIADAS AL PUNTO
    $('#despachar').click(function (e) {

        var datos = $("#TablaC").DataTable();

        // obtiene los numeros de las cajas de la tabla seleccionada
        var numcaja = new Array();
        for (var i in datos.data().toArray()) {
            numcaja[i] = datos.row(i).id();

        }



        ajax('api/cajas/conductor', 'GET').done(function (res) {

            if (res) {
                if (res['estado']) {

                    let opciones = res['contenido'];
                    if (!opciones) {
                        swal({
                            type: 'error',
                            title: 'No hay transportadores',
                        })
                    }
                    swal({
                        title: 'Seleccionar Transportador',
                        input: 'select',
                        inputOptions: opciones,
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar',
                        confirmButtonText: 'Asignar',
                        confirmButtonClass: 'green darken-3',
                        inputValidator: function (value) {
                            return new Promise(function (resolve, reject) {
                                if (value !== '') {
                                    resolve();
                                } else {
                                    reject('Por favor seleccione un tranportador');
                                }
                            });
                        }
                    }).then(function (result) {
                        if (result.value) {
                            transportador = result.value;
                            $.ajax({
                                url: 'api/cajas/despachar',
                                method: "POST",
                                data: { 'cajas': numcaja, 'transportador': transportador },
                                dataType: 'JSON',
                                success: function (res) {
                                    if (res) {
                                        swal({
                                            type: 'success',
                                            html: 'Cajas asignadas  para despachar'
                                        }).then(function (res) {
                                            recargarCajas();

                                        });

                                    } else {
                                        swal({
                                            type: 'error',
                                            html: 'No se puedo asignar cajas para despachar'
                                        });
                                    }
                                }
                            });

                        }
                    });

                } else {
                    swal({
                        type: 'error',
                        title: res['contenido'],
                    })
                }
            }
        })

        return 0;

    });

    // REASIGNA CAJA
    $("#cambiar").click(function (e) { 

        // busca usuarios alistadores
        ajax('api/tareas/usuarios', 'GET').done(function (res) {
            // return 0;
            if (res) {
                if (res['estado']) {

                    let opciones = res['contenido'];
                    if (!opciones) {
                        swal({
                            type: 'error',
                            title: 'Error',
                        })
                    }
                    let alistadores= new Array();
                    for (let i in opciones) {
                        alistadores[opciones[i]['id_usuario']]=opciones[i]['nombre'];
                    }
                    
                    swal({
                        title: 'Seleccionar Alistador',
                        input: 'select',
                        inputOptions: alistadores,
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar',
                        confirmButtonText: 'Asignar',
                        confirmButtonClass: 'green darken-3',
                        inputValidator: function (value) {
                            return new Promise(function (resolve, reject) {
                                if (value !== '') {
                                    resolve();
                                } else {
                                    reject('Por favor seleccione un alistador');
                                }
                            });
                        }
                    }).then(function (result) {
                        
                        if (result.value) {
                            // console.log(result.value);
                            // return 0
                            alistador = result.value;
                            $.ajax({
                                url: 'api/cajas/caja',
                                method: "POST",
                                data: { 'cajas': numcaja, 'alistador': alistador },
                                dataType: 'JSON',
                                success: function (res) {
                                    if (res) {
                                        swal({
                                            type: 'success',
                                            html: 'Caja reasignada'
                                        }).then(function (res) {
                                            recargarCajas();

                                        });

                                    } else {
                                        swal({
                                            type: 'error',
                                            html: 'No se puedo reasignar la cajas'
                                        });
                                    }
                                }
                            });

                        }
                    });

                } else {
                    swal({
                        type: 'error',
                        title: res['contenido'],
                    })
                }
            }
        })
        
    });
    // IMPRIMI LISTA DE ITEMS PARA PEGAR EN LA CAJA Al ENVIAR
    $('#imprimir').click(function (e) {

        //consigue el numero de requerido
        let requeridos = $(".requeridos").val();
        //id usuario es obtenida de las variables de sesion
        let req = [requeridos, id_usuario];

        // numero de la caja
        let numcaja = $(".NumeroCaja").html();

        return $.ajax({
            type: 'GET',
            url: 'api/alistar/listadoc',
            data: { 'numcaja': numcaja, 'req': req },
            dataType: 'JSON',
            success: function (res) {

                // si hay un error al buscar los archivos no genera el documento
                if (!res) {
                    swal({
                        title: '!Error al generar el documento¡',
                        type: 'error',
                    });

                    // si no hay error genera le documento y lo manda a decargar
                } else {

                    var nomdoc = numcaja + '.txt';
                    var element = document.createElement('a');
                    element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(res['contenido']));
                    element.setAttribute('download', nomdoc);

                    element.style.display = 'none';
                    document.body.appendChild(element);

                    element.click();

                    document.body.removeChild(element);

                    $('.modal').modal('close');
                    recargarCajas();

                }
            },
            error: function (res) {
                if (!res) {
                    swal({
                        title: '!Error al generar el documento¡',
                        type: 'error',
                    });


        //espera a que la funcion termine para reiniciar las tablas
        $.when(MostrarCajas()).done(function(){
            
            table=iniciar_tabla("#TablaC");

        });
 
            
    });

    $("#Documento").click(function (e) {
       
        swal({
            title: "¿Generar Documento?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((documento) => {
            swal({
                title: "!Documento Generado Exitosamente¡",
                icon: "success",
                buttons: true,
                dangerMode: true,
              });

              $('.modal').modal('close');

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
        error: function (xhr, status) {

            alert(status);

        },
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

                }                  
                
            }

        }

  }); 

} 

function MostrarItemsCaja(e) {
    

    //obtienen los datos de la caja para pasarlo al modal
    var NumCaja=$(".NumCaja");
    NumCaja=$(NumCaja[e]).html();
    var alistador=$(".alistadores");
    alistador=$(alistador[e]).html();
    var tipocaja=$(".tipocajas");
    tipocaja=$(tipocaja[e]).html();
    var cierre=$(".cierres");
    cierre=$(cierre[e]).html();
   

    // se muestran los datos generales de la caja
    $('#TituloCaja').html("Caja No "+NumCaja);
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

<<<<<<< Updated upstream
   return tabla;
   
=======
    return tabla;

}

function ajax(url, method) {

    return $.ajax({
        url: url,
        method: method,
        dataType: 'JSON'

    });
>>>>>>> Stashed changes
}