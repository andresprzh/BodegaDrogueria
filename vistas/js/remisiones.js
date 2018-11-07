$(document).ready(function () {
  
   /* ============================================================================================================================
                                                        INICIALIZACION   
    ============================================================================================================================*/
   
    //INICIA EL MODAL
    $('.modal').modal({
      onOpenStart: function () {
          // console.log("hola");
      }
    });
    // pone requisiciones en el input select
  $.ajax({
    url: "api/remisiones/franquicias",
    method: "GET",
    data: '',
    contentType: false,
    processData: false,
    dataType: "JSON",
    success: function (res) {
        
        // SE MUESTRAN LAS REQUISICIONES EN EL MENU DE SELECCION
        for (var i in res) {
            
            // $("#requeridos").append($('<option id= value="' + res[i]['no_req'] + '">' + res[i]['no_req'].substr(4) + res[i]['descripcion'] + '</option>'));
            $("#franquicias").append($(`<option  value="${res[i]['codigo']}"> ${res[i]['descripcion']}</option>`));

        }
        $('select').formSelect();

    }
  });
  /* ============================================================================================================================
                                                        EVENTOS   
    ============================================================================================================================*/
  $('#remisiones #archivos').change(function () {

    if (this.files.length > 0) {
      $('.file-upload-res').css('text-align', 'left');

      let lista = '<ul style:>';
      for (let i = 0; i < this.files.length; i++) {
        lista += `<li>${this.files[i].name}</li>`;
      }
      lista += '</ul>';

      $('.file-upload-res').html(lista);
    } else {
      $('.file-upload-res').css('text-align', 'center');

      $('.file-upload-res').html('<p class=""><i class="fas fa-upload"></i>Subir</p>');
    }

  });

  $("#remisiones").submit(function (e) {
    e.preventDefault();
    
    
    // muestra que se estan cargando los archivos
    $("#submitbutton").attr("disabled", "disabled");//evita que se de doble click en el boton
    $('#remisiones #archivos').attr('disabled', 'disabled');
    $('.file-upload-res').css('text-align', 'center');
    $('.file-upload-res').html(`
    <div class="preloader-wrapper active">
      <div class="spinner-layer spinner-green-only">
        <div class="circle-clipper left">
          <div class="circle"></div>
        </div><div class="gap-patch">
          <div class="circle"></div>
        </div><div class="circle-clipper right">
          <div class="circle"></div>
        </div>
      </div>
    </div>`);

    let form_data = new FormData();
    let ins = document.getElementById('archivos').files.length;

    let path = document.getElementById('archivos').files[0].webkitRelativePath;
    let folder = path.split("/")[0];
    let franquicia=document.getElementById('franquicias').value;
    
    form_data.append("franquicia", franquicia);
    form_data.append("usuario", id_usuario);

    for (let x = 0; x < ins; x++) {
      form_data.append("files[]", document.getElementById('archivos').files[x]);
    }

    $.ajax({
      url: 'api/remisiones/docrem', // point to server-side PHP script 
      dataType: 'JSON', // what to expect back from the PHP script
      cache: false,
      contentType: false,
      processData: false,
      data: form_data,
      type: 'POST',
      success: function (res) {
        
        // si hay un error al buscar los archivos no genera el documento
        if (!res) {
          swal({
            title: '!Error al generar el documento¡',
            icon: 'error',
          });

          // si no hay error genera le documento y lo manda a decargar
        } else if(res['documento']) {
          let nomdoc = res['nomdoc'];

          let element = document.createElement('a');
          element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(res['documento']));
          element.setAttribute('download', nomdoc);

          element.style.display = 'none';
          document.body.appendChild(element);

          element.click();

          document.body.removeChild(element);

          document.getElementById('archivos').value = "";
          $('.file-upload-res').css('text-align', 'center');

          $('.file-upload-res').html('<p class=""><i class="fas fa-upload"></i>Subir</p>');         
        }
        if (res['lotes']) {
          $('#TablaL tbody').html("");
          
          for (var i in res['lotes']) {
            $('#TablaL tbody').append(`<tr id=[${res['lotes'][i]['item']}}><td>
            ${res['lotes'][i]['descripcion']} </td><td>    
            ${res['lotes'][i]['item']} </td><td>    
            ${res['lotes'][i]['cantidad']}</td><td>
            <input type="text" class="lote" placeholder="lote" required></td><td>
            <input type="date" class="vencimiento" placeholder="vencimiento" required></td>
            </tr>`);
          }
          $('.modal').modal("open")
        }
        // habilita nuevamente input
        $("#submitbutton").removeAttr("disabled");
        $("#remisiones #archivos").removeAttr("disabled");  
      }
    });
  });

});
/* ============================================================================================================================
                                                   FUNCIONES   
  ============================================================================================================================*/

  function documento(){
    let tabla=document.getElementById("TablaL");
    let items=new Array();
    for (let i = 1; i < tabla.rows.length; i++) {
        items[i-1]={
          "item": (tabla.rows[i].cells[1].innerText).trim(),
          "valor": parseFloat(tabla.rows[i].cells[2].innerText), 
          "cantidad":  parseFloat(tabla.rows[i].cells[3].innerText), 
        }
        // console.log((tabla.rows[i].cells[1].innerText).trim());
    }
    return 0;
    $.ajax({
      type: "POST",
      url: "api/remisiones/doclotes",
      data: "data",
      dataType: "dataType",
      success: function (response) {
        
      }
    });
  }
