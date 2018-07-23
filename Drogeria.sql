/*Elimina la base de datos si existe*/
-- drop database if exists drogueria;
/*se crea la base de datos*/

create database  IF NOT EXISTS drogueria;
/*se usa la base de daos*/
use drogueria;

-- tabla perfiles de usuarios
CREATE TABLE  perfiles(
id_perfil int(1) NOT NULL AUTO_INCREMENT,
des_perfil char(20),

PRIMARY KEY(id_perfil)
);

-- tabla de usuarios
CREATE TABLE usuario(
id_usuario int(10) NOT NULL AUTO_INCREMENT,
nombre varchar(40) COLLATE ucs2_spanish_ci,
cedula int(10),
usuario char(20),
password varchar(60) NOT NULL,
perfil int(1),

PRIMARY KEY(id_usuario),

CONSTRAINT usuario_perfil
FOREIGN KEY(perfil)
REFERENCES perfiles(id_perfil)
);

/*tabla de la requisicion a bodega*/
create table requisicion(
No_Req char(10) NOT NULL,
fecha date NOT NULL,
hora char(8),
Lo_Origen char(6),
Lo_Destino char(6),
Tip_Inventario int(2),
Solicitante varchar(40) COLLATE ucs2_spanish_ci,
estado boolean,

PRIMARY KEY(No_req)
);

/*tabla caja*/
CREATE TABLE caja(
No_caja int(10) NOT NULL auto_increment,
Persona int(10),
tipo_caja char(3) ,
abrir datetime ,
cerrar datetime ON UPDATE CURRENT_TIMESTAMP,

PRIMARY KEY(No_caja),

CONSTRAINT caja_usuario
FOREIGN KEY(persona) 
REFERENCES usuario(id_usuario)
);

/*Crea la tabla donde se almacenan los productos pedidos en la requisicion*/
create table pedido(
Item char(6) NOT NULL,
No_Req char(10) NOT NULL,
No_caja int(10) ,
disp  int(5) NOT NULL,
pedido int(5) NOT NULL,
alistado int(5),
alistamiento int(1) NOT NULL,
ubicacion varchar(6) NOT NULL,



PRIMARY KEY(ITEM,No_req),

CONSTRAINT pedido_item
FOREIGN KEY(Item) 
REFERENCES `ITEMS`(`ID_ITEM`),

CONSTRAINT pedido_requisicion
FOREIGN KEY(No_Req) 
REFERENCES requisicion(No_Req),

CONSTRAINT pedido_caja
FOREIGN KEY(No_caja) 
REFERENCES caja(No_caja)
);



 
-- se llena un primer registro a caja que define las cajas no asignadas
insert into caja(No_caja) values(1);

insert into perfiles(des_perfil) values("Administrador"),("Jefe"),("Alistador"),("PVenta");

-- funcion que busca la ultima caja abierta por el alistador pers
DROP FUNCTION IF EXISTS numerocaja;
Delimiter $$
create  function numerocaja(pers INT(10) )
	returns int
begin  
	DECLARE numcaja INT(10);
	SELECT no_caja INTO numcaja
	FROM caja
	WHERE persona=pers
	AND cerrar is null
	ORDER BY abrir
	DESC LIMIT 1;
	
	return numcaja;
end$$

-- procedimiento que busca un item con el codigo de barras en la la lista de rquerido especificada
-- el procedimiento tambien cambia el estado del item a 1 que significa que esta siendo alistado
DROP PROCEDURE IF EXISTS buscarcod;
DELIMITER $$
CREATE PROCEDURE buscarcod(IN codigo char(15), IN no_req char(10),IN alistador INT(10),IN numerocaja CHAR(10))
BEGIN

	DECLARE numcaja INT(10);
	set numcaja=numerocaja(alistador);
	
	select pedido.alistamiento,cod_barras.ID_CODBAR,id_items, id_referencia, descripcion, disp, pedido, alistado,caja.No_caja,usuario.nombre,ubicacion,requisicion.Lo_Origen,requisicion.Lo_Destino
	from cod_barras
	inner join items on items.ID_CODBAR=cod_barras.ID_CODBAR
	inner join pedido on item=ID_ITEM	
	inner join requisicion on requisicion.No_Req=pedido.No_Req
	left join caja on caja.No_caja=pedido.No_caja
	left join usuario on id_usuario=persona
	where cod_barras.ID_CODBAR like codigo 
	and pedido.no_req=no_req
	and pedido.No_caja like numerocaja;

	
	UPDATE pedido
	SET alistamiento=1,no_caja=numcaja
	WHERE item=(select ID_Items from cod_barras where ID_CODBAR=codigo)
	AND pedido.No_req=no_req
	AND alistamiento=0 ;
	
