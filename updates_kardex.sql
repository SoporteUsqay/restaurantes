ALTER TABLE `detallepedido` ADD COLUMN `terminal`  varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish2_ci NULL DEFAULT null AFTER `tipoPedido`;

ALTER TABLE `insumos` ADD COLUMN `porcentaje_merma`  decimal(10,4) NULL DEFAULT null AFTER `stockMinimo`;

CREATE TABLE `insumo_porcion` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`insumo_id`  int(11) NULL DEFAULT null ,
`unidad_id`  int(11) NULL DEFAULT null ,
`cantidad`  decimal(10,4) NULL DEFAULT null ,
`valor`  decimal(10,4) NULL DEFAULT null ,
`descripcion`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT null ,
`deleted_at`  datetime NULL DEFAULT null ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Dynamic
;

CREATE TABLE `n_almacen` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`nombre`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '' ,
`tipo`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT null ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Dynamic
;

INSERT INTO `n_almacen` (`nombre`) VALUES (`PRINCIPAL`);

CREATE TABLE `n_detalle_movimiento_almacen` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`movimiento_id`  int(11) NULL DEFAULT null ,
`tipo`  tinyint(4) NULL DEFAULT null ,
`insumo_id`  int(11) NULL DEFAULT null ,
`insumo_porcion_id`  int(11) NULL DEFAULT null ,
`unidad_id`  int(11) NULL DEFAULT null ,
`precio`  decimal(10,4) NULL DEFAULT null ,
`cantidad`  decimal(10,4) NULL DEFAULT null ,
`fecha`  datetime NULL DEFAULT null ,
`almacen_id`  int(11) NULL DEFAULT null ,
`motivo`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT null ,
`created_at`  datetime NULL DEFAULT null ,
`updated_at`  datetime NULL DEFAULT null ,
`deleted_at`  datetime NULL DEFAULT null ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Dynamic
;

CREATE TABLE `n_historial_stock_insumo` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`insumo_id`  int(11) NULL DEFAULT null ,
`insumo_porcion_id`  int(11) NULL DEFAULT null ,
`almacen_id`  int(11) NULL DEFAULT null ,
`stock_inicial`  decimal(10,4) NULL DEFAULT null ,
`stock_final`  decimal(10,4) NULL DEFAULT null ,
`fecha`  date NULL DEFAULT null ,
`trabajador_id`  int(11) NULL DEFAULT null ,
`created_at`  datetime NULL DEFAULT null ,
`updated_at`  datetime NULL DEFAULT null ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci
ROW_FORMAT=Dynamic
;

CREATE TABLE `n_movimiento_almacen` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`tipo_comprobante_id`  int(11) NULL DEFAULT null ,
`numero_comprobante`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT null ,
`fecha`  date NOT NULL ,
`almacen_id`  int(11) NULL DEFAULT null ,
`proveedor_id`  int(11) NULL DEFAULT null ,
`trabajador_id`  int(11) NULL DEFAULT null ,
`tipo`  tinyint(4) NULL DEFAULT null ,
`created_at`  datetime NULL DEFAULT null ,
`updated_at`  datetime NULL DEFAULT null ,
`deleted_at`  datetime NULL DEFAULT null ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Dynamic
;

CREATE TABLE `n_receta` (
`id`  int(11) NOT NULL AUTO_INCREMENT ,
`plato_id`  char(6) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT null ,
`insumo_id`  int(11) NULL DEFAULT null ,
`insumo_porcion_id`  int(11) NULL DEFAULT null ,
`unidad_id`  int(11) NULL DEFAULT null ,
`cantidad`  decimal(11,4) NULL DEFAULT null ,
`almacen_id`  int(11) NULL DEFAULT null ,
`receta_id`  int(11) NULL DEFAULT null ,
`terminal`  varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT null ,
`created_at`  datetime NULL DEFAULT null ,
`updated_at`  datetime NULL DEFAULT null ,
`deleted_at`  datetime NULL DEFAULT null ,
PRIMARY KEY (`id`)
)
ENGINE=InnoDB
DEFAULT CHARACTER SET=latin1 COLLATE=latin1_swedish_ci
ROW_FORMAT=Dynamic
;