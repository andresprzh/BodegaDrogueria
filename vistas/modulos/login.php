  
  
  <main class="login " >
    <center>
      <img class="responsive-img" style="width: 20vh;" src="vistas\imagenes\plantilla\logo.svg" />       

      <div class=" row">
      
        <div class="z-depth-1 col s10 m6 l4 offset-s1 offset-m3  offset-l4 login-box" >

          <form class="col s12"  id="login">
            
            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='usuario' id='usuario' required />
                <label for='usuario'><i class="fas fa-user"></i> Ingrese Usuario</label>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col  s12'>
                <input class='validate' type='password' name='password' id='password' required />
                <label for='password'><i class="fas fa-key"></i> Contraseña</label>
              </div>
            </div>

            <center>
              <div class='row'>
                <button type='submit' name='btn_login' class='col  s12 btn btn-large waves-effect green'>Ingresar</button>
              </div>
             <div class='card-panel  red darken-4 hide' id='error'>Error al ingresas, vuelva a intentar</div>
            </center>

              <?php
        
                // $login = new ControladorUsuarios();
                // $login->ctrIngresoUsuario();

              ?>

          </form>

        </div>

      </div>
     
    </center>
  </main>

<script src="vistas/js/login.js">

</script>


