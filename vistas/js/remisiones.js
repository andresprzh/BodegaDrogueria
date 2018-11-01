$(document).ready(function () {

   /* ============================================================================================================================
                                                        INICIALIZACION   
    ============================================================================================================================*/
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
    let form_data = new FormData();
    let ins = document.getElementById('archivos').files.length;

    let path = document.getElementById('archivos').files[0].webkitRelativePath;
    let folder = path.split("/")[0];
    let franquicia=document.getElementById('franquicias').value;
    
    form_data.append("franquicia", franquicia);

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
        console.log(res);
        
        // si hay un error al buscar los archivos no genera el documento
        if (!res) {
          swal({
            title: '!Error al generar el documento¡',
            type: 'error',
          });

          // si no hay error genera le documento y lo manda a decargar
        } else {
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
      }
    });
  });
});
