/*Elimina la base de datos si existe*/
-- drop database if exists drogueria;
/*se crea la base de datos*/

CREATE DATABASE  IF NOT EXISTS drogueria;
/*se usa la base de daos*/
USE drogueria;

-- tabla perfiles de usuarios
CREATE TABLE  perfiles(
	id_perfil INT(1) NOT NULL AUTO_INCREMENT,
	des_perfil CHAR(20),

	PRIMARY KEY(id_perfil)
);

-- tabla de usuarios
CREATE TABLE usuario(
	id_usuario INT(10) NOT NULL AUTO_INCREMENT,
	nombre VARCHAR(40) COLLATE ucs2_spanish_ci,
	cedula INT(10),
	usuario CHAR(20),
	pasword VARCHAR(60) NOT NULL,
	perfil INT(1),

	PRIMARY KEY(id_usuario),

	CONSTRAINT usuario_perfil
	FOREIGN KEY(perfil)
	REFERENCES perfiles(id_perfil)
);

/*tabla de la requisicion a bodega*/
CREATE TABLE requisicion(
	No_Req CHAR(10) NOT NULL,
	creada DATETIME NOT NULL,
	Lo_Origen CHAR(6),
	Lo_Destino CHAR(6),
	Tip_Inventario INT(2),
	Solicitante VARCHAR(40) COLLATE ucs2_spanish_ci,
	enviado DATETIME,
	recibido DATETIME,

	PRIMARY KEY(No_req)
);

/*tabla caja*/
CREATE TABLE caja(
	No_caja INT(10) NOT NULL auto_increment,
	Alistador INT(10),
	encargado_punto INT(10),
	tipo_caja CHAR(3) ,
	abrir DATETIME ,
	cerrar DATETIME ON UPDATE CURRENT_TIMESTAMP,


	PRIMARY KEY(No_caja),

	CONSTRAINT caja_alistador_usuario
	FOREIGN KEY(Alistador) 
	REFERENCES usuario(id_usuario),

	CONSTRAINT caja_encargado_usuario
	FOREIGN KEY(encargado_punto) 
	REFERENCES usuario(id_usuario)
);

/*Crea la tabla donde se almacenan los productos pedidos en la requisicion*/
CREATE TABLE pedido(
	Item CHAR(6) NOT NULL,
	No_Req CHAR(10) NOT NULL,
	No_caja INT(10) default 1,
	ubicacion VARCHAR(6) NOT NULL,
	disp  INT(5) NOT NULL,
	pedido INT(5) NOT NULL,
	alistado INT(5) default 0,
	estado INT(1) NOT NULL default 0,



	PRIMARY KEY(Item,No_req),

	CONSTRAINT pedido_Item
	FOREIGN KEY(Item) 
	REFERENCES `ITEMS`(`ID_Item`),

	CONSTRAINT pedido_requisicion
	FOREIGN KEY(No_Req) 
	REFERENCES requisicion(No_Req),

	CONSTRAINT pedido_caja
	FOREIGN KEY(No_caja) 
	REFERENCES caja(No_caja)
);


CREATE TABLE recibido(
	Item CHAR(6) NOT NULL,
	No_Req CHAR(10) NOT NULL,
	No_caja INT(10) default 1,
	recibidos INT(5) default 0,
	estado INT(1) NOT NULL default 0,


	PRIMARY KEY(Item,No_req,No_caja),

	CONSTRAINT recibido_Item
	FOREIGN KEY(Item) 
	REFERENCES ITEMS(ID_Item),

	CONSTRAINT recibido_requisicion
	FOREIGN KEY(No_Req) 
	REFERENCES requisicion(No_Req),

	CONSTRAINT recibido_caja
	FOREIGN KEY(No_caja) 
	REFERENCES caja(No_caja)
);


 
-- se llena un primer registro a caja que define las cajas no asignadas
INSERT INTO caja(No_caja) VALUES(1);

INSERT INTO perfiles(des_perfil) VALUES("Administrador"),("Jefe"),("Alistador"),("PVenta");

