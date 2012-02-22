<?php
return array(
	'account' => array(
		'id' => array(
			'type' => 'BIGINT',
			'constraint' => 20,
			'auto_increment' => true
		),
		
		'username' => array(
			'type' => 'VARCHAR',
			'constraint' => 24
		),
		
		'email' => array(
			'type' => 'VARCHAR',
			'constraint' => 160
		),
		
		'password' => array(
			'type' => 'VARCHAR',
			'constraint' => 60
		),
		
		'createdon' => array(
			'type' => 'DATETIME',
		),
		
		'verifiedon' => array(
			'type' => 'DATETIME'
		),
		
		'lastsignedon' => array(
			'type' => 'DATETIME'
		),
		
		'resetsenton' => array(
			'type' => 'DATETIME'
		),
		
		'deleteon' => array(
			'type' => 'DATETIME'
		),
		
		'suspendedon' => array(
			'type' => 'DATETIME'
		),
		
		'META' => array(
			'KEYS' => array(
				'id' => 'PRIMARY',
				'username' => 'UNIQUE',
				'email' => 'UNIQUE'
			),
			'ENGINE' => 'MyISAM',
			'CHARSET' => 'utf8',
			'ROW_FORMAT' => 'DYNAMIC',
			'AUTO_INCREMENT' => 1
		)
	),
	
	'account_details' => array(
		'account_id' => array(
			'type' => 'BIGINT',
			'constraint' => 20,
			'unsigned' => true
		),
		
		'fullname' => array(
			'type' => 'VARCHAR',
			'constraint' => 160,
		),
		
		'firstname' => array(
			'type' => 'VARCHAR',
			'constraint' => 80,
		),
		
		'dateofbirth' => array(
			'type' => 'DATE',
			'null' => true,
		),
		
		'gender' => array(
			'type' => 'CHAR',
			'constraint' => 1,
			'null' => true
		),
		
		'postalcode' => array(
			'type' => 'VARCHAR',
			'constraint' => 40,
			'null' => true
		),
		
		'country' => array(
			'type' => 'CHAR',
			'constraint' => 2,
			'null' => true
		),
		
		'language' => array(
			'type' => 'CHAR',
			'constraint' => 2,
			'null' => true
		),
		
		'timezone' => array(
			'type' => 'VARCHAR',
			'constraint' => 40,
			'null' => true
		),
		
		'picture' => array(
			'type' => 'VARCHAR',
			'constraint' => 240,
			'null' => true
		),
		
		'META' => array(
			'KEYS' => array(
				'account_id' => 'PRIMARY',
			),
			'ENGINE' => 'MyISAM',
			'CHARSET' => 'utf8',
			'ROW_FORMAT' => 'DYNAMIC',
		)
	),
	
	'account_facebook' => array(
		'account_id' => array(
			'type' => 'BIGINT',
			'constraint' => 20,
			'unsigned' => true
		),

		'facebook_id' => array(
			'type' => 'BIGINT',
			'constraint' => 20
		),
		
		'linkedon' => array(
			'type' => 'DATETIME'
		),
		
		'META' => array(
			'KEYS' => array(
				'account_id' => 'PRIMARY',
				'facebook_id' => 'UNIQUE',
			),
			'ENGINE' => 'MyISAM',
			'CHARSET' => 'utf8',
			'ROW_FORMAT' => 'DYNAMIC',
		)
	),
	
	'account_openid' => array(
		'openid' => array(
			'type' => 'VARCHAR',
			'constraint' => 240
		),

		'account_id' => array(
			'type' => 'BIGINT',
			'constraint' => 20,
			'unsigned' => true
		),
		
		'linkedon' => array(
			'type' => 'DATETIME'
		),
		
		'META' => array(
			'KEYS' => array(
				'openid' => 'PRIMARY',
				'account_id' => 'KEY',
			),
			'ENGINE' => 'MyISAM',
			'CHARSET' => 'utf8',
			'ROW_FORMAT' => 'DYNAMIC',
		)
	),

	'account_twitter' => array(
		'account_id' => array(
			'type' => 'BIGINT',
			'constraint' => 20,
		),

		'twitter_id' => array(
			'type' => 'BIGINT',
			'constraint' => 20,
		),

		'oauth_token' => array(
			'type' => 'VARCHAR',
			'constraint' => 80,
		),
		
		'oauth_token_secret' => array(
			'type' => 'VARCHAR',
			'constraint' => 80,
		),
	
		'linkedon' => array(
			'type' => 'DATETIME'
		),
		
		'META' => array(
			'KEYS' => array(
				'account_id' => 'PRIMARY',
				'twitter_id' => 'KEY',
			),
			'ENGINE' => 'MyISAM',
			'CHARSET' => 'utf8',
			'ROW_FORMAT' => 'DYNAMIC',
		)
	),
	
	'acl_permission' => array(
		'id' => array(
			'type' => 'BIGINT',
			'constraint' => 20,
			'unsigned' => true,
			'auto_increment' => true
		),

		'key' => array(
			# COLLATE utf8_unicode_ci ?
			'type' => 'VARCHAR',
			'constraint' => 20,
		),

		'description' => array(
			# COLLATE utf8_unicode_ci ?
			'type' => 'VARCHAR',
			'constraint' => 160,
		),
		
		'suspendedon' => array(
			'type' => 'DATETIME'
		),
		
		'META' => array(
			'KEYS' => array(
				'id' => 'PRIMARY',
				'key' => 'KEY',
			),
			'ENGINE' => 'MyISAM',
			'CHARSET' => 'utf8',
			'ROW_FORMAT' => 'DYNAMIC',
			'AUTO_INCREMENT' => 2,
			'COLLATE' => 'utf8_unicode_ci'
		)
	),

	'acl_role' => array(
		'id' => array(
			'type' => 'BIGINT',
			'constraint' => 20,
			'unsigned' => true,
			'auto_increment' => true
		),

		'name' => array(
			# COLLATE utf8_unicode_ci ?
			'type' => 'VARCHAR',
			'constraint' => 80,
		),

		'description' => array(
			# COLLATE utf8_unicode_ci ?
			'type' => 'VARCHAR',
			'constraint' => 160,
		),
		
		'suspendedon' => array(
			'type' => 'DATETIME'
		),
		
		'META' => array(
			'KEYS' => array(
				'id' => 'PRIMARY',
				'name' => 'UNIQUE',
			),
			'ENGINE' => 'MyISAM',
			'CHARSET' => 'utf8',
			'ROW_FORMAT' => 'DYNAMIC',
			'AUTO_INCREMENT' => 1,
			'COLLATE' => 'utf8_unicode_ci'
		)
	),

	'rel_account_permission' => array(
		'account_id' => array(
			'type' => 'BIGINT',
			'constraint' => 20,
			'unsigned' => true
		),

		'permission_id' => array(
			'type' => 'BIGINT',
			'constraint' => 20,
			'unsigned' => true
		),

		'META' => array(
			'KEYS' => array(
				'account_id' => 'PRIMARY',
				'permission_id' => 'PRIMARY',
			),
			'ENGINE' => 'MyISAM',
			'CHARSET' => 'utf8',
			'ROW_FORMAT' => 'DYNAMIC',
			'COLLATE' => 'utf8_unicode_ci'
		)
	),

	'rel_account_role' => array(
		'account_id' => array(
			'type' => 'BIGINT',
			'constraint' => 20,
			'unsigned' => true
		),

		'role_id' => array(
			'type' => 'BIGINT',
			'constraint' => 20,
			'unsigned' => true
		),

		'META' => array(
			'KEYS' => array(
				'account_id' => 'PRIMARY',
				'role_id' => 'PRIMARY',
			),
			'ENGINE' => 'MyISAM',
			'CHARSET' => 'utf8',
			'ROW_FORMAT' => 'DYNAMIC',
			'COLLATE' => 'utf8_unicode_ci'
		)
	),

	'rel_role_permission' => array(
		'role_id' => array(
			'type' => 'BIGINT',
			'constraint' => 20,
			'unsigned' => true
		),

		'permission_id' => array(
			'type' => 'BIGINT',
			'constraint' => 20,
			'unsigned' => true
		),

		'META' => array(
			'KEYS' => array(
				'role_id' => 'PRIMARY',
				'permission_id' => 'PRIMARY',
			),
			'ENGINE' => 'MyISAM',
			'CHARSET' => 'utf8',
			'ROW_FORMAT' => 'DYNAMIC',
			'COLLATE' => 'utf8_unicode_ci'
		)
	),

	'ref_country' => array(
		/*'id' => array(
			'type' => 'BIGINT',
			'constraint' => 20,
			'unsigned' => true,
			'auto_increment' => true
		),*/
		
		'alpha2' => array(
			'type' => 'CHAR',
			'constraint' => 2,
		),
		
		'alpha3' => array(
			'type' => 'CHAR',
			'constraint' => 3,
		),
		
		'numeric' => array(
			'type' => 'VARCHAR',
			'constraint' => 3,
		),
		
		'country' => array(
			'type' => 'VARCHAR',
			'constraint' => 80,
		),

		'META' => array(
			'KEYS' => array(
				'alpha2' => 'PRIMARY',
				'alpha3' => 'UNIQUE',
			),
			'ENGINE' => 'MyISAM',
			'CHARSET' => 'utf8',
		)
	),

	'ref_country' => array(
		
		'alpha' => array(
			'type' => 'CHAR',
			'constraint' => 3,
		),
		
		'numeric' => array(
			'type' => 'VARCHAR',
			'constraint' => 3,
		),
		
		'currency' => array(
			'type' => 'VARCHAR',
			'constraint' => 80,
		),

		'META' => array(
			'KEYS' => array(
				'alpha' => 'PRIMARY',
				'numeric' => 'KEY',
			),
			'ENGINE' => 'MyISAM',
			'CHARSET' => 'utf8',
			'ROW_FORMAT' => 'DYNAMIC',
		)
	),

	'ref_iptocountry' => array(
		
		'ip_from' => array(
			'type' => 'INT',
			'constraint' => 10,
			'unsigned' => true
		),

		'ip_to' => array(
			'type' => 'INT',
			'constraint' => 10,
			'unsigned' => true
		),
		
		'country_code' => array(
			'type' => 'CHAR',
			'constraint' => 2,
		),
		
		'META' => array(
			'KEYS' => array(
				'country_code' => 'KEY',
				'ip_to' => 'KEY',
				'ip_from' => 'KEY',
			),
			'ENGINE' => 'MyISAM',
			'CHARSET' => 'utf8',
		)
	),

	'ref_language' => array(
		
		'one' => array(
			'type' => 'CHAR',
			'constraint' => 2,
		),

		'two' => array(
			'type' => 'CHAR',
			'constraint' => 3,
			'unsigned' => true
		),
		
		'language' => array(
			'type' => 'VARCHAR',
			'constraint' => 120,
		),
		
		'native' => array(
			'type' => 'VARCHAR',
			'contraint' => 80
		),
		
		'META' => array(
			'KEYS' => array(
				'one' => 'PRIMARY',
				'two' => 'KEY',
			),
			'ENGINE' => 'MyISAM',
			'CHARSET' => 'utf8',
		)
	),
	
	'ref_timezone' => array(
		
		'id' => array(
			'type' => 'TINYINT',
			'constraint' => 3,
			'unsigned' => true,
			'auto_increment'  => true
		),
		
		'abbr' => array(
			'type' => 'VARCHAR',
			'constraint' => 8
		),
		
		'name' => array(
			'type'=> 'VARCHAR',
			'constraint' => 80
		),
		
		'utc' => array(
			'type' => 'VARCHAR',
			'constraint' => 18
		),
		
		'hours' => array(
			'type' => 'TINYINT',
			'constraint' => 4
		),
	
		'META' => array(
			'KEYS' => array(
				'id' => 'PRIMARY',
				'name' => 'UNIQUE',
				'abbr' => 'KEY',
				'utc' => 'KEY'
			),
			'ENGINE' => 'MyISAM',
			'CHARSET' => 'utf8',
			'AUTO_INCREMENT' => 109
		)
	),
	
	'ref_zoneinfo' => array(
		'zoneinfo' => array(
			'type' => 'VARCHAR',
			'constraint' => 40
		),
		
		'offset' => array(
			'type' => 'VARCHAR',
			'constraint' => 16
		),
		
		'summer' => array(
			'type' => 'VARCHAR',
			'constraint' => 16
		),
		
		'country' => array(
			'type' => 'CHAR',
			'constraint' => 2
		),
		
		'META' => array(
			'KEYS' => array(
				'zoneinfo' => 'PRIMARY',
				'country' => 'KEY'
			),
			'ENGINE' => 'MyISAM',
			'CHARSET' => 'utf8',
			'ROW_FORMAT' => 'DYNAMIC'
		)
	)
);
