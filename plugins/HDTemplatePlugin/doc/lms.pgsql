
/* --------------------------------------------------------
  Structure of table "hdtemplates"
-------------------------------------------------------- */
DROP SEQUENCE IF EXISTS hdtemplates_id_seq;
CREATE SEQUENCE hdtemplates_id_seq;
DROP TABLE IF EXISTS hdtemplates CASCADE;
CREATE TABLE hdtemplates (
	id integer DEFAULT nextval('hdtemplates_id_seq'::text) NOT NULL,
	name varchar(64) DEFAULT '' NOT NULL,
	type smallint		DEFAULT 0 NOT NULL,
	template text NOT NULL DEFAULT 0,
	PRIMARY KEY (id),
	UNIQUE (name)
);

INSERT INTO dbinfo (keytype, keyvalue) VALUES ('dbversion_HDTemplatePlugin', '2019032100');
