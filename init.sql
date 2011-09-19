CREATE TABLE visitor(
    id INT  PRIMARY KEY AUTO_INCREMENT,
    visitor_barcode VARCHAR(14),
    firstname VARCHAR(128) NOT NULL,
    lastname VARCHAR(128) NOT NULL,
    patronymic VARCHAR(128) NOT NULL,
    post VARCHAR(128) NOT NULL,
    company VARCHAR(128) NOT NULL,
    country VARCHAR(255) NOT NULL,
    town VARCHAR(255) NOT NULL,
    address VARCHAR (255) NOT NULL,
    postcode INT NOT NULL,
    email VARCHAR(128) NOT NULL,
    website VARCHAR(256),
    preregister BOOLEAN,
    phone VARCHAR (128),
    fax   VARCHAR (128),
    ticket_printed BOOLEAN
) DEFAULT CHARSET=utf8 ;

CREATE UNIQUE INDEX visitor_index ON visitor (visitor_barcode);

CREATE TABLE exibition_membership (
    visitor_barcode VARCHAR(14) NOT NULL,
    exibition_id INT NOT NULL,

   PRIMARY KEY (visitor_barcode, exibition_id)
);

CREATE TABLE exibition (
    exibition_id INT PRIMARY KEY AUTO_INCREMENT,
    title_ru VARCHAR(512) NOT NULL,
    title_en VARCHAR(512) NOT NULL,
    active BOOL NOT NULL
);

CREATE TABLE exibition_barcode (
    visitor_id INT,
    exibition_barcode VARCHAR(14) PRIMARY KEY
);