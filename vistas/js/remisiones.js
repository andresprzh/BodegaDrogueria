$(document).ready(function(){

  
  
  $('#remisiones input').change(function () {
    console.log(this.files)
    if (this.files.length>0) {
      $('.file-upload-res').css('text-align', 'left');
      
      let lista='<ul style:>';
      for (let i = 0; i < this.files.length ; i++) {
        lista+=`<li>${this.files[i].name}</li>`;
      }
      lista+='</ul>';
      
      $('.file-upload-res').html(lista);
    }else{
      $('.file-upload-res').css('text-align', 'center');
      
      $('.file-upload-res').html('<p class=""><i class="fas fa-upload"></i>Subir</p>');
    }

  });

  $("#remisiones").submit(function (e) { 
    e.preventDefault();
    var form_data = new FormData();
    var ins = document.getElementById('archivos').files.length;
    
    var path = document.getElementById('archivos').files[0].webkitRelativePath;
    var folder = path.split("/")[0];

    form_data.append("folder",folder);
    
    for (var x = 0; x < ins; x++) {
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
            console.log(res); // display success response from the PHP script
        }
    });
  });
});
