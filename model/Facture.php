<?php
require "../config/conexion.php";

class Facture
{
    public function __construct()
    {
    }
    public function index()
    {
        $sql = "SELECT FEC_COMPRA as fecha_documento, IDTIPO as tipo_documento,prefijo,V.ID as numero,NOFACTURA 
        as nota,IDFORMPAGO 
        as metodo_pago,SUBTOTAL AS valor_bruto,V.IVA as valor_iva,RETEFUENTE as valor_retencion,DCTO 
        as valor_descuento,V.TOTAL as valor_neto,
        FEC_VENC as fecha_expiracion
        FROM ventas v 
        INNER JOIN tipos_facturas TP
        ON V.IDTIPO = TP.ID";
        return ejecutarConsulta($sql);
    }

    public function cabezera($fecha)
    {
        $sql = "SELECT 
        V.ID as IDF, V.NOFACTURA as numero,V.FEC_COMPRA as fecha_documento,TP.prefijo as prefijo,
        V.NOFACTURA as facturap,V.PEDIDO as pedido, 
        V.IDFORMPAGO as metodo_pago, FO.DESCRIPCION AS manera_pago,
        V.SUBTOTAL as valor_bruto, V.IVA as valor_iva,
        V.RETEFUENTE as valor_retencion,DCTO as valor_descuento, 
        V.TOTAL as valor_neto,V.FEC_VENC as fecha_expiracion,V.OBSERVACION as observacion,
        C.CODIGO as codigo, C.NIT as nit ,C.REPRESENTANTE as nombres ,C.tipo_regimen as tipo_regimen,
        CI.CODDIAN as ciudad,CI.ciudad as ubicacion_envio ,B.nombre as barrio,
        C.TELEFONOS as telefono,C.DIRECCION as direccion,
        C.ID as documento, C.EMPRESA as punto_venta,
        C.DPTO as departamento,c.cliente as tipo_persona,U.NOMBRE as asesor,U.DOC as zona
        FROM ventas V 
        INNER JOIN clientes C 
        ON V.TERCERO = C.CODIGO 
        LEFT JOIN barrios B 
        ON C.BARRIO = B.codigo 
        INNER JOIN tipos_facturas TP
        ON V.IDTIPO = TP.ID 
        INNER JOIN usuarios U
        ON U.USUARIO = V.VENDEDOR
        INNER JOIN ciudades CI
        ON CI.CODIGO = C.CIUDAD
        INNER JOIN formas_pagos FO
        ON FO.ID = V.IDFORMPAGO
        WHERE   V.FEC_COMPRA = '$fecha' and TP.ID NOT IN(7)";
        return ejecutarConsulta($sql);
    }
    public function cabezeraunica($nofactura)
    {
        $sql = "SELECT 
        V.ID as IDF, V.NOFACTURA as numero,V.FEC_COMPRA as fecha_documento,TP.prefijo as prefijo,
        V.NOFACTURA as facturap,V.PEDIDO as pedido, 
        V.IDFORMPAGO as metodo_pago, FO.DESCRIPCION AS manera_pago,
        V.SUBTOTAL as valor_bruto, V.IVA as valor_iva,
        V.RETEFUENTE as valor_retencion,DCTO as valor_descuento, 
        V.TOTAL as valor_neto,V.FEC_VENC as fecha_expiracion,V.OBSERVACION as observacion,
        C.CODIGO as codigo, C.NIT as nit ,C.REPRESENTANTE as nombres ,C.tipo_regimen as tipo_regimen,
        CI.CODDIAN as ciudad,CI.ciudad as ubicacion_envio ,B.nombre as barrio,
        C.TELEFONOS as telefono,C.DIRECCION as direccion,
        C.ID as documento, C.EMPRESA as punto_venta,
        C.DPTO as departamento,c.cliente as tipo_persona,U.NOMBRE as asesor,U.DOC as zona
        FROM ventas V 
        INNER JOIN clientes C 
        ON V.TERCERO = C.CODIGO 
        LEFT JOIN barrios B 
        ON C.BARRIO = B.codigo 
        INNER JOIN tipos_facturas TP
        ON V.IDTIPO = TP.ID 
        INNER JOIN usuarios U
        ON U.USUARIO = V.VENDEDOR
        INNER JOIN ciudades CI
        ON CI.CODIGO = C.CIUDAD
        INNER JOIN formas_pagos FO
        ON FO.ID = V.IDFORMPAGO
        WHERE    TP.ID NOT IN(7) and V.nofactura = '$nofactura'";
        return ejecutarConsulta($sql);
    }
    public function notacredito($fecha)
    {
        $sql = " SELECT 
        CO.ID AS id, TP.PREFIJO AS prefijo, CO.CONSECUTIVO AS consecutivo, 
        CO.NOFACTURA AS facturap,CO.NOFACTURA AS vnot,
        CO.FEC_COMPRA AS fecha_documento,CO.IDTIPO AS tipo_documento,
        CO.SUBTOTAL AS valor_bruto, CO.IVA AS valor_iva,
        CO.RETEFUENTE AS valor_retencion,CO.TOTAL AS valor_neto,
        CO.FEC_VENC AS fecha_expiracion,CO.OBSERVACION AS observacion,
        FO.DESCRIPCION AS manera_pago ,
        C.CODIGO AS codigo,C.NIT AS nit ,C.REPRESENTANTE AS nombres ,
        C.TIPO_REGIMEN AS tipo_regimen,
        C.TELEFONOS AS telefono,C.DIRECCION AS direccion,
        C.ID AS documento, C.EMPRESA AS punto_venta,
        C.DPTO as departamento,C.CLIENTE AS tipo_persona,
        CI.CODDIAN AS ciudad,CI.CIUDAD AS ubicacion_envio ,B.NOMBRE AS barrio,
        U.NOMBRE AS asesor,U.DOC AS zona,V.PEDIDO AS pedido
        FROM COMPRAS  CO
        INNER JOIN VENTAS V
        ON V.NOFACTURA = CO.NOFACTURA
        INNER JOIN FORMAS_PAGOS FO
        ON FO.ID = V.IDFORMPAGO
        INNER JOIN TIPOS_FACTURAS TP
         ON V.IDTIPO = TP.ID 
         INNER JOIN USUARIOS U
         ON U.USUARIO = V.VENDEDOR
        INNER JOIN CLIENTES C
        ON V.TERCERO = C.CODIGO
        LEFT JOIN BARRIOS B 
        ON C.BARRIO = B.CODIGO
        INNER JOIN CIUDADES CI
        ON CI.CODIGO = C.CIUDAD
         WHERE CO.FEC_COMPRA  = '$fecha'";
        return ejecutarConsulta($sql);
    }
    //DETALLE VENTA
    public function detalle($id)
    {
        $sql = "SELECT P.IDMARCA as tipo,P.CODIGO as codigo,P.NOMBRE as nombre,VD.UNID as cantidad ,VD.CAJA as caja,
        VD.VRCAJA as valor_caja,
        VD.IDBODEGA as bodega,
        VD.VRUNITARIO  as valor_referencial,VD.VRUNITARIO as valor_unitario_bruto, VD.TOTAL as subtotal,
        VD.IVA as iva,VD.DESCUENTOA AS descuentoA,VD.DESCUENTOB as descuentoB,VD.TOTAL as totalvd
        FROM ventas_detalles VD
        INNER JOIN productos P 
        ON VD.IDPROD = P.REFERENCIA
        WHERE VD.IDVENTA ='$id'";
        return ejecutarConsulta($sql);
    }
    //DETALLE COMPRA
    public function compraDetalle($id)
    {
        $sql = "SELECT 
        P.CODIGO AS codigo,P.NOMBRE AS nombre,
        CD.UNID AS cantidad,CD.CAJA AS caja,
        CD.IDBODEGA AS bodega,CD.VRUNITARIO AS valor_referencial,
        CD.VRUNITARIO AS valor_unitario_bruto, CD.TOTAL as subtotal,
        CD.IVA AS iva,CD.TOTAL AS totalcd
        FROM COMPRAS_DETALLES CD
        INNER JOIN PRODUCTOS P 
        ON CD.IDPROD = P.REFERENCIA
        WHERE CD.IDCOMPRA = '$id'";
        return ejecutarConsulta($sql);
    }
}