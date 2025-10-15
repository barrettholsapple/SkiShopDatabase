CREATE DATABASE skishop;
USE skishop;

CREATE TABLE customers (
customerid INT NOT NULL AUTO_INCREMENT,
fname VARCHAR(15) NOT NULL,
lname VARCHAR(15) NOT NULL,
streetadd VARCHAR(30) NOT NULL,
city VARCHAR(15) NOT NULL,
stateadd VARCHAR(18) NOT NULL,
zip INT NOT NULL,
driversli VARCHAR(15),
phonenum CHAR(10) NOT NULL,
email VARCHAR(40) NOT NULL,
birthday DATE NOT NULL,
weightlbs INT NOT NULL,
heightft INT NOT NULL,
heightin INT NOT NULL,
skilllevel INT NOT NULL,
PRIMARY KEY(customerid)
);

CREATE TABLE skis (
serialnumber VARCHAR(15) NOT NULL,
make VARCHAR(30) NOT NULL,
model VARCHAR(30) NOT NULL,
sizeof VARCHAR(6) NOT NULL,
notes VARCHAR(100),
PRIMARY KEY (serialnumber)
);

CREATE TABLE boards (
boardserialnumber VARCHAR(15) NOT NULL,
boardmake VARCHAR(30) NOT NULL,
boardmodel VARCHAR(30) NOT NULL,
boardsize VARCHAR(30) NOT NULL,
boardnotes VARCHAR(100),
PRIMARY KEY (boardserialnumber)
);

CREATE TABLE rentals (
serialnumber VARCHAR(15) NOT NULL,
daterented DATE NOT NULL,
returndate DATE NOT NULL,
poles CHAR(1) NOT NULL,
polemake VARCHAR(60),
polesize VARCHAR(6),
boots CHAR(1) NOT NULL,
bootmake VARCHAR(60),
bootsize VARCHAR(6),
seasonal CHAR(1) NOT NULL,
bindingdinlt VARCHAR(6),
bindingdinlh VARCHAR(6),
bindingdinrt VARCHAR(6),
bindingdinrh VARCHAR(6),
salesperson VARCHAR(10) NOT NULL,
customerid INT NOT NULL,
notes VARCHAR(100),
FOREIGN KEY(customerid) references customers(customerid),
FOREIGN KEY(serialnumber) references skis(serialnumber)
);

CREATE TABLE boardrentals (
boardserialnumber VARCHAR(15) NOT NULL,
daterented DATE NOT NULL,
returndate DATE NOT NULL,
boots CHAR(1) NOT NULL,
bootmake VARCHAR(60),
bootsize VARCHAR(6),
stance CHAR(1) NOT NULL,
seasonal CHAR(1) NOT NULL,
salesperson VARCHAR(10) NOT NULL,
customerid INT NOT NULL,
notes VARCHAR(100),
FOREIGN KEY(customerid) references customers(customerid),
FOREIGN KEY(boardserialnumber) references boards(boardserialnumber)
);

CREATE TABLE pastrentals (
serialnumber VARCHAR(15) NOT NULL,
daterented DATE NOT NULL,
returndate DATE NOT NULL,
datereturned DATE NOT NULL,
poles CHAR(1) NOT NULL,
polemake VARCHAR(60),
polesize VARCHAR(6),
boots CHAR(1) NOT NULL,
bootmake VARCHAR(60),
bootsize VARCHAR(6),
seasonal CHAR(1) NOT NULL,
bindingdinlt VARCHAR(6) NOT NULL,
bindingdinlh VARCHAR(6) NOT NULL,
bindingdinrt VARCHAR(6) NOT NULL,
bindingdinrh VARCHAR(6) NOT NULL,
salesperson VARCHAR(10) NOT NULL,
returnsalesperson VARCHAR(10) NOT NULL,
customerid INT NOT NULL,
notes VARCHAR(100),
FOREIGN KEY(customerid) references customers(customerid),
FOREIGN KEY(serialnumber) references skis(serialnumber)
);

CREATE TABLE pastboardrentals (
boardserialnumber VARCHAR(15) NOT NULL,
daterented DATE NOT NULL,
returndate DATE NOT NULL,
datereturned DATE NOT NULL,
boots CHAR(1) NOT NULL,
bootmake VARCHAR(60),
bootsize VARCHAR(6),
stance CHAR(1) NOT NULL,
seasonal CHAR(1) NOT NULL,
salesperson VARCHAR(10) NOT NULL,
returnsalesperson VARCHAR(10) NOT NULL,
customerid INT NOT NULL,
notes VARCHAR(100),
FOREIGN KEY(customerid) references customers(customerid),
FOREIGN KEY(boardserialnumber) references boards(boardserialnumber)
);