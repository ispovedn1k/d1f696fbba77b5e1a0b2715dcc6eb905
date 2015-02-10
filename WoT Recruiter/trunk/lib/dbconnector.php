<?php

class DBConnector extends PDO {
	
	protected $lastQuery;
	
	public function query( $statement ) {
		$this->lastQuery = $statement;
		
		return parent::query( $statement );
	}
	
	
	public function tables($table) {
		return TABLE_PREFIX . $table;
	}
	
	
	public function getLastQuery() {
		return $this->lastQuery;
	}
}