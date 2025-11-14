<?php $session = session(); ?>

  <div class="container">
      <main class="col-md-8 col-md-offset-2">
        <div class="login">
          <div class="image-container">
            <img
                alt="FreeComETT"
                class="login__image"
                src="/images/freecomett.svg"
              />
          </div>
          <p class="login__intro">
            Welcome to FreeComETT, FreeUKGenealogy's transcription applications.
            To get started, please select the project you wish to work with.
          </p>
          <div class="login__select">
            <a href="#" class="active">
                <img
                  alt="FreeREG"
                  class="active"
                  src="/images/freereg-logo.svg"
                />
            </a>
            <a href="#">
                <img
                  alt="FreeBMD"
                  src="/images/freebmd.svg"
                />
            </a>
          </div>
          <form action="<?=(base_url('identity/signin_step2')); ?>" class="login__form" method="POST" name="signin" >
            <div class="login__form-field">
              <label for="user-id">User ID</label>
              <input type="text" name="identity" id="identity" placeholder="FreeREG User ID" />
            </div>
            <div class="login__form-field">
              <label for="password">Password</label>
              <input type="password" name="password" id="password" placeholder="FreeREG Password" />
            </div>
            <a href="#">Forgotten Password?</a>
            <input class="login__submit" type="submit" value="Log in">
            <div class="login__register">
              <p>You don't have a FreeREG Identity?</p>

              <a href="https://www.freereg.org.uk/cms/opportunities-to-volunteer-with-freereg.html">Start the FreeREG registration process</a>
            </div>
          </form>
        </div>
      </main>
    </div>
