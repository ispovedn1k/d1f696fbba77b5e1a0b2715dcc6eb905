<?php


class ControllerQueue extends Controller {
	
	/**
	 * 
	 */
	public function start() {
		if (! self::hasAccess() ) {
			$ret = array(
				'status' => "fail",
				'msg' => "access denied"
			);
		}
		else { 
			if ( ModelQueue::Start() ) {
				// запускаем скрипт через сокет
				$http = fsockopen( DOMAIN_NAME, 80 );
				if (false === $http) {
					$ret = array('status' => "fail", 'msg' => "failed to create socket");
				}
				else {
					fputs($http, "GET ". QUEUE_SCRIPT_URI . " HTTP/1.0\r\n");
					fputs($http, "Host: ". DOMAIN_NAME ."\r\n");
					fputs($http, "\r\n");
					fclose($http);
					
					$ret = array('status' => "ok");
				}
			}
			else {
				$ret = array('status' => "fail", 'msg' => "something got wrong" . print_r( Engine::getInstance()->db->errorInfo(), true));
			}
		}
		
		$this->view->display('default.json', $ret, 'default_page.json');
	}
	
	
	/**
	 * 
	 */
	public function stop() {
		if (! self::hasAccess() ) {
			$ret = array(
				'status' => "fail",
				'msg' => "access denied"
			);
		}
		else {
			if ( ModelQueue::Stop() ) {
				$ret = array('status' => "ok");
			}
			else {
				$ret = array('status' => "fail", 'msg' => "something got wrong". print_r( Engine::getInstance()->db->errorInfo(), true));
			}
		}
		
		$this->view->display('default.json', $ret, 'default_page.json');
	}
	
	
	/**
	 * 
	 */
	public function terminate() {
		if (! self::hasAccess() ) {
			$ret = array(
				'status' => "fail",
				'msg' => "access denied"
			);
		}
		else {
			if ( ModelQueue::Terminate() ) {
				$ret = array('status' => "ok");
			}
			else {
				$ret = array('status' => "fail", 'msg' => "something got wrong");
			}
		}
		
		$this->view->display('default.json', $ret, 'default_page.json');
	}
	
	
	/**
	 * 
	 */
	public function status() {
		if (! self::hasAccess() ) {
			$ret = array(
					'status' => "fail",
					'msg' => "access denied"
			);
		}
		else {
			$ret = array(
					'status' => "ok",
					'data' => ModelQueue::Status()
			);
		}
		$this->view->display(
				'default.json',
				$ret,
				'default_page.json'
		);
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see Controller::defaultAction()
	 */
	public function defaultAction() {
		// сбрасываем все неразрешенные подключения
		if ( QUEUE_ALLOWED_IP !== $_SERVER['REMOTE_ADDR'] ) {
			return;
		}
		
		$model = new ModelQueue();
		$model->getTopTask();
		if ( $model->executeTask() ) {
			// перезапускаем скрипт через сокет
			$http = fsockopen( DOMAIN_NAME, 80 );
			fputs($http, "GET ". QUEUE_SCRIPT_URI. " HTTP/1.0\r\n");
			fputs($http, "Host: ". DOMAIN_NAME ."\r\n");
			fputs($http, "\r\n");
			fclose($http);
		}
	}
	
	
	/**
	 * @return boolean
	 */
	private static function hasAccess() {
		if ( in_array(Engine::getInstance()->user->id, Secrets::$blessed) ) {
			return true;
		}
	
		return false;
	}
}