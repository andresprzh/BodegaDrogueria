USE bodegadrogueria;
USE drogueria;

CREATE OR REPLACE VIEW drogueria.itemview 
AS SELECT 
`ID_ITEM` ,  
`ID_REFERENCIA` ,  
`ID_CODBAR` ,  
`DESCRIPCION`,  
`DESCRIPCION_2` ,  
`PESO` ,  
`VOLUMEN`,  
`ID_BODEGA_DEFAULT` ,  
`NOM_TERC` , 
`FECHA_INGRESO`
FROM drogueria.items; 


CREATE OR REPLACE VIEW drogueria.codview
AS SELECT  
`ID_ITEMS`,
`ID_CODBAR`,
`UNIMED_VENTA`
FROM drogueria.COD_BARRAS;

CREATE OR REPLACE VIEW bodegadrogueria.itemview 
AS SELECT *
FROM drogueria.itemview; 

CREATE OR REPLACE VIEW bodegadrogueria.codview
AS SELECT  *
FROM drogueria.codview;

INSERT INTO bodegadrogueria.items
(SELECT *
FROM bodegadrogueria.itemview)
ON DUPLICATE KEY UPDATE
items.DESCRIPCION=itemview.DESCRIPCION,
items.DESCRIPCION_2=itemview.DESCRIPCION_2,
items.PESO=itemview.PESO,
items.VOLUMEN=itemview.VOLUMEN,
items.ID_BODEGA_DEFAULT=itemview.ID_BODEGA_DEFAULT,
items.NOM_TERC=itemview.NOM_TERC,
items.FECHA_INGRESO=itemview.FECHA_INGRESO;

INSERT INTO bodegadrogueria.cod_barras
(SELECT *
FROM bodegadrogueria.codview)
ON DUPLICATE KEY UPDATE
COD_BARRAS.UNIMED_VENTA=codview.UNIMED_VENTA;