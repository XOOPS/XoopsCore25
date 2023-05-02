CREATE TABLE log (
  lid mediumint(8) unsigned NOT NULL auto_increment,
  uid mediumint(8) unsigned NOT NULL default 0,
  ip varchar(255) NOT NULL default '0.0.0.0',
  type varchar(255) NOT NULL default '',
  agent varchar(255) NOT NULL default '',
  description text,
  extra text,
  timestamp DATETIME,
  PRIMARY KEY (lid) ,
  KEY (uid) ,
  KEY (ip(100)) ,
  KEY (type(100)) ,
  KEY (timestamp)
) ENGINE=MyISAM;

CREATE TABLE access (
  ip varchar(255) NOT NULL default '0.0.0.0',
  request_uri varchar(255) NOT NULL default '',
  malicious_actions varchar(255) NOT NULL default '',
  expire int NOT NULL default 0,
  KEY (ip(100)),
  KEY (request_uri(100)),
  KEY (malicious_actions(100)),
  KEY (expire)
) ENGINE=MyISAM;
