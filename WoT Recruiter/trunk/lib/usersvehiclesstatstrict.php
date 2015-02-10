<?php


class UsersVehiclesStatStrict extends UsersVehiclesStat {
	
	/**
	 * https://ru.wargaming.net/developers/api_reference/wot/tanks/stats/
	 */
	public
		$battle_type,
		$spotted,
		$hits,
		$battle_avg_xp,
		$draws,
		$wins,
		$losses,
		$capture_points,
		$battles,
		$damage_dealt,
		$hits_percents,
		$damage_received,
		$shots,
		$xp,
		$frags,
		$survived_battles,
		$dropped_capture_points;
	
	
	/**
	 * @param array of UsersVehiclesStatStrict $data : array 
	 * @param string $battle_type
	 * @return UsersVehiclesStatStrict
	 */
	public static function Init($data, $battle_type = 'all') {
		$res = new UsersVehiclesStatStrict();
		$res->InitWith( $data );
		
		foreach( $data[ $battle_type ] as $key => $value) {
			$res->$key = $value;
		}
		$res->battle_type = $battle_type;
		
		return $res;
	}
	
	
	/**
	 * @return array of string 
	 */
	public function toPDOArray() {
		$res = array();
		foreach ( $this as $k=>$v) {
			$res[':'. $k] = $v;
		}
		
		$res[':total_frags'] = is_array( $this->total_frags ) ? serialize( $this->total_frags ) : '';
		
		return $res;
	}
	
	
	/**
	 * Запрашивает у ВГ статистику игрока по технике.
	 * Возвращает ответ в виде ассоциативного массива ($tank_id => UsersVehiclesStat $stat )
	 *
	 * @param numeric $user_id
	 * @param string $access_token
	 * @param numeric $tank_id
	 * @param string $fields
	 * @throws Exception
	 * @return multitype:UsersVehiclesStat array[ $tank_id ][ $battle_type ]
	 */
	public static function RequestVehiclesStatInfo($user_id, $access_token = '', $tank_id = '', $fields = '') {
		$context = stream_context_create(
				array('http' =>
						array(
								'method' => 'POST',
								'content' => http_build_query(
										array(
												'application_id' => Secrets::WG_ID,
												'account_id' => $user_id,
												'access_token' => $access_token,
												'tank_id' => $tank_id,
												'fields' => $fields,
										)
								)
						)
				)
		);
		$data = json_decode(@file_get_contents('http://api.worldoftanks.ru/wot/tanks/stats/', false, $context), true);
	
		if ("ok" !== $data['status']) {
			throw new Exception("failes to get users vehicles info". print_r($data, true));
		}
	
		$result = array();
		foreach ( $data['data'][$user_id] as $vehiclesInfo ) {
			$result[ $vehiclesInfo['tank_id'] ] = array(
					'all' => UsersVehiclesStatStrict::Init( $vehiclesInfo, "all" ),
					'clan' => UsersVehiclesStatStrict::Init( $vehiclesInfo, "clan" ),
					'team' => UsersVehiclesStatStrict::Init( $vehiclesInfo, "team" ),
					'company' => UsersVehiclesStatStrict::Init( $vehiclesInfo, "company" ),
			);
		}
	
		return $result;
	}
	
	
	/**
	 * @param array of UsersVehiclesStatStrict $vehicles_list <br />
	 *  array[ $tank_id ][ $battle_type ]
	 * @throws Exception
	 */
	public static function SaveVehiclesStatInfo( $vehicles_list ) {
		$stages = array();
		
		if (! is_array( $vehicles_list )) {
			throw new Exception("Unable to save vehiclesStatInfo. It's not array");
		}
	
		$db = Engine::getInstance()->db;
		// подготавливаем запросы
		$vars = get_class_vars( get_class() );
		$update_query = "UPDATE `". $db->tables("users_vehicles") ."` SET ";
		$insert_query = "INSERT INTO `". $db->tables("users_vehicles") ."` (";
		$values = '';
		
		foreach ( $vars as $k=>$v) {
			$update_query .= "`{$k}` = :{$k}, ";
			if ($k === "id") {
				continue;
			}
			$insert_query .= "`{$k}`, ";
			$values .= ":{$k}, ";
		}
		
		$update_query = substr($update_query, 0, -2). "
				WHERE `tank_id` = :tank_id
					AND `account_id` = :account_id
					AND `battle_type` = :battle_type;";
		$insert_query = substr($insert_query, 0, -2);
		$values = substr($values, 0, -2);
		$insert_query .= ") VALUES (" . $values .");";
				
		$update = $db->prepare($update_query);
		if (! $update) {
			throw new ErrorException("failquery ". $update_query. "<pre>". print_r($db->errorInfo(), true)."</pre>");
		}
		$unsaved = array();
		
		$stages[] = "queries precompiled";

		$counter = 0;
		// пытаемся обновить все записи
		foreach ( $vehicles_list as $tank_id => $battles ) {
			$update->execute( $battles['all']->toPDOArray() );
			// если обновить не удается, то отметим id и будем записывать заново
			if ( $update->rowCount() !== 1) {
				$unsaved[] = $tank_id;
				continue;
			}
			
			$update->execute( $battles['team']->toPDOArray() );
			$update->execute( $battles['clan']->toPDOArray() );
			$update->execute( $battles['company']->toPDOArray() );
			$counter++;
		}
		
		$stages[] = "data uppdated {$counter} times";
		$counter = 0;
		
		// теперь сохраним все, что не удалось обновить
		$insert = $db->prepare( $insert_query );
		if (! $insert) {
			throw new ErrorException("failquery ". $insert_query. "<pre>". print_r($db->errorInfo(), true)."</pre>");
		}
		foreach ( $unsaved as $tank_id ) {
			if (! $insert->execute( $vehicles_list[ $tank_id ]['all']->toPDOArray() ) ) {
				throw new ErrorException("failinsert <pre>". print_r($db->errorInfo(), true)."</pre>");
			}
			if (! $insert->execute( $vehicles_list[ $tank_id ]['team']->toPDOArray() ) ) {
				throw new ErrorException("failinsert <pre>". print_r($db->errorInfo(), true)."</pre>");
			}
			if (! $insert->execute( $vehicles_list[ $tank_id ]['clan']->toPDOArray() ) ) {
				throw new ErrorException("failinsert <pre>". print_r($db->errorInfo(), true)."</pre>");
			}
			if (! $insert->execute( $vehicles_list[ $tank_id ]['company']->toPDOArray() ) ) {
				throw new ErrorException("failinsert <pre>". print_r($db->errorInfo(), true)."</pre>");
			}
			$counter++;
		}
		
		$stages[] = "data inserted {$counter} times";
		
		return $stages;
	}
	
	
	/**
	 * @param numeric | array $users
	 * 	один или несколько пользовательских id
	 * @param numeric | array $tanks
	 * 	один или несколько id танков
	 * @param string $battle_type
	 */
	public static function loadVehiclesStatInfo( $users, $tanks = null, $battle_type = null ) {
		$db = Engine::getInstance()->db;
		
		$where_users = '';
		
		if ( is_array( $users )) {
			if (! isset($users[0])) {
				// если пользователей нет, то и статистики нет
				return array();
			}
			// @warning!: sql инъекция ?
			$where_users = "`account_id` IN (". implode(", ", $users) .") ";
		} else {
			$where_users ="`account_id` = ". (0+ $users) ." ";
		}
		
		$where_tanks = '';
		if ( $tanks ) {
			if ( is_array( $tanks )) {
				$where_tanks = "AND `tank_id` IN (". implode(", ", $tanks) .") ";
			} else {
				$where_tanks = "AND `tank_id` = ". (0+ $tank_id) ." ";
			}
		}
		
		$where_battle_type = '';
		if ( $battle_type ) {
			if ( is_array( $battle_type )) {
				$where_battle_type = "AND `battle_type` IN ('". implode("', '", $battle_type) ."' ";
			} else {
				$where_battle_type = "AND `battle_type` = '". $battle_type ."' ";
			}
		}
		
		$selectQuery = "SELECT * FROM `". $db->tables("users_vehicles") ."`
				WHERE " . $where_users .  $where_tanks . $where_battle_type;
		
		$res = $db->query( $selectQuery );
		
		if (! $res) {
			throw new ErrorException("Fail in sql request");
		}
		
		$responce = array();
		foreach ( $res->fetchAll(PDO::FETCH_CLASS, 'UsersVehiclesStatStrict') as $row ) {
			$row->total_frags = unserialize( $row->total_frags );
			/* попробуем вариант без обработки, тупо скинем все кучей. Обработаем на стороне клиента на JS.
			 * Один хрен ему передавать все.
			if (! $responce[ $row->tank_id ] ) {
				$responce[ $row->tank_id ] = array();
			}
			if (! $responce[ $row->tank_id ][ $row->battle_type ] ) {
				$responce[ $row->tank_id ][ $row->battle_type ] = array();
			}
			$responce[ $row->tank_id ][ $row->battle_type ][] = $row;
			*/
			$responce[] = $row;
		}
		
		return $responce;
	}
}