DROP SEQUENCE IF EXISTS proxmox_nodes_id_seq;
CREATE SEQUENCE proxmox_nodes_id_seq;
DROP TABLE IF EXISTS proxmox_nodes CASCADE;
CREATE TABLE proxmox_nodes (
	id integer DEFAULT nextval('proxmox_nodes_id_seq'::text) NOT NULL,
	name varchar(32) DEFAULT '' NOT NULL,
    ipaddr 	bigint          DEFAULT 0 NOT NULL,
	realm	varchar(32)		DEFAULT '' NOT NULL,
	login	varchar(32)		DEFAULT '' NOT NULL,
	password	varchar(32)		DEFAULT '' NOT NULL,
	PRIMARY KEY (id),
	UNIQUE (name)
);

DROP SEQUENCE IF EXISTS proxmox_node_bridges_id_seq;
CREATE SEQUENCE proxmox_node_bridges_id_seq;
DROP TABLE IF EXISTS proxmox_node_bridges CASCADE;
CREATE TABLE proxmox_node_bridges (
	id integer DEFAULT nextval('proxmox_node_bridges_id_seq'::text) NOT NULL,
	nodeid integer NOT NULL
       CONSTRAINT proxmox_bridges_nodeid_fkey REFERENCES proxmox_nodes (id) ON DELETE CASCADE ON UPDATE CASCADE,
    name varchar(32) DEFAULT '' NOT NULL,
	PRIMARY KEY (id),
	UNIQUE (name)
);

DROP SEQUENCE IF EXISTS proxmox_node_storages_id_seq;
CREATE SEQUENCE proxmox_node_storages_id_seq;
DROP TABLE IF EXISTS proxmox_node_storages CASCADE;
CREATE TABLE proxmox_node_storages (
	id integer DEFAULT nextval('proxmox_node_storages_id_seq'::text) NOT NULL,
	nodeid integer NOT NULL
       CONSTRAINT proxmox_node_storages_nodeid_fkey REFERENCES proxmox_nodes (id) ON DELETE CASCADE ON UPDATE CASCADE,
    name varchar(32) DEFAULT '' NOT NULL,
	PRIMARY KEY (id),
	UNIQUE (name)
);

DROP SEQUENCE IF EXISTS proxmox_node_vztemplates_id_seq;
CREATE SEQUENCE proxmox_node_vztemplates_id_seq;
DROP TABLE IF EXISTS proxmox_node_vztemplates CASCADE;
CREATE TABLE proxmox_node_vztemplates (
	id integer DEFAULT nextval('proxmox_node_vztemplates_id_seq'::text) NOT NULL,
	nodeid integer  NOT NULL
       CONSTRAINT proxmox_node_vztemplates_nodeid_fkey REFERENCES proxmox_nodes (id) ON DELETE CASCADE ON UPDATE CASCADE,
    name text DEFAULT '' NOT NULL,
	PRIMARY KEY (id),
	UNIQUE (name)
);

DROP SEQUENCE IF EXISTS proxmox_offerts_id_seq;
CREATE SEQUENCE proxmox_offerts_id_seq;
DROP TABLE IF EXISTS proxmox_offerts CASCADE;
CREATE TABLE proxmox_offerts (
	id integer DEFAULT nextval('proxmox_offerts_id_seq'::text) NOT NULL,
	name varchar(64) DEFAULT '' NOT NULL,
    type smallint DEFAULT 0 NOT NULL,
	nodeid integer DEFAULT 0 NOT NULL,
	clone integer DEFAULT NULL,
	cores smallint DEFAULT 0 NOT NULL,
	memory integer DEFAULT 0 NOT NULL,
	storageid integer DEFAULT NULL
       CONSTRAINT  proxmox_offers_storageid_fkey REFERENCES proxmox_node_storages (id) ON DELETE SET NULL ON UPDATE CASCADE,
    size integer  DEFAULT 0 NOT NULL,
	vztemplateid integer DEFAULT NULL
       CONSTRAINT  proxmox_offers_vztemplateid_fkey REFERENCES proxmox_node_vztemplates (id) ON DELETE SET NULL ON UPDATE CASCADE,
    net text DEFAULT '' NOT NULL,
	nettype smallint DEFAULT NULL,
	bridgeid integer DEFAULT NULL
       CONSTRAINT  proxmox_offers_bridgeid_fkey REFERENCES proxmox_node_bridges (id) ON DELETE SET NULL ON UPDATE CASCADE,
    ratelimit integer  DEFAULT NULL,
	PRIMARY KEY (id),
	UNIQUE (name)
);

DROP SEQUENCE IF EXISTS proxmox_vmct_id_seq;
CREATE SEQUENCE proxmox_vmct_id_seq;
DROP TABLE IF EXISTS proxmox_vmct CASCADE;
CREATE TABLE proxmox_vmct (
	id integer DEFAULT nextval('proxmox_vmct_id_seq'::text) NOT NULL,
    name varchar(32) 	DEFAULT '' NOT NULL,
    type smallint DEFAULT 0 NOT NULL,
    password varchar(32)	DEFAULT '' NOT NULL,
	customerid integer NOT NULL
       CONSTRAINT customerassignments_customerid_fkey REFERENCES customers (id) ON DELETE CASCADE ON UPDATE CASCADE,
    ipaddr bigint DEFAULT 0 NOT NULL,
    nodeid integer NOT NULL
       CONSTRAINT proxmox_vmct_nodeid_fkey REFERENCES proxmox_nodes (id) ON DELETE RESTRICT ON UPDATE CASCADE,
    vmid integer DEFAULT 0 NOT NULL,
    cdate integer DEFAULT 0 NOT NULL,
	PRIMARY KEY (id)
);

INSERT INTO dbinfo (keytype, keyvalue) VALUES ('dbversion_ProxmoxPlugin', '2021020700');
