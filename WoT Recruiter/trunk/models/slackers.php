<?php


/**
 * @author Boris Pavlenko (aka Ispovedn1K) <borpavlenko@ispovedn1k.com>
 *
 */
class ModelSlackers extends Model {
	
	
	public function update( $data ) {
		$db = Engine::getInstance()->db;
		list($year, $month, $day) = explode("-", @date("Y-m-d"));
		$ip = $_SERVER['REMOTE_ADDR'];
		$sql = "UPDATE `". $db->tables("slackers") ."` SET
					`resources` = :resources,
					`ipSender` = '{$ip}'
				WHERE
					 `playerID` = :playerID AND `year` = {$year} AND `month` = {$month} AND `day` = {$day};";
		$pre = $db->prepare( $sql );
		if (! $pre ) {
			Log::put("sql error in ". $sql);
			throw new Exception("sql error");
		}
		
		$insertRequired = array();
		
		foreach ( $data['slackers'] as $slacker ) {
			$param = array(':resources' => $slacker->total, ':playerID' => $slacker->playerID);
			
			if (! $pre->execute($param) ) {
				Log::put("sql troubles: ". print_r($pre->errorInfo(), true) );
			}
			
			if ( $pre->rowCount() == 0) {
				$insertRequired[] = $slacker->playerID;
			}
		}
		
		if (! isset($insertRequired[0])) {
			return;
		}
		
		$sql = "INSERT IGNORE INTO `". $db->tables("slackers") ."`
					(`clanID`, `clanTag`, `year`, `month`, `day`, `ipSender`, `playerID`, `playerName`, `resources`)
				VALUES (".
					$data['clanID'] .", '".
					$data['clanTag'] ."', ".
					"{$year}, {$month}, {$day}, '{$ip}', :playerID, :playerName, :resources);";
		
		$ins = $db->prepare( $sql );
		if (! $ins ) {
			Log::put("sql error in ". $sql);
			throw new Exception("sql error");
		}
		
		foreach ( $insertRequired as $playerID ) {
			$param = array(
					':playerID' => $playerID,
					':playerName' => $data['slackers'][$playerID]->playerName,
					':resources' => $data['slackers'][$playerID]->total
			);
			
			if (! $ins->execute( $param ) ) {
				Log::put("failed to save width data: ". print_r($data['slackers'][$playerID], true));
				throw new Exception("sql error in slackers update()");
			}
		}
	}
	
	
	/**
	 * @param integer $clanID
	 */
	public function getPerMonth( $clanID ) {
		$db = Engine::getInstance()->db;
		$today = new DateTime();
		
		$monthAgo = new DateTime();
		$monthAgo->modify("-1 month");

		// попытаемся получить данные за тот день, который нужен.
		// .. на сегодня
		$todayData = $this->getDataAtDate( $clanID, $today );
		// .. на день в прошлом
		$pastData = $this->getDataAtDate( $clanID, $monthAgo );
		
		// проверяем получение данных.
		if(isset($todayData) && isset($pastData)) {
			// все хорошо, данные получены
			return array(
					'strict' => true,
					'start' => $monthAgo,
					'end' => $today,
					'data' => $this->_calcDiff($todayData, $pastData)
				);
		}
		
		// где-то данные получить не удалось. Нужен календарь...
		$sql = "SELECT `year`, `month`, `day` FROM `". $db->tables("slackers") ."` WHERE `clanID` = {$clanID} GROUP BY `year`, `month`, `day`;";
		$res = $db->query( $sql );
		if (! $res ) {
			Log::put("sql fail in getDataAtDate");
			throw new Exception("SQL-error");
		}
		
		$calendar = $res->fetchAll(PDO::FETCH_ASSOC);
		
		if (! isset($todayData) ) {
			// если не получены данные на сегодня. Ищем ближайшую дату в прошлом
			$today = $this->_getNearestDate($calendar, $today, $monthAgo, "-");
			if ($today ) {
				$todayData = $this->getDataAtDate($clanID, $today);				
			}
		}
		if (! isset($pastData) ) {
			// если не получены данные на дату в прошлом. Ищем ближайшую дату после нее
			$monthAgo = $this->_getNearestDate($calendar, $monthAgo, $today, "+");
			if ($monthAgo ) {
				$pastData = $this->getDataAtDate($clanID, $monthAgo);
			}
		}
		
		return array(
					'strict' => false,
					'start' => $monthAgo,
					'end' => $today,
					'data' => $this->_calcDiff($todayData, $pastData)
				);
	}
	
	
	/**
	 * @param integer $clanID
	 * @param DateTime $date
	 * @throws Exception
	 * @return multitype:string | null
	 */
	public function getDataAtDate( $clanID, $date ) {
		$db = Engine::getInstance()->db;
		
		list($year, $month, $day) = explode("-", $date->format("Y-m-d"));
		
		$sql = "SELECT `clanTag`, `playerName`, `resources`, `playerID` FROM `". $db->tables("slackers") ."`
		WHERE `clanID` = {$clanID} AND `year` = {$year} AND `month` = {$month} AND `day` = {$day}";
		$res = $db->query( $sql );
		if (! $res ) {
			Log::put("sql fail in getDataAtDate");
			throw new Exception("SQL-error");
		}
		
		$rows = $res->fetchAll(PDO::FETCH_ASSOC);
		if (! isset($rows[0]) ) {
			return null;
		}
		$ret = array();
		foreach ($rows as $row) {
			$ret[ $row['playerID'] ] = $row;
		}
		
		return $ret;
	}
	
	
	/**
	 * @desc ищет ближайшую дату в календаре в заданном направлении
	 * @param array $calendar
	 * @param DateTime $start
	 * @param DateTime $stop
	 * @param +/- $direction
	 * @return ближайшую дату, или false
	 */
	private function _getNearestDate( $calendar, $start, $stop, $direction = "+") {
		$pointer = clone $start;
		
		if ($direction === "-") {
			// из будущего в прошлое
			while ( $pointer > $stop ) {
				$pointer->modify("-1 day");
				
				list($year, $month, $day) = explode("-", $pointer->format("Y-m-d"));
				
				foreach ( $calendar as $index => $cmpDate ) {
					if ((int)$cmpDate['year'] == (int)$year &&
						(int)$cmpDate['month'] == (int)$month &&
						(int)$cmpDate['day'] == (int)$day ) {
						// если дата совпала с одной из тех, что есть в календаре
						return $pointer;							
					}
				}
			}
			return false;
		}
		else {
			// из прошлого будущее
			while ( $pointer <= $stop ) {
				$pointer->modify("+1 day");
			
				list($year, $month, $day) = explode("-", $pointer->format("Y-m-d"));
			
				foreach ( $calendar as $index => $cmpDate ) {
					if ((int)$cmpDate['year'] == (int)$year &&
						(int)$cmpDate['month'] == (int)$month &&
						(int)$cmpDate['day'] == (int)$day ) {
						// если дата совпала с одной из тех, что есть в календаре
						return $pointer;
					}
				}
			}
			return false;
		}
	}
	
	
	/**
	 * @desc считает кол-во добытых ресурсов между датами
	 * @param array $today
	 * @param array $past
	 * @return multitype:multitype:number
	 */
	private function _calcDiff($today, $past) {
		foreach ($today as $playerID => $today_row) {
			// дополним данные
			$today[ $playerID ]['diff'] = $today_row['resources'] - ( $past[ $playerID ] ? $past[ $playerID ]['resources'] : 0);
		}
		return $today;
	}
}