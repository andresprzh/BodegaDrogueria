$(document).ready(function () {
    
    /* ============================================================================================================================
                                                        INICIALIZACION   
    ============================================================================================================================*/
    // INICIA DATATABLE
    // table=iniciar_tabla();

    //inicia modal
    $('.modal').modal();
    
     // CARGA DATO AL MENU DE SELECCION DE PERFILES
    $.when(CargarPerfiles()).done(function () {
        // espera a que termine de cargar los perfiles
        // INICIA TABLA USUARIOS

        CargarUsuarios();
        cargarfranquicias();
    });
        

    /* ============================================================================================================================
                                                        EVENTOS   
    ============================================================================================================================*/
    // si se da click en agregar o editar usuario
    $(document).on('click', '.modal-trigger', function () {
        
        // si se da click en agregar usuario
        if (this.id=='addusuario') {
            
            //cambia titulo modal
            $('#iduser').html("nuevo");
        
            $('#nombre').val("");
            $('#cedula').val("");
            $('#usuario').val("");
            $('#perfil').val(3);
            // INICIA MENU DE SELECCION
            $('select').formSelect();

            // Muestra el boton agregar y oculta el boton de modificar
            $('#agregar').removeClass('hide');
            $('#modificar').addClass('hide');

        // si se da clicken editar usuario
        }else{
            
            let iduser = $(this).closest('tr').attr('id');
            const usuario =  $('td:eq(0)', $(this).parents('tr')).text().replace(/(^\s+|\s+$)/g, '');
            const nombre =  $('td:eq(1)', $(this).parents('tr')).text().replace(/(^\s+|\s+$)/g, '');
            const cedula =  parseInt($('td:eq(2)', $(this).parents('tr')).text());
            const perfil =  $('td:eq(3)', $(this).parents('tr')).attr('name');
            // cambia titulo modal
            $('#iduser').html(iduser);
            
            $('#nombre').val(nombre);
            $('#cedula').val(cedula);
            $('#usuario').val(usuario);
            
            $('#perfil').val(perfil);            
            // INICIA MENU DE SELECCION
            $('select').formSelect();

            // Muestra el boton modificar y oculta el boton de agregar
            $('#modificar').removeClass('hide');
            $('#agregar').addClass('hide');

        }
    } );

    // input para buscar usuario en la tabla
    $("#buscar").keyup(function (e) { 
        let input, filter, table, tr, nombres,cedulas;

        filter = this.value.toUpperCase();
        table = document.getElementById("TablaU");
        tr = table.getElementsByTagName("tr");
        for (let i = 0; i < tr.length; i++) {
            usuarios = tr[i].getElementsByTagName("td")[0];
            nombres =tr[i].getElementsByTagName("td")[1];
            cedulas =tr[i].getElementsByTagName("td")[2];
            perfil =tr[i].getElementsByTagName("td")[3];
            if (nombres && cedulas) {
                if ((usuarios.innerHTML.toUpperCase().indexOf(filter) > -1)||(nombres.innerHTML.toUpperCase().indexOf(filter) > -1)||
                    (cedulas.innerHTML.toUpperCase().indexOf(filter) > -1)||(perfil.innerHTML.toUpperCase().indexOf(filter) > -1)) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    });

    // si se agrga o modifica usuarios
    $('.modal').on('submit', 'form', function (event) {
        var buttonid = $(this).find("button:focus").attr('id');
        
        
        event.preventDefault();
        swal({
            title: "¿Agregar Usuario?",
            icon: "warning",
            buttons: ['Cancelar', 'Agregar']
        })
            .then((Cerrar) => {
                if (Cerrar) {
        
                    var datosusuario = new Array()
                    datosusuario = {
                        'id': $('#iduser').html(),
                        'nombre': $('#nombre').val(),
                        'cedula': $('#cedula').val(),
                        'usuario': $('#usuario').val(),
                        'password': $('#password').val(),
                        'perfil': $('#perfil').val(),
                        'franquicia': null
                    };
                    if ($('#perfil').val()==7) {
                        datosusuario['franquicia']=$('#franquicia').val()
                    }
                                            
                    $.ajax({
                        type: "POST",
                        url: "api/usuarios/modificar",
                        data: {"datosusuario":datosusuario,"button":buttonid},
                        dataType: "JSON",
                        success: function (res) {
                            
                            // si se modifica o inserta el usuario el ajax regresa el id de dicho usuario
                            if (!isNaN(parseFloat(res))) {
                                $('#iduser').html("");
                                $('#nombre').val("");
                                $('#cedula').val("");
                                $('#usuario').val("");
                                $('#password').val("");
                                $('#perfil').val("");
                                let perfil=$(`#perfil option[value="${datosusuario['perfil']}"]`).text();

                                if (buttonid=="agregar") {
                                    swal({
                                        title: "Usuario Agregado",
                                        icon: "success",
                                    })
                                    .then(()=>{
                                        
                                        
                                        // muestra el nuevo usuario en la tabla
                                        $("#TablaU tbody").append($(`
                                            <tr id=${res}><td>
                                                ${datosusuario["usuario"]}</td><td>
                                                ${datosusuario["nombre"]}</td><td>
                                                ${datosusuario["cedula"]}</td><td class='perfiles' name='${datosusuario['perfil']}'>
                                                ${perfil}</td><td>
                                                <button  title='Editar Usuario' data-target='editarusuario' class='editar modal-trigger btn-floating btn-small waves-effect waves-light tea darken-1 ' > 
                                                    <i class='fas fa-user-edit'></i> 
                                                </button>
                                            </tr>`)
                                        )

                                    }) 
                                }else{
                                    swal({
                                        title: "Usuario modificado",
                                        icon: "success",
                                    }).then(()=>{

                                        // se obtiene la fila donde esta el usuario
                                        let fila=$('#TablaU tbody ').find(`#${datosusuario['id']}`);
                                        // console.log(fila.text());
                                        $('td:eq(0)', fila).text(datosusuario['usuario']);
                                        $('td:eq(1)', fila).text(datosusuario['nombre']);
                                        $('td:eq(2)', fila).text(datosusuario['cedula']);
                                        $('td:eq(3)', fila).text(perfil);

                                    }) ;
                                }
                                // cierra el modal
                                $('.modal').modal('close'); 
                            }else if(res==false){
                                swal({
                                    title: "No se pudo agregar o modificar el usuario",
                                    icon: "error",
                                });
                            }else{
                                swal({
                                    title: res,
                                    icon: "error",
                                });
                            }
                        }
                    });

                }
            });
    });

    // busca si el usuario que se esta agregando es franquicia
    $('.modal').on('change', '#perfil', function (e) {
        e.preventDefault();
        let perfil=$(this).val();
        
        if (perfil==7) {
            $('#fran').removeClass('hide');
        }else{
            $('#fran').addClass('hide');
        }
    });
    
});
/* ============================================================================================================================
                                                    FUNCIONES   
    ============================================================================================================================*/
    

function CargarPerfiles() {
    
    return $.ajax({
        type: "GET",
        url: "api/usuarios/perfiles",
        dataType: "JSON",
        success: function (res) {
            
            if (res['estado'] == 'encontrado') {
                var perfiles=res['contenido'];
                                
                for (var i in perfiles) {
                    $("#perfil").append($('<option value="'+perfiles[i]['id']+'">'+perfiles[i]['perfil']+'</option>'));
                }   

                // INICIA MENU DE SELECCION
                $('select').formSelect();
            }

        }
    });
    
}

function CargarUsuarios() {
    
    return $.ajax({
        type: "GET",
        url: "api/usuarios/usuarios",
        dataType: "JSON",
        success: function (res) {
            // console.log(res);
            if (res['estado'] == 'encontrado') {
                var usuarios=res['contenido'];
                
                // si el resultado es n array
                if (usuarios.constructor===Array) {
                    for (var i in usuarios) {
                        // obtiene el perfil del usuario dle menu de seleccion
                        let perfil=$(`#perfil option[value="${usuarios[i]['perfil']}"]`).text();
                        $("#TablaU tbody").append($(`
                            <tr id=${usuarios[i]["id"]}><td >
                                ${usuarios[i]["usuario"]}</td><td >
                                ${usuarios[i]["nombre"]}</td><td >
                                ${usuarios[i]["cedula"]}</td><td  class='perfiles' name='${usuarios[i]['perfil']}'>
                                ${perfil}</td><td>
                                <button  title='Editar Usuario' data-target='editarusuario' class='editar modal-trigger btn-floating btn-small waves-effect waves-light tea darken-1 ' > 
                                    <i class='fas fa-user-edit'></i> 
                                </button>
                            </tr>`)
                        );  
                    } 
                // si solo hay 1 dato en usuarios
                }else{
                    
                    // obtiene el perfil del usuario dle menu de seleccion cargado anteriormente
                    
                    let perfil=$(`#perfil option[value="${usuarios['perfil']}"]`).text();
                    $("#TablaU tbody").append($(`
                        <tr id=${usuarios["id"]}><td>
                            ${usuarios["usuario"]}</td><td>
                            ${usuarios["nombre"]}</td><td>
                            ${usuarios["cedula"]}</td><td class='perfiles' name='${usuarios['perfil']}'>
                            ${perfil}</td><td>
                            <button  title='Editar Usuario' data-target='editarusuario' class='editar modal-trigger btn-floating btn-small waves-effect waves-light tea darken-1 ' > 
                                <i class='fas fa-user-edit'></i> 
                            </button>
                        </tr>`)
                    )
                }
            }
        }    
    });
}

function modUsuario(datosusuario,buttonid) {

    return $.ajax({
        type: "POST",
        url: "api/usuarios/modificar",
        data: {"datosusuario":datosusuario,"button":buttonid},
        dataType: "JSON",
        success: function (res) {

            if (res) {
                if (buttonid=="agregar") {
                    swal({
                        title: "Usuario Agregado",
                        icon: "success",
                    });
                }else{
                    swal({
                        title: "Usuario modificado",
                        icon: "success",
                    });
                }
                // cierra el modal
                $('.modal').modal('close'); 
            }else{
                swal({
                    title: "No se pudo agregar o modificar el usuario",
                    icon: "error",
                });
            }
        }
    });
    
}

function cargarfranquicias() {
    
    return $.ajax({
        type: "GET",
        url: "api/usuarios/franquicias",
        dataType: "JSON",
        success: function (res) {
            
            if (res) {
                
                for (let i in res) {
                   $("#franquicia").append($('<option value="'+res[i]['codigo']+'">'+res[i]['descripcion']+'</option>'));
                }
                
            }
        }
    });
}