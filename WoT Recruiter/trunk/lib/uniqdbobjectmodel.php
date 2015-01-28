<?php

class UniqDBObjectModel extends Model {
	protected $_table_name;
	
	
	public function __construct( $table_name ) {
		$this->_table_name = $table_name;
	}
	
	
	protected function insertObjectToDB( $skip = array() ) {
		$sql = "INSERT INTO `". $this->_table_name ."` (";
		$values = "";
		
		foreach ($this as $n => $v) {
			if ($n[0] !== '_') {
				if (in_array( $n, $skip )) {
					continue;
				}
				$sql .= $n. ", ";
				if ("_" === $n[1]) {
					//value should be serialized
					$values .= "'". addslashes( serialize( $v ) ) . "', ";
				} else {
					$values .= "'". addslashes( $v ) . "', ";
				}
			}
		}
		
		if ($values) {
			$sql = substr($sql, 0, -2);
			$values = substr($values, 0, -2);
		}
		
		$sql .= ") VALUES ({$values});";
		
		$db = Engine::getInstance()->db;
		
		return $db->query($sql);
	}
	
	
	protected function updateObjectToDB( $whereKeys ) {
		$where = "WHERE ";
		if ( is_array( $whereKeys ) ) {
			foreach ($whereKeys as $kn) {
				$where .= "`{$kn}` = '". addslashes( $this->$kn ). "', ";
			}
			$where = substr($sql, 0, -2);
		}
		elseif ( $whereKeys ) {
			$where .= "`{$whereKeys}` = '". addslashes( $this->$whereKeys ). "'";
		}
		else {
			return null;
		}
		
		$sql = "UPDATE `". $this->_table_name . "` SET ";
		foreach ($this as $n => $v) {
			if ($n[0] !== '_') {
				if ("_" === $n[1]) {
					//value should be serialized
					$sql .= "`{$n}` = '". addslashes( serialize( $v ) ) . "', ";
				} else {
					$sql .= "`{$n}` = '". addslashes( $v ) . "', ";
				}
			}
		}
		
		$sql = substr($sql, 0, -2) . $where . ";";

		$db = Engine::getInstance()->db;
		
		return $db->query( $sql );
	}
	
	
	protected function deleteObjectFromDB( $whereKeys ) {
		$where = "WHERE ";
		if ( is_array( $whereKeys ) ) {
			foreach ($whereKeys as $kn) {
				$where .= "`{$kn}` = '". addslashes( $this->$kn ). "', ";
			}
			$where = substr($sql, 0, -2);
		}
		elseif ( $whereKeys ) {
			$where .= "`{$whereKeys}` = '". addslashes( $this->$whereKeys ). "'";
		}
		else {
			return null;
		}
		
		$sql = "DELETE FROM `". $this->_table_name . "` ". $where . ";";
		
		$db = Engine::getInstance()->db;
		
		return $db->query( $sql );
	}
	
	
	/**
	 * @param unknown $whereKeys
	 * @throws ErrorException
	 * @return NULL|boolean
	 */
	protected function loadObjectFromDB( $whereKeys ) {
		$where = "WHERE ";
		if ( is_array( $whereKeys ) ) {
			foreach ($whereKeys as $kn) {
				$where .= "`{$kn}` = '". addslashes( $this->$kn ). "', ";
			}
			$where = substr($sql, 0, -2);
		}
		elseif ( $whereKeys ) {
			$where .= "`{$whereKeys}` = '". addslashes( $this->$whereKeys ). "'";
		}
		else {
			return null;
		}
		
		$sql = "SELECT * FROM `". $this->_table_name . "` ". $where . ";";
		
		$db = Engine::getInstance()->db;
		
		$res = $db->query( $sql );
		
		if ( false === $res ) {
			throw new ErrorException("SQL error at: ". $sql. "\n". print_r( $db->errorInfo(), true), 0, 1, __FILE__, __LINE__);
		}
		
		$data = $res->fetch( PDO::FETCH_ASSOC );
		if (! is_array($data)) {
			return false;
		}
		
		foreach ( $data as $n => $v ) {
			if ("_" === $n[1]) {
				$this->$n = unserialize( stripslashes($v) );
			} else {
				$this->$n = $v;
			}
		}
		
		return true;
	}
}