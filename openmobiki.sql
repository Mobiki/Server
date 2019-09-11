CREATE TABLE alert_logs (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  alert_key varchar(250) COLLATE utf8_general_ci NOT NULL,
  alert_rules_id int(11) NOT NULL,
  device_id int(11) NOT NULL,
  gateway_mac int(11) NOT NULL,
  suspended_user_id int(11) NOT NULL,
  closed_user_id int(11) NOT NULL,
  suspend_date datetime NOT NULL,
  close_date datetime NOT NULL,
  status tinyint(1) NOT NULL COMMENT 'open 1/suspend 2/closed 3'
);

CREATE TABLE alert_rules (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name varchar(250) COLLATE utf8_general_ci NOT NULL,
  device_id int(11) NOT NULL,
  device_type int(11) NOT NULL,
  sensor_value varchar(11) COLLATE utf8_general_ci NOT NULL COMMENT 'C/L/T/H > /123/1/*/*',
  equation varchar(2) COLLATE utf8_general_ci NOT NULL COMMENT '0 =, 1 >, 2 <,'
);

CREATE TABLE assets (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name varchar(250) COLLATE utf8_general_ci NOT NULL COMMENT 'Asset name',
  image varchar(250) COLLATE utf8_general_ci NOT NULL,
  type_id int(11) NOT NULL COMMENT 'Asset type id',
  description varchar(250) COLLATE utf8_general_ci NOT NULL,
  department_id int(11) NOT NULL,
  personnel_id int(11) NOT NULL,
  device_id int(11) NOT NULL,
  serial_number varchar(250) COLLATE utf8_general_ci NOT NULL,
  manufacturer varchar(250) COLLATE utf8_general_ci NOT NULL,
  stock_code varchar(250) COLLATE utf8_general_ci NOT NULL,
  status tinyint(1) NOT NULL,
  date_added datetime NOT NULL,
  date_modified datetime NOT NULL
);

CREATE TABLE asset_type (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name varchar(250) COLLATE utf8_general_ci NOT NULL
);

CREATE TABLE company (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name varchar(250) COLLATE utf8_general_ci NOT NULL,
  token varchar(250) COLLATE utf8_general_ci NOT NULL
);

CREATE TABLE departments (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name varchar(250) COLLATE utf8_general_ci NOT NULL,
  parent_id int(11) NOT NULL,
  expiry_date date NOT NULL
);

CREATE TABLE devices (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name varchar(250) COLLATE utf8_general_ci NOT NULL,
  mac varchar(250) COLLATE utf8_general_ci NOT NULL,
  description varchar(250) COLLATE utf8_general_ci NOT NULL,
  type_id int(11) NOT NULL,
  status tinyint(1) NOT NULL,
  update_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE devices_type (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name varchar(250) COLLATE utf8_general_ci NOT NULL,
  description varchar(250) COLLATE utf8_general_ci NOT NULL
);

INSERT INTO devices_type(name, description) VALUES ('Card','Card devices');

INSERT INTO devices_type(name, description) VALUES ('Sensors','Sensors devices');

INSERT INTO devices_type(name, description) VALUES ('Light','Light devices');

INSERT INTO devices_type(name, description) VALUES ('Assets','Assets devices');

CREATE TABLE gateways (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name varchar(250) COLLATE utf8_general_ci NOT NULL,
  zone_id int(11) NOT NULL,
  lat double NOT NULL,
  lng double NOT NULL,
  mac varchar(250) COLLATE utf8_general_ci NOT NULL,
  description varchar(250) COLLATE utf8_general_ci NOT NULL,
  status tinyint(1) NOT NULL COMMENT '1 active 0 passive'
);

CREATE TABLE personnel (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name varchar(250) COLLATE utf8_general_ci NOT NULL,
  image varchar(250) COLLATE utf8_general_ci NOT NULL,
  email varchar(250) COLLATE utf8_general_ci NOT NULL,
  phone varchar(250) COLLATE utf8_general_ci NOT NULL,
  type_id int(11) NOT NULL,
  department_id int(11) NOT NULL,
  device_id int(11) NOT NULL,
  status tinyint(1) NOT NULL COMMENT '1 active 0 passive'
);

CREATE TABLE personnel_type (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name varchar(250) COLLATE utf8_general_ci NOT NULL
);

CREATE TABLE users (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  role_id int(11) NOT NULL,
  name varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  email varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  password varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  phone varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  description varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  token varchar(255) COLLATE utf8_unicode_ci NOT NULL
);

CREATE TABLE users_role (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name varchar(250) COLLATE utf8_general_ci NOT NULL COMMENT 'Admin/Departmant Manager/Viewer'
);

INSERT INTO users_role (id,name) VALUES (1,'Admin');

INSERT INTO users_role (id,name) VALUES (2,'Departmant Manager');

INSERT INTO users_role (id,name) VALUES (3,'Viewer');

CREATE TABLE zones (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name varchar(250) COLLATE utf8_general_ci NOT NULL,
  parent_id int(11) NOT NULL,
  description varchar(250) COLLATE utf8_general_ci NOT NULL
)