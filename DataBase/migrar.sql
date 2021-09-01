

SET SQL_SAFE_UPDATES = 0;
UPDATE TRABAJADOR SET PASSWORD = MD5(PASSWORD);
SET SQL_SAFE_UPDATES = 1;

INSERT INTO N_ALMACEN VALUES(1, 'PRINCIPAL', NULL);


INSERT INTO n_historial_stock_insumo ( insumo_id, insumo_porcion_id, almacen_id, stock_inicial, stock_final, fecha, trabajador_id, created_at)
                 SELECT pkInsumo, NULL, 1, 0, 0, date(now()),1,now()
                 FROM insumos i WHERE i.estado = 0;

insert into n_receta (plato_id, insumo_id, insumo_porcion_id, unidad_id, cantidad, almacen_id, created_at)
    select pkPlato, pkInsumo, null, null, cantidadTotal, 1, now() from insumo_menu;


    -- motivos anulacion


ALTER TABLE `pedido_detraccion`
ADD COLUMN `comprobante_id`  int(11) NULL AFTER `total`;