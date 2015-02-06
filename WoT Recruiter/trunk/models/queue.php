<?php


class ModelQueue extends Model {
	
	public $id, 
		$callName,
		$params,
		$shots, 
		$done,
		$execmicrotime;

	
	public static function Start() {		
		$db = Engine::getInstance()->db;
		$mctime = microtime(true);
		
		// используем последнюю запись об остановке
		$sql = "UPDATE `". $db->tables("queue") ."` SET
				`callName` = 'start',
				`execmicrotime` = {$mctime},
				`done` = 1
				WHERE `callName` = 'prestart';";
		
		$res = $db->query( $sql );
		
		if ( $res->rowCount() == 0 ) {
			// Записей об остановке не было, вставим запись старта
			$sql = "INSERT INTO `". $db->tables("queue") ."`
					(`id`, `callName`, `execmicrotime`, `done`)
					VALUES
					(1, 'start', {$mctime}, 1);";
			return $db->query( $sql );
		}
		
		return true;
	}
	
	
	/**
	 * Ставит в очередь задачу об остановке выполнения очереди.
	 * Остановка осуществляется прерыванием рекурсивного вызова.
	 * Задача должна удаляться из очереди сразу после выполнения.
	 */
	public static function Stop() {		
		$db = Engine::getInstance()->db;
		$mctime = microtime(true);
		
		// записываем метку для остановки и подготавливаем точку для старта
		$sql = "INSERT INTO `". $db->tables("queue") ."` (`callName`, `execmicrotime`) VALUES ('start', '{$mctime}'), ('prestart', 0);";
		
		return $db->query( $sql );
	}
	
	
	/**
	 * Помещает на позицию 0 в очереди задачу об остановке. Т. о. остановка производится немедленно.
	 */
	public static function Terminate() {
		if (! self::hasAccess() ) {
			return false;
		}
		
		$db = Engine::getInstance()->db;
		$mctime = microtime(true);
		// записываем метку для остановки и подготавливаем точку для старта
		$sql = "INSERT INTO `". $db->tables("queue") ."` (`id`, `callName`, `execmicrotime`) VALUES (0, 'stop', '{$mctime}');";
		$sql .= "INSERT INTO `". $db->tables("queue") ."` (`callName`, `execmicrotime`) VALUES ('prestart', 0);";
		
		return $db->query( $sql );
	}
	
	
	/**
	 * Выдает информацию о дате последнего запуска,
	 * количестве задач, выполненных с момента последнего запуска,
	 * --- выполненных всего,
	 * --- находящихся в очереди?
	 * id ближайшей на выполнение задачи,
	 * id последней задачи
	 */
	public static function Status() {
		$db = Engine::getInstance()->db;
		$row = array();
		$ret = array(
				'start_id' => 0,
				'start_time' => 0,
				'done_now' => 0,
				'done_total' => 0,
				'to_do' => 0,
				'nearest_id' => 0,
				'endpoint_id' => 0
		);
		
		$sql = "SELECT * FROM `". $db->tables("queue") ."` WHERE `callName` = 'start' ORDER BY `id` DESC LIMIT 0, 1;";
		$res = $db->query( $sql );
		
		if ( $res ) {
			$row = $res->fetch(PDO::FETCH_ASSOC);
			$ret['start_id'] = $row['id'];
			$ret['start_time'] = $row['execmincrotime'];
		}
		
		$sql = "SELECT COUNT(`id`) FROM `". $db->tables("queue") ."` WHERE `done` = 1 AND `id` > " . $ret['start_id'] .";";
		$res = $db->query( $sql );
		
		if ( $res ) {
			$row = $res->fetch();
			$ret['done_now'] = $row[0];
		}
		
		$sql = "SELECT COUNT(`id`) FROM `". $db->tables("queue") ."` WHERE `done` = 1;";
		$res = $db->query( $sql );
		
		if ( $res ) {
			$row = $res->fetch();
			$ret['done_total'] = $row[0];
		}
		
		$sql = "SELECT COUNT(`id`) FROM `". $db->tables("queue") ."` WHERE `done` = 0;";
		$res = $db->query( $sql );
		
		if ( $res ) {
			$row = $res->fetch();
			$ret['to_do'] = $row[0];
		}
		
		$sql = "SELECT * FROM `". $db->tables("queue") ."` WHERE `done` = 0 ORDER BY `id` ASC LIMIT 0, 1;";
		$res = $db->query( $sql );
		
		if ( $res ) {
			$row = $res->fetch();
			$ret['nearest_id'] = $row[0];
		}
		
		$sql = "SELECT * FROM `". $db->tables("queue") ."` WHERE `done` = 0 ORDER BY `id` DESC LIMIT 0, 1;";
		$res = $db->query( $sql );
		
		if ( $res ) {
			$row = $res->fetch();
			$ret['endpoint_id'] = $row[0];
		}
		
		return $ret;
	}
	
	
	/**
	 * @param string $callName
	 * @param assoc array $params
	 * @return boolean
	 */
	public static function addTask($callName, $params, $shots = 0) {
		$db = Engine::getInstance()->db;
	
		$sql = "INSERT INTO `". $db->tables("queue") ."`
			(`callName`, `params`, `shots`, `done`)
			VALUES
			('{$callName}', '". serialize( $params ) ."', {$shots}, 0);";
	
		return $db->query( $sql );
	}
	
	
	/**
	 * @throws Exception
	 * @return ModelQueue
	 */
	public function getTopTask() {
		$db = Engine::getInstance()->db;
		
		$sql = "SELECT * FROM `". $db->tables("queue") ."` WHERE `done` = 0 ORDER BY `id` ASC LIMIT 0, 1;";
		
		$res = $db->query( $sql );
		
		if (! $res ) {
			Log::put( print_r( $db->errorInfo(), true ) );
			throw new Exception("sql error");
		}
		
		$row = $res->fetch(PDO::FETCH_ASSOC);
		if (! $row ) {
			return;
		}
		
		$this->id = $row['id'];
		$this->callName = $row['callName'];
		$this->params = unserialize( $row['params'] );
		$this->shots = $row['shots'];
		$this->done = $row['done'];
		$this->execmicrotime = $row['execmincrotime'];
	}
	
	
	/**
	 * @return PDOStatement
	 */
	protected function updateTask() {
		$db = Engine::getInstance()->db;
		
		$sql = "UPDATE `". $db->tables("queue") ."` SET
				`shots` = ". $this->shots ."
				`done` = ". $this->done ."
				`execmicrotime` = ". $this->execmicrotime .";";
		
		return $db->query( $sql );
	}
	
	
	/**
	 * 
	 */
	protected function deleteTask() {
		$db = Engine::getInstance()->db;
		
		$sql = "DELETE FROM `". $db->tables("queue"). "` WHERE `id` = ". $this->id. ";";
		
		return $db->query( $sql );
	}
	
	
	/**
	 * 
	 */
	public function executeTask() {
		// выполнение разрешено только для определенного скрипта
		if (! defined('BACKGROUND_QUEUE') ) {
			return false;
		}
		// если нет задачи
		if (! $this->callName ) {
			sleep( 5 );
			return true;
		}
		
		$this->shots += 1;
		
		switch( $this->callName ) {
			case 'stop':
				// если это терминатор, убираем его
				if ( $this->id == 0 ) {
					$this->deleteTask();
				}
				else {
					$this->done = 1;
					$this->updateTask();
				}
				return false;

			case 'updateUserStat':
				if ( $this->updateUserStat() ) {
					$this->done = 1;
					$this->execmicrotime = microtime(true) - $start_time;
				}
				else {
					// защитимся от зафлуживания
					if ( $this->shots < QUEUE_MAX_TRIES ) {
						// перекидываем задачу в конец.
						self::addTask( $this->callName, $this->params, $this->shots );
					}
					else {
						// иначе просто закрываем ее
						$this->done = 1;
					}
				}
				break;
				
			default:
				// всем неизвестным задачам ставим отметку, что они выполнены.
				// о том, что они выполнены некорректно сообщим поставив shot = -1
				
				$this->done = 1;
				$this->shots = -1;
		}
		
		$this->updateTask();
		// успешное завершение, надо продолжать работу.
		return true;
	}
		
	
	/**
	 * @return PDOStatement
	 */
	protected function updateUserStat() {
		$time = time();
		
		$stat = UsersVehiclesStatStrict::RequestVehiclesStatInfo(
				$this->params['user_id'],
				$this->params['access_token']
		);
		UsersVehiclesStatStrict::SaveVehiclesStatInfo( $stat );
		
		$db = Engine::getInstance()->db;
			
		$sqlUpdate = "UPDATE `". $db->tables("users") ."` SET
				`lastUpdated` = {$time}
				
				WHERE `id` = ". $this->params['user_id'] . ";";
		
		return $db->query( $sqlUpdate );
	}
}