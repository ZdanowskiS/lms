DROP SEQUENCE IF EXISTS ust_log_id_seq;
CREATE SEQUENCE ust_log_id_seq;
DROP TABLE IF EXISTS ust_log CASCADE;
CREATE TABLE ust_log (
    id              integer			NOT NULL DEFAULT nextval('locdict_groups_id_seq'::text),
    action          integer			NOT NULL,
	userid integer NOT NULL
		REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
    cdate 	        integer			NOT NULL,
	PRIMARY KEY (id)
);
CREATE INDEX ust_log_action_idx ON ust_log (action);

INSERT INTO uiconfig (section, var, value, description, disabled) VALUES
('ustats', 'customerinfo','FALSE','Enable customerinfo display loging',1);

INSERT INTO dbinfo (keytype, keyvalue) VALUES ('dbversion_UStatsPlugin', '2019033000');
