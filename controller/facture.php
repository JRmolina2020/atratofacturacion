<?php
require_once "../model/Facture.php";
require "inc/zipfile.inc.php";
require "authapi.php";
require "clear.php";

class App
{
    public $fac;
    public $id;
    public $rspta;
    public $reg;
    public $fecha; //fecha actual.zip
    public $fechac; //fecha consulta parametro
    public $nofac; //parametro
    public $detalle;
    public $clear;

    //variables globales para producto
    public $tipo; //tipo_producto
    //descuentos
    public $descuento;
    public $cantidadunit;
    public $cantidadcajaunit;
    public $valor_unit;
    public $valor_unitcaja;

    public function __construct()
    {
        $this->fac = new Facture();
        //parametro para la consulta por fecha
        $this->fechac = isset($_POST["fecha"]) ? ($_POST["fecha"]) : "";
        $this->nofac = isset($_POST["facturaunica"]) ? ($_POST["facturaunica"]) : "";
        if (empty($this->nofac)) {
            $this->rspta = $this->fac->cabezera($this->fechac);
        } else {
            $this->rspta = $this->fac->cabezeraunica($this->nofac);
        }
        date_default_timezone_set("America/Bogota");
        $this->fecha = date("Y-m-d");
        //clear function
        $this->clear = new Clear();
        //inicializaciones
        $this->tipo = 1;
        $this->descuento = 0;
    }
    function detalle($id)
    {
        $id = $id;
        $this->detalle = array();
        $this->rsptad = $this->fac->detalle($id);
        while ($this->reg = $this->rsptad->fetch_object()) {

            //Valindando la cantidad de productos si es en caja o si es por unidad
            if ($this->reg->cantidad == 0) { //si la cantidad und es 0 es por que es una caja
                $cantidad = $this->reg->caja; //le asignamos a la cantidad el total de cajas
                $valor_unitario_bruto = $this->reg->valor_caja;
                $embalaje = 'caja';
                //descuento
                $this->valor_unitcaja = $valor_unitario_bruto;
                $this->cantidadcajaunit = $cantidad;
            } else {
                $cantidad = $this->reg->cantidad;
                $this->cantidadunit = $cantidad;
                $embalaje = 'und';
                if (
                    $this->reg->valor_unitario_bruto < 0.01 || $this->reg->valor_unitario_bruto == ""
                    || $this->reg->valor_unitario_bruto == 0
                ) {
                    $valor_unitario_bruto = 0.01;
                    $this->tipo = 4;
                } else {
                    $valor_unitario_bruto = $this->reg->valor_unitario_bruto;
                    $this->valor_unit = $valor_unitario_bruto; //obteniedo el valor de la unidad para el descuento 
                }
            }
            //Validando si el producto dado es regalo o no.
            if ($this->reg->totalvd == 0) {
                $this->tipo = 4;
                $this->reg->descuentoA = 0.0;
                $this->reg->descuentoB = 0.0;
                $valor_unitario_bruto = 0.01;
            } else {
                $this->tipo = 1;
                $this->reg->descuentoA =  $this->reg->descuentoA;
                $this->reg->descuentoB =  $this->reg->descuentoB;
            }

            //VALIDANDO DESCUENTO
            if ($this->reg->cantidad == 0) { //SI LA CANTIDAD ES 0 ES POR QUE ES UNA CAJA
                if ($this->reg->descuentoA == 0) {
                    $base = 0;
                } else {
                    $base1 = $this->valor_unitcaja * $this->cantidadcajaunit;
                    $db = $base1 * $this->reg->descuentoB / 100;
                    $base = $db;
                    $base = $base1 - $db;
                    //  $tot = $base;
                    // $base = $tot * $this->reg->descuentoA / 100;
                }
            } else {
                if ($this->reg->descuentoA == 0) { // ES UNA UNIDAD
                    $base = 0;
                } else {
                    $base1 = $this->valor_unit * $this->cantidadunit;
                    $db = $base1 * $this->reg->descuentoB / 100;
                    $base = $db;
                    $base = $base1 - $db;
                    // $tot = $base;
                    //$base = $tot * $this->reg->descuentoA / 100;
                }
            }

            //##########################################################################################
            $this->detalle[] = array(
                "tipo" => $this->tipo,
                "marca" => "",
                "codigo" => $this->reg->codigo,
                "nombre" =>  $this->clear->cadena($this->reg->nombre),
                "cantidad" => $cantidad,
                "impuestos" => array(
                    array(
                        "tipo" => "01",
                        "porcentaje" => $this->reg->iva
                    )
                ),
                "descuentos" => array(
                    array(
                        "razon" => "DescuentoB",
                        "valor" => 0.0,
                        "codigo" => "00",
                        "porcentaje" =>  $this->reg->descuentoB
                    ),
                    array(
                        "razon" => "DescuentoA",
                        "valor" => 0.0,
                        "codigo" => "00",
                        "base" => round($base, 2),
                        "porcentaje" =>  $this->reg->descuentoA
                    ),
                ),
                "extensibles" =>
                array(
                    "tipo_embalaje" => "",
                    "tipo_empaque" => $embalaje,
                    "bodega" => $this->reg->bodega
                ),

                "tipo_gravado" => 1,
                "valor_referencial" => 0.0,
                "valor_unitario_bruto" => $valor_unitario_bruto,
                "valor_unitario_sugerido" => $this->reg->valor_caja
            );
        }
        return ($this->detalle);
    }
    //#################################################################################
    function Consultas()
    {
        //VALIDACIONES
        while ($this->reg = $this->rspta->fetch_object()) {
            //validando departamento
            if ($this->reg->departamento == null) {
                $departamento = 20;
            } else {
                $departamento = $this->reg->departamento;
            }
            //Validando la ciudad del cliente.
            if ($this->reg->ciudad == "") {
                $ciudad = 20001;
            } else {
                $ciudad = $this->reg->ciudad;
            }
            //validando el barrio del cliente
            $barrio = $this->reg->barrio;

            //Valindado el telefono del cliente
            if ($this->reg->telefono == "" || $this->reg->telefono == 0 || $this->reg->telefono == 1) {
                $telefono = 11111111;
            } else {
                $telefono = substr($this->reg->telefono, 0, 10); //recortando telefonos a 10 digitos
            }
            //validando el metodo de pago
            if (
                $this->reg->metodo_pago == 1 || $this->reg->metodo_pago == 13
                ||  $this->reg->metodo_pago == 14
            ) {
                $metodo_pago = 1; //contado
            } else {
                $metodo_pago = 2; //credito
            }
            //Vvalidando el tipo de regimen
            if ($this->reg->tipo_regimen == null || $this->reg->tipo_regimen == 0) {
                $tipo_regimen = 49;
            } else {
                $tipo_regimen = $this->reg->tipo_regimen;
            }

            //quintando prefijo al numero de la factura EJEM: B123 -> 123
            $numero = preg_replace('/[^0-9]/', '', $this->reg->numero);
            //Validando nit
            $nit  = str_replace('.', '', $this->reg->nit);
            $nit = preg_replace('/-/', '', $nit);
            $nit = substr($nit, 0, 10);
            //Quitando las letras del pedido EJEM : APP123 -> 123
            $pedido = preg_replace('/[^0-9]/', '', $this->reg->pedido);
            //end
            //Validar el mensaje de resolucion 
            if ($this->reg->prefijo == "B") {
                $resolucion = "RESOLUCION DIAN 18762009353951 FECHA: 2018/07/25 DEL No. b109728 AL No. b200000 prefijo[B] habilita.";
            } elseif ($this->reg->prefijo == "C") {
                $resolucion = "RESOLUCION DIAN 18762009353951 FECHA: 2018/07/25 DEL No. c17612 AL No. c30000 PREFIJO [C] habilita.";
            } elseif ($this->reg->prefijo == "TAT") {
                $resolucion = "Res. Dian No. 18762010933894 Fecha : 2018-10-25 Del TAT 19229 al tat 30000 habilita FACTURA POR COMPUTADOR.";
            } elseif ($this->reg->prefijo == "F") {
                $resolucion = "RESOLUCION DIAN 240000035883 FECHA: 2015/09/21 DEL No. 776 AL No. 10000 PREFIJO [F] HABILITA.";
            } elseif ($this->reg->prefijo == "V") {
                $resolucion = "Res. Dian No. 240000018505 Fecha : 2009-07-10 Del V-1 al 4000 HABILITA FACTURA POR COMPUTADOR.";
            } elseif ($this->reg->prefijo == "FF") {
                $resolucion = "RESOLUCION DIAN 18762015697813 FECHA: 2019/07/15 DEL No. 30001 AL No. 50000 PREFIJO [FF] habilita.";
            } else if ($this->reg->prefijo == "B1") {
                $resolucion = "FACTURACION ELECTRONICA N° 18763002254005 AUTORIZA DESDE B1 HASTA B10000000 02/10/2019 HASTA 02/10/2021";
            } elseif ($this->reg->prefijo == "C1") {
                $resolucion = "RESOLUCION DIAN 18762009353951 FECHA: 2018/07/25 DEL No. c17612 AL No. c30000 PREFIJO [C] habilita.";
            } elseif ($this->reg->prefijo == "TAT1") {
                $resolucion = "Res. Dian No. 18762010933894 Fecha : 2018-10-25 Del TAT 19229 al tat 30000 habilita FACTURA POR COMPUTADOR.";
            } elseif ($this->reg->prefijo == "F1") {
                $resolucion = "RESOLUCION DIAN 240000035883 FECHA: 2015/09/21 DEL No. 776 AL No. 10000 PREFIJO [F] HABILITA.";
            } elseif ($this->reg->prefijo == "V1") {
                $resolucion = "Res. Dian No. 240000018505 Fecha : 2009-07-10 Del V-1 al 4000 HABILITA FACTURA POR COMPUTADOR.";
            } elseif ($this->reg->prefijo == "FF1") {
                $resolucion = "RESOLUCION DIAN 18762015697813 FECHA: 2019/07/15 DEL No. 30001 AL No. 50000 PREFIJO [FF] habilita.";
            } else {
                $resolucion = "";
            }
            $observacion = str_replace("\r\n", '', $this->reg->observacion);
            //ARRAYS
            $data[] =  array(
                "nota" => $observacion,
                "numero" => $numero,
                "codigo_empresa" => 59,
                "tipo_documento" => '01',
                "prefijo" =>  $this->reg->prefijo,
                'fecha_documento' => $this->reg->fecha_documento,
                "valor_descuento" =>  $this->reg->valor_descuento,
                "anticipos" => null,
                "valor_ico" => 0.0,
                "valor_iva" => $this->reg->valor_iva,
                "valor_bruto" => $this->reg->valor_bruto,
                "valor_neto" => $this->reg->valor_neto,
                "metodo_pago" => $metodo_pago,
                "valor_retencion" => $this->reg->valor_retencion,
                "factura_afectada" => 0,
                "fecha_expiracion" =>  $this->reg->fecha_expiracion,
                //CLIENTES ARRAY
                'cliente'     => array(
                    "codigo" => $this->reg->codigo,
                    "nombres" =>  $this->clear->cadena($this->reg->nombres),
                    "apellidos" => $this->clear->cadena($this->reg->nombres),
                    "departamento" => $departamento,
                    "ciudad" => $ciudad,
                    "barrio" => $this->clear->cadena($barrio) . "-" . $this->reg->ubicacion_envio,
                    "correo" => "",
                    "telefono" => intval($telefono),
                    "direccion" =>  $this->clear->cadena($this->reg->direccion),
                    "documento" => $nit,
                    "punto_venta" =>  $this->reg->codigo,
                    "obligaciones" => ["ZZ"],
                    "razon_social" => $this->clear->cadena($this->reg->nombres),
                    "punto_venta_nombre" => $this->clear->cadena($this->reg->punto_venta),
                    "codigo_postal" => "000000",
                    "nombre_comercial" => $this->clear->cadena($this->reg->punto_venta),
                    "numero_mercantil" => 0,
                    "informacion_tributaria" => "ZZ",
                    "tipo_persona" => 1,
                    "tipo_regimen" => $tipo_regimen,
                    "es_responsable_iva" => false,
                    "tipo_identificacion" => 13,

                ),
                'factura'     => array(
                    "moneda" => null,
                    "subtipo_factura" => "10",
                    "intercambio_acordado" => 0.0
                ),
                'pagos'     => array(
                    array(
                        "fecha" =>  $this->reg->fecha_documento,
                        "valor" => 0.0,
                        "metodo_pago" => $metodo_pago,
                        "detalle_pago" => "ZZZ",
                    )
                ),
                'descuentos'     => array(
                    array(
                        "razon" => null,
                        "valor" => $this->reg->valor_descuento,
                        "codigo" => null,
                        "porcentaje" => 0.0
                    )
                ),
                'extensibles'     => array(
                    "resolucion" => $resolucion,
                    "manera_pago" => $this->reg->manera_pago,
                    "zona" => $this->reg->zona,
                    "asesor" => $this->clear->cadena($this->reg->asesor),
                    "pedido" => $pedido,
                    "peso" => 0.0,
                    "orden" => 0,
                    "canastas" => 0,
                    "planilla" => "",
                    "logistica" => "",
                    "recibo_caja" => 0.0,
                    "distribucion" => "",
                    "asesor_numero" => 0,
                    "logistica_numero" => 0,
                    "cantidad_productos" => 0,
                    "distribucion_numero" => 0,
                ),
                'nota_debito'     => array(
                    "razon" => 4,
                    "factura" => $this->reg->facturap,
                    "id_felam" => 0,
                    "tipo_documento" => "",
                    "descripcion_razon" => ""
                ),
                'nota_credito'     => array(
                    "razon" => 5,
                    "factura" => $this->reg->facturap,
                    "id_felam" => 0,
                    "tipo_documento" => "23",
                    "descripcion_razon" => "En este apartado se genera la nota credito con fines internos entre la empresa y el cliente referente"
                ),
                //productos
                'productos'     =>  $this->detalle($this->reg->IDF)
            );
        }

        //END
        if (empty($data)) {
            header("Location: ../view/errfacture.php");;
            die();
        } else {
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            // $jstring =  json_encode($data, true);
            // $zip = new ZipArchive();
            // $filename = "archivo-" . $this->fecha . ".zip";
            // if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
            //     exit("cannot open <$filename>\n");
            // }
            // $zip->addFromString("archivo-" . $this->fecha . ".txt", $jstring);
            // $zip->close();
            // $api = new Login();
            // $api->Uploader($filename);
        }
    }
}

$app = new App();
$app->Consultas();