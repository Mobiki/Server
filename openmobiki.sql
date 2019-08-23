CREATE TABLE alert_logs (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  device_mac varchar(250) COLLATE utf8_general_ci NOT NULL,
  user_id int(11) NOT NULL,
  closed_user_id int(11) NOT NULL,
  epoch int(11) NOT NULL,
  suspend_date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  close_date datetime NOT NULL,
  msg varchar(250) COLLATE utf8_general_ci NOT NULL,
  gateway varchar(250) COLLATE utf8_general_ci NOT NULL,
  status int(11) NOT NULL
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
  name varchar(250) COLLATE utf8_general_ci NOT NULL,
  description varchar(250) COLLATE utf8_general_ci NOT NULL,
  serial_number varchar(250) COLLATE utf8_general_ci NOT NULL
);

CREATE TABLE company (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name varchar(250) COLLATE utf8_general_ci NOT NULL,
  token varchar(250) COLLATE utf8_general_ci NOT NULL
);

CREATE TABLE departments (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name varchar(250) COLLATE utf8_general_ci NOT NULL
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
  name varchar(250) COLLATE utf8_general_ci NOT NULL
);

CREATE TABLE zones (
  id int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name varchar(250) COLLATE utf8_general_ci NOT NULL,
  parent_id int(11) NOT NULL,
  description varchar(250) COLLATE utf8_general_ci NOT NULL
)