-- taxxo_contractor --
DROP SEQUENCE IF EXISTS taxxo_contractor_id_seq;
CREATE SEQUENCE taxxo_contractor_id_seq;
DROP TABLE IF EXISTS taxxo_contractor CASCADE;
CREATE TABLE taxxo_contractor(
	id integer DEFAULT nextval('taxxo_contractor_id_seq'::text) NOT NULL,
	name varchar(255) NOT NULL,
	PRIMARY KEY (id)
);

-- taxxo_documents --
DROP SEQUENCE IF EXISTS taxxo_documents_id_seq;
CREATE SEQUENCE taxxo_documents_id_seq;
DROP TABLE IF EXISTS taxxo_documents CASCADE;
CREATE TABLE taxxo_documents (
	id integer DEFAULT nextval('taxxo_documents_id_seq'::text) NOT NULL,
	status smallint NOT NULL DEFAULT 0,
	errorcode integer NOT NULL DEFAULT 0,
	notdigitalize smallint NOT NULL DEFAULT 0,
	type smallint NOT NULL DEFAULT 0,
	taxxoid integer NOT NULL DEFAULT 0,
	docid integer NOT NULL DEFAULT 0,
	filename varchar(255) NOT NULL,
	md5sum varchar(32) NOT NULL DEFAULT '',
	ctime integer NOT NULL DEFAULT 0,
	utime integer NOT NULL DEFAULT 0,
	PRIMARY KEY (id)
);
CREATE INDEX taxxo_documents_docid ON taxxo_documents (docid);

-- taxxo_content --
CREATE TABLE taxxo_content (
	tdocid integer NOT NULL DEFAULT 0
		REFERENCES taxxo_documents (id) ON DELETE CASCADE ON UPDATE CASCADE,
	contractorid integer NOT NULL DEFAULT 0
		REFERENCES taxxo_contractor (id) ON DELETE CASCADE ON UPDATE CASCADE,
	totalnet numeric(9,2) NOT NULL DEFAULT '0',
	totaltax numeric(9,2) NOT NULL DEFAULT '0',
	totalgross numeric(9,2) NOT NULL DEFAULT '0'
);

INSERT INTO dbinfo (keytype, keyvalue) VALUES ('dbversion_TaxxoPlugin', '2019040400');
