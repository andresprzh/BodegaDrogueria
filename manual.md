# Aplicacion de bodega

## Login

<!-- ![alt text](estaticos/imagenes/login.png "image title") -->
<img src="estaticos/imagenes/login.png" alt="drawing" width="400px"/>

Al ingresar a la pagina princial se pedira al usuario iniciar sesion, en dicha pagina el usuario debera ingresar sus datos, de usuario y su contraseña.

<img src="estaticos/imagenes/login2.png" alt="drawing" width="400px"/>

los tipos de usuario o perfiles son los siguientes:

* Administrador: puede ver e interactura en todo el aplicativo web y es el unico perfil que permite modificar usuarios
* Jefe Bodega: puede subir la requisición  y generar los documentos de salida de las cajas
* Alistador :puede alistar los items usando el aplicativo alistar
* Encargado punto de venta: puede registrar los items recibidos usando el aplicativo PV

******

## Pagina Inicio
Al iniciar sesion se mostrara una pagina de inicio, en el menu de navegacion en la esquina suoperior está el menu desplegable  donde se podran realizar diferentes acciones segun el usuario.

<img src="estaticos/imagenes/inicio.png" alt="drawing" width="800px"/>

Links de la pagina :
1. Subir archivo de requisición
2. Alistar
3. Caja
4. Pventa
5. Usuarios
6. Salir(Cerrar sesion)

****

## 1. Subir archivo de requisición

<img src="estaticos/imagenes/req.png" alt="drawing" width="800px"/>

En esta pagina el usuario podra subir el archivo plano  de la requisición el cual alimentara la base de datos

### 1.1. Seleccionando archivo

<img src="estaticos/imagenes/req2.png" alt="drawing" width="800px"/>

### 1.2. Mensajes que puede mostrar la aplicacion.

1.  Si se sube un archivo que no sea de texto

<img src="estaticos/imagenes/req3.png" alt="drawing" width="800px"/>

2. Si no encuentra la requisición en el archivo plano

<img src="estaticos/imagenes/req4.png" alt="drawing" width="800px"/>

2. Si la requisición ya se subió a la base de datos

<img src="estaticos/imagenes/req5.png" alt="drawing" width="800px"/>

3. Si si el archivo se subió a la base de datos exitosamente

<img src="estaticos/imagenes/req6.png" alt="drawing" width="800px"/>

*****

## 2. Alistar

En esta pagina el usuario podra seleccionar una requisición y alistar los items de dicha requisición en cajas. 

<img src="estaticos/imagenes/alistar.png" alt="drawing" width="800px"/>

### 2.1. Seleccionar requisición

En la entrada de numero de requisición aparecen todas las requisicones  pendiente donde el usuario podra seleccionar una de estas para hacer el alistamiento

<img src="estaticos/imagenes/alistar2.png" alt="drawing" width="800px"/>

#### 2.1.1 Cajas sin cerrar(enviar)

si hay cajas abiertas en otra requisicion que no han sido enviadas a la base de datos la pagina no dejara alistar items hasya que dicha caja sea cerrada.


<img src="estaticos/imagenes/alistarreqe.png" alt="drawing" width="800px"/>


### 2.2. Tabla Items

Tabla donde se muestran los items de la requisicion seleccionada, dicha tabla se actualiza cada vez que se cambia de requisicion o si se agrega o quita un item de caja.

La tabla se organiza por pagina mostrando 5 items en cada una, ademas de tener una entrada  de busqueda que permite encontrar datos en dicha tabla

<img src="estaticos/imagenes/alistar3.png" alt="drawing" width="800px"/>

### 2.3. Codigo de barras

Al seleccionar la requisicion se activa la entrada de codigo de barras donde se podra ingresar el item. Dicha entrada solo permite  el ingreso de numeros.

<img src="estaticos/imagenes/alistarcod.png" alt="drawing" width="800px"/>


Para agregar un item a la caja solo basta con introducir el codigo de barras en la entrada correspondiente y presionar enter o dar lick en el boton de agregar item

Si el item digitado es correcto se mostrara un mensaje a la derecha de la pantalla con el nombre del item agregado
<img src="estaticos/imagenes/alistarcod2.png" alt="drawing" width="800px"/>

El item agregado aparecera en la tabla de Caja donde se podra editar la cantidad a alistar o donde se podra eliminar con el boton de eliminar item

<img src="estaticos/imagenes/alistarcod3.png" alt="drawing" width="800px"/>

#### 2.3.1. Errores al Cargar el item 

Es posible que el item no se pueda cargar en la caja, de ser asi la pagina mostrara el mensaje en pantalla.

1. Si no se encuentra el item en la requisicion

<img src="estaticos/imagenes/alistarcode1.png" alt="drawing" width="800px"/>

2. Si el item ya esta siendo alistado por otro usuario

<img src="estaticos/imagenes/alistarcode2.png" alt="drawing" width="800px"/>

3. Si el item ya fue alistado en una caja

