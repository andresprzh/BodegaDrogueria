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
	password VARCHAR(60) NOT NULL,
	perfil INT(1),

	PRIMARY KEY(id_usuario),

	CONSTRAINT usuario_perfil
	FOREIGN KEY(perfil)
	REFERENCES perfiles(id_perfil)
);

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

	PRIMARY KEY(no_req)
);

/*tabla caja*/
CREATE TABLE caja(
	no_caja INT(10) NOT NULL AUTO_INCREMENT,
	alistador INT(10),
	encargado_punto INT(10),
	tipo_caja CHAR(3) ,
	estado INT(1) DEFAULT '0' ,
	abrir DATETIME ,
	cerrar DATETIME,
	enviado DATETIME ,
   recibido DATETIME,


	PRIMARY KEY(no_caja),

	CONSTRAINT caja_alistador_usuario
	FOREIGN KEY(alistador) 
	REFERENCES usuario(id_usuario),

	CONSTRAINT caja_encargado_usuario
	FOREIGN KEY(encargado_punto) 
	REFERENCES usuario(id_usuario)
);

/*Crea la tabla donde se almacenan los productos pedidos en la requisicion*/
CREATE TABLE pedido(
	item CHAR(6) NOT NULL,
	no_req CHAR(10) NOT NULL,
	no_caja INT(10) default 1,
	ubicacion VARCHAR(6) NOT NULL,
	disp  INT(5) NOT NULL,
	pedido INT(5) NOT NULL,
	alistado INT(5) default 0,
	estado INT(1) NOT NULL default 0,



	PRIMARY KEY(item,no_req),

	CONSTRAINT pedido_Item
	FOREIGN KEY(item) 
	REFERENCES `ITEMS`(`ID_ITEM`),

	CONSTRAINT pedido_requisicion
	FOREIGN KEY(no_req) 
	REFERENCES requisicion(no_req),

	CONSTRAINT pedido_caja
	FOREIGN KEY(no_caja) 
	REFERENCES caja(no_caja)
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
	REFERENCES caja(no_caja)
);


 
-- se llena un primer registro a caja que define las cajas no asignadas
INSERT INTO caja(no_caja) VALUES(1);

INSERT INTO perfiles(des_perfil) VALUES("Administrador"),("Jefe"),("Alistador"),("PVenta"),("Inactivo");

INSERT INTO usuario(nombre,cedula,usuario,password,perfil) VALUES("Admin","1111111111","admin","$2y$10$bpNOdujEVRMWB7JtWJX7Y.HPBjVCMSLS/r2YeafW5Mu.wfmyi/iLy",1);

/* ELIMINA PROCEDIMIENTOS SI EXISTE */
DROP FUNCTION IF EXISTS NumeroCaja;
DROP PROCEDURE IF EXISTS BuscarCod;
DROP PROCEDURE IF EXISTS BuscarRecibido;
DROP PROCEDURE IF EXISTS Empacar;
DROP PROCEDURE IF EXISTS Buscarcaja;
DROP PROCEDURE IF EXISTS BuscarItemsCaja;
DROP PROCEDURE IF EXISTS BuscarIE;

/* ELIMINA TRIGGER SI EXISTEN */
DROP TRIGGER IF EXISTS InicioAbrir;
DROP TRIGGER IF EXISTS CerrarCaja;
DROP TRIGGER IF EXISTS EstadoRecibido;
DROP TRIGGER IF EXISTS ReqEnviado;

-- funcion que busca la ultima caja abierta por el alistador pers
DELIMITER $$
	CREATE  FUNCTION NumeroCaja(pers INT(10) )
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
	CREATE PROCEDURE BuscarCod(IN codigo CHAR(40), IN no_req CHAR(10),IN alistador INT(10),IN numerocaja CHAR(10))
	BEGIN

		DECLARE numcaja INT(10);
		SET numcaja=NumeroCaja(alistador);
		
		SELECT pedido.item,ID_CODBAR,ID_REFERENCIA,pedido.estado,pedido.no_req, ID_REFERENCIA, 
        descripcion, disp, pedido, alistado,caja.no_caja,usuario.nombre,ubicacion,
        requisicion.lo_origen,requisicion.lo_destino
		FROM ITEMS
		INNER JOIN pedido ON Item=ID_ITEM	
		INNER JOIN requisicion ON requisicion.no_req=pedido.no_req
		LEFT JOIN caja ON caja.no_caja=pedido.no_caja
		LEFT JOIN usuario ON id_usuario=caja.alistador
		WHERE (ID_CODBAR LIKE codigo
		OR ID_ITEM LIKE codigo
		OR ID_REFERENCIA LIKE codigo
		OR LOWER(DESCRIPCION)  LIKE codigo ) 
		AND pedido.no_req LIKE no_req
		AND pedido.no_caja LIKE numerocaja;

		
		UPDATE pedido
		SET estado=1,no_caja=numcaja
		WHERE (Item=(SELECT ID_ITEM FROM ITEMS WHERE (ID_CODBAR = codigo
		OR ID_ITEM = codigo
		OR ID_REFERENCIA = codigo
		OR LOWER(DESCRIPCION)  LIKE codigo )))
		AND pedido.no_req=no_req
		AND estado=0 ;
		
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


-- procedimiento que empaca los ITEMS(asigna  la fecha a la caja y el numero de caja al Item)
DELIMITER $$
	CREATE PROCEDURE Empacar(IN codigo CHAR(15), IN alistar INT(5),IN pers INT(10),IN caj CHAR(3),IN req CHAR(10))
	BEGIN

		DECLARE numcaja INT(10);
		SET numcaja=numerocaja(pers);

		UPDATE pedido
		SET alistado=alistar,estado=2
		WHERE Item=(SELECT ID_ITEMS FROM COD_BARRAS WHERE ID_CODBAR=codigo)
		AND no_req=req;

		UPDATE caja
		SET tipo_caja=caj
		WHERE no_caja=numcaja;

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


-- prcedimiento que busca cualquier Item con el parametro buscar que no este en los requeridos
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
		SET new.cerrar=now() ;
	END 

$$

-- trigger que modifica el estado del Item recibido  
DELIMITER $$

	CREATE TRIGGER EstadoRecibido 
	BEFORE INSERT ON recibido
	FOR EACH ROW 
	BEGIN
	
		DECLARE caja INT(10);
		DECLARE numalistado INT(5);
		SELECT no_caja,alistado INTO caja, numalistado
		FROM pedido
		RIGHT JOIN ITEMS ON ITEMS.ID_Item=pedido.Item
		WHERE ITEMS.ID_Item = new.Item;
	
		IF caja IS NULL THEN
			SET new.estado=2;
		ELSEIF caja<>new.no_caja THEN
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
-- y agrega los items en recibido con estado 2
DELIMITER $$

	CREATE TRIGGER ReqEnviado 
	AFTER UPDATE ON pedido
	FOR EACH ROW 
	BEGIN
		DECLARE numalistados TINYINT;
		SELECT count(estado) INTO numalistados
		FROM pedido
		WHERE estado<>2
		AND no_req=new.no_req;
		
        IF new.estado=2 then
			INSERT INTO recibido(Item,No_Req,no_caja,recibidos) 
			VALUES(new.item,new.no_req,new.no_caja,0);
        END IF;
        
		IF numalistados=0 THEN
			UPDATE requisicion
			SET enviado=NOW()
			WHERE requisicion.no_req=new.no_req;
		END IF;
	END 

$$


-- *********************************************************************************************************************************************************************************************
-- *********************************************************************************************************************************************************************************************
-- *********************************************************************************************************************************************************************************************


