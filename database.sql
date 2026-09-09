CREATE DATABASE IF NOT EXISTS churrasco CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE churrasco;

DROP TABLE IF EXISTS convidados;
CREATE TABLE convidados (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  tipo ENUM('adulto','crianca') NOT NULL
);

DROP TABLE IF EXISTS produtos;
CREATE TABLE produtos (
  id INT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  grupo VARCHAR(30) NOT NULL,
  unidade VARCHAR(30) NOT NULL,
  fator_adulto DECIMAL(8,3) NOT NULL,
  fator_crianca DECIMAL(8,3) NOT NULL,
  icone VARCHAR(10) NOT NULL
);

INSERT INTO convidados (nome,tipo) VALUES
('Paula','adulto'),('Lucas (filho de Paula)','crianca'),('Marina (filha de Paula)','crianca'),
('Carlos','adulto'),('Ana','adulto'),('Bruno','adulto'),('Camila','adulto'),('Daniel','adulto'),
('Eduarda','adulto'),('Felipe','adulto'),('Gabriela','adulto'),('Henrique','adulto'),('Isabela','adulto'),
('João','adulto'),('Karina','adulto'),('Leonardo','adulto'),('Mariana','adulto'),('Nicolas','adulto'),
('Olivia','adulto'),('Pedro','adulto'),('Rafaela','adulto'),('Samuel','adulto'),('Tatiane','adulto'),
('Vinícius','adulto'),('Yasmin','adulto'),('André','adulto'),('Beatriz','adulto'),('Caio','adulto'),
('Davi','crianca'),('Laura','crianca');

INSERT INTO produtos VALUES
(1,'Picanha','Sólidos','kg',0.120,0.070,'🥩'),
(2,'Linguiça','Sólidos','kg',0.100,0.060,'🌭'),
(3,'Frango','Sólidos','kg',0.100,0.060,'🍗'),
(4,'Pão de alho','Sólidos','unid.',1.000,0.500,'🥖'),
(5,'Arroz','Sólidos','kg',0.080,0.050,'🍚'),
(6,'Farofa','Sólidos','kg',0.050,0.030,'🥣'),
(7,'Vinagrete','Sólidos','kg',0.060,0.040,'🥗'),
(8,'Queijo coalho','Sólidos','kg',0.060,0.040,'🧀'),
(9,'Refrigerante','Líquidos','L',0.350,0.450,'🥤'),
(10,'Suco','Líquidos','L',0.200,0.350,'🧃'),
(11,'Água','Líquidos','L',0.400,0.450,'💧'),
(12,'Cerveja','Líquidos','latas 350 ml',4.000,0.000,'🍺'),
(13,'Gelo','Apoio','kg',0.500,0.500,'🧊'),
(14,'Carvão','Apoio','kg',0.150,0.150,'🔥'),
(15,'Descartáveis','Apoio','kits',1.000,1.000,'🍽️');
