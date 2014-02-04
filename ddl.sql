CREATE DATABASE IF NOT EXISTS link_analyzer;

USE link_analyzer;

DROP TABLE IF EXISTS parent_link;

DROP TABLE IF EXISTS child_link;



CREATE TABLE parent_link
(
  id INT NOT NULL AUTO_INCREMENT,
  link VARCHAR(255) NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE `child_link` (
  `link` varchar(255) NOT NULL,
  `http_status` varchar(10) DEFAULT NULL,
  UNIQUE KEY `child_link_idx` (`link`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