end $$


-- procedimiento que empaca los items(asigna  la fecha a la caja y el numero de caja al item)
DROP PROCEDURE IF EXISTS empacar;
DELIMITER $$
CREATE PROCEDURE empacar(IN codigo CHAR(15), IN alistar int(5),IN pers INT(10),IN caj CHAR(3),IN req CHAR(10))
BEGIN
DECLARE numcaja INT(10);
set numcaja=numerocaja(pers);

UPDATE pedido
SET alistado=alistar,alistamiento=2
WHERE item=(select ID_Items from cod_barras where ID_CODBAR=codigo)
AND No_req=req;

UPDATE caja
SET tipo_caja=caj
WHERE No_caja=numcaja;

END $$

-- procedimiento que busca las cajas por el numero de caja y la requisicion
DROP PROCEDURE IF EXISTS buscarcaja;
DELIMITER $$
CREATE PROCEDURE buscarcaja(IN numcaja CHAR(10),IN req CHAR(10))
BEGIN
	select caja.No_caja, usuario.nombre,tipo_caja,abrir,cerrar 
	from caja 
	inner join pedido on pedido.No_caja=caja.No_caja
	inner join usuario on usuario.id_usuario=Persona
	where caja.No_caja like numcaja 
	and pedido.No_Req=req
	and caja.No_caja <> 1
	group by caja.No_caja ;
END $$


-- procedimiento que items de 1 caja
DROP PROCEDURE IF EXISTS buscaritemscaja;
DELIMITER $$
CREATE PROCEDURE buscaritemscaja(IN numcaja CHAR(10))
BEGIN
	select *
	from caja 
	inner join pedido on pedido.No_caja=caja.No_caja
	inner join usuario on usuario.id_usuario=Persona
	where caja.No_caja = numcaja 
	and caja.No_caja <> 1;

END $$


-- prcedimiento que busca cualquier item con el parametro buscar que no este en los requeridos
DROP PROCEDURE IF EXISTS buscarIE;
DELIMITER $$
CREATE PROCEDURE buscarIE(IN buscar char(40))
BEGIN
	SELECT cod_barras.ID_CODBAR, items.id_referencia,items.descripcion
	FROM cod_barras 
	INNER JOIN items ON items.ID_CODBAR=cod_barras.ID_CODBAR
	LEFT JOIN pedido on pedido.Item=items.ID_ITEM
	WHERE (cod_barras.ID_CODBAR =buscar
	OR items.ID_REFERENCIA=buscar
	OR items.DESCRIPCION like concat('%',buscar,'%'))
	and pedido.Item is null;
end $$


-- triger que asigna fecha de inicio cada ves que se crea una caja
DROP TRIGGER IF EXISTS ins_abrir;
DELIMITER $$
CREATE TRIGGER ins_abrir 
BEFORE INSERT ON caja
FOR EACH ROW 
BEGIN
	SET new.abrir=now() ;
END;
$$


-- item que modifica el estado de la requisicion 
DROP TRIGGER IF EXISTS cam_estado;
DELIMITER $$
CREATE TRIGGER cam_estado 
AFTER UPDATE ON pedido
FOR EACH ROW 
BEGIN
	declare numalistados tinyint;
	select count(alistamiento) into numalistados
	from pedido
	where alistamiento<>2
	AND No_req=new.No_req;
	
	IF numalistados=0 THEN
		UPDATE requisicion
		SET estado=1
		WHERE requisicion.No_Req=new.No_Req;
	END IF;
END;
$$


-- *********************************************************************************************************************************************************************************************
-- *********************************************************************************************************************************************************************************************
-- *********************************************************************************************************************************************************************************************
drop table pedido;
drop table requisicion;
DROP TABLE caja;
