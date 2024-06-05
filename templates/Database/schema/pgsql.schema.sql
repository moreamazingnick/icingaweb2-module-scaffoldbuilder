CREATE DOMAIN uint2 AS int4
    CHECK(VALUE >= 0 AND VALUE < 65536);
CREATE DOMAIN biguint AS bigint CONSTRAINT positive CHECK ( VALUE IS NULL OR 0 <= VALUE );
CREATE TYPE boolenum AS ENUM ('n', 'y');

-- Used when sorting certificates by expiration date.
CREATE OR REPLACE FUNCTION UNIX_TIMESTAMP(datetime timestamptz DEFAULT NOW())
    RETURNS biguint
    LANGUAGE plpgsql
    PARALLEL SAFE
    AS $$
BEGIN
    RETURN EXTRACT(EPOCH FROM datetime);
END;
$$;

-- IPL ORM renders SQL queries with LIKE operators for all suggestions in the search bar,
-- which fails for numeric and enum types on PostgreSQL. Just like in Icinga DB Web.
CREATE OR REPLACE FUNCTION anynonarrayliketext(anynonarray, text)
  RETURNS bool
  LANGUAGE plpgsql
  IMMUTABLE
  PARALLEL SAFE
  AS $$
BEGIN
    RETURN $1::TEXT LIKE $2;
END;
$$;
CREATE OPERATOR ~~ (LEFTARG=anynonarray, RIGHTARG=text, PROCEDURE=anynonarrayliketext);


CREATE TABLE __tableprefix__schema (
     id serial,
     version varchar(64) NOT NULL,
     timestamp bigint NOT NULL,
     success boolenum DEFAULT NULL,
     reason text DEFAULT NULL,

     CONSTRAINT pk___tableprefix__schema PRIMARY KEY (id),
     CONSTRAINT idx___tableprefix__schema_version UNIQUE (version)
);

INSERT INTO __tableprefix__schema (version, timestamp, success)
VALUES ('0.1.0', UNIX_TIMESTAMP() * 1000, 'y');
