CREATE TABLE __tablename__ (
  id serial PRIMARY KEY,
  name varchar(255) NOT NULL,
  enabled boolenum NOT NULL DEFAULT 'n',
  ctime biguint NOT NULL,
);
