<?php
require 'header.php';
?>
<?php
require 'footer.php';
?>
<script>
let timerInterval
Swal.fire({
    title: 'HOLA...',
    icon: 'error',
    text: 'NO HAY FACTURAS PARA ESTE DIA',
    timer: 2000,
    timerProgressBar: true,
    onBeforeOpen: () => {
        Swal.showLoading()
        timerInterval = setInterval(() => {
            Swal.getContent().querySelector('b')
                .textContent = Swal.getTimerLeft()
        }, 100)
    },
    onClose: () => {
        clearInterval(timerInterval)
    }
}).then((result) => {
    if (
        result.dismiss === Swal.DismissReason.timer
    ) {
        window.location.assign("home.php")
    }
})
</script>