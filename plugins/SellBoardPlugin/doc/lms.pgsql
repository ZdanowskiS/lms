/* --------------------------------------------------------
  Structure of table "sellboard_hosts"
-------------------------------------------------------- */

DROP SEQUENCE IF EXISTS sellboard_hosts_id_seq;
CREATE SEQUENCE sellboard_hosts_id_seq;
DROP TABLE IF EXISTS sellboard_hosts CASCADE;
CREATE TABLE sellboard_hosts (
	id integer DEFAULT nextval('sellboard_hosts_id_seq'::text) NOT NULL,
	name varchar(64) DEFAULT '' NOT NULL,
    url 	varchar(128) DEFAULT 0 NOT NULL,
	access smallint DEFAULT 1 NOT NULL,
	share smallint DEFAULT 1 NOT NULL,
	PRIMARY KEY (id),
	UNIQUE (name)
);

/* --------------------------------------------------------
  Structure of table "sellboard_sellers"
-------------------------------------------------------- */

DROP SEQUENCE IF EXISTS sellboard_sellers_id_seq;
CREATE SEQUENCE sellboard_sellers_id_seq;
DROP TABLE IF EXISTS sellboard_sellers CASCADE;
CREATE TABLE sellboard_sellers (
	id integer DEFAULT nextval('sellboard_sellers_id_seq'::text) NOT NULL,
	name varchar(64) DEFAULT '' NOT NULL,
	phone varchar(32) DEFAULT '' NOT NULL,
	email varchar(255) DEFAULT '' NOT NULL,
	PRIMARY KEY (id)
);

/* --------------------------------------------------------
  Structure of table "sellboard_items"
-------------------------------------------------------- */

DROP SEQUENCE IF EXISTS sellboard_items_id_seq;
CREATE SEQUENCE sellboard_items_id_seq;
DROP TABLE IF EXISTS sellboard_items CASCADE;
CREATE TABLE sellboard_items (
	id integer DEFAULT nextval('sellboard_hosts_id_seq'::text) NOT NULL,
	name varchar(64) DEFAULT '' NOT NULL,
	description text DEFAULT '' NOT NULL,
	value numeric(9,2) DEFAULT 0 NOT NULL,
	ammount integer DEFAULT 0 NOT NULL,
	cdate integer DEFAULT 0 NOT NULL,
	sellerid integer DEFAULT NULL
		CONSTRAINT items_sellerid_fkey REFERENCES sellboard_sellers (id) ON DELETE SET NULL ON UPDATE CASCADE,
	userid integer DEFAULT NULL
		CONSTRAINT items_userid_fkey REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE,
	PRIMARY KEY (id)
);

/* --------------------------------------------------------
  Structure of table "sellboard_category"
-------------------------------------------------------- */

DROP SEQUENCE IF EXISTS sellboard_category_id_seq;
CREATE SEQUENCE sellboard_category_id_seq;
DROP TABLE IF EXISTS sellboard_category CASCADE;
CREATE TABLE sellboard_category (
	id integer DEFAULT nextval('sellboard_category_id_seq'::text) NOT NULL,
	name varchar(64) DEFAULT '' NOT NULL,
	PRIMARY KEY (id)
);

/* --------------------------------------------------------
  Structure of table "sellboard_itemcategories"
-------------------------------------------------------- */

DROP SEQUENCE IF EXISTS sellboard_itemcategories_id_seq;
CREATE SEQUENCE sellboard_itemcategories_id_seq;
DROP TABLE IF EXISTS sellboard_itemcategories CASCADE;
CREATE TABLE sellboard_itemcategories (
	itemid integer NOT NULL
	    REFERENCES sellboard_items (id) ON DELETE CASCADE ON UPDATE CASCADE,
	categoryid integer NOT NULL
	    REFERENCES sellboard_category (id) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO uiconfig (section, var, value, description, disabled) VALUES
('sellboard', 'blockip','','Block ip separated by comma',1),
('sellboard', 'allowip','','Allowed ip separated by comma',1);

INSERT INTO dbinfo (keytype, keyvalue) VALUES ('dbversion_SellBoardPlugin', '2019010800');
