#
# Table structure for table 'tx_gdpr_domain_model_log'
#
CREATE TABLE tx_gdpr_domain_model_log (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	table_name varchar(255) NOT NULL DEFAULT '',
	record_id  int(11) DEFAULT '0' NOT NULL,
	status tinyint(4) DEFAULT '0' NOT NULL,
	user int(11) DEFAULT '0' NOT NULL,
	user_name_text varchar(255) NOT NULL DEFAULT '',

	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (
	gdpr_restricted tinyint(4) DEFAULT '0' NOT NULL,
	gdpr_randomized tinyint(4) DEFAULT '0' NOT NULL
);

CREATE TABLE be_users (
	gdpr_module_enable tinyint(4) DEFAULT '0' NOT NULL
);
