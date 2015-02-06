<?php

class UserAuth {
	
	public $id;

	public $personName;
	
	public $lastLogin;
	
	public $blocked;
	
	public $loginHash;
	
	public $lastIp;
	
	public $lastUpdated;
	
	public $lastForceUpdated;
	
	public $access_token;
	
	public $expires;
	
	protected $_authStatus;
	
	
	const AUTH_SUCCESS = 0;
	const FAIL_NO_DBDATA = 1;
	const FAIL_ACC_BLOCK = 2;
	const FAIL_BAD_HASH = 3;
	const FAIL_NO_COOKIE = 4;
	const FAIL_OPENID_VALIDATION = 5;
	const SAVING_ERROR = 6;
	const AUTH_RELINKED = 7;
	
	
	public function calcLoginHash() {
		return md5( getenv('REMOTE_ADDR') . $_SERVER['HTTP_USER_AGENT'] );
	}
	
	
	public function openIDtoLocal() {
		
	}
	
	
	/**
	 * Пытается авторизовать пользователя через куки.
	 * Возвращает 1 ответ успешной авторизации AUTH_SUCCESS,
	 * все остальные - ошибки.
	 * 
	 * @throws ErrorException
	 * @return const int
	 */
	public function validateCookieAuth() {		
		if (! isset($_COOKIE['wgid']) )
			return self::FAIL_NO_COOKIE;
			
		$db = Engine::getInstance()->db;
		
		$sqlSelect = "SELECT * FROM `". $db->tables("users") ."` ".
				"WHERE `id` = '". addslashes( $_COOKIE['wgid'] ) ."';";
		
		$res = $db->query( $sqlSelect );
		
		if (! $res) {
			throw new ErrorException("SQL Error.\n" . print_r($db->errorInfo(), true));
		}
		
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		$count = count( $data );
		if ($count === 0 ) {
			$this->_authStatus = self::FAIL_NO_DBDATA;
			return self::FAIL_NO_DBDATA;
		} elseif ($count !== 1) {
			throw new ErrorException("Epic fail! More than single userId note or some other bug.");
		}
		
		$row = $data[0];
		
		$this->id = $row['id'];
		$this->personName = $row['personName'];
		$this->lastLogin = $row['lastLogin'];
		$this->blocked = $row['blocked'];
		$this->loginHash = $row['loginHash'];
		$this->lastIp = $row['lastIp'];
		$this->lastUpdated = $row['lastUpdated'];
		$this->lastForceUpdated = $row['lastForceUpdated'];
		$this->access_token = $row['access_token'];
		$this->expires = $row['expires'];
		
		if ($this->blocked) {
			$this->_authStatus = self::FAIL_ACC_BLOCK;
			return self::FAIL_ACC_BLOCK;
		}
		
		if ($this->loginHash !== $this->calcLoginHash()) {
			$this->_authStatus = self::FAIL_BAD_HASH;
			return self::FAIL_BAD_HASH;
		}
		
		$this->_authStatus = self::AUTH_SUCCESS;
		return self::AUTH_SUCCESS;
	}
	
	
	/**
	 * Обновляет запись в БД о пользователе после авторизации через OpenID
	 * @return PDOStatement
	 */
	public function updateAuth() {
		$db = Engine::getInstance()->db;
		
		$sqlUpdate = "UPDATE `". $db->tables("users") ."` SET
			`lastIp` = '". $this->lastIp . "',
			`loginHash` = '" . $this->loginHash . "',
			`lastLogin` = ". $this->lastLogin . ",
			`access_token` = '". addslashes( $this->access_token ) ."',
			`expires` = ". (0 + $this->expires) .
			" WHERE `id` = ". $this->id . ";";
		
		return $db->query( $sqlUpdate );
	}
	
	
	/**
	 * Создает новую запись данных о пользователе после авторизации через OpenID
	 * @return PDOStatement
	 */
	public function saveNewAuth() {
		$db = Engine::getInstance()->db;
		
		$sqlInsert = "INSERT INTO `". $db->tables("users") . "` (
					`id`,
					`personName`,
					`lastLogin`,
					`blocked`,
					`loginHash`,
					`lastIp`,
					`access_token`,
					`expires` )
				VALUES (
					".( 0+ $this->id ). ", 
					'". addslashes( $this->personName ). "',
					CURRENT_TIMESTAMP,
					'". (int) $this->blocked . "',
					'". addslashes( $this->loginHash ). "',
					'". addslashes( $this->lastIp ). "',
					'". addslashes( $this->access_token ) ."',
					". (0 + $this->expires) ."		
				);";
		
		return $db->query( $sqlInsert );
	}
	
	
	/**
	 * Проверяет существование записи пользователя в БД.
	 * @throws ErrorException
	 * @return boolean
	 */
	public function isExist() {
		$db = Engine::getInstance()->db;
		
		$sqlSelect = "SELECT `id` FROM `". $db->tables("users"). "`".
				" WHERE `id` = '". addslashes( $this->id) ."';";
		
		$res = $db->query( $sqlSelect );
		
		if (! $res) {
			throw new ErrorException( "SQLERROR: \n". print_r( $db->errorInfo(), true));
		}
		
		$data = $res->fetchAll(PDO::FETCH_ASSOC);
		$count = count( $data );
		if ($count === 1 ) {
			return true;
		}
		elseif ($count === 0 ) {
			return false;
		} else {
			throw new ErrorException("Epic fail! More than single userId note or some other bug.");
		}
	}
	
	
	/**
	 * Записывает пользователю id, по которому будет производиться авторизация
	 */
	public function saveCookieAuth() {
		setcookie("wgid", $this->id);
	}
	
	
	public function updatePlayerStat( $force = false ) {
		ModelQueue::addTask(
			"updateUserStat",
			array(
				'user_id' => $this->id,
				'access_token' => $this->access_token
			)
		);
		
		if ( $force ) {
			$this->lastForceUpdated = time();
			$sqlUpdate = "UPDATE `". $db->tables("users") ."` SET
				`lastForceUpdated` = ". $this->lastForceUpdated . " ,
			
				WHERE `id` = ". $this->id . ";";
			
			return $db->query( $sqlUpdate );
		}
		/*	
		$enforce = '';
		$time = time();
		
		// запрещаем обновление чаще чем раз в сутки
		if (( $time - $this->lastForceUpdated )< ( 60 * 60 * 24 ) ) {
			return false;
		}
		
		$stat = UsersVehiclesStatStrict::RequestVehiclesStatInfo( $this->id, $this->access_token );
		UsersVehiclesStatStrict::SaveVehiclesStatInfo( $stat );
		$this->lastUpdated = $time;
		
		if ( $force ) {
			$this->lastForceUpdated = $time;
			$enforce = " `lastForceUpdated` = ". $this->lastForceUpdated . " ,";
		}
		
		$db = Engine::getInstance()->db;
		
		$sqlUpdate = "UPDATE `". $db->tables("users") ."` SET
			{$enforce}
			`lastUpdated` = ". $this->lastUpdated . "
			
			WHERE `id` = ". $this->id . ";";
	
		return $db->query( $sqlUpdate );
		*/
	}
	
	
	public function getStatus() {
		return $this->_authStatus;
	}
	
	
	public function isStatUp2Date() {
		if (time() - $this->lastUpdated > 2592000 ) {
			return false;
		}
		return true;
	}
}