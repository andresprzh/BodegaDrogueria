<div class="" >

    
    <h2 class="header center ">Subir Requisición</h3>

    <form role="form" method="post" enctype="multipart/form-data" style="padding-left:15px;" id="subir">
<!-- ============================================================================================================================
                                                    INPUTS   
============================================================================================================================ -->
        <div class="file-field input-field row">
        
            <div class="btn green darken-2 col s2 m3 l1 offset-l3 offset-m1">

                <span><i class="fas fa-upload"></i>Subir</span>
                <input type="file" class="archivo" name="archivo" id="archivo">
                
            </div>
            

            <div class="file-path-wrapper col s9 m7 l5" >

                <input class="file-path validate" type="text" placeholder="Subir archivo .PO1" id="urlarchivo" >

            </div>
            
            
        </div>
<!-- ============================================================================================================================
                                                    ICONO DE CARGA  
============================================================================================================================ -->
        <div class="row  " style="padding-top:15vh;">

            <div class="col offset-s4 offset-l6 offset-m5 hide">

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
            <?php
                
                //comprueba si hay algun error con el archivo
                if (isset($_FILES["archivo"]["tmp_name"])) {
                    
                    if ( 0 < $_FILES['archivo']['error'] ) {
                        echo '<script>
                        swal({
                            title: "¡Error al subir el acrhivo¡",
                            icon: "error"
                        });
                        </script>';
                    }
                    //abre el archivo si no hay errores
                    else {
                        
                        


                        $tipos_permitidos = array ( 'text/plain' );//tipos permitidos de archivos
                        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
                        $tipo = finfo_file( $fileInfo, $_FILES['archivo']['tmp_name'] );//tipo de archivo subido
                        // SI EL ARCHIVO NO ES DE TIPO TEXTO NO LO ABRE
                        if ( !in_array($tipo, $tipos_permitidos) ) {

                            echo ( '<script>
                                swal({
                                    title: "¡Tipo de archivo no valido¡",
                                    icon: "error"
                                });
                                </script> ' );

                        }else {

                            $archivo=file($_FILES['archivo']['tmp_name']); 
                            //se crea objeto requerir, que busca y manda los items a la base de datos
                            $Requerir=new ControladorRequerir($archivo);
                            
                        }
                        
                        finfo_close( $fileInfo );
                        
                        
                    }
                    
                }
                
            ?>
            
        </div>

    </form>

<!-- ============================================================================================================================
                                                        EVENTOS PAGINA REQUERIR    
    ============================================================================================================================ -->
    <script src="vistas/js/requerir.js"></script>

</div>