/* ELIMINA PROCEDIMIENTOS SI EXISTE */
DROP FUNCTION IF EXISTS numerocaja;
DROP PROCEDURE IF EXISTS buscarcod;
DROP PROCEDURE IF EXISTS BuscarRecibido;
DROP PROCEDURE IF EXISTS empacar;
DROP PROCEDURE IF EXISTS buscarcaja;
DROP PROCEDURE IF EXISTS buscarITEMScaja;
DROP PROCEDURE IF EXISTS buscarIE;

/* ELIMINA TRIGGER SI EXISTEN */
DROP TRIGGER IF EXISTS ins_abrir;
DROP TRIGGER IF EXISTS RecibidoEstado;
DROP TRIGGER IF EXISTS req_enviado;

-- funcion que busca la ultima caja abierta por el alistador pers
DELIMITER $$
	CREATE  FUNCTION numerocaja(pers INT(10) )
	RETURNS INT
	BEGIN  
		DECLARE numcaja INT(10);
		SELECT no_caja INTO numcaja
		FROM caja
		WHERE Alistador=pers
		AND cerrar IS NULL
		ORDER BY abrir
		DESC LIMIT 1;

		RETURN numcaja;
	END 
$$

-- procedimiento que busca un Item con el codigo de barras en la la lista de rquerido especificada
-- el procedimiento tambien cambia el estado del Item a 1 que significa que esta siendo alistado
DELIMITER $$
	CREATE PROCEDURE buscarcod(IN codigo CHAR(15), IN no_req CHAR(10),IN alistador INT(10),IN numerocaja CHAR(10))
	BEGIN

		DECLARE numcaja INT(10);
		SET numcaja=numerocaja(alistador);
		
		SELECT pedido.estado,COD_BARRAS.ID_CODBAR,ID_ITEMS, ID_REFERENCIA, descripcion, disp, pedido, alistado,caja.No_caja,usuario.nombre,ubicacion,requisicion.Lo_Origen,requisicion.Lo_Destino
		FROM COD_BARRAS
		INNER JOIN ITEMS ON ITEMS.ID_CODBAR=COD_BARRAS.ID_CODBAR
		INNER JOIN pedido ON Item=ID_Item	
		INNER JOIN requisicion ON requisicion.No_Req=pedido.No_Req
		LEFT JOIN caja ON caja.No_caja=pedido.No_caja
		LEFT JOIN usuario ON id_usuario=Alistador
		WHERE COD_BARRAS.ID_CODBAR LIKE codigo 
		AND pedido.no_req=no_req
		AND pedido.No_caja LIKE numerocaja;

		
		UPDATE pedido
		SET estado=1,no_caja=numcaja
		WHERE Item=(SELECT ID_ITEMS FROM COD_BARRAS WHERE ID_CODBAR=codigo)
		AND pedido.No_req=no_req
		AND estado=0 ;
		
	END 
$$


DELIMITER $$
	CREATE PROCEDURE BuscarRecibido(IN codigo CHAR(15))
	BEGIN

		SELECT ITEMS.ID_CODBAR,id_referencia, descripcion,No_caja,alistado
		FROM pedido
		RIGHT JOIN ITEMS ON ITEMS.ID_Item=pedido.Item
		WHERE COD_BARRAS.ID_CODBAR = codigo;

	END 
$$


-- procedimiento que empaca los ITEMS(asigna  la fecha a la caja y el numero de caja al Item)
DELIMITER $$
	CREATE PROCEDURE empacar(IN codigo CHAR(15), IN alistar INT(5),IN pers INT(10),IN caj CHAR(3),IN req CHAR(10))
	BEGIN

		DECLARE numcaja INT(10);
		SET numcaja=numerocaja(pers);

		UPDATE pedido
		SET alistado=alistar,estado=2
		WHERE Item=(SELECT ID_ITEMS FROM COD_BARRAS WHERE ID_CODBAR=codigo)
		AND No_req=req;

		UPDATE caja
		SET tipo_caja=caj
		WHERE No_caja=numcaja;

	END
$$

