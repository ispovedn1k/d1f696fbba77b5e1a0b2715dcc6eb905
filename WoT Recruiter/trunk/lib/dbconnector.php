<?php

class DBConnector extends PDO {
	
	public function query( $statement ) {
		file_put_contents(
			BASE_DIR . "queryhist.sql",
			$statement ."\n=============================================\n",
			FILE_APPEND
		);
		
		return parent::query( $statement );
	}
	
	
	public function tables($table) {
		return TABLE_PREFIX . $table;
	}
}