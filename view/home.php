<?php
require 'header.php';
?>



<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home">Facturas</a></li>
    <li> <a data-toggle="tab" href="#home2">Factura unica</a></li>
    <li><a data-toggle="tab" href="#menu1">Notas creditos</a></li>
</ul>

<div class="tab-content">
    <div id="home" class="tab-pane fade in active">
        <H3>FACTURAS GENERAL</H3>
        <div class="row mt-5">
            <div class="col-lg-6 col-md-12">
                <form method="post" action="../controller/facture.php">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="form-group">
                                <input class="form-control" placeholder="digitar fecha factura" type="text" readonly
                                    name="fecha" id="datepicker">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <input class="btn btn-danger" type="submit" value="Enviar facturas">
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- Fin row -->
        <br><br>
        <div class="alert alert-danger mt-5" role="alert">
            <strong>AQUI PODRA ENVIAR TODAS LAS FACTURAS DEL DIA</strong>
        </div>
    </div>
    <div id="home2" class="tab-pane fade ">
        <H3>FACTURA UNICA</H3>
        <div class="row mt-5">
            <div class="col-lg-6 col-md-12">
                <form method="post" action="../controller/facture.php" autocomplete="off">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="form-group">
                                <div class="form-group">
                                    <label for=""></label>
                                    <input type="text" class="form-control" name="facturaunica" id="facturaunica"
                                        placeholder="NUMERO DE FACTURA: EJEM:B1229">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <input class="btn btn-danger" type="submit" value="Enviar factura unica">
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- Fin row -->
        <br><br>
        <div class="alert alert-danger mt-5" role="alert">
            <strong>POR FAVOR VERIFIQUE BIEN EL NUMERO DE LA FACTURA</strong> ANTES DE MANDAR
        </div>
    </div>
    <div id="menu1" class="tab-pane fade">
        <H3>NOTAS CREDITOS</H3>
        <div class="row mt-5">
            <div class="col-lg-6 col-md-12">
                <!-- Contenido -->
                <form method="post" action="../controller/note_credit.php">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="form-group">
                                <input class="form-control" placeholder="digitar fecha nota credito" type="text"
                                    readonly name="fecha" id="datepickernota">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <input class="btn btn-primary" type="submit" value="Enviar nota credito">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require 'footer.php';
?>
<script>
$(function() {
    $("#datepicker").datepicker({
        format: 'yyyy-mm-dd',
        language: 'es'
    });
    $("#datepickernota").datepicker({
        format: 'yyyy-mm-dd',
        language: 'es'
    });
});
</script>