<img src="estaticos/imagenes/alistarcode2.png" alt="drawing" width="800px"/>

### 2.4. Tabla caja

En esta tabla se mostraran los items a alistar y se actualiza cada vez que es modifica, agrega o elimina un item de la tabla.

Si hay una caja sin cerrar de la requisicion selecionada la tabla  mostrara los items de dicha caja.

<img src="estaticos/imagenes/alistaritem.png" alt="drawing" width="800px"/>

#### 2.4.1. Tipo de caja

En el menu de seleccion inferior izquierdo se podra seleccionar el tipo de caja en el que se introduciran los items.

<img src="estaticos/imagenes/tipocaja.png" alt="drawing" width="800px"/>

#### 4.4.2. Cerrar Caja

Las cajs se cierran y haciendo click en el boton de cerrar caja, que mostrara un ventana de confirmacion.

<img src="estaticos/imagenes/cerrarcaja1.png" alt="drawing" width="800px"/>

Si al hacer click en cerrar la caja con sus items son almacenado exitosamente en la base de datos se mostrara un mensaje confrimando la creacion de la caja. y se reiniciara la pagina.

<img src="estaticos/imagenes/cerrarcaja2.png" alt="drawing" width="800px"/>

*****

## 3. Cajas

En esta pagina el usuario podra ver las cajas creadas por los alistadores y generar un archivo plano para enviar la caja.

<img src="estaticos/imagenes/cajas.png" alt="drawing" width="800px"/>

### 3.1. Seleccionar requisición

En la entrada de numero de requisición aparecen todas las requisicones  pendiente donde el usuario podra seleccionar una de estas para poder ver lacs cajas correspondientes.

<img src="estaticos/imagenes/cajasreq.png" alt="drawing" width="800px"/>

### 3.2. tabla cajas

al seleccionar una requisicion en una tabla se mostraran todas las cajas correspondinetes a ta requisicion.

Para cada caja se muestra su numero, el nombre de alistador, la fecha en que se abrio y cerro la caja y el tipo de caja:

* **CRT**: Caja de carton.
* **CPL**: Caja plástica.
* **CAP**: Canasta plástica.
* **GLN**: Galon.
* **GLA**: Galoneta. 

<img src="estaticos/imagenes/cajacaja.png" alt="drawing" width="800px"/>

### 3.3 Revisar caja

Presionando el boton de revisar a la izquierda de cada caja  se puede ver su contenido.


<img src="estaticos/imagenes/cajaitems.png" alt="drawing" width="800px"/>


Dentro de esta ventana se puede agregar un texto corta para cada item no mayor a 20 caracteres

<img src="estaticos/imagenes/cajaitemt.png" alt="drawing" width="800px"/>

#### 3.3.1. Generar Documento

Para generar el documento es necesario hacer click sobre el boton Generar documento en la esquina inferior de la pantalla  de cada caja.

<img src="estaticos/imagenes/cajadoc.png" alt="drawing" width="800px"/>

Archivo plano generado:

<img src="estaticos/imagenes/cajadocg.png" alt="drawing" width="800px"/>

## 4. PV (Punto de Venta)

En esta pagina el usuario podra revisar los items que llegaron de una requisicion al punto de venta y generar un documento de los items recividos en comaracion a los enviados.

<img src="estaticos/imagenes/pv.png" alt="drawing" width="800px"/>

### 4.1. Seleccionar requisición

En la entrada de numero de requisición aparecen todas las requisicones.

<img src="estaticos/imagenes/pvreq.png" alt="drawing" width="800px"/>

### 4.2. Seleccionar caja

Al seleccionar una requisicion aparecera una opcion donde se podra selecionar alguna de las cajas que ya fueron enviadas al punto.

<img src="estaticos/imagenes/pvcaja.png" alt="drawing" width="800px"/>

### 4.3. Agregar Item.

En la entrada de codbarras se digita el codigo de barras del item y al presionar enter o dar click en agregar item dicho item es añadido a la tabla. si se pasan varias veces el mismo item este se acumulara.

<img src="estaticos/imagenes/pvitems.png" alt="drawing" width="800px"/>

### 4.4. Tabla items

En la tabla Items aparecen todos los items pasado por el codigo de barras, dichos items se pueden eliminar de la tabla con el boton de eliminar o se puede modificar su cantidad.

<img src="estaticos/imagenes/pvtabla.png" alt="drawing" width="800px"/>

### 4.5 Registrar item

para Registrar los items hay que presionar el boton de registrar en la esquina inferior izquierda de la pagina,  esto abrira una pagina de confrmacion

<img src="estaticos/imagenes/pvreg.png" alt="drawing" width="800px"/>

Si se da click en confirmar se generara un documento de texto de los  items rescivido.

<img src="estaticos/imagenes/pvdoc.png" alt="drawing" width="800px"/>

Archivo plano generado:

<img src="estaticos/imagenes/pvdocg.png" alt="drawing" width="800px"/>
