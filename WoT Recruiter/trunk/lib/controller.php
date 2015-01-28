<?php

class Controller {
	
	public $model;
	
	public $view;
	
	
	public function __construct() {
		$this->view = new View();
	}
	
	
	/**
	 * 
	 */
	public function execute() {
		$action = $this->get('action');
		
		if ( $action && method_exists( $this, $action ) ) {
			$this->$action();
		} else {
			// Default action
			$this->defaultAction();
		}
	}
	
	
	/**
	 * @param unknown $key
	 * @return multitype:Ambigous <unknown, NULL> |Ambigous <unknown, NULL>
	 */
	public function get( $key ) {
		if ( is_array($key) ) {
			$data = array();
			foreach ($key as $k) {
				$data[ $k ] =	isset($_GET[$k]) ? $_GET[$k] :
				isset($_POST[$k]) ? $_POST[$k] :
				null;
			}
			return $data;
		}
		
		return	isset($_GET[$key]) ? $_GET[$key] :
				(isset($_POST[$key]) ? $_POST[$key] :
				null);
	}
	
	
	/**
	 * @param string | array $keys
	 * @param object $obj
	 * @return string | array | NULL
	 */
	public function getToObj( $keys, $obj = null ) {
		if (! is_object( $obj ) ) {
			return $this->get( $keys );
		}
	
		if ( is_array($keys) ) {
			$data = array();
			foreach ($keys as $k) {
				$data[ $k ] =	isset($_GET[$k]) ? $_GET[$k] :
								(isset($_POST[$k]) ? $_POST[$k] :
								null);
				$obj->$k = $data[ $k ];
			}
			return $data;
		}
	
		$data =	isset($_GET[ $keys ]) ? $_GET[ $keys ] :
				(isset($_POST[ $keys ]) ? $_POST[ $keys ] :
				null);
		$obj->$keys = $data;
		return $data;
	}
	

	public function defaultAction() {
		$this->view->display();
	}
}