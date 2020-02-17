function init() {
  $("#alert_error").hide();
  validar_mensaje();
  validar();
  $("#username").focus();
}
function validar_mensaje() {
  $("#frmAcceso").on("submit", function(e) {
    e.preventDefault();
    username = $("#username").val();
    password = $("#password").val();
    $.post(
      "controller/auth.php?op=login",
      { username: username, password: password },
      function(data) {
        data = JSON.parse(data);
        if (data != null) {
          $(location).attr("href", "view/index.php");
        } else {
          Swal.fire({
            icon: "error",
            title: "Denegado",
            text: "Verifica las credenciales!"
          });
          limpiar();
        }
      }
    );
  });
}
function limpiar() {
  $("#username").focus();
  $("#username").val("");
  $("#password").val("");
}
function validar() {
  $(document).ready(function() {
    $("#frmAcceso").bootstrapValidator({
      fields: {
        username: {
          message: "Nombre invalido",
          validators: {
            notEmpty: {
              message:
                "El nombre de usuario es obligatorio,no puede estar vacio."
            },
            stringLength: {
              min: 5,
              max: 20,
              message: "Minimo 5 caracteres y Maximo 20 caracteres"
            }
          }
        },
        password: {
          validators: {
            notEmpty: {
              message: "El password es obligatorio y no puede estar vacio."
            },
            stringLength: {
              min: 4,
              max: 20,
              message: "Minimo 5 caracteres Max 20 caracteres"
            }
          }
        }
      }
    });
  });
}
init();
