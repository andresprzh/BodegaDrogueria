$(document).ready(function(){
    
    // sube el archivo 
    $("#archivo").change(function(e){
        $("#subir").submit();
    });
     
    $("#urlarchivo").change(function (e) { 
        $("#carga").removeClass("hide");
    });
    
});