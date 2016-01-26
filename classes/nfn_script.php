<?php
function get_script(){

return "CREATE TABLE IF NOT EXISTS categoria (
  categoria_id int(11) NOT NULL AUTO_INCREMENT,
  categoria_nombre varchar(100) NOT NULL,
  categoria_descripcion varchar(500) NOT NULL,
  categoria_padre int(11) NOT NULL,
  PRIMARY KEY (categoria_id)
  )ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE IF NOT EXISTS categoria_empresa (
  categoria_id int(11) NOT NULL,
  categoria_descripcion varchar(500) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE IF NOT EXISTS categoria_marca (
  categoria_marca_id int(11) NOT NULL,
  categoria_id int(11) NOT NULL,
  marca_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE IF NOT EXISTS categoria_oferta (
  categoria_oferta_id int(11) NOT NULL,
  categoria_id int(11) NOT NULL,
  categoria_oferta int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE IF NOT EXISTS categoria_producto (
  categoria_producto_id int(11) NOT NULL,
  categoria_id int(11) NOT NULL,
  producto_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla ciudad
--

CREATE TABLE IF NOT EXISTS ciudad (
  ciudad_id int(11) NOT NULL,
  ciudad_nombre varchar(50) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla ciudad
--

INSERT INTO ciudad (ciudad_id, ciudad_nombre) VALUES
(1, 'Santiago');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla comuna
--

CREATE TABLE IF NOT EXISTS comuna (
  comuna_id int(11) NOT NULL,
  comuna_nombre varchar(50) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE IF NOT EXISTS venta_anulada (
  venta_anulada_id int(11) NOT NULL AUTO_INCREMENT,
  venta_id int(11) NOT NULL,
  venta_bruto int(11) NOT NULL,
  venta_descuento int(11) NOT NULL,
  venta_neto int(11) NOT NULL,
  venta_usuario_anulacion int(11) NOT NULL,
  venta_anulacion_fecha datetime NOT NULL,
  PRIMARY KEY (venta_anulada_id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla comuna
--

CREATE TABLE registro_usuario_login (
  registro_usuario_login_id int(11) NOT NULL AUTO_INCREMENT,
  usuario_id int(11) NOT NULL,
  registro_usuario_login_fecha datetime NOT NULL,
  PRIMARY KEY (registro_usuario_login_id)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS usuario_respuesta_secreta (
  usuario_respuesta_secreta_id int(11) NOT NULL AUTO_INCREMENT,
  usuario_respuesta_secreta varchar(100) DEFAULT NULL,
  usuario_id int(11) NOT NULL,
  PRIMARY KEY (usuario_respuesta_secreta_id)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla empresa
--

CREATE TABLE IF NOT EXISTS empresa (
  empresa_id int(11) NOT NULL,
  empresa_nombre varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  empresa_rut varchar(12) COLLATE utf8_spanish_ci NOT NULL,
  empresa_direccion varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  empresa_comuna int(11) NOT NULL,
  empresa_ciudad int(11) NOT NULL,
  empresa_region int(11) NOT NULL,
  empresa_pais int(11) NOT NULL,
  categoria_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla empresa_giro
--

CREATE TABLE IF NOT EXISTS empresa_giro (
  empresa_id int(11) NOT NULL,
  giro_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla filtro_oferta
--

CREATE TABLE IF NOT EXISTS filtro_oferta (
  filtro_oferta_id int(11) NOT NULL,
  filtro_codigo varchar(32) COLLATE utf8_spanish_ci NOT NULL,
  filtro_nombre varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  filtro_query varchar(1000) COLLATE utf8_spanish_ci NOT NULL,
  filtro_alias varchar(100) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla filtro_oferta
--

INSERT INTO filtro_oferta (filtro_oferta_id, filtro_codigo, filtro_nombre, filtro_query, filtro_alias) VALUES
(3, 'todos', 'Todas', \"SELECT o.oferta_id as id, o.oferta_proveedor as proveedor, o.oferta_descripcion as descripcion, o.oferta_oferta_tipo as oferta_tipo, o.oferta_descuento as descuento, o.oferta_precio as precio, o.oferta_cantidad as cantidad, o.oferta_fecha_inicio as f_inicio, o.oferta_fecha_termino as f_termino, o.oferta_stock as stock, o.oferta_tipo as tipo, o.producto_id as p_id, o.oferta_estado as estado FROM oferta o ORDER BY oferta_precio\", 'conexion_new.php');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla giro
--

CREATE TABLE IF NOT EXISTS giro (
  giro_id int(11) NOT NULL,
  giro_descripcion varchar(500) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla materia_prima
--

CREATE TABLE IF NOT EXISTS materia_prima (
  materia_prima_id int(11) NOT NULL,
  materia_prima_codigo varchar(32) COLLATE utf8_spanish_ci NOT NULL,
  materia_prima_nombre varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  materia_prima_descripcion varchar(500) COLLATE utf8_spanish_ci NOT NULL,
  materia_prima_unidad int(11) DEFAULT 0,
  materia_prima_unidad_medida varchar(3) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla materia_prima_producto
--

CREATE TABLE IF NOT EXISTS materia_prima_producto (
  materia_prima_producto_id int(11) NOT NULL,
  materia_prima_id int(11) NOT NULL,
  producto_id int(11) NOT NULL,
  materia_prima_unidad int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla materia_prima_sucursal
--

CREATE TABLE IF NOT EXISTS materia_prima_sucursal (
  materia_prima_sucursal_id int(11) NOT NULL,
  materia_prima_id int(11) NOT NULL,
  sucursal_id int(11) NOT NULL,
  materia_prima_sucursal_stock_real int(11) DEFAULT 0,
  materia_prima_sucursal_stock_minimo int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla medio_pago
--

CREATE TABLE IF NOT EXISTS medio_pago (
  medio_pago_id int(11) NOT NULL,
  medio_pago_descripcion varchar(30) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla oferta
--

CREATE TABLE IF NOT EXISTS oferta (
  oferta_id int(11) NOT NULL,
  oferta_fecha_creacion datetime NOT NULL,
  oferta_proveedor int(11) NOT NULL,
  oferta_descripcion varchar(500) COLLATE utf8_spanish_ci NOT NULL,
  oferta_oferta_tipo int(11) NOT NULL,
  oferta_descuento int(255) NOT NULL,
  oferta_precio int(11) NOT NULL,
  oferta_cantidad int(11) NOT NULL,
  oferta_fecha_inicio datetime NOT NULL,
  oferta_fecha_termino datetime NOT NULL,
  oferta_stock int(11) NOT NULL,
  oferta_tipo int(11) NOT NULL,
  oferta_estado tinyint(1) NOT NULL,
  producto_id int(11) NOT NULL,
  producto_precio_original int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla oferta_sucursal
--

CREATE TABLE IF NOT EXISTS oferta_sucursal (
  oferta_sucursal_id int(11) NOT NULL,
  oferta_id int(11) NOT NULL,
  sucursal_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla orden_cancelada
--

CREATE TABLE IF NOT EXISTS orden_cancelada (
  orden_id int(11) NOT NULL,
  usuario_id int(11) NOT NULL,
  orden_cancelada_fecha datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla orden_compra
--

CREATE TABLE IF NOT EXISTS orden_compra (
  orden_id int(11) NOT NULL,
  empresa_solicitada int(11) NOT NULL,
  empresa_solicitante int(11) NOT NULL,
  usuario_id int(11) NOT NULL,
  orden_voucher varchar(32) COLLATE utf8_spanish_ci NOT NULL,
  orden_fecha_creacion datetime NOT NULL,
  orden_fecha_vencimiento datetime NOT NULL,
  orden_total_bruto int(11) NOT NULL,
  orden_total_descuentos int(11) NOT NULL,
  orden_total int(11) NOT NULL,
  orden_estado int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla orden_producto
--

CREATE TABLE IF NOT EXISTS orden_producto (
  orden_producto_id int(11) NOT NULL,
  orden_id int(11) NOT NULL,
  producto_id int(11) NOT NULL,
  producto_valor int(11) NOT NULL,
  producto_cantidad int(11) NOT NULL,
  orden_compra_oferta_id int(11) NOT NULL,
  orden_compra_promocion_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla orden_rechazo
--

CREATE TABLE IF NOT EXISTS orden_rechazo (
  orden_id int(11) NOT NULL,
  orden_rechazo varchar(500) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla pais
--

CREATE TABLE IF NOT EXISTS pais (
  pais_id int(11) NOT NULL,
  pais_nombre varchar(50) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla producto
--

CREATE TABLE IF NOT EXISTS producto (
  producto_id int(11) NOT NULL,
  producto_codigo varchar(32) COLLATE utf8_spanish_ci NOT NULL,
  producto_nombre varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  marca_id int(11) NOT NULL,
  producto_modelo varchar(100) COLLATE utf8_spanish_ci NULL,
  producto_descripcion varchar(500) COLLATE utf8_spanish_ci NULL,
  producto_imagen varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  producto_talla int(11) NULL,
  producto_peso int(11) NULL,
  producto_volumen int(11) NULL,
  producto_dimension int(11) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla producto_dimension
--

CREATE TABLE IF NOT EXISTS producto_dimension (
  producto_dimension_id int(11) NOT NULL,
  producto_alto int(11) DEFAULT NULL,
  producto_alto_unidad_medida varchar(5) COLLATE utf8_spanish_ci NOT NULL,
  producto_largo int(11) DEFAULT NULL,
  producto_largo_unidad_medida varchar(5) COLLATE utf8_spanish_ci NOT NULL,
  producto_ancho int(11) DEFAULT NULL,
  producto_ancho_unidad_medida varchar(5) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla producto_marca
--

CREATE TABLE IF NOT EXISTS producto_marca (
  producto_marca_id int(11) NOT NULL,
  producto_marca_nombre varchar(80) COLLATE utf8_spanish_ci NOT NULL,
  producto_marca_logo varchar(100) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla producto_peso
--

CREATE TABLE IF NOT EXISTS producto_peso (
  producto_peso_id int(11) NOT NULL,
  producto_peso int(11) DEFAULT NULL,
  producto_peso_unidad_medida varchar(10) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla producto_sucursal
--

CREATE TABLE IF NOT EXISTS producto_pesable (
  producto_pesable_id int(11) NOT NULL AUTO_INCREMENT,
  producto_id int(11) NOT NULL,
  PRIMARY KEY (producto_pesable_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla producto_sucursal
--

CREATE TABLE IF NOT EXISTS producto_sucursal (
  producto_sucursal_id int(11) NOT NULL,
  producto_id int(11) NOT NULL,
  sucursal_id int(11) NOT NULL,
  producto_sucursal_precio_unitario int(11) NOT NULL,
  producto_sucursal_precio_mayorista int(11) NOT NULL,
  producto_sucursal_stock_real int(11) NOT NULL,
  producto_sucursal_stock_minimo int(11) NOT NULL,
  producto_sucursal_costo int(11) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla producto_talla
--

CREATE TABLE IF NOT EXISTS producto_talla (
  producto_talla_id int(11) NOT NULL,
  producto_talla varchar(5) COLLATE utf8_spanish_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla producto_volumen
--

CREATE TABLE IF NOT EXISTS producto_volumen (
  producto_volumen_id int(11) NOT NULL,
  producto_volumen int(11) DEFAULT NULL,
  producto_volumen_unidad_medida varchar(2) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla promocion
--

CREATE TABLE IF NOT EXISTS promocion (
  promocion_id int(11) NOT NULL,
  promocion_descripcion varchar(500) COLLATE utf8_spanish_ci NOT NULL,
  promocion_oferta_tipo int(11) NOT NULL,
  promocion_descuento int(255) NOT NULL,
  promocion_precio int(11) NOT NULL,
  promocion_cantidad int(11) NOT NULL,
  promocion_fecha_inicio datetime NOT NULL,
  promocion_fecha_termino datetime NOT NULL,
  promocion_stock int(11) NOT NULL,
  promocion_tipo int(11) NOT NULL,
  promocion_estado tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla promocion_especial
--

CREATE TABLE IF NOT EXISTS promocion_especial (
  promocion_especial_id int(11) NOT NULL,
  promocion_especial_tipo_id int(11) NOT NULL,
  promocion_especial_descripcion varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  promocion_especial_porcentaje int(11) DEFAULT NULL,
  promocion_especial_estado int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla promocion_especial_tipo
--

CREATE TABLE IF NOT EXISTS promocion_especial_tipo (
  promocion_especial_tipo_id int(11) NOT NULL,
  promocion_especial_tipo_descripcion varchar(255) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla promocion_producto
--

CREATE TABLE IF NOT EXISTS promocion_producto (
  promocion_producto_id int(11) NOT NULL,
  promocion_id int(11) NOT NULL,
  producto_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla promocion_sucursal
--

CREATE TABLE IF NOT EXISTS promocion_sucursal (
  promocion_sucursal_id int(11) NOT NULL,
  promocion_id int(11) NOT NULL,
  sucursal_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla region
--

CREATE TABLE IF NOT EXISTS region (
  region_id int(11) NOT NULL,
  region_nombre varchar(50) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla registro_stock
--

CREATE TABLE IF NOT EXISTS registro_stock (
  registro_stock_id int(11) NOT NULL,
  producto_id int(11) NOT NULL,
  sucursal_id int(11) NOT NULL,
  usuario_id int(11) NOT NULL,
  producto_stock_agregado int(11) NOT NULL,
  fecha_stock_agregado datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla registro_stock_materia_prima
--

CREATE TABLE IF NOT EXISTS registro_stock_materia_prima (
  registro_stock_materia_prima_id int(11) NOT NULL,
  materia_prima_id int(11) NOT NULL,
  sucursal_id int(11) NOT NULL,
  usuario_id int(11) NOT NULL,
  registro_stock_materia_prima_cantidad_agregada int(11) NOT NULL,
  registro_stock_materia_prima_fecha datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla sucursal
--

CREATE TABLE IF NOT EXISTS sucursal (
  sucursal_id int(11) NOT NULL,
  empresa_id int(11) NOT NULL,
  sucursal_direccion varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  sucursal_comuna int(11) NOT NULL,
  sucursal_ciudad int(11) NOT NULL,
  sucursal_region int(11) NOT NULL,
  sucursal_pais int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla tag
--

CREATE TABLE IF NOT EXISTS tag (
  tag_id int(11) NOT NULL,
  tag_nombre varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  tag_codigo varchar(100) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla tag_producto
--

CREATE TABLE IF NOT EXISTS tag_producto (
  tag_producto_id int(11) NOT NULL,
  tag_id int(11) NOT NULL,
  producto_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla tipo
--

CREATE TABLE IF NOT EXISTS tipo (
  tipo_id int(11) NOT NULL,
  tipo_descripcion int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla tipo_oferta_promocion
--

CREATE TABLE IF NOT EXISTS tipo_oferta_promocion (
  tipo_oferta_promocion_id int(11) NOT NULL,
  tipo_oferta_promocion_nombre varchar(50) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla tipo_oferta_promocion
--

INSERT INTO tipo_oferta_promocion (tipo_oferta_promocion_id, tipo_oferta_promocion_nombre) VALUES
(1, 'Por precio fijo (3 x $2000)'),
(2, 'Por descuento (%)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla tipo_usuario
--

CREATE TABLE IF NOT EXISTS tipo_usuario (
  tipo_usuario_id int(11) NOT NULL,
  tipo_id int(11) NOT NULL,
  usuario_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla traspaso_stock_registro
--

CREATE TABLE IF NOT EXISTS traspaso_stock_registro (
  traspaso_stock_registro_id int(11) NOT NULL,
  usuario_id int(11) NOT NULL,
  producto_id int(11) NOT NULL,
  sucursal_origen int(11) NOT NULL,
  sucursal_destino int(11) NOT NULL,
  traspaso_stock_registro_cantidad int(11) NOT NULL,
  traspaso_stock_registro_fecha datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla usuario
--

CREATE TABLE IF NOT EXISTS usuario (
  usuario_id int(11) NOT NULL,
  usuario_nombres varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  usuario_apellidos varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  usuario_mail varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  usuario_direccion varchar(100) COLLATE utf8_spanish_ci NOT NULL,
  usuario_avatar varchar(100) COLLATE utf8_spanish_ci NULL,
  usuario_comuna int(11) NULL,
  usuario_ciudad int(11) NULL,
  usuario_region int(11) NULL,
  usuario_pais int(11) NULL,
  usuario_login varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  usuario_pregunta_secreta int(11) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla usuario_empresa
--

CREATE TABLE IF NOT EXISTS usuario_empresa (
  usuario_empresa_id int(11) NOT NULL,
  usuario_id int(11) NOT NULL,
  empresa_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla usuario_pass
--

CREATE TABLE IF NOT EXISTS usuario_pass (
  usuario_id int(11) NOT NULL,
  usuario_pass varchar(32) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla usuario_sucursal
--

CREATE TABLE IF NOT EXISTS usuario_sucursal (
  usuario_sucursal_id int(11) NOT NULL,
  usuario_id int(11) NOT NULL,
  sucursal_id int(11) NOT NULL,
  usuario_sucursal_tipo_cuenta int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla venta
--

CREATE TABLE IF NOT EXISTS venta (
  venta_id int(11) NOT NULL,
  venta_fecha timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  venta_bruto int(11) NOT NULL,
  venta_descuentos int(11) NOT NULL,
  venta_neto int(11) NOT NULL,
  empresa_id int(11) NOT NULL,
  usuario_venta_id int(11) NOT NULL,
  usuario_compra_id int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla venta_acumulada
--

CREATE TABLE IF NOT EXISTS venta_acumulada (
  venta_acumulada_id int(11) NOT NULL,
  tag_id int(11) NOT NULL,
  venta_cantidad int(11) NOT NULL,
  venta_ultima_actualizacion datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla venta_descuento_adicional
--

CREATE TABLE IF NOT EXISTS venta_descuento_adicional (
  venta_descuento_adicional_id int(11) NOT NULL,
  venta_id int(11) NOT NULL,
  venta_descuento_total int(11) NOT NULL,
  promocion_especial_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla venta_pago
--

CREATE TABLE IF NOT EXISTS venta_pago (
  venta_pago_id int(11) NOT NULL,
  venta_id int(11) NOT NULL,
  venta_monto int(11) NOT NULL,
  venta_medio_pago_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla venta_producto
--

CREATE TABLE IF NOT EXISTS venta_producto (
  venta_producto_id int(11) NOT NULL,
  venta_id int(11) NOT NULL,
  producto_id int(11) NOT NULL,
  producto_precio int(11) NOT NULL,
  producto_cantidad int(11) NOT NULL,
  producto_promocion int(11) NOT NULL,
  producto_oferta int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla vuelto
--

CREATE TABLE IF NOT EXISTS vuelto (
  vuelto_id int(11) NOT NULL,
  venta_id int(11) NOT NULL,
  vuelto_total int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla linea_credito
--

CREATE TABLE IF NOT EXISTS cliente (
  cliente_id int(11) NOT NULL,
  cliente_rut varchar(10) NOT NULL,
  cliente_nombre varchar(50) NOT NULL,
  cliente_apellido_paterno varchar(50) NOT NULL,
  cliente_apellido_materno varchar(50) NOT NULL,
  cliente_direccion varchar(255) NULL,
  cliente_comuna_id int(11) NULL,
  cliente_fecha_nacimiento date NULL,
  cliente_fecha_creacion datetime NOT NULL,
  cliente_telefono varchar(30) NULL,
  cliente_correo varchar(100) NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS cuota (
  cuota_id int(11) NOT NULL,
  venta_credito_id int(11) NOT NULL,
  cuota_monto int(11) NOT NULL,
  cuota_fecha_pago date NOT NULL,
  cuota_estado int(11) NOT NULL,
  cuota_fecha_pagada date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS linea_credito (
  linea_credito_id int(11) NOT NULL,
  empresa_id int(11) NOT NULL,
  cliente_id int(11) NOT NULL,
  linea_credito_monto_autorizado int(11) NOT NULL,
  linea_credito_fecha_facturacion date NOT NULL,
  linea_credito_fecha_pago date NOT NULL,
  linea_credito_saldo_favor int(11) NOT NULL,
  plan_credito_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS venta_credito (
  venta_credito_id int(11) NOT NULL,
  linea_credito_id int(11) NOT NULL,
  venta_credito_estado int(11) NOT NULL,
  venta_credito_fecha_otorgada date NOT NULL,
  venta_credito_total_bruto int(11) NOT NULL,
  venta_credito_tasa_interes int(11) NOT NULL,
  venta_credito_valor_cuota_total int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

CREATE TABLE IF NOT EXISTS empresa_contacto (
  empresa_id int(11) NOT NULL,
  empresa_telefono varchar(12) DEFAULT NULL,
  empresa_correo varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE plan_credito (
  plan_credito_id int(11) NOT NULL AUTO_INCREMENT,
  plan_credito_nombre varchar(100) NOT NULL,
  plan_credito_codigo varchar(32) NOT NULL,
  plan_credito_costo_mantencion int(11) NOT NULL,
  plan_credito_costo_uso int(11) NOT NULL,
  plan_credito_estado int(11) NOT NULL,
  PRIMARY KEY (plan_credito_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE plan_pago (
  plan_pago_id int(11) NOT NULL AUTO_INCREMENT,
  plan_pago_nombre varchar(100) NOT NULL,
  plan_pago_codigo varchar(32) NOT NULL,
  plan_pago_cuota int(11) NOT NULL,
  plan_pago_interes int(11) NOT NULL,
  PRIMARY KEY (plan_pago_id)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS venta_credito_habilitado (
  venta_credito_id int(11) NOT NULL,
  venta_credito_habilitado_rut varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS usuario_edad (
  usuario_edad_id int(11) NOT NULL,
  usuario_id int(11) NOT NULL,
  usuario_fecha_nacimiento date NOT NULL,
  PRIMARY KEY (usuario_edad_id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla usuario_sexo
--

CREATE TABLE IF NOT EXISTS usuario_sexo (
  usuario_sexo_id int(11) NOT NULL,
  sexo_id int(11) NOT NULL,
  usuario_id int(11) NOT NULL,
  PRIMARY KEY (usuario_sexo_id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indices de la tabla categoria_empresa
--
ALTER TABLE categoria_empresa
  ADD PRIMARY KEY (categoria_id);

--
-- Indices de la tabla categoria_marca
--
ALTER TABLE categoria_marca
  ADD PRIMARY KEY (categoria_marca_id);

--
-- Indices de la tabla categoria_oferta
--
ALTER TABLE categoria_oferta
  ADD PRIMARY KEY (categoria_oferta_id);

--
-- Indices de la tabla categoria_producto
--
ALTER TABLE categoria_producto
  ADD PRIMARY KEY (categoria_producto_id);

--
-- Indices de la tabla ciudad
--
ALTER TABLE ciudad
  ADD PRIMARY KEY (ciudad_id);

--
-- Indices de la tabla comuna
--
ALTER TABLE comuna
  ADD PRIMARY KEY (comuna_id);

--
-- Indices de la tabla empresa
--
ALTER TABLE empresa
  ADD PRIMARY KEY (empresa_id);

--
-- Indices de la tabla empresa_giro
--
ALTER TABLE empresa_giro
  ADD PRIMARY KEY (empresa_id,giro_id);

--
-- Indices de la tabla filtro_oferta
--
ALTER TABLE filtro_oferta
  ADD PRIMARY KEY (filtro_oferta_id);

--
-- Indices de la tabla giro
--
ALTER TABLE giro
  ADD PRIMARY KEY (giro_id);

--
-- Indices de la tabla materia_prima
--
ALTER TABLE materia_prima
  ADD PRIMARY KEY (materia_prima_id);

--
-- Indices de la tabla materia_prima_producto
--
ALTER TABLE materia_prima_producto
  ADD PRIMARY KEY (materia_prima_producto_id);

--
-- Indices de la tabla materia_prima_sucursal
--
ALTER TABLE materia_prima_sucursal
  ADD PRIMARY KEY (materia_prima_sucursal_id);

--
-- Indices de la tabla medio_pago
--
ALTER TABLE medio_pago
  ADD PRIMARY KEY (medio_pago_id);

--
-- Indices de la tabla oferta
--
ALTER TABLE oferta
  ADD PRIMARY KEY (oferta_id);

--
-- Indices de la tabla oferta_sucursal
--
ALTER TABLE oferta_sucursal
  ADD PRIMARY KEY (oferta_sucursal_id);

--
-- Indices de la tabla orden_cancelada
--
ALTER TABLE orden_cancelada
  ADD PRIMARY KEY (orden_id);

--
-- Indices de la tabla orden_compra
--
ALTER TABLE orden_compra
  ADD PRIMARY KEY (orden_id);

--
-- Indices de la tabla orden_producto
--
ALTER TABLE orden_producto
  ADD PRIMARY KEY (orden_producto_id);

--
-- Indices de la tabla pais
--
ALTER TABLE pais
  ADD PRIMARY KEY (pais_id);

--
-- Indices de la tabla producto
--
ALTER TABLE producto
  ADD PRIMARY KEY (producto_id);

--
-- Indices de la tabla producto_dimension
--
ALTER TABLE producto_dimension
  ADD PRIMARY KEY (producto_dimension_id);

--
-- Indices de la tabla producto_marca
--
ALTER TABLE producto_marca
  ADD PRIMARY KEY (producto_marca_id);

--
-- Indices de la tabla producto_peso
--
ALTER TABLE producto_peso
  ADD PRIMARY KEY (producto_peso_id);

--
-- Indices de la tabla producto_sucursal
--
ALTER TABLE producto_sucursal
  ADD PRIMARY KEY (producto_sucursal_id);

--
-- Indices de la tabla producto_talla
--
ALTER TABLE producto_talla
  ADD PRIMARY KEY (producto_talla_id);

--
-- Indices de la tabla producto_volumen
--
ALTER TABLE producto_volumen
  ADD PRIMARY KEY (producto_volumen_id);

--
-- Indices de la tabla promocion
--
ALTER TABLE promocion
  ADD PRIMARY KEY (promocion_id);

--
-- Indices de la tabla promocion_especial
--
ALTER TABLE promocion_especial
  ADD PRIMARY KEY (promocion_especial_id);

--
-- Indices de la tabla promocion_especial_tipo
--
ALTER TABLE promocion_especial_tipo
  ADD PRIMARY KEY (promocion_especial_tipo_id);

--
-- Indices de la tabla promocion_producto
--
ALTER TABLE promocion_producto
  ADD PRIMARY KEY (promocion_producto_id);

--
-- Indices de la tabla promocion_sucursal
--
ALTER TABLE promocion_sucursal
  ADD PRIMARY KEY (promocion_sucursal_id);

--
-- Indices de la tabla region
--
ALTER TABLE region
  ADD PRIMARY KEY (region_id);

--
-- Indices de la tabla registro_stock
--
ALTER TABLE registro_stock
  ADD PRIMARY KEY (registro_stock_id);

--
-- Indices de la tabla registro_stock_materia_prima
--
ALTER TABLE registro_stock_materia_prima
  ADD PRIMARY KEY (registro_stock_materia_prima_id);

--
-- Indices de la tabla sucursal
--
ALTER TABLE sucursal
  ADD PRIMARY KEY (sucursal_id);

--
-- Indices de la tabla tag
--
ALTER TABLE tag
  ADD PRIMARY KEY (tag_id);

--
-- Indices de la tabla tag_producto
--
ALTER TABLE tag_producto
  ADD PRIMARY KEY (tag_producto_id);

--
-- Indices de la tabla tipo
--
ALTER TABLE tipo
  ADD PRIMARY KEY (tipo_id);

--
-- Indices de la tabla tipo_oferta_promocion
--
ALTER TABLE tipo_oferta_promocion
  ADD PRIMARY KEY (tipo_oferta_promocion_id);

--
-- Indices de la tabla tipo_usuario
--
ALTER TABLE tipo_usuario
  ADD PRIMARY KEY (tipo_usuario_id);

--
-- Indices de la tabla traspaso_stock_registro
--
ALTER TABLE traspaso_stock_registro
  ADD PRIMARY KEY (traspaso_stock_registro_id);

--
-- Indices de la tabla usuario
--
ALTER TABLE usuario
  ADD PRIMARY KEY (usuario_id);

--
-- Indices de la tabla usuario_empresa
--
ALTER TABLE usuario_empresa
  ADD PRIMARY KEY (usuario_empresa_id);

--
-- Indices de la tabla usuario_pass
--
ALTER TABLE usuario_pass
  ADD PRIMARY KEY (usuario_id);

--
-- Indices de la tabla usuario_sucursal
--
ALTER TABLE usuario_sucursal
  ADD PRIMARY KEY (usuario_sucursal_id);

--
-- Indices de la tabla venta
--
ALTER TABLE venta
  ADD PRIMARY KEY (venta_id);

--
-- Indices de la tabla venta_acumulada
--
ALTER TABLE venta_acumulada
  ADD PRIMARY KEY (venta_acumulada_id);

--
-- Indices de la tabla venta_descuento_adicional
--
ALTER TABLE venta_descuento_adicional
  ADD PRIMARY KEY (venta_descuento_adicional_id);

--
-- Indices de la tabla venta_pago
--
ALTER TABLE venta_pago
  ADD PRIMARY KEY (venta_pago_id);

--
-- Indices de la tabla venta_producto
--
ALTER TABLE venta_producto
  ADD PRIMARY KEY (venta_producto_id);

--
-- Indices de la tabla vuelto
--
ALTER TABLE vuelto
  ADD PRIMARY KEY (vuelto_id);
  
ALTER TABLE cliente
  ADD PRIMARY KEY (cliente_id);
  
ALTER TABLE empresa_contacto
  ADD PRIMARY KEY (empresa_id);
  
ALTER TABLE cuota
  ADD PRIMARY KEY (cuota_id);
  
ALTER TABLE linea_credito
  ADD PRIMARY KEY (linea_credito_id);
  
ALTER TABLE venta_credito
  ADD PRIMARY KEY (venta_credito_id);

ALTER TABLE venta_credito_habilitado
  ADD PRIMARY KEY (venta_credito_id);
--
-- AUTO_INCREMENT de la tabla categoria_empresa
--
ALTER TABLE categoria_empresa
  MODIFY categoria_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla categoria_marca
--
ALTER TABLE categoria_marca
  MODIFY categoria_marca_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla categoria_oferta
--
ALTER TABLE categoria_oferta
  MODIFY categoria_oferta_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla categoria_producto
--
ALTER TABLE categoria_producto
  MODIFY categoria_producto_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla ciudad
--
ALTER TABLE ciudad
  MODIFY ciudad_id int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla comuna
--
ALTER TABLE comuna
  MODIFY comuna_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla empresa
--
ALTER TABLE empresa
  MODIFY empresa_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla filtro_oferta
--
ALTER TABLE filtro_oferta
  MODIFY filtro_oferta_id int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT de la tabla giro
--
ALTER TABLE giro
  MODIFY giro_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla materia_prima
--
ALTER TABLE materia_prima
  MODIFY materia_prima_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla materia_prima_producto
--
ALTER TABLE materia_prima_producto
  MODIFY materia_prima_producto_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla materia_prima_sucursal
--
ALTER TABLE materia_prima_sucursal
  MODIFY materia_prima_sucursal_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla medio_pago
--
ALTER TABLE medio_pago
  MODIFY medio_pago_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla oferta_sucursal
--
ALTER TABLE oferta_sucursal
  MODIFY oferta_sucursal_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla orden_compra
--
ALTER TABLE orden_compra
  MODIFY orden_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla orden_producto
--
ALTER TABLE orden_producto
  MODIFY orden_producto_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla pais
--
ALTER TABLE pais
  MODIFY pais_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla producto
--
ALTER TABLE producto
  MODIFY producto_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla producto_dimension
--
ALTER TABLE producto_dimension
  MODIFY producto_dimension_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla producto_marca
--
ALTER TABLE producto_marca
  MODIFY producto_marca_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla producto_peso
--
ALTER TABLE producto_peso
  MODIFY producto_peso_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla producto_sucursal
--
ALTER TABLE producto_sucursal
  MODIFY producto_sucursal_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla producto_talla
--
ALTER TABLE producto_talla
  MODIFY producto_talla_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla producto_volumen
--
ALTER TABLE producto_volumen
  MODIFY producto_volumen_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla promocion
--
ALTER TABLE promocion
  MODIFY promocion_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla promocion_especial
--
ALTER TABLE promocion_especial
  MODIFY promocion_especial_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla promocion_especial_tipo
--
ALTER TABLE promocion_especial_tipo
  MODIFY promocion_especial_tipo_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla promocion_producto
--
ALTER TABLE promocion_producto
  MODIFY promocion_producto_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla promocion_sucursal
--
ALTER TABLE promocion_sucursal
  MODIFY promocion_sucursal_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla region
--
ALTER TABLE region
  MODIFY region_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla registro_stock
--
ALTER TABLE registro_stock
  MODIFY registro_stock_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla registro_stock_materia_prima
--
ALTER TABLE registro_stock_materia_prima
  MODIFY registro_stock_materia_prima_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla sucursal
--
ALTER TABLE sucursal
  MODIFY sucursal_id int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla tag
--
ALTER TABLE tag
  MODIFY tag_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla tag_producto
--
ALTER TABLE tag_producto
  MODIFY tag_producto_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla tipo
--
ALTER TABLE tipo
  MODIFY tipo_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla tipo_usuario
--
ALTER TABLE tipo_usuario
  MODIFY tipo_usuario_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla traspaso_stock_registro
--
ALTER TABLE traspaso_stock_registro
  MODIFY traspaso_stock_registro_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla usuario
--
ALTER TABLE usuario
  MODIFY usuario_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla usuario_empresa
--
ALTER TABLE usuario_empresa
  MODIFY usuario_empresa_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla usuario_sucursal
--
ALTER TABLE usuario_sucursal
  MODIFY usuario_sucursal_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla venta
--
ALTER TABLE venta
  MODIFY venta_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla venta_acumulada
--
ALTER TABLE venta_acumulada
  MODIFY venta_acumulada_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla venta_descuento_adicional
--
ALTER TABLE venta_descuento_adicional
  MODIFY venta_descuento_adicional_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla venta_pago
--
ALTER TABLE venta_pago
  MODIFY venta_pago_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla venta_producto
--
ALTER TABLE venta_producto
  MODIFY venta_producto_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla vuelto
--
ALTER TABLE vuelto
  MODIFY vuelto_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla cliente
--
ALTER TABLE cliente
  MODIFY cliente_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla cuota
--
ALTER TABLE cuota
  MODIFY cuota_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla linea_credito
--
ALTER TABLE linea_credito
  MODIFY linea_credito_id int(11) NOT NULL AUTO_INCREMENT;
  
ALTER TABLE plan_credito
  MODIFY plan_credito_id int(11) NOT NULL AUTO_INCREMENT;
  
ALTER TABLE plan_pago
  MODIFY plan_pago_id int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla venta_credito
--
ALTER TABLE venta_credito
  MODIFY venta_credito_id int(11) NOT NULL AUTO_INCREMENT;
  
ALTER TABLE empresa_contacto
  MODIFY empresa_id int(11) NOT NULL AUTO_INCREMENT;
  
INSERT INTO  plan_credito (
plan_credito_id ,
plan_credito_nombre ,
plan_credito_codigo ,
plan_credito_costo_mantencion ,
plan_credito_costo_uso ,
plan_credito_estado
)
VALUES (
'1',  'PLAN NORMAL',  'plan_normal',  '0',  '0',  '1'
);

INSERT INTO plan_credito (
plan_credito_id ,
plan_credito_nombre ,
plan_credito_codigo ,
plan_credito_costo_mantencion ,
plan_credito_costo_uso ,
plan_credito_estado
)
VALUES (
'2',  'PLAN ILIMITADO',  'plan_ilimitado',  '0',  '0',  '1'
);";
}
?>