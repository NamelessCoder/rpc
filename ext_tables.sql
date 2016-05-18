#
# Table structure for table 'tx_rpc_token'
#
CREATE TABLE tx_rpc_token (
        uid int(11) NOT NULL auto_increment,
        pid int(11) DEFAULT '0' NOT NULL,
        tstamp int(11) DEFAULT '0' NOT NULL,
        crdate int(11) DEFAULT '0' NOT NULL,
        cruser_id int(11) DEFAULT '0' NOT NULL,
        editlock tinyint(4) DEFAULT '0' NOT NULL,

		    token varchar(255) DEFAULT '' NOT NULL,
        validated tinyint(4) DEFAULT '0' NOT NULL,
        client_ip int(11) DEFAULT '0' NOT NULL,
		    allowed_tasks text NOT NULL

        PRIMARY KEY (uid),
        KEY parent (pid),
        KEY token (token)
);

#
# Table structure for table 'tx_rpc_connection'
#
CREATE TABLE tx_rpc_connection (
        uid int(11) NOT NULL auto_increment,
        pid int(11) DEFAULT '0' NOT NULL,
        tstamp int(11) DEFAULT '0' NOT NULL,
        crdate int(11) DEFAULT '0' NOT NULL,
        cruser_id int(11) DEFAULT '0' NOT NULL,
        editlock tinyint(4) DEFAULT '0' NOT NULL,

		    token varchar(255) DEFAULT '' NOT NULL,
        remote_hostname text NOT NULL,
        uses_https tinyint(4) DEFAULT '0' NOT NULL,

        PRIMARY KEY (uid),
        KEY parent (pid),
        KEY token (token)
);
