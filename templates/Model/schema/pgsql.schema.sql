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

CREATE TABLE __tablename__ (
  id serial PRIMARY KEY,
  name varchar(255) NOT NULL,
  enabled boolenum NOT NULL DEFAULT 'n',
  ctime biguint NOT NULL,
);
