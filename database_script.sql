
DROP DATABASE IF EXISTS vulnerable_php_app;
CREATE DATABASE IF NOT EXISTS vulnerable_php_app;
USE vulnerable_php_app;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(30),
    password VARCHAR(30)
)   ENGINE = InnoDB DEFAULT CHARACTER SET=utf8;


INSERT INTO usuarios(nombre, password)
    VALUES
    ('root','r00tme'),
    ('administrador','123456'),
    ('usuario1','456654'),
    ('usuario2','utnfrc')
;
