$(document).ready(function () {

/* ============================================================================================================================
                                                    INICIALIZACION   
============================================================================================================================*/
    $('.modal').modal();    

    // pone requisiciones en el input select
    $.ajax({
        url: "ajax/tareas.usuarios.ajax.php",
        method: "POST",
        contentType: false,
        processData: false,
        dataType: "JSON",
        success: function (res) {
            
            if (res['estado'] == 'encontrado') {
                let usuarios=res['contenido'];

                // si el resultado es n array
                if (usuarios.constructor===Array) {
                    for (var i in usuarios) {
                        $("#TablaU tbody").append($(`<tr id=${usuarios[i]["id"]}><td>
                        ${usuarios[i]["nombre"]}</td><td>
                        ${usuarios[i]["cedula"]}</td><td>
                        <a class="tareanueva btn-floating btn-small waves-effect waves-light white" title="asignar tarea"><i class="grey-text fas fa-wrench"></i></a></td>
                        </tr>`));  
                    } 
                // si solo hay 1 dato en usuarios
                }else{
                    
                    $("#TablaU tbody").append($(`<tr id=${usuarios["id"]}><td>
                        ${usuarios["nombre"]}</td><td>
                        ${usuarios["cedula"]}</td><td>
                        <a class="tareanueva"><i class="fas fa-wrench"></i></a></td>
                        </tr>`));  
                }
                  
            }

        }
    });


/* ============================================================================================================================
                                                    EVENTOS   
============================================================================================================================*/

    $("#buscar").keyup(function (e) { 
        let input, filter, table, tr, nombres,cedulas;

        filter = this.value.toUpperCase();
        table = document.getElementById("TablaU");
        tr = table.getElementsByTagName("tr");
        for (let i = 0; i < tr.length; i++) {
            nombres = tr[i].getElementsByTagName("td")[0];
            cedulas =tr[i].getElementsByTagName("td")[1];
            if (nombres && cedulas) {
                if ((nombres.innerHTML.toUpperCase().indexOf(filter) > -1)||(cedulas.innerHTML.toUpperCase().indexOf(filter) > -1)) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    });

    $("#TablaU tbody").on("click","a", function () {
        // se consigue el id del item y el nombre
        let iduser = $(this).closest('tr').attr('id');
        const nombre =  $('td:eq(0)', $(this).parents('tr')).text();
        const cedula =  $('td:eq(1)', $(this).parents('tr')).text();
       $("#nombre").html(nombre);
       $("#cedula").html(cedula);
       $('.modal').modal('open');
      
    });
});