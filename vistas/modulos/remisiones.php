<script src="vistas\lib\dropzone.js\dropzone.js"></script>
<div class="" >

    
    <h2 class="header center ">Subir Requisición</h3>

    <!-- <form role="form" method="post" enctype="multipart/form-data" style="padding-left:15px;" id="subir"> -->
<!-- ============================================================================================================================
                                                    INPUTS   
============================================================================================================================ -->
        <div class="row container">
        
            <form action="/file-upload"
            class="dropzone col s12"
            id="my-awesome-dropzone">
            </form>
            
            
        </div>
<!-- ============================================================================================================================
                                                    ICONO DE CARGA  
============================================================================================================================ -->
        <div class="row hide" id="carga"  style="padding-top:15vh;">

            <div class="col offset-s4 offset-l6 offset-m5 " >

                <div class="preloader-wrapper big active ">
                    <div class="spinner-layer spinner-green-only">

                        <div class="circle-clipper left">
                            <div class="circle"></div>
                        </div>
                        <div class="gap-patch">
                            <div class="circle"></div>
                        </div>
                        <div class="circle-clipper right">
                            <div class="circle"></div>
                        </div>

                    </div>

                </div>
            </div>

        </div>


<!-- ============================================================================================================================
                                                        EVENTOS PAGINA REQUERIR    
    ============================================================================================================================ -->
<script src="vistas/js/remisiones.js"></script>

</div>


