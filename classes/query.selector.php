<?php

/****************************************************************************************************************************************
*  Consulta que obtiene Usuarios de crédito de una empresa, con sus repectivos cupos y montos autorizados y fechas de facturación
*****************************************************************************************************************************************
SELECT c.cliente_rut as rut, c.cliente_nombre as nombre, c.cliente_apellido_paterno as paterno, c.cliente_apellido_materno as materno,
l.linea_credito_monto_autorizado as autorizado, l.linea_credito_cupo as cupo, l.linea_credito_fecha_facturacion as facturacion, l.linea_credito_fecha_pago as fechaPago, l.linea_credito_saldo_favor as aFavor
FROM `cliente` AS c
JOIN linea_credito AS l ON l.cliente_id=c.cliente_id

*****************************************************************************************************************************************/

/****************************************************************************************************************************************
*  Consulta que obtiene las promociones sobre total Activas
*****************************************************************************************************************************************

SELECT p.promocion_especial_id as id, p.promocion_especial_descripcion as nombre, p.promocion_especial_porcentaje as porcentaje, p.promocion_especial_tipo_id as tipo
FROM `promocion_especial` as p WHERE p.promocion_especial_estado=1
*****************************************************************************************************************************************/

/****************************************************************************************************************************************
*  Consulta que obtiene las categorías cuya descripción se encuentra repetida
*****************************************************************************************************************************************

SELECT nombre, total FROM (SELECT COUNT(categoria_descripcion) as total ,categoria_descripcion as nombre FROM `categoria` GROUP BY categoria_descripcion) AS verificacion WHERE total>1
*****************************************************************************************************************************************/

/****************************************************************************************************************************************

*Consulta que obtiene el monto a pagar por un cliente, obtiene todas las cuotas con estado 0 que tienen fecha de pago inferior a fecha de facturacion

SELECT SUM(cu.cuota_monto) as monto FROM cuota cu JOIN venta_credito vc ON cu.venta_credito_id = vc.venta_credito_id JOIN linea_credito lc ON vc.linea_credito_id = lc.linea_credito_id JOIN cliente c ON lc.cliente_id = c.cliente_id WHERE cu.cuota_estado = 0 AND cu.cuota_fecha_pago < lc.linea_credito_fecha_facturacion AND c.cliente_rut = '$rut'

*****************************************************************************************************************************************/

?>
