$(document).ready(function () {
  
   /* ============================================================================================================================
                                                        INICIALIZACION   
    ============================================================================================================================*/
   
    //INICIA EL MODAL
    $('.modal').modal({
      onOpenStart: function () {
          // console.log('hola');
      }
    });
    
    // pone requisiciones en el input select
  $.ajax({
    url: 'api/remisiones/franquicias',
    method: 'GET',
    data: '',
    contentType: false,
    processData: false,
    dataType: 'JSON',
    success: function (res) {
        
        // SE MUESTRAN LAS REQUISICIONES EN EL MENU DE SELECCION
        for (var i in res) {
            
            // $('#requeridos').append($('<option id= value='' + res[i]['no_req'] + ''>' + res[i]['no_req'].substr(4) + res[i]['descripcion'] + '</option>'));
            $('#franquicias').append($(`<option  value='${res[i]['codigo']}'> ${res[i]['descripcion']}</option>`));

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

      $('.file-upload-res').html(`<p class=''><i class='fas fa-upload'></i>Subir</p>`);
    }

  });

  $("#remisiones").submit(function (e) {
    e.preventDefault();
    
    
    // muestra que se estan cargando los archivos
    $('#submitbutton').attr('disabled', 'disabled');//evita que se de doble click en el boton
    $('#remisiones #archivos').attr('disabled', 'disabled');
    $('.file-upload-res').css('text-align', 'center');
    $('.file-upload-res').html(`
    <div class='preloader-wrapper active'>
      <div class='spinner-layer spinner-green-only'>
        <div class='circle-clipper left'>
          <div class='circle'></div>
        </div><div class='gap-patch'>
          <div class='circle'></div>
        </div><div class='circle-clipper right'>
          <div class='circle'></div>
        </div>
      </div>
    </div>`);

    let form_data = new FormData();
    let ins = document.getElementById('archivos').files.length;

    let path = document.getElementById('archivos').files[0].webkitRelativePath;
    let folder = path.split('/')[0];
    let franquicia=document.getElementById('franquicias').value;
    
    form_data.append('franquicia', franquicia);
    form_data.append('usuario', id_usuario);

    for (let x = 0; x < ins; x++) {
      form_data.append('files[]', document.getElementById('archivos').files[x]);
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
        
        // habilita nuevamente input
        $('#submitbutton').removeAttr('disabled');
        $('#remisiones #archivos').removeAttr('disabled'); 
        $('#remisiones #archivos').val('');
        $('.file-upload-res').css('text-align', 'center');
        $('.file-upload-res').html(`<p class=''><i class='fas fa-upload'></i>Subir</p>`);    

        // return  0;
        // si hay un error al buscar los archivos no genera el documento
        if (!res) {
          swal({
            title: '!Error al generar el documento¡',
            icon: 'error',
          });

          // si no hay error genera le documento y lo manda a decargar
        } else if (res['lotes']) {
          $('#TablaL tbody').html('');
          $('.modal .remision').html(res['no_rem']);

          for (var i in res['lotes']) {
            $('#TablaL tbody').append(`<tr id=[${res['lotes'][i]['item']}}><td>
            ${res['lotes'][i]['descripcion']} </td><td>    
            ${res['lotes'][i]['item']} </td><td>    
            ${res['lotes'][i]['cantidad']}</td><td>
            <input type='text' class='lote' placeholder='lote' maxlength='6' required></td><td>
            <input type='date' class='vencimiento' placeholder='vencimiento' required></td>
            </tr>`);
          }
          $('.modal').modal('open')
        } else if(res['documento']) {
          let no_rem=('000'+res['no_rem']).slice(-3);
          let nomdoc=`REMIS${no_rem}.RM0`;

          let element = document.createElement('a');
          element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(res['documento']));
          element.setAttribute('download', nomdoc);

          element.style.display = 'none';
          document.body.appendChild(element);

          element.click();

          document.body.removeChild(element);

          document.getElementById('archivos').value = '';
                
        }
        
      }
    });
  });

  $('#formlotes').submit(function (e) { 
    e.preventDefault();
    
    documento();
  });

});
/* ============================================================================================================================
                                                   FUNCIONES   
  ============================================================================================================================*/

  function documento(){
    
    let tabla=document.getElementById('TablaL');
    let items=new Array();
    for (let i = 1; i < tabla.rows.length; i++) {
        items[i-1]={
          'item':   tabla.rows[i].cells[1].innerText.trim(),
          'lote':   tabla.rows[i].cells[3].querySelector('input').value,
          'vencimiento':  tabla.rows[i].cells[4].querySelector('input').value
        }
    }
    let no_rem=$('.modal .remision').html();
    // console.log(items);
    let form_data = new FormData();
    
    form_data.append('items', items);
    form_data.append('rem',no_rem );
    
    
    $.ajax({
      type: 'POST',
      url: 'api/remisiones/doclotes',
      dataType: 'JSON', // what to expect back from the PHP script
      data: {'items':items,'rem':no_rem},
      success: function (res) {
        // si hay un error al buscar los archivos no genera el documento
        if (!res) {
          swal({
            title: '!Error al generar el documento¡',
            icon: 'error',
          });

          // si no hay error genera le documento y lo manda a decargar
        } else if(res['documento']) {
          let no_rem=('000'+res['no_rem']).slice(-3);
          let nomdoc=`REMIS${no_rem}.RM0`;

          let element = document.createElement('a');
          element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(res['documento']));
          element.setAttribute('download', nomdoc);

          element.style.display = 'none';
          document.body.appendChild(element);

          element.click();

          document.body.removeChild(element);

          document.getElementById('archivos').value = '';

          $('.modal').modal('close');      
        }
      }
    });
  }
