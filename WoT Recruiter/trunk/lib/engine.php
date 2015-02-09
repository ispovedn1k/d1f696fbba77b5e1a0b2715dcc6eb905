<?php

class Engine {
	
	private static $_instance;
	
	public $db;
	
	public $user;
	
	public $controllerName;
	
	public $modelName;
	
	public $controller;
	
	
	public static function getInstance() {
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	
	public function execute () {
		ob_start();
		
		$this->user->validateCookieAuth();
		
		$this->getControllerName();
				
		$controller = new $this->controllerName();
		$controller->execute();
		
		$buffer = ob_get_clean();
		
		echo $buffer;
	}


	/**
	 * @param string $optName
	 * @throws ErrorException
	 * @return NULL|mixed
	 */
	public function getOption( $optName ) {
		$sql = "SELECT * FROM `". $this->db->tables("options") ."` WHERE `optName` = '". addslashes( $optName ) ."';";
		$res = $this->db->query( $sql );
		
		if (! $res ) {
			Log::put( print_r($this->db->errorInfo(), true ) );
			throw new ErrorException("sql error");
		}
		
		$row = $res->fetch(PDO::FETCH_ASSOC);
		
		if (! $row ) {
			return null;
		}
		
		return unserialize( $row['value'] );
	}
	
	
	/**
	 * @param string $optName
	 * @param mixed $value
	 * @return boolean
	 */
	public function setOption( $optName, $value ) {
		$sql = "UPDATE `". $this->db->tables("options") ."` SET `value` = '".
				addslashes( serialize($value) ) ."' WHERE `optName` = '". addslashes( $optName ) ."';";
		$res = $this->db->query( $sql );
		
		if ($res->rowCount() === 1) {
			return true;
		}
		
		$sql = "INSERT INTO `". $this->db->tables("options") ."` (`optName`, `value`) VALUES ('".
				addslashes( $optName ) ."', '".
				addslashes( serialize($value) ) ."' );";
		return $this->db->query( $sql );
	}
	
	
	private function __construct() {
		$this->db = new DBConnector(
				Secrets::getDSN(),
				Secrets::DB_USER,
				Secrets::DB_PASS
		);
		
		$this->user = new UserAuth();
	}
	
	
	private function __clone() {		
	}
	
	
	private function getControllerName() {
		$this->controllerName = isset($_GET['cont']) ? $_GET['cont'] : '';
		$this->controllerName = 'controller'. $this->controllerName;
	}
}