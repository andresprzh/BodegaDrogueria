<div class="" >

    
    <h2 class="header center ">Subir Requisición</h3>

    <form role="form" method="post" enctype="multipart/form-data" style="padding-left:15px;" id="subir">
<!-- ============================================================================================================================
                                                    INPUTS   
============================================================================================================================ -->
        <div class="file-field input-field row">
        
            <div class="btn green darken-2 col s3 m3 l1 offset-l3 offset-m1">

                <span><i class="fas fa-upload"></i><span class="hide-on-small-only">Subir</span></span>
                <input type="file" class="archivo" name="archivo" id="archivo">
                
            </div>
            

            <div class="file-path-wrapper col s9 m7 l5" >

                <input class="file-path validate" type="text" placeholder="Subir archivo .PO1" id="urlarchivo" >

            </div>
            
            
        </div>

    </form>
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
            <div id="resultado" class="col s11 m10 l6 offset-l3 offset-m1">
            </div>
            <table class="tabla hide" id="tabla"  >

                <thead>
                    <tr class="white-text green darken-3 ">
                        <th>Descripción</th>
                        <th>Codigo de barras</th>
                        <th>ID Item</th>
                        <th>Referencia</th> 
                        <th><span class="truncate">Disponibilidad</span></th>
                        <th><span class="truncate">Solicitados</span></th>              
                        <th>Ubicacion</th>
                    </tr>
                </thead>
                    
                <tbody></tbody>

            </table> 
        </div>



</div>
<style scoped>
    table#tabla td:nth-child(2), td:nth-child(3),td:nth-child(4),th:nth-child(2),th:nth-child(3),th:nth-child(4),td:nth-child(5), td:nth-child(6),th:nth-child(5),th:nth-child(6) {
        width:15%;
    }

@media(max-width:1000px){
    
    #resultado{
        display:none;
    }
    table#tabla td:nth-child(2), td:nth-child(3),td:nth-child(4),th:nth-child(2), th:nth-child(3),th:nth-child(4) {
        display: none;
    }
    table#tabla td:nth-child(5), td:nth-child(6),th:nth-child(5), th:nth-child(6) {
        width:20%;
    }
}
@media(max-width:380px){
    
    table#tabla td:nth-child(2), td:nth-child(3),td:nth-child(4),td:nth-child(7),th:nth-child(2), th:nth-child(3),th:nth-child(4),th:nth-child(7) {
        display: none;
    }
}
</style>
<!-- ============================================================================================================================
                                                        EVENTOS PAGINA REQUERIR    
    ============================================================================================================================ -->
<script src="vistas/js/requerir.js"></script>