-- procedimiento que busca las cajas por el numero de caja y la requisicion
DELIMITER $$
	CREATE PROCEDURE buscarcaja(IN numcaja CHAR(10),IN req CHAR(10))
	BEGIN
		SELECT caja.No_caja, usuario.nombre,tipo_caja,abrir,cerrar
		FROM caja 
		INNER JOIN pedido ON pedido.No_caja=caja.No_caja
		INNER JOIN usuario ON usuario.id_usuario=Alistador
		WHERE caja.No_caja LIKE numcaja 
		AND pedido.No_Req=req
		AND caja.No_caja <> 1
		GROUP BY caja.No_caja ;
	END 
$$


-- procedimiento que busca ITEMS de 1 caja
DELIMITER $$
	CREATE PROCEDURE buscarITEMScaja(IN numcaja CHAR(10))
	BEGIN

		SELECT *
		FROM caja 
		INNER JOIN pedido ON pedido.No_caja=caja.No_caja
		INNER JOIN usuario ON usuario.id_usuario=Alistador
		WHERE caja.No_caja = numcaja 
		AND caja.No_caja <> 1;

	END 
$$


-- prcedimiento que busca cualquier Item con el parametro buscar que no este en los requeridos
DELIMITER $$
	CREATE PROCEDURE buscarIE(IN buscar CHAR(40))
	BEGIN
		SELECT COD_BARRAS.ID_CODBAR, ITEMS.id_referencia,ITEMS.descripcion
		FROM COD_BARRAS 
		INNER JOIN ITEMS ON ITEMS.ID_CODBAR=COD_BARRAS.ID_CODBAR
		LEFT JOIN pedido ON pedido.Item=ITEMS.ID_Item
		WHERE (COD_BARRAS.ID_CODBAR =buscar
		OR ITEMS.ID_REFERENCIA=buscar
		OR ITEMS.DESCRIPCION LIKE concat('%',buscar,'%'))
		AND pedido.Item IS NULL;
	END 
$$


-- triger que asigna fecha de inicio cada ves que se crea una caja
DELIMITER $$
	CREATE TRIGGER ins_abrir 
	BEFORE INSERT ON caja
	FOR EACH ROW 
	BEGIN
		SET new.abrir=now() ;
	END 
$$

-- trigger que modifica el estado del Item recibido  
DELIMITER $$

	CREATE TRIGGER RecibidoEstado 
	BEFORE INSERT ON recibido
	FOR EACH ROW 
	BEGIN
	
		DECLARE caja INT(10);
		DECLARE numalistado INT(5);
		SELECT No_caja,alistado INTO caja, numalistado
		FROM pedido
		RIGHT JOIN ITEMS ON ITEMS.ID_Item=pedido.Item
		WHERE ITEMS.ID_Item = new.Item;
	
		IF caja IS NULL THEN
			SET new.estado=2;
		ELSEIF caja<>new.No_caja THEN
			SET new.estado=3;
		ELSEIF new.recibidos<numalistado THEN
			SET new.estado=0;
		ELSEIF new.recibidos>numalistado THEN
			SET new.estado=1;
		ELSE 
			SET new.estado=4;
		END IF;
		
	END 
	
$$

-- trigger que modifica el estado de enviado de la requisicion 
DELIMITER $$

	CREATE TRIGGER req_enviado 
	AFTER UPDATE ON pedido
	FOR EACH ROW 
	BEGIN
		DECLARE numalistados TINYINT;
		SELECT count(estado) INTO numalistados
		FROM pedido
		WHERE estado<>2
		AND No_req=new.No_req;
		
		IF numalistados=0 THEN
			UPDATE requisicion
			SET enviado=NOW()
			WHERE requisicion.No_Req=new.No_Req;
		END IF;
	END 

$$


-- *********************************************************************************************************************************************************************************************
-- *********************************************************************************************************************************************************************************************
-- *********************************************************************************************************************************************************************************************
DROP TABLE pedido;
DROP TABLE recibido;
DROP TABLE requisicion;
DROP TABLE caja;
