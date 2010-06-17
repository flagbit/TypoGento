#
# Table structure for table 'fe_users'
#
CREATE TABLE fe_users (
    firstname varchar(50) NOT NULL default '',
	tx_fbmagento_id int(11) default '0'
);

#
# Table structure for table 'sys_language'
#
CREATE TABLE sys_language (
	tx_fbmagento_store varchar(255) NOT NULL default '',
);

#
# Table structure for table 'be_users'
#
CREATE TABLE be_users (
	tx_fbmagento_group int(11) default '0',
);


