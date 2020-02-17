<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="public/bootstrap/bootstrap.min.css">
  <link rel="stylesheet" href="https://rawgit.com/nghuuphuoc/bootstrapvalidator/v0.5.0/vendor/bootstrap/css/bootstrap.css" />
  <link rel="stylesheet" href="https://rawgit.com/nghuuphuoc/bootstrapvalidator/v0.5.0/dist/css/bootstrapValidator.css" />
  <title>Login</title>
</head>

<body>
  <main class="login-form">
    <div class="cotainer mt-5">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <!-- actions -->
          <form id="frmAcceso" method="post" autocomplete="off">
            <div class="mt-5">
              <img src="public/images/logo.png" height="50" height="50" class="rounded mx-auto d-block" alt="no hay imagen">
            </div>
            <div class="form-group row mt-5">
              <label for="email_address" class="col-md-4 col-form-label text-md-right">Usuario</label>
              <div class="col-md-6">
                <input type="text" id="username" class="form-control" name="username" autofocus>
              </div>
            </div>
            <div class="form-group row">
              <label for="password" class="col-md-4 col-form-label text-md-right">Contraseña</label>
              <div class="col-md-6">
                <input type="password" id="password" class="form-control" name="password">
              </div>
            </div>
            <div class="col-md-6 offset-md-4">
              <button type="submit" class="btn btn-primary">
                Ingresar
              </button>
            </div>
        </div>
        </form>
      </div>
    </div>
  </main>
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="public/bootstrap/popper.min.js"></script>
  <script src="public/bootstrap/bootstrap.min.js"></script>
  <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.2/js/bootstrapValidator.min.js"></script>
  <script src="view/scripts/login.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
</body>

</html>