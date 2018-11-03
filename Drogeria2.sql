/*Elimina la base de datos si existe*/
-- drop database if exists drogueria;
/*se crea la base de datos*/
/*******************************************************************************************************************************
											CREA BASE DE DATOS
********************************************************************************************************************************/
CREATE DATABASE  IF NOT EXISTS bodegadrogueria;
/*se usa la base de daos*/
USE bodegadrogueria;



/*******************************************************************************************************************************
											CREA TABLAS BASE DE DATPS
********************************************************************************************************************************/

CREATE TABLE IF NOT EXISTS emails(
	id_correos INT(4) NOT NULL AUTO_INCREMENT,
	correo VARCHAR(255) NOT NULL,
	
	PRIMARY KEY(id_correos)
);

CREATE TABLE IF NOT EXISTS `ITEMS` (
	`ID_ITEM` CHAR(6) NOT NULL,
	`ID_REFERENCIA` CHAR(15) DEFAULT NULL,
	`DESCRIPCION` CHAR(40) DEFAULT NULL,
	`ID_LINEA2` CHAR(6) DEFAULT NULL,
	`ID_GRUPO2` CHAR(6) DEFAULT NULL,
	`UNIMED_INV_1` CHAR(3) DEFAULT NULL,
	`UNIMED_EMPAQ` CHAR(3) DEFAULT NULL,
	`FACTOR_EMPAQ` decimal(20,4) DEFAULT NULL,
	`PESO` decimal(20,4) DEFAULT NULL,
	`VOLUMEN` decimal(20,4) DEFAULT NULL,
	`ULTIMO_COSTO_ED` decimal(20,4) DEFAULT NULL,
	`FECHA_INGRESO` CHAR(8) DEFAULT NULL,
	
	CONSTRAINT ITEMS_PK 
	PRIMARY KEY (`ID_ITEM`),
	
	INDEX (`ID_REFERENCIA`),
	INDEX (`DESCRIPCION`)
  
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
-- Volcando estructura para tabla BD_BIABLE01.COD_BARRAS
	
CREATE TABLE IF NOT EXISTS `COD_BARRAS` (
  `ID_ITEMS` CHAR(6) DEFAULT NULL,
  `ID_CODBAR` CHAR(15) BINARY NOT NULL,
  `UNIMED_VENTA` CHAR(3) DEFAULT NULL,
  
  CONSTRAINT CODBAR_PK 
  PRIMARY KEY (`ID_CODBAR`),
  
  CONSTRAINT BAR_ITEM 
  FOREIGN KEY (ID_ITEMS)
  REFERENCES ITEMS (ID_ITEM) 
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- tabla perfiles de usuarios
CREATE TABLE IF NOT EXISTS perfiles(
	id_perfil INT(1) NOT NULL AUTO_INCREMENT,
	des_perfil CHAR(20),

	PRIMARY KEY(id_perfil)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `sedes` (
  `codigo` CHAR(6) NOT NULL,
  `descripcion` CHAR(40) DEFAULT NULL,
  `direccion1` CHAR(40) DEFAULT NULL,
  `direccion2` CHAR(40) DEFAULT NULL,
  `direccion3` CHAR(40) DEFAULT NULL,
  `grupo_co` CHAR(2) DEFAULT NULL,
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS franquicias (
  `codigo` CHAR(6) NOT NULL,
  `descripcion` CHAR(40) DEFAULT NULL,
  `direccion1` CHAR(40) DEFAULT NULL,
  `cod_sucursal` CHAR(2) DEFAULT 00,
  `nit` CHAR(12) DEFAULT '000000000',
  
  PRIMARY KEY (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- tabla de usuarios
CREATE TABLE IF NOT EXISTS usuario(
	id_usuario INT(10) NOT NULL AUTO_INCREMENT,	
	nombre VARCHAR(40) COLLATE ucs2_spanish_ci,
	cedula CHAR(12),
	usuario CHAR(20),
	password VARCHAR(60) NOT NULL,
	perfil INT(1),
	franquicia CHAR(6) NOT NULL DEFAULT "NFRA", 

	PRIMARY KEY(id_usuario),
	UNIQUE(cedula),
	UNIQUE(usuario),
	
	CONSTRAINT usuario_perfil
	FOREIGN KEY(perfil)
	REFERENCES perfiles(id_perfil),
	
	CONSTRAINT usuario_sedes
	FOREIGN KEY(franquicia)
	REFERENCES franquicias(codigo),	
	
	INDEX (id_usuario)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*tabla de la requisicion a bodega*/
CREATE TABLE IF NOT EXISTS requisicion(
	no_req CHAR(10) NOT NULL,
	creada DATETIME NOT NULL,
	lo_origen CHAR(6),
	lo_destino CHAR(6),
	tip_inventario INT(2),
	solicitante VARCHAR(40) COLLATE ucs2_spanish_ci,
	enviado DATETIME,
	recibido DATETIME,
	documentos INT(3) DEFAULT 0,
	estado INT(1) DEFAULT 0,

	PRIMARY KEY(no_req),

	CONSTRAINT requisicion_origen
	FOREIGN KEY(lo_origen) 
	REFERENCES sedes(codigo),

	CONSTRAINT requisicion_destino
	FOREIGN KEY(lo_destino) 
	REFERENCES sedes(codigo)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS  tipo_caja(
	tipo_caja CHAR(3) NOT NULL,
	descripcion CHAR(20) NOT NULL,
	
	PRIMARY KEY(tipo_caja)
	
)ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*tabla caja*/
CREATE TABLE IF NOT EXISTS caja(
	no_caja INT(10) NOT NULL AUTO_INCREMENT,
	alistador INT(10),
	encargado_punto INT(10),
	transportador INT(10),
	tipo_caja CHAR(3) ,
	estado INT(1) DEFAULT '0' ,
	peso FLOAT(6,2) DEFAULT 0,
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
)ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Crea la tabla donde se almacenan los productos pedidos en la requisicion*/
CREATE TABLE IF NOT EXISTS pedido(
	item CHAR(6) NOT NULL,
	no_req CHAR(10) NOT NULL,
	ubicacion VARCHAR(6) NOT NULL DEFAULT '----',
	disp  INT(5) NOT NULL,
	pedido INT(5) NOT NULL,
	pendientes INT(5) default 0,
	estado INT(1) NOT NULL default 0,



	PRIMARY KEY(item,no_req),

	CONSTRAINT pedido_Item
	FOREIGN KEY(item) 
	REFERENCES `ITEMS`(`ID_ITEM`),

	CONSTRAINT pedido_requisicion
	FOREIGN KEY(no_req) 
	REFERENCES requisicion(no_req),
	
	INDEX (estado)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS alistado(
	item CHAR(6) NOT NULL,
	no_req CHAR(10) NOT NULL,
	no_caja INT(10) default 1,
	alistado INT(5) default 0,
	estado INT(1) NOT NULL default 1,

	PRIMARY KEY(item,no_caja),

	CONSTRAINT alistado_pedido
	FOREIGN KEY(item,no_req) 
	REFERENCES pedido(item,no_req),
	
	CONSTRAINT pedido_caja
	FOREIGN KEY(no_caja) 
	REFERENCES caja(no_caja),
	
	
	INDEX (estado)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS recibido(
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
)ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS errores(
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
)ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS tareas(
	id_tarea INT(10) AUTO_INCREMENT,
	usuario INT(10),
	creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
	terminacion DATETIME,
	
	PRIMARY KEY(id_tarea),
	
	CONSTRAINT tareas_usuario
	FOREIGN KEY(usuario)
	REFERENCES usuario(id_usuario)
	
)ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS tareas_det(
	id_tareadet INT(10),
	id_tarea INT(10),
	ubicacion VARCHAR(6) NOT NULL,
	
	PRIMARY KEY(id_tareadet,id_tarea),
	
	CONSTRAINT det_tareas_tareas
	FOREIGN KEY(id_tarea)
	REFERENCES tareas(id_tarea)
	
)ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS remisiones(
	no_rem INT(5) NOT NULL AUTO_INCREMENT,
	creada DATETIME DEFAULT CURRENT_TIMESTAMP,
	estado INT(1) DEFAULT 0,
	ubicacion CHAR(6) DEFAULT '001-BD' ,
	franquicia CHAR(6) NOT NULL,
	encargado INT(10) NOT NULL,
	encargado_franquicia INT(10) ,

	PRIMARY KEY(no_rem),

	CONSTRAINT encargado_usuario_remision
	FOREIGN KEY(encargado) 
	REFERENCES usuario(id_usuario),	

	CONSTRAINT encargadofranquicia_usuario_remision
	FOREIGN KEY(encargado_franquicia) 
	REFERENCES usuario(id_usuario)

)ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS pedido_remisiones(
	item CHAR(6) NOT NULL,
	no_rem INT(5) NOT NULL,
	cantidad  INT(5) NOT NULL,
	unidad CHAR(5) NOT NULL DEFAULT 'UND',
	valor FLOAT(12,3) DEFAULT 0,
	descuento FLOAT(12,3) DEFAULT 0,
	impuesto FLOAT(8,3) DEFAULT 0,
	total FLOAT(12,3) DEFAULT 0,
	costo FLOAT(12,3) DEFAULT 0,
	rent FLOAT(5,3) DEFAULT 0,
	estado INT(1) NOT NULL DEFAULT 0,

	PRIMARY KEY(item,no_rem),

	CONSTRAINT pedidorem_Item
	FOREIGN KEY(item) 
	REFERENCES `ITEMS`(`ID_ITEM`),

	CONSTRAINT pedido_remision
	FOREIGN KEY(no_rem) 
	REFERENCES remisiones(no_rem),
	
	INDEX (estado)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS recibido_remisiones(
	item CHAR(6) NOT NULL,
	no_rem INT(5) NOT NULL,
	recibidos INT(5) default 0,
	estado INT(1) NOT NULL default 0,

	PRIMARY KEY(item,no_rem),

	CONSTRAINT recibido_Item_remisio
	FOREIGN KEY(item) 
	REFERENCES ITEMS(ID_ITEM),

	CONSTRAINT recibido_remision
	FOREIGN KEY(no_rem) 
	REFERENCES remisiones(no_rem),
	
	INDEX (estado)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS errores_remisiones(
	item CHAR(6) NOT NULL,
	no_rem INT(5) NOT NULL,
	recibidos INT(5) default 0,
	estado INT(1) NOT NULL default 0,
	alistado INT(5) default 0,


	PRIMARY KEY(item,no_rem),

	CONSTRAINT errores_Item_remision
	FOREIGN KEY(item) 
	REFERENCES ITEMS(ID_ITEM),

	CONSTRAINT errores_remision
	FOREIGN KEY(no_rem) 
	REFERENCES remisiones(no_rem),
	
	INDEX (estado)
)ENGINE=InnoDB DEFAULT CHARSET=latin1;


/*******************************************************************************************************************************
											INICIALIZA REGISTROS BASE DE DATOS 
********************************************************************************************************************************/
/* REPLACE INTO tipo_caja  VALUES 
	('CRT','Caja de cartón'),
	('CPL','Caja plástica'),
	('CAP','Canasta plástica'),
	('GLN','Galon'),
	('GLA','Galoneta'
);
-- se llena un primer registro a caja que define las cajas no asignadas
INSERT INTO caja(no_caja) VALUES(1);
UPDATE caja SET no_caja=0 WHERE no_caja=1;
ALTER TABLE caja AUTO_INCREMENT=0;

REPLACE INTO franquicias(codigo,descripcion,cod_sucursal,nit) VALUES
	('NFRA','NO ES FRANQUICIA','00','000000000'),
	('PAL1','PALMIRA 1','01','94506068'),('PAL2','PALMIRA 2','02','94506068'),('PAL3','PALMIRA 3','03','94506068'),
	('SANT','SANTANDER DE QUILICHAO','00','34611591'),
	('UBBS','CHAPINERO VASQUEZ BARRENECHE','00','800097434'
);

REPLACE INTO perfiles VALUES(-1,"Inactivo"),(1,"Administrador"),(2,"Jefe"),(3,"Alistador"),(4,"PVenta"),(5,"JefeD"),(6,"Transportador"),(7,"Franquicia");
UPDATE perfiles SET id_perfil=0 WHERE id_perfil=-1;

REPLACE INTO usuario(nombre,cedula,usuario,password,perfil) VALUES("Administrador","0","admin","$2y$10$bpNOdujEVRMWB7JtWJX7Y.HPBjVCMSLS/r2YeafW5Mu.wfmyi/iLy",1);


REPLACE INTO `sedes` (`codigo`, `descripcion`, `direccion1`, `direccion2`, `direccion3`, `grupo_co`) VALUES
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
	('XXX-VE', ' C.O PARA CIERRE', '', '', '', '\r'
); */


/*******************************************************************************************************************************
											PROCEDIMIENTOS FUNCIONES Y TRIGGERS BASE DE DATOS
********************************************************************************************************************************/
/* ELIMINA PROCEDIMIENTOS Y FUNCIONES SI EXISTE */
DROP FUNCTION IF EXISTS NumeroCaja;
DROP FUNCTION IF EXISTS VerificarCaja;
DROP FUNCTION IF EXISTS VerificarRemision;
DROP PROCEDURE IF EXISTS BuscarCod;


/* ELIMINA TRIGGER SI EXISTEN */
DROP TRIGGER IF EXISTS CrearTarea;
DROP TRIGGER IF EXISTS SetPendientes;
DROP TRIGGER IF EXISTS EstadoPedido;
DROP TRIGGER IF EXISTS EstadoReq;
DROP TRIGGER IF EXISTS EstadoAlistado;
DROP TRIGGER IF EXISTS EstadoAlistadoUpd;
DROP TRIGGER IF EXISTS InsertPedidoRemision;
DROP TRIGGER IF EXISTS EliminarAlistado;
DROP TRIGGER IF EXISTS InicioAbrir;
DROP TRIGGER IF EXISTS CerrarCaja;
DROP TRIGGER IF EXISTS EstadoRecibido;
DROP TRIGGER IF EXISTS EstadoRecibidoRemision;
DROP TRIGGER IF EXISTS AutoincTareas;

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

-- procedimiento que verifica el estado de los items recibidos de una requisicion comparandolos con  los items pedidos o alistados
DELIMITER $$
	CREATE FUNCTION VerificarCaja(numcaja INT(10),req CHAR(10))
	RETURNS TINYINT(1)
	BEGIN
		DECLARE cantidad TINYINT;
		-- actualiza el estado de los items recibidos de una requisicion para recalcular el estado
		UPDATE recibido
		SET estado=0
		WHERE no_req=req;
		
		-- cuenta la cantidad de items que tienen errores
		SELECT COUNT(item) INTO cantidad
		FROM recibido
		WHERE no_caja=numcaja
		AND no_req=req
		AND estado <>4;
		
		-- si no hay errores regreasa verdadero
		IF cantidad=0 THEN
			
			-- cambia el estado de la caja a recibida
			UPDATE caja
			SET estado=4
			WHERE no_caja=numcaja;
			return true;
			
		ELSE
		
			return false;
			
		END IF;
		
	END 
$$

-- procedimiento que verifica el estado de los items recibidos de una remision comparandolos con  los items pedidos o alistados
DELIMITER $$
	CREATE FUNCTION VerificarRemision(rem INT(10))
	RETURNS TINYINT(1)
	BEGIN
		DECLARE cantidad TINYINT;
		
		UPDATE recibido_remisiones
		SET estado=0
		WHERE no_rem=rem;
		
		
		SELECT COUNT(item) INTO cantidad
		FROM recibido_remisiones
		WHERE no_rem=rem
		AND estado <>4;
		

		IF cantidad=0 THEN
			

			UPDATE remisiones
			SET estado=4
			WHERE no_rem=rem;
			return true;
			
		ELSE
		
			return false;
			
		END IF;
		
	END 
$$
-- procedimiento que busca un Item con el codigo de barras en la la lista de rquerido especificada
-- el procedimiento tambien cambia el estado del Item a 1 que significa que esta siendo alistado
DELIMITER $$
	CREATE PROCEDURE BuscarCod(IN codigo CHAR(40), IN no_req CHAR(10),IN numerocaja CHAR(10))
	BEGIN
		
		IF(numerocaja IS NULL) THEN
		
			SELECT pedido.item,pedido.estado,pedido.no_req,pedido,pendientes,pedido.disp,pedido.ubicacion,alistado.alistado,
			ITEMS.ID_REFERENCIA,ITEMS.DESCRIPCION,
			alistado.no_caja,usuario.nombre,
			requisicion.lo_origen,requisicion.lo_destino,MIN(COD_BARRAS.ID_CODBAR) AS ID_CODBAR
			FROM COD_BARRAS
			INNER JOIN ITEMS ON ID_ITEM=ID_ITEMS	
			INNER JOIN pedido ON item=ID_ITEM	
			INNER JOIN requisicion ON requisicion.no_req=pedido.no_req
			LEFT JOIN alistado ON alistado.item=pedido.item	
			LEFT JOIN caja ON caja.no_caja=alistado.no_caja
			LEFT JOIN usuario ON usuario.id_usuario=caja.alistador
			WHERE (COD_BARRAS.ID_CODBAR LIKE codigo
			OR ID_ITEM LIKE codigo
			OR ID_REFERENCIA LIKE codigo
			OR LOWER(DESCRIPCION)  LIKE codigo ) 
			AND pedido.no_req LIKE no_req
			GROUP BY  pedido.item,pedido.estado,pedido.no_req,pedido,pendientes,pedido.disp,pedido.ubicacion,alistado.alistado,
			ITEMS.ID_REFERENCIA,ITEMS.DESCRIPCION,
			alistado.no_caja,usuario.nombre,
			requisicion.lo_origen,requisicion.lo_destino
			ORDER BY pedido.ubicacion ASC;
			
		ELSE 
		
			SELECT pedido.item AS iditem,pedido.no_req,pedido.pendientes,pedido.pedido,alistado.alistado,disp,ubicacion,
			ITEMS.DESCRIPCION AS descripcion ,ITEMS.ID_REFERENCIA AS referencia,MIN(COD_BARRAS.ID_CODBAR) AS codigo
			FROM alistado
			INNER JOIN pedido ON (pedido.item=alistado.item AND pedido.no_req=alistado.no_req)
			INNER JOIN ITEMS ON ID_ITEM=pedido.item
			INNER JOIN COD_BARRAS ON ID_ITEMS=ID_ITEM
			WHERE alistado.no_caja=numerocaja
			GROUP BY pedido.item,pedido.no_req,pedido.pendientes,pedido.pedido,alistado.alistado,disp,ubicacion,
			ITEMS.DESCRIPCION ,ITEMS.ID_REFERENCIA;
			
		END IF;
		
	END 
$$

-- trigger que crea tarea la crear usuario
DELIMITER $$
	CREATE TRIGGER CrearTarea
	AFTER INSERT ON usuario
	FOR EACH ROW 
	BEGIN

		INSERT INTO tareas(usuario) VALUES(new.id_usuario);			
		
	END 
$$

-- trigger que inicializa pendientes
DELIMITER $$
	CREATE TRIGGER SetPendientes
	BEFORE INSERT ON pedido
	FOR EACH ROW 
	BEGIN

		SET new.pendientes=new.pedido;				
		
	END 
$$

-- item que modifica el estado  de los items pedidos
DELIMITER $$
	CREATE TRIGGER EstadoPedido
	BEFORE UPDATE ON pedido
	FOR EACH ROW 
	BEGIN
		
		DECLARE numalistados TINYINT;		
		
		-- solo modifica si los items no han sido enviados
		IF new.pendientes<=0 THEN
			SET new.pendientes=0;
			IF new.estado<2 THEN
				SET new.estado=1;
			END IF;
		ELSE
			SET new.estado=0;
		END IF;
		
	END 
$$
-- MODIFICA ESTADO DE LA REQUISICION
DELIMITER $$
	CREATE TRIGGER EstadoReq
	AFTER UPDATE ON pedido
	FOR EACH ROW 
	BEGIN
		
		DECLARE numalistados TINYINT;		
		
		SELECT count(estado) INTO numalistados
		FROM pedido
		WHERE (estado=0
		OR estado=1)
		AND no_req=new.no_req;

		IF numalistados=0 THEN
			UPDATE requisicion
			SET enviado=NOW(),estado=1
			WHERE requisicion.no_req=new.no_req;
		ELSE
			UPDATE requisicion
			SET estado=0
			WHERE requisicion.no_req=new.no_req;
		END IF;
	
	END 
$$

-- trigger que modifica la cantidad alistada al agregar items
DELIMITER $$
	CREATE TRIGGER EstadoAlistado
	AFTER INSERT ON alistado
	FOR EACH ROW 
	BEGIN

		
		UPDATE pedido
		SET pendientes=pedido.pendientes-new.alistado
		WHERE pedido.item=new.item;
				
	END 
$$

-- trigger que modifica la cantidad alistada al modificar items
DELIMITER $$
	CREATE TRIGGER EstadoAlistadoUpd
	AFTER UPDATE ON alistado
	FOR EACH ROW 
	BEGIN
		
		DECLARE alistados INT(5);
		
		UPDATE pedido
		SET pendientes=pedido.pendientes+(old.alistado-new.alistado)
		WHERE pedido.item=new.item;
		
		SELECT SUM(alistado) INTO alistados 
		FROM alistado
		WHERE (estado=2)
		AND no_req=new.no_req
		AND item=new.item;
		
		UPDATE pedido
		SET estado=2
		WHERE 
		pedido.pedido <= alistados AND
		pedido.item=new.item;

		IF new.estado=2 THEN
			REPLACE INTO recibido(Item,No_Req,no_caja,recibidos) 
			VALUES(new.item,new.no_req,new.no_caja,0);
		-- si el item fue corregido
      	ELSEIF new.estado=3 THEN 
			-- inserta valores en pedido solo si no exites el registrp
			INSERT INTO recibido (Item,No_Req,no_caja,recibidos) 
			SELECT * FROM (SELECT new.item as item, new.no_req as req,new.no_caja as caja ,new.alistado as recibido) as temp
			WHERE NOT EXISTS (
				SELECT 1 FROM recibido WHERE item = new.item AND no_req=new.no_req AND no_caja=new.no_caja
			) LIMIT 1;
		END if;

	END 
$$

-- trigger que agrea items atabla recibido_remisiones
DELIMITER $$
	CREATE TRIGGER InsertPedidoRemision
	AFTER INSERT ON pedido_remisiones
	FOR EACH ROW 
	BEGIN
		
		INSERT INTO recibido_remisiones (item,no_rem,recibidos) 
		SELECT * FROM (SELECT new.item as item, new.no_rem as rem,0 as recibido) as temp
		WHERE NOT EXISTS (
			SELECT 1 FROM recibido_remisiones WHERE item = new.item AND no_rem=new.no_rem
		) LIMIT 1;

	END 
$$

-- item que modifica el estado  de los items pedidos al eliminar item alistado
DELIMITER $$
	CREATE TRIGGER EliminarAlistado
	BEFORE DELETE ON alistado
	FOR EACH ROW 
	BEGIN

		UPDATE pedido
		SET pedido.pendientes=pedido.pendientes+old.alistado
		WHERE pedido.item=old.item;
				
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

-- trigger que cierra caja y alista items cuando se cambia de estado a creada (estado = 1)
DELIMITER $$

	CREATE TRIGGER CerrarCaja
	BEFORE UPDATE ON caja
	FOR EACH ROW 
	BEGIN
		IF new.estado=1 THEN 
			SET new.cerrar=now();
			UPDATE alistado
			SET estado=2
			WHERE alistado.no_caja=new.no_caja;
		END IF;
	END 

$$

-- DROP TRIGGER IF EXISTS EstadoRecibido;
DELIMITER $$
	CREATE TRIGGER EstadoRecibido 
	BEFORE UPDATE ON recibido
	FOR EACH ROW 
	BEGIN
	
		DECLARE cja INT(10);
		DECLARE numalistado INT(5);
		DECLARE ubc VARCHAR(6);
		DECLARE numpedido INT(5);
		DECLARE req CHAR(10);
		DECLARE ider INT(5);
		DECLARE num INT(1);
		DECLARE estado INT(1);
								
		-- busca si el item está mas de 1  vez en la requisicions
		SELECT COUNT(no_caja) INTO num
		FROM alistado
		WHERE alistado.item = new.item
		AND alistado.no_req = new.no_req;

		--	si está se busca si dicha caja coincide con la que se recibio
		IF num>1 THEN

			SELECT no_caja,alistado,ubicacion,pedido,pedido.no_req INTO cja, numalistado,ubc,numpedido,req
			FROM alistado
			RIGHT JOIN pedido ON pedido.item=alistado.item
			RIGHT JOIN ITEMS ON ITEMS.ID_Item=pedido.Item
			WHERE ITEMS.ID_Item = new.item
			AND pedido.no_req=new.no_req
			AND alistado.no_caja=new.no_caja;
			
		-- si no se busca normalmente en pedido
		ELSE
			SELECT no_caja,alistado,ubicacion,pedido,pedido.no_req INTO cja, numalistado,ubc,numpedido,req
			FROM alistado
			RIGHT JOIN pedido ON pedido.item=alistado.item
			RIGHT JOIN ITEMS ON ITEMS.ID_Item=pedido.Item
			WHERE ITEMS.ID_Item = new.item
			AND pedido.no_req=new.no_req;
	
		END IF;

		IF req IS NULL THEN
			SET new.estado=2;
			SET cja=0;
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
		
		IF new.estado<>4  THEN
			REPLACE INTO errores(item,no_req,no_caja,no_caja_recibido,recibidos,estado,ubicacion,pedido,alistado) 
			VALUES(new.item,new.no_req,cja,new.no_caja,new.recibidos,new.estado,ubc,numpedido,numalistado);
		END IF;

	END 	
$$

-- DROP TRIGGER IF EXISTS EstadoRecibido;
DELIMITER $$
	CREATE TRIGGER EstadoRecibidoRemision 
	BEFORE UPDATE ON recibido_remisiones
	FOR EACH ROW 
	BEGIN
	
		DECLARE numalistado INT(5);
		DECLARE rem INT(5);
		DECLARE estado INT(1);
								
		SELECT cantidad,pedido_remisiones.no_rem INTO numalistado,rem
		FROM pedido_remisiones
		RIGHT JOIN ITEMS ON ITEMS.ID_Item=pedido_remisiones.Item
		WHERE ITEMS.ID_Item = new.item
		AND pedido_remisiones.no_rem=new.no_rem;

		IF rem IS NULL THEN
			SET new.estado=2;
		ELSEIF new.recibidos<numalistado THEN
			SET new.estado=0;
		ELSEIF new.recibidos>numalistado THEN
			SET new.estado=1;
		ELSE 
			SET new.estado=4;
		END IF;

		
		IF new.estado<>4  THEN
			REPLACE INTO errores_remisiones(item,no_rem,recibidos,estado,alistado) 
			VALUES(new.item,new.no_rem,new.recibidos,new.estado,numalistado);
		END IF;

	END 	
$$

-- autoincrementa id de tabla tareas_det
DELIMITER $$
	CREATE TRIGGER AutoincTareas
	BEFORE INSERT ON tareas_det
	FOR EACH ROW 
	BEGIN

		DECLARE id INT(10) UNSIGNED DEFAULT 1;
		
		SELECT id_tareadet+1 INTO id
		FROM tareas_det
		WHERE id_tarea=new.id_tarea
		ORDER BY id_tareadet DESC
		LIMIT 1;
		
		SET new.id_tareadet=id;	
			
	END 
$$

-- *********************************************************************************************************************************************************************************************
-- *********************************************************************************************************************************************************************************************
-- *********************************************************************************************************************************************************************************************
-- *********************************************************************************************************************************************************************************************
-- *********************************************************************************************************************************************************************************************
-- *********************************************************************************************************************************************************************************************
-- *********************************************************************************************************************************************************************************************
