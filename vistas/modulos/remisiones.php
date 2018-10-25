<div class="" >

    
    <h2 class="header center ">Subir Requisición</h3>

    <!-- <form role="form" method="post" enctype="multipart/form-data" style="padding-left:15px;" id="subir"> -->
    <form>
<!-- ============================================================================================================================
                                                    INPUTS   
============================================================================================================================ -->
        <div class="file-field input-field row">
        
            <div class="btn green darken-2 col s2 m3 l1 offset-l3 offset-m1">

                <span><i class="fas fa-upload"></i>Subir</span>
                <input type="file" class="archivo" name="archivo" id="archivo" multiple >
                
            </div>
            

            <div class="file-path-wrapper col s9 m7 l5" >

                <input class="file-path validate" type="text" placeholder="Subir archivo .PO1" id="urlarchivo" >

            </div>
            
            
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

        <div class="row">
<!-- ============================================================================================================================
                                                      SUBIR ARCHIVO
    ============================================================================================================================ -->
            
        </div>

    </form>

<!-- ============================================================================================================================
                                                        EVENTOS PAGINA REQUERIR    
    ============================================================================================================================ -->
<script src="vistas/js/requerir.js"></script>

</div>


