Insert into moneda values('1','SOL PERUANO','S/','2');

Insert into medio_pago values('1','EFECTIVO','1','','1');
Insert into medio_pago values('2','VISA','1','','1');
Insert into medio_pago values('3','MASTERCARD','1','','1');

INSERT INTO tipo_gasto VALUES (1, 'Ingreso Adicional', 1, 1),(2, 'Pago Diario', 0, 1),(3, 'Pago Fijo', 0, 1),(4, 'Pago Planilla', 0, 1),(5, 'Devolucion', 0, 1);
INSERT INTO tipo_impuesto VALUES (1, 'GRAVADA'),(2, 'INAFECTA'),(3, 'EXONERADA'),(4, 'GRATUITA'),(5, 'ICBPER');

Insert into cloud_config values(NULL,'icbper','0.30');