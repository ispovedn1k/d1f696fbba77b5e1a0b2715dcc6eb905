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