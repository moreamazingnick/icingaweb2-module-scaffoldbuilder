CREATE TABLE __tablename__ (
    id int(10) unsigned NOT NULL AUTO_INCREMENT,
    name  varchar(255) NOT NULL,
    enabled enum ('n', 'y') DEFAULT NULL,
    ctime bigint unsigned DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
