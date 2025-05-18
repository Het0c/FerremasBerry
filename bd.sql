CREATE DATABASE IF NOT EXISTS FERREMAS;
USE FERREMAS;

CREATE TABLE IF NOT EXISTS ingreso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL,
    telefono VARCHAR(15) NOT NULL,
    mensaje TEXT NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE `ferremas`.`tipousuario` (
  `idTipoUsuario` INT NOT NULL AUTO_INCREMENT,
  `descripcion` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`idTipoUsuario`));


CREATE TABLE `ferremas`.`usuario` (
  `idUsuario` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `apellido` VARCHAR(50) NOT NULL,
  `email` VARCHAR(50) NOT NULL,
  `telefono` INT NOT NULL,
  `contrasena` VARCHAR(255) NOT NULL,
  `tipoUsuario` INT NOT NULL,
  PRIMARY KEY (`idUsuario`));

ALTER TABLE `ferremas`.`usuario` 
ADD INDEX `fk_tipoUsuario_idx` (`tipoUsuario` ASC);

ALTER TABLE `ferremas`.`usuario` 
ADD CONSTRAINT `fk_tipoUsuario`
  FOREIGN KEY (`tipoUsuario`)
  REFERENCES `ferremas`.`tipousuario` (`idTipoUsuario`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


CREATE TABLE `ferremas`.`marca` (
  `idMarca` INT NOT NULL AUTO_INCREMENT,
  `descripcion` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`idMarca`));

CREATE TABLE `ferremas`.`categoria` (
  `idCategoria` INT NOT NULL,
  `descripcion` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`idCategoria`));



CREATE TABLE `ferremas`.`producto` (
  `idProducto` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(60) NOT NULL,
  `descripcion` VARCHAR(200) NOT NULL,
  `precio` INT NOT NULL,
  `marca` INT NOT NULL,
  `categoria` INT NOT NULL,
  PRIMARY KEY (`idProducto`),
  CONSTRAINT `fk_marca`
    FOREIGN KEY (`marca`)
    REFERENCES `ferremas`.`marca` (`idMarca`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_categoria`
    FOREIGN KEY (`categoria`)
    REFERENCES `ferremas`.`categoria` (`idCategoria`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  INDEX `fk_marca_idx` (`marca` ASC),
  INDEX `fk_categoria_idx` (`categoria` ASC)
);

ALTER TABLE `ferremas`.`producto` 
ADD COLUMN `imagen` MEDIUMBLOB NULL AFTER `categoria`;



INSERT INTO `ferremas`.`categoria`(`idCategoria`, `descripcion`) VALUES ('0','Martillos');
INSERT INTO `ferremas`.`categoria`(`idCategoria`, `descripcion`) VALUES ('1','Destornilladores');
INSERT INTO `ferremas`.`categoria`(`idCategoria`, `descripcion`) VALUES ('2','Llaves');
INSERT INTO `ferremas`.`categoria`(`idCategoria`, `descripcion`) VALUES ('3','Herramientas Eléctricas');
INSERT INTO `ferremas`.`categoria`(`idCategoria`, `descripcion`) VALUES ('4','Taladros');
INSERT INTO `ferremas`.`categoria`(`idCategoria`, `descripcion`) VALUES ('5','Sierras');
INSERT INTO `ferremas`.`categoria`(`idCategoria`, `descripcion`) VALUES ('6','Lijadoras');
INSERT INTO `ferremas`.`categoria`(`idCategoria`, `descripcion`) VALUES ('7','Materiales de Construcción');
INSERT INTO `ferremas`.`categoria`(`idCategoria`, `descripcion`) VALUES ('8','Cemento');
INSERT INTO `ferremas`.`categoria`(`idCategoria`, `descripcion`) VALUES ('9','Arena');
INSERT INTO `ferremas`.`categoria`(`idCategoria`, `descripcion`) VALUES ('10''Ladrillos');
INSERT INTO `ferremas`.`categoria`(`idCategoria`, `descripcion`) VALUES ('11''Acabados');
INSERT INTO `ferremas`.`categoria`(`idCategoria`, `descripcion`) VALUES ('12''Pinturas');
INSERT INTO `ferremas`.`categoria`(`idCategoria`, `descripcion`) VALUES ('13''Barnices');
INSERT INTO `ferremas`.`categoria`(`idCategoria`, `descripcion`) VALUES ('14''Cerámicos');
INSERT INTO `ferremas`.`categoria`(`idCategoria`, `descripcion`) VALUES ('15''Cascos');
INSERT INTO `ferremas`.`categoria`(`idCategoria`, `descripcion`) VALUES ('16''Guantes');
INSERT INTO `ferremas`.`categoria`(`idCategoria`, `descripcion`) VALUES ('17''Lentes de Seguridad');
INSERT INTO `ferremas`.`categoria`(`idCategoria`, `descripcion`) VALUES ('18''Accesorios Varios');
INSERT INTO `ferremas`.`categoria` (`idCategoria`, `descripcion`) VALUES ('1', 'asd');

INSERT INTO `ferremas`.`marca` (`idMarca`, `descripcion`) VALUES 
(1, 'Bosch'),
(2, 'Makita'),
(3, 'DeWalt'),
(4, 'Black+Decker'),
(5, 'Stanley'),
(6, 'Tramontina'),
(7, '3M'),
(8, 'Stihl'),
(9, 'Hilti'),
(10, 'Irwin'),
(11, 'Sodimac'),
(12, 'MTS'),
(13, 'Holcim'),
(14, 'Cementos Bío Bío'),
(15, 'Melón'),
(16, 'Pinturas Ceresita'),
(17, 'Pintuco'),
(18, 'Klaukol'),
(19, 'Sika'),
(20, 'Bosca');


INSERT INTO `ferremas`.`tipousuario` (`idTipoUsuario`, `descripcion`) VALUES ('1', 'Cliente');
INSERT INTO `ferremas`.`tipousuario` (`idTipoUsuario`, `descripcion`) VALUES ('2', 'Admin');
INSERT INTO `ferremas`.`tipousuario` (`idTipoUsuario`, `descripcion`) VALUES ('3', 'Contador');
INSERT INTO `ferremas`.`tipousuario` (`idTipoUsuario`, `descripcion`) VALUES ('4', 'Vendedor');
INSERT INTO `ferremas`.`tipousuario` (`idTipoUsuario`, `descripcion`) VALUES ('5', 'Bodeguero');

INSERT INTO `ferremas`.`usuario` (`idUsuario`, `nombre`, `apellido`, `email`, `telefono`, `contrasena`, `tipoUsuario`) VALUES ('1', 'admin', 'test', 'admin@gmail.com', '983848729', '1234', '2');
