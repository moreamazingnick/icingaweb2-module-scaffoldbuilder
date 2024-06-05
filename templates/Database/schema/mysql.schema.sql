CREATE TABLE __tableprefix__schema (
      id        int unsigned NOT NULL AUTO_INCREMENT,
      version   varchar(64) NOT NULL,
      timestamp bigint unsigned NOT NULL,
      success   enum('n', 'y') DEFAULT NULL,
      reason    text DEFAULT NULL,

      PRIMARY KEY (id),
      CONSTRAINT idx___tableprefix__schema_version UNIQUE (version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin ROW_FORMAT=DYNAMIC;

INSERT INTO __tableprefix__schema (version, timestamp, success)
VALUES ('0.1.0', UNIX_TIMESTAMP() * 1000, 'y');
