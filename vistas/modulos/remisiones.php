   
<h2 class="header center ">Subir Requisición</h3>

<!-- <form role="form" method="post" enctype="multipart/form-data" style="padding-left:15px;" id="subir"> -->
<!-- ============================================================================================================================
                                                INPUTS   
============================================================================================================================ -->
<div class="row container" >

    <form class="file-upload" id="remisiones">
        <div class="file-upload-main " >
            <input type="file" id="archivos" webkitdirectory mozdirectory msdirectory odirectory directory multiple />
            <!-- <input type="file" id="archivos"/> -->
            
            <div class="file-upload-res" id='contenido'>
                <p class=""><i class="fas fa-upload"></i>Subir</p>
            </div>
        </div>
        <button class="btn green col s12" type="submit">Subir</button>
    </form>
</div>
    
<!-- <form class="file-upload">
  <p class="btnup">Upload a file</p>
  <input type="file" name="myfile" />
</form> -->
<div>

<style>

.file-upload .file-upload-main{
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-align: center;
  -webkit-align-items: center;
      -ms-flex-align: center;
          align-items: center;
  position: relative;
  min-height: 200px;
  width:100%;
  border: 2px dashed green;
  
}

.file-upload input[type=file]{
    
  position: absolute;
  left: 0;
  top: 0;
  opacity: 0;
  width: 100%;
  height: 100%;
  cursor: pointer;
}
.file-upload .file-upload-res{
    width: 100%;
    text-align: center;
    color:gray;
}



</style>
<!-- ============================================================================================================================
                                                        EVENTOS PAGINA REQUERIR    
    ============================================================================================================================ -->
<script src="vistas/js/remisiones.js"></script>

</div>


