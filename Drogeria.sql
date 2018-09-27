/*Elimina la base de datos si existe*/
-- drop database if exists drogueria;
/*se crea la base de datos*/

CREATE DATABASE  IF NOT EXISTS bodegadrogueria;
/*se usa la base de daos*/
USE bodegadrogueria;

CREATE TABLE IF NOT EXISTS `ITEMS` (
	`ID_ITEM` char(6) NOT NULL,
	`ID_REFERENCIA` char(15) DEFAULT NULL,
	`DESCRIPCION` char(40) DEFAULT NULL,
	`ID_LINEA2` char(6) DEFAULT NULL,
	`ID_GRUPO2` char(6) DEFAULT NULL,
	`UNIMED_INV_1` char(3) DEFAULT NULL,
	`UNIMED_EMPAQ` char(3) DEFAULT NULL,
	`FACTOR_EMPAQ` decimal(20,4) DEFAULT NULL,
	`PESO` decimal(20,4) DEFAULT NULL,
	`VOLUMEN` decimal(20,4) DEFAULT NULL,
	`ULTIMO_COSTO_ED` decimal(20,4) DEFAULT NULL,
	`FECHA_INGRESO` char(8) DEFAULT NULL,
	
	CONSTRAINT ITEMS_PK 
	PRIMARY KEY (`ID_ITEM`),
	
	INDEX (`ID_REFERENCIA`),
	INDEX (`DESCRIPCION`)
  
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
-- Volcando estructura para tabla BD_BIABLE01.COD_BARRAS

CREATE TABLE IF NOT EXISTS `COD_BARRAS` (
  `ID_ITEMS` char(6) DEFAULT NULL,
  `ID_CODBAR` char(15) BINARY NOT NULL,
  `UNIMED_VENTA` char(3) DEFAULT NULL,
  
  CONSTRAINT CODBAR_PK 
  PRIMARY KEY (`ID_CODBAR`),
  
  CONSTRAINT BAR_ITEM 
  FOREIGN KEY (ID_ITEMS)
  REFERENCES ITEMS (ID_ITEM) 
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
	cedula CHAR(10),
	usuario CHAR(20),
	password VARCHAR(60) NOT NULL,
	perfil INT(1),

	PRIMARY KEY(id_usuario),
	UNIQUE(cedula),
	UNIQUE(usuario),

	CONSTRAINT usuario_perfil
	FOREIGN KEY(perfil)
	REFERENCES perfiles(id_perfil),
	
	INDEX (id_usuario)
);

CREATE TABLE IF NOT EXISTS `sedes` (
  `codigo` char(6) NOT NULL,
  `descripcion` char(40) DEFAULT NULL,
  `direccion1` char(40) DEFAULT NULL,
  `direccion2` char(40) DEFAULT NULL,
  `direccion3` char(40) DEFAULT NULL,
  `grupo_co` char(2) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*tabla de la requisicion a bodega*/
CREATE TABLE requisicion(
	no_req CHAR(10) NOT NULL,
	creada DATETIME NOT NULL,
	lo_origen CHAR(6),
	lo_destino CHAR(6),
	tip_inventario INT(2),
	solicitante VARCHAR(40) COLLATE ucs2_spanish_ci,
	enviado DATETIME,
	recibido DATETIME,
	estado INT(1) DEFAULT 0,

	PRIMARY KEY(no_req),

	CONSTRAINT requisicion_origen
	FOREIGN KEY(lo_origen) 
	REFERENCES sedes(codigo),

	CONSTRAINT requisicion_destino
	FOREIGN KEY(lo_destino) 
	REFERENCES sedes(codigo)
);

CREATE TABLE  tipo_caja(
	tipo_caja CHAR(3) NOT NULL,
	descripcion CHAR(20) NOT NULL,
	
	PRIMARY KEY(tipo_caja)
	
);
/*tabla caja*/
CREATE TABLE caja(
	no_caja INT(10) NOT NULL AUTO_INCREMENT,
	alistador INT(10),
	encargado_punto INT(10),
	transportador INT(10),
	tipo_caja CHAR(3) ,
	estado INT(1) DEFAULT '0' ,
	abrir DATETIME ,
	cerrar DATETIME,
	enviado DATETIME ,
   recibido DATETIME,
   registrado DATETIME,


	PRIMARY KEY(no_caja),

	CONSTRAINT caja_alistador_usuario
	FOREIGN KEY(alistador) 
	REFERENCES usuario(id_usuario),

	CONSTRAINT caja_encargado_usuario
	FOREIGN KEY(encargado_punto) 
	REFERENCES usuario(id_usuario),
	
	CONSTRAINT caja_transportador_usuario
	FOREIGN KEY(transportador) 
	REFERENCES usuario(id_usuario),
	
	CONSTRAINT caja_tipo
	FOREIGN KEY(tipo_caja) 
	REFERENCES tipo_caja(tipo_caja)
);

/*Crea la tabla donde se almacenan los productos pedidos en la requisicion*/
CREATE TABLE pedido(
	item CHAR(6) NOT NULL,
	no_req CHAR(10) NOT NULL,
	no_caja INT(10) default 1,
	ubicacion VARCHAR(6) NOT NULL DEFAULT '----',
	disp  INT(5) NOT NULL,
	pedido INT(5) NOT NULL,
	alistado INT(5) default 0,
	estado INT(1) NOT NULL default 0,



	PRIMARY KEY(item,no_req,no_caja),

	CONSTRAINT pedido_Item
	FOREIGN KEY(item) 
	REFERENCES `ITEMS`(`ID_ITEM`),

	CONSTRAINT pedido_requisicion
	FOREIGN KEY(no_req) 
	REFERENCES requisicion(no_req),

	CONSTRAINT pedido_caja
	FOREIGN KEY(no_caja) 
	REFERENCES caja(no_caja),
	
	INDEX (estado)
);


CREATE TABLE recibido(
	item CHAR(6) NOT NULL,
	no_req CHAR(10) NOT NULL,
	no_caja INT(10) default 1,
	recibidos INT(5) default 0,
	estado INT(1) NOT NULL default 0,


	PRIMARY KEY(Item,no_req,no_caja),

	CONSTRAINT recibido_Item
	FOREIGN KEY(item) 
	REFERENCES ITEMS(ID_ITEM),

	CONSTRAINT recibido_requisicion
	FOREIGN KEY(no_req) 
	REFERENCES requisicion(no_req),

	CONSTRAINT recibido_caja
	FOREIGN KEY(no_caja) 
	REFERENCES caja(no_caja),
	
	INDEX (estado)
);

CREATE TABLE errores(
	item CHAR(6) NOT NULL,
	no_req CHAR(10) NOT NULL,
	no_caja INT(10) default 1,
	no_caja_recibido INT(10) default 1,
	recibidos INT(5) default 0,
	estado INT(1) NOT NULL default 0,
	ubicacion VARCHAR(6) NOT NULL default '----',
	pedido INT(5) NOT NULL,
	alistado INT(5) default 0,


	PRIMARY KEY(item,no_req,no_caja_recibido),

	CONSTRAINT errores_Item
	FOREIGN KEY(item) 
	REFERENCES ITEMS(ID_ITEM),

	CONSTRAINT errores_requisicion
	FOREIGN KEY(no_req) 
	REFERENCES requisicion(no_req),

	CONSTRAINT errores_caja
	FOREIGN KEY(no_caja) 
	REFERENCES caja(no_caja),
	
	CONSTRAINT errores_cajarecibida
	FOREIGN KEY(no_caja_recibido) 
	REFERENCES caja(no_caja),
	
	
	INDEX (estado)
);


INSERT INTO tipo_caja  VALUES 
('CRT','Caja de cartón'),
('CPL','Caja plástica'),
('CAP','Canasta plástica'),
('GLN','Galon'),
('GLA','Galoneta');
-- se llena un primer registro a caja que define las cajas no asignadas
INSERT INTO caja(no_caja) VALUES(1);

INSERT INTO perfiles VALUES(-1,"Inactivo"),(1,"Administrador"),(2,"Jefe"),(3,"Alistador"),(4,"PVenta"),(5,"JefeD"),(6,"Transportador");
UPDATE perfiles SET id_perfil=0 WHERE id_perfil=-1;

INSERT INTO usuario(nombre,cedula,usuario,password,perfil) VALUES("Administrador","0","admin","$2y$10$bpNOdujEVRMWB7JtWJX7Y.HPBjVCMSLS/r2YeafW5Mu.wfmyi/iLy",1);

INSERT INTO `sedes` (`codigo`, `descripcion`, `direccion1`, `direccion2`, `direccion3`, `grupo_co`) VALUES
	('001-BD', ' CENTRO', ' CR 2 14 34', '', '', ' 0'),
	('001-VE', ' CENTRO', ' CR 2 14 34', '', '', ' 0'),
	('002-VE', ' VERSALLES', ' CL 23BN 3N 100', '', '', ' 0'),
	('003-VE', ' CAMINO REAL', ' CL 9 51 05', '', '', ' 0'),
	('004-VE', ' ALFONSO LOPEZ', ' CL 70 7TBIS 59', '', '', ' 0'),
	('005-VE', ' INGENIO', ' CR 85C 15 119', '', '', ' 0'),
	('006-VE', ' VILLA DEL SUR', ' CR 42A 26E 41', '', '', ' 0'),
	('007-VE', ' PORTADA', ' AV 4OESTE 7 47', '', '', ' 0'),
	('008-VE', ' SAN FERNANDO', ' CL 5 37 A 65/67', '', '', ' 0'),
	('009-VE', ' CALIMA LA 14', ' CL 70 1 245 L CENTRO COMERCIAL CALIMA', '', '', ' 0'),
	('010-VE', ' PLAZA CAICEDO', ' CR 5 12 16 LOCAL 1', '', '', ' 0'),
	('011-VE', ' VALLE LILI', ' CR 98B 25 130', '', '', ' 0'),
	('012-VE', ' COSMOCENTRO', ' CL 5 50 106 LC 282', '', '', ' 0'),
	('013-VE', ' CALLE 7A', ' CL 7 No.30A 12', '', '', ' 0'),
	('014-VE', ' CENTRO SAN JORGE', ' CR 2 14 34', '', '', ' 0'),
	('015-VE', ' LA FLORA', ' CL 52 N NRO 5B88', '', '', ' 0'),
	('016-VE', ' UNICENTRO LOCAL 362', ' CR 100 5 169', '', '', ' 0'),
	('017-VE', ' ALFAGUARA LOCAL 1 66', ' CL 2#22175 CENTRO COMERCIAL ALFAGUARA', ' A LOCAL 166', '', '\r'),
	('018-VE', ' ELITE', ' CR 7 1452 LOCAL 156 PRIMER PISO', ' EDIFICIO COMERCIAL CENTRO ELITE', '', '\r'),
	('019-VE', ' VILLA COLOMBIA', ' CLL 50 12 09 L 102A', '', '', '\r'),
	('020-VE', ' GASTOS ADMINISTRATIVOS POR REPARTIR', ' CLL 23BN 3N 100', '', '', '\r'),
	('021-VE', ' VINCULADAS', ' CL 23 B N 3 N 100', '', '', '\r'),
	('022-VE', ' CALL CENTER', ' CL 23BN 3N 100', '', '', '\r'),
	('023-VE', ' JARDIN PLAZA', ' CL 16 98 155', '', '', '\r'),
	('090-VE', ' MEDICAMENTOS', ' CR 2 14 26', '', '', '\r'),
	('091-VE', ' COSMETICOS', ' CR 2 14 26', '', '', '\r'),
	('092-VE', ' VARIOS', ' CR 2 14 26', '', '', '\r'),
	('093-VE', ' REEMPAQUE', ' CR 2 14 26', '', '', '\r'),
	('099-VE', ' VENTAS AL POR MAYOR (CREDITOS)', ' CR 2 14 34', '', '', '\r'),
	('100-VE', ' CALLE 7A', ' CL 7 30A 12', '', '', '\r'),
	('101-VE', ' BOGOTA CHAPINERO', ' CLL 53 13 64', ' CLL 53 13 52', '', '\r'),
	('102-VE', ' BOGOTA AUTONORTE', ' LOCAL #1 AV CR 45 #97 80', '', '', '\r'),
	('110-VE', ' GASTOS ADMINISTRATIVOS POR DISTRIBUIR', ' CLL 7 30A12', '', '', '\r'),
	('900-VE', ' LABORATORIO SAN JORGE LTDA', ' CR 2 14 26', '', '', '\r'),
	('XXX-VE', ' C.O PARA CIERRE', '', '', '', '\r');
	

/* ELIMINA PROCEDIMIENTOS SI EXISTE */
DROP FUNCTION IF EXISTS NumeroCaja;
DROP PROCEDURE IF EXISTS BuscarCod;
DROP PROCEDURE IF EXISTS BuscarRecibido;
DROP PROCEDURE IF EXISTS Buscarcaja;
DROP PROCEDURE IF EXISTS BuscarItemsCaja;
DROP PROCEDURE IF EXISTS BuscarIE;

/* ELIMINA TRIGGER SI EXISTEN */
DROP TRIGGER IF EXISTS InicioAbrir;
DROP TRIGGER IF EXISTS CerrarCaja;
DROP TRIGGER IF EXISTS AutoIncrementG;
DROP TRIGGER IF EXISTS EstadoRecibido;
DROP TRIGGER IF EXISTS EstadoRecibido2;
DROP TRIGGER IF EXISTS ReqEnviado;
DROP TRIGGER IF EXISTS ReqEnviado2;

-- funcion que busca la ultima caja abierta por el alistador pers
DELIMITER $$
	CREATE  FUNCTION NumeroCaja(pers INT(10) )
	RETURNS INT
	BEGIN  
		DECLARE numcaja INT(10);
		SELECT no_caja INTO numcaja
		FROM caja
		WHERE Alistador=pers
		AND estado =0
		ORDER BY abrir
		DESC LIMIT 1;

		RETURN numcaja;
	END 
$$

-- procedimiento que busca un Item con el codigo de barras en la la lista de rquerido especificada
-- el procedimiento tambien cambia el estado del Item a 1 que significa que esta siendo alistado
DELIMITER $$
	CREATE PROCEDURE BuscarCod(IN codigo CHAR(40), IN no_req CHAR(10),IN alistador INT(10),IN numerocaja CHAR(10))
	BEGIN

		DECLARE numcaja INT(10);
		SET numcaja=NumeroCaja(alistador);
		
		SELECT pedido.item,pedido.estado,pedido.no_req,pedido,pedido.disp,pedido.alistado,pedido.ubicacion,pedido.no_caja,
		MIN(COD_BARRAS.ID_CODBAR) AS ID_CODBAR,ITEMS.ID_REFERENCIA, ITEMS.ID_REFERENCIA,ITEMS.DESCRIPCION,
		usuario.nombre,requisicion.lo_origen,requisicion.lo_destino
		FROM COD_BARRAS
		INNER JOIN ITEMS ON ID_ITEM=ID_ITEMS	
		INNER JOIN pedido ON Item=ID_ITEM	
		INNER JOIN requisicion ON requisicion.no_req=pedido.no_req
		LEFT JOIN caja ON caja.no_caja=pedido.no_caja
		LEFT JOIN usuario ON id_usuario=caja.alistador
		WHERE (COD_BARRAS.ID_CODBAR LIKE codigo
		OR ID_ITEM LIKE codigo
		OR ID_REFERENCIA LIKE codigo
		OR LOWER(DESCRIPCION)  LIKE codigo ) 
		AND pedido.no_req LIKE no_req
		AND pedido.no_caja LIKE numerocaja
		GROUP BY  pedido.item,pedido.estado,pedido.no_req,pedido,pedido.disp,pedido.alistado,pedido.ubicacion,pedido.no_caja;

		
		UPDATE pedido
		SET estado=1,no_caja=numcaja
		WHERE item=(SELECT ID_ITEM FROM ITEMS 
				WHERE ID_ITEM = (SELECT ID_ITEMS FROM COD_BARRAS
										WHERE ID_CODBAR=codigo)
				OR ID_ITEM = codigo
				OR ID_REFERENCIA = codigo
				OR LOWER(DESCRIPCION)  = codigo)
		AND pedido.no_req= no_req
		AND estado=0;
		
	END 
$$


DELIMITER $$
	CREATE PROCEDURE BuscarRecibido(IN codigo CHAR(15))
	BEGIN

		SELECT ITEMS.ID_CODBAR,id_referencia, descripcion,no_caja,alistado
		FROM pedido
		RIGHT JOIN ITEMS ON ITEMS.ID_Item=pedido.Item
		WHERE COD_BARRAS.ID_CODBAR = codigo;

	END 
$$


-- procedimiento que busca las cajas por el numero de caja y la requisicion
DELIMITER $$
	CREATE PROCEDURE BuscarCaja(IN numcaja CHAR(10),IN req CHAR(10), IN est CHAR(2))
	BEGIN
		SELECT caja.no_caja, usuario.nombre,tipo_caja,abrir,cerrar,recibido
		FROM caja 
		INNER JOIN pedido ON pedido.no_caja=caja.no_caja
		INNER JOIN usuario ON usuario.id_usuario=Alistador
		WHERE caja.no_caja LIKE numcaja 
		AND pedido.no_req=req
		AND caja.no_caja <> 1
		AND caja.estado like est 
		GROUP BY caja.no_caja ;
	END 
$$




-- procedimiento que busca ITEMS de 1 caja
DELIMITER $$
	CREATE PROCEDURE BuscarItemsCaja(IN numcaja CHAR(10))
	BEGIN

		SELECT *
		FROM caja 
		INNER JOIN pedido ON pedido.no_caja=caja.no_caja
		INNER JOIN usuario ON usuario.id_usuario=Alistador
		WHERE caja.no_caja = numcaja 
		AND caja.no_caja <> 1;

	END 
$$


-- procedimiento que busca cualquier Item con el parametro buscar que no este en los requeridos
DELIMITER $$
	CREATE PROCEDURE BuscarIE(IN buscar CHAR(40))
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
	CREATE TRIGGER InicioAbrir 
	BEFORE INSERT ON caja
	FOR EACH ROW 
	BEGIN
		SET new.abrir=now() ;
	END 
$$

DELIMITER $$

	CREATE TRIGGER CerrarCaja
	BEFORE UPDATE ON caja
	FOR EACH ROW 
	BEGIN
		IF new.estado=1 THEN 
			SET new.cerrar=now() ;
		END IF;
	END 

$$

-- DROP TRIGGER IF EXISTS  EstadoRecibido;
-- trigger que modifica el estado del Item recibido  
DELIMITER $$
	CREATE TRIGGER EstadoRecibido 
	BEFORE INSERT ON recibido
	FOR EACH ROW 
	BEGIN
	
		DECLARE cja INT(10);
		DECLARE numalistado INT(5);
		DECLARE ubc VARCHAR(6);
		DECLARE numpedido INT(5);
		DECLARE ider INT(5);
		DECLARE num INT(1);
		DECLARE estado INT(1);
		
		-- busca si el item está mas de 1  vez en la requisicions
		SELECT COUNT(no_caja) INTO num
		FROM pedido
		WHERE pedido.item = new.item
		AND pedido.no_req = new.no_req;

		--	si está se busca si dicha caja coincide con la que se recibio
		IF num>1 THEN

			SELECT no_caja,alistado,ubicacion,pedido INTO cja, numalistado,ubc,numpedido
			FROM pedido
			RIGHT JOIN ITEMS ON ITEMS.ID_Item=pedido.Item
			WHERE ITEMS.ID_Item = new.item
			AND pedido.no_req=new.no_req
			AND pedido.no_caja=new.no_caja;
			
		-- si no se busca normalmente en pedido
		ELSE
			SELECT no_caja,alistado,ubicacion,pedido INTO cja, numalistado,ubc,numpedido
			FROM pedido
			RIGHT JOIN ITEMS ON ITEMS.ID_Item=pedido.Item
			WHERE ITEMS.ID_Item = new.item
			AND pedido.no_req=new.no_req;
	
		END IF;

		IF cja IS NULL THEN
			SET new.estado=2;
			SET cja=1;
		ELSEIF cja<>new.no_caja THEN
			SET new.estado=3;
		ELSEIF new.recibidos<numalistado THEN
			SET new.estado=0;
		ELSEIF new.recibidos>numalistado THEN
			SET new.estado=1;
		ELSE 
			SET new.estado=4;
		END IF;

		IF ubc IS NULL THEN
			SET ubc='----';
			SET numpedido=0;
			SET numalistado=0;
		END IF;
		
		IF new.estado<>4 THEN
			REPLACE INTO errores(item,no_req,no_caja,no_caja_recibido,recibidos,estado,ubicacion,pedido,alistado) 
			VALUES(new.item,new.no_req,cja,new.no_caja,new.recibidos,new.estado,ubc,numpedido,numalistado);
		END IF;

	END 	
$$

DELIMITER $$
	CREATE TRIGGER EstadoRecibido2 
	BEFORE UPDATE ON recibido
	FOR EACH ROW 
	BEGIN
	
		DECLARE cja INT(10);
		DECLARE numalistado INT(5);
		DECLARE ubc VARCHAR(6);
		DECLARE numpedido INT(5);
		DECLARE ider INT(5);
		DECLARE num INT(1);
		DECLARE estado INT(1);
								
		-- busca si el item está mas de 1  vez en la requisicions
		SELECT COUNT(no_caja) INTO num
		FROM pedido
		WHERE pedido.item = new.item
		AND pedido.no_req = new.no_req;

		--	si está se busca si dicha caja coincide con la que se recibio
		IF num>1 THEN

			SELECT no_caja,alistado,ubicacion,pedido INTO cja, numalistado,ubc,numpedido
			FROM pedido
			RIGHT JOIN ITEMS ON ITEMS.ID_Item=pedido.Item
			WHERE ITEMS.ID_Item = new.item
			AND pedido.no_req=new.no_req
			AND pedido.no_caja=new.no_caja;
			
		-- si no se busca normalmente en pedido
		ELSE
			SELECT no_caja,alistado,ubicacion,pedido INTO cja, numalistado,ubc,numpedido
			FROM pedido
			RIGHT JOIN ITEMS ON ITEMS.ID_Item=pedido.Item
			WHERE ITEMS.ID_Item = new.item
			AND pedido.no_req=new.no_req;
	
		END IF;

		IF cja IS NULL THEN
			SET new.estado=2;
			SET cja=1;
		ELSEIF cja<>new.no_caja THEN
			SET new.estado=3;
		ELSEIF new.recibidos<numalistado THEN
			SET new.estado=0;
		ELSEIF new.recibidos>numalistado THEN
			SET new.estado=1;
		ELSE 
			SET new.estado=4;
		END IF;

		IF ubc IS NULL THEN
			SET ubc='----';
			SET numpedido=0;
			SET numalistado=0;
		END IF;
		
		IF new.estado<>4 THEN
			REPLACE INTO errores(item,no_req,no_caja,no_caja_recibido,recibidos,estado,ubicacion,pedido,alistado) 
			VALUES(new.item,new.no_req,cja,new.no_caja,new.recibidos,new.estado,ubc,numpedido,numalistado);
		END IF;

	END 	
$$


-- trigger que modifica el estado de enviado de la requisicion 
-- y agrega los items en recibido con estado 2

DELIMITER $$
	CREATE TRIGGER ReqEnviado 
	AFTER UPDATE ON pedido
	FOR EACH ROW 
	BEGIN
		DECLARE numalistados TINYINT;		
		DECLARE numrecibidos TINYINT;	
		
		SELECT count(estado) INTO numalistados
		FROM pedido
		WHERE (estado=0
		OR estado=1)
		AND no_req=new.no_req;
		
		SELECT count(estado) INTO numrecibidos
		FROM pedido
		WHERE estado<>4
		AND estado<>3
		AND no_req=new.no_req;
		
		
      IF new.estado=2 THEN
			REPLACE INTO recibido(Item,No_Req,no_caja,recibidos) 
			VALUES(new.item,new.no_req,new.no_caja,0);
		-- si el item fue corregido
      ELSEIF new.estado=3 THEN 
--      	REPLACE INTO recibido(Item,No_Req,no_caja,recibidos) 
--			VALUES(new.item,new.no_req,new.no_caja,new.alistado);
			INSERT INTO recibido(Item,No_Req,no_caja,recibidos) 
			VALUES(new.item,new.no_req,new.no_caja,new.alistado)
			ON DUPLICATE KEY UPDATE
        	estado=0;
		  
      ELSEIF new.estado=0 THEN
      	DELETE FROM recibido
      	WHERE item=new.item
      	AND no_caja=new.no_caja;
		END IF;
        
		IF numalistados=0 THEN
			UPDATE requisicion
			SET enviado=NOW(),estado=1
			WHERE requisicion.no_req=new.no_req;
		ELSE
			UPDATE requisicion
			SET estado=0
			WHERE requisicion.no_req=new.no_req;
		END IF;
		
		IF numrecibidos=0 THEN
			UPDATE requisicion
			SET enviado=NOW(),estado=1
			WHERE requisicion.no_req=new.no_req;
		END IF;		

			
	END 
$$

DROP TRIGGER IF EXISTS ReqEnviado2;
DELIMITER $$
	CREATE TRIGGER ReqEnviado2
	AFTER INSERT ON pedido
	FOR EACH ROW 
	BEGIN
				

		IF new.estado=3 THEN      
			INSERT INTO recibido(Item,No_Req,no_caja,recibidos) 
			VALUES(new.item,new.no_req,new.no_caja,new.alistado)
			ON DUPLICATE KEY UPDATE
        	estado=0;
      ELSEIF new.estado=0 THEN
--      IF new.estado=0 THEN
      	DELETE FROM recibido
      	WHERE item=new.item
      	AND no_req=new.no_req
      	AND no_caja=new.no_caja;
		END IF;		
		
	END 
$$
-- *********************************************************************************************************************************************************************************************
-- *********************************************************************************************************************************************************************************************
-- *********************************************************************************************************************************************************************************************


