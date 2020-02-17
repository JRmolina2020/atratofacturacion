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
    public $detalle;
    public $clear;

    //variables globales para producto
    public $tipo; //tipo_producto


    public function __construct()
    {
        $this->fac = new Facture();
        //parametro para la consulta por fecha
        $this->fechac = isset($_POST["fecha"]) ? ($_POST["fecha"]) : "";
        $this->rspta = $this->fac->notacredito($this->fechac);
        $this->clear = new Clear();
        date_default_timezone_set("America/Bogota");
        $this->fecha = date("Y-m-d");
        $this->tipo = 1;
    }
    function detalle($id)
    {
        $id = $id;
        $this->detalle = array();
        $this->rsptad = $this->fac->compraDetalle($id);
        while ($this->reg = $this->rsptad->fetch_object()) {
            //Valindando la cantidad de productos si es en caja o si es por unidad
            if ($this->reg->cantidad == 0) {
                $cantidad = $this->reg->caja;
                $valor_unitario_bruto = $this->reg->valor_unitario_bruto;
                $embalaje = 'caja';
            } else {
                $cantidad = $this->reg->cantidad;
                $embalaje = 'und';
                if (
                    $this->reg->valor_unitario_bruto < 0.01 || $this->reg->valor_unitario_bruto == ""
                    || $this->reg->valor_unitario_bruto == 0
                ) {
                    $valor_unitario_bruto = 0.01;
                    $this->tipo = 4; //tipo de producto
                } else {
                    $valor_unitario_bruto = $this->reg->valor_unitario_bruto;
                }
            }
            //validando si el producto dado es regalo o no.
            if ($this->reg->totalcd == 0 ||  $this->reg->valor_unitario_bruto == 0) {
                $this->tipo = 4;
            } else {
                $this->tipo = 1;
            }

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
                        "porcentaje" =>  0.0
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
                "valor_unitario_sugerido" => $this->reg->totalcd
            );
        }
        return ($this->detalle);
    }
    function Consultas()
    {
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
                $telefono = substr($this->reg->telefono, 0, 10);
            }

            if ($this->reg->tipo_documento == 3 || $this->reg->tipo_documento == 6) {
                $tipo_documento = 91;
            }
            //Valindando el tipo de regimen
            if ($this->reg->tipo_regimen == null) {
                $tipo_regimen = 49;
            } else {
                $tipo_regimen = $this->reg->tipo_regimen;
            }
            //Validando el departamento
            if ($this->reg->departamento == null) {
                $departamento = 20;
            } else {
                $departamento = $this->reg->departamento;
            }
            //validando nit
            $nit  = str_replace('.', '', $this->reg->nit);
            $nit = preg_replace('/-/', '', $nit);
            $nit = substr($nit, 0, 10);
            //end nit
            //Quitando las letras del pedido EJEM : APP123 -> 123
            $pedido = preg_replace('/[^0-9]/', '', $this->reg->pedido);
            //VALOR PRUEBA PARA LA NOTA
            //$numero = preg_replace('/[^0-9]/', '', $this->reg->vnot);
            $observacion = str_replace("\r\n", '', $this->reg->observacion);
            $data[] = array(
                "nota" => $observacion,
                "numero" => $this->reg->consecutivo,
                "codigo_empresa" => 59,
                "tipo_documento" => $tipo_documento,
                "prefijo" => $this->reg->prefijo,
                'fecha_documento' => $this->reg->fecha_documento,
                "valor_descuento" =>  0,
                "anticipos" => null,
                "valor_ico" => 0.0,
                "valor_iva" => $this->reg->valor_iva,
                "valor_bruto" => $this->reg->valor_bruto,
                "valor_neto" => $this->reg->valor_neto,
                "metodo_pago" => 1,
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
                    "direccion" => $this->clear->cadena($this->reg->direccion),
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
                        "metodo_pago" => 1,
                        "detalle_pago" => "ZZZ"

                    )
                ),
                'descuentos'     => array(
                    array(
                        "razon" => null,
                        "valor" => 0,
                        "codigo" => null,
                        "porcentaje" => 0.0
                    )
                ),
                'extensibles'     => array(
                    "asesor" => $this->clear->cadena($this->reg->asesor),
                    "pedido" => $pedido,
                    "zona" => $this->reg->zona,
                ),
                'nota_debito'     => array(
                    "razon" => 0,
                    "factura" => "", //$this-reg->facturap contiene el prefijo
                    "id_felam" => 0,
                    "tipo_documento" => "",
                    "descripcion_razon" => ""
                ),
                'nota_credito'     => array(
                    "razon" => 5,
                    "factura" => $this->reg->facturap, //$this-reg->facturap contiene el prefijo
                    "id_felam" => 0,
                    "tipo_documento" => "20",
                    "descripcion_razon" =>  $this->reg->observacion
                ),
                //productos
                'productos'     =>  $this->detalle($this->reg->id)
            );
        }
        //end productos

        if (empty($data)) {
            header("Location: ../view/errnote.php");;
            die();
        } else {
            //echo json_encode($data);
            $jstring =  json_encode($data, true);
            $zip = new ZipArchive();
            $filename = "archivo-" . $this->fecha . ".zip";
            if ($zip->open($filename, ZipArchive::CREATE) !== TRUE) {
                exit("cannot open <$filename>\n");
            }
            $zip->addFromString("archivo-" . $this->fecha . ".txt", $jstring);
            $zip->close();
            $api = new Login();
            $api->Uploader($filename);
        }
    }
}

$app = new App();
$app->Consultas();