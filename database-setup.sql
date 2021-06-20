CREATE DATABASE IF NOT EXISTS beehivecontrol;

CREATE TABLE IF NOT EXISTS users (
    username varchar(50) NOT NULL,
    password char(255) NOT NULL,
    email varchar(80) NOT NULL,
	PRIMARY KEY (username)
);

CREATE TABLE IF NOT EXISTS kit (
    kid char(5) NOT NULL,
	username varchar(50) NULL,
	PRIMARY KEY (kid),
	FOREIGN KEY (username) REFERENCES users(username)
);
