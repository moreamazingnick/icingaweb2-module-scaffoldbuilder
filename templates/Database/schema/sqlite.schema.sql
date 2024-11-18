CREATE TABLE __tableprefix__schema
(
    id      INTEGER PRIMARY KEY,
    timestamp   REAL,
    version TEXT,
    success TEXT,
    reason  TEXT

);

INSERT INTO __tableprefix__schema (version, timestamp, success)
VALUES ('0.1.0', UNIX_TIMESTAMP() * 1000, 'y');
