<?php


/**
 * @author Boris Pavlenko (aka Ispovedn1K) <borpavlenko@ispovedn1k.com>
 *
 */
class ModelInterview extends UniqDBObjectModel {
	public	$itrv_id,
			$itrv_name,
			$active,
			$owner,
			$squads_num,
			$itrv_comment,
			$plan,
			$visability;
	
	public $a_vehicles;
	
	public $_candidates;
	
	public $_all_vehicles;
	
	public $_statData;
	
	
	/**
	 * @param number $itrv_id
	 */
	public function __construct( $itrv_id = 0 ) {
		parent::__construct( Engine::getInstance()->db->tables("interviews") );
		
		if (0 === $itrv_id) {
			$this->owner = 0;
			return;
		}
		
		$this->load($itrv_id);
	}
	
	
	/**
	 * @return NULL|multitype:
	 */
	public function getAllVehiclesList() {
		$db = Engine::getInstance()->db;
		
		$sql = "SELECT * FROM `". $db->tables("vehicles") ."` as A
				LEFT JOIN `". $db->tables("vehicles_". LANG). "` as B
				ON A.`tank_id` = B.`tank_id`
				ORDER BY `lvl`, `type`, `nation` ASC;";
		$res = $db->query( $sql );
		
		if (! $res ) {
			return null;
		}
		
		$this->_all_vehicles = $res->fetchAll(PDO::FETCH_CLASS, 'WoTVehicle');
		return $this->_all_vehicles;
	}
	
	
	/**
	 * @param array $data : sunitised input array
	 * @return multitype:string 
	 */
	public function create( $data ) {
		$this->itrv_name = $data['itrv_name'];
		$this->a_vehicles = $data['vehicles'];
		$this->active = $data['active'];
		$this->owner = Engine::getInstance()->user->id;
		$this->squads_num = $data['squads_num'];
		$this->itrv_comment = $data['itrv_comment'];
		$this->plan = $data['plan'];
		$this->visability = $data['visability'];
		
		if ( false !== $this->insertObjectToDB(array("itrv_id")) ){
			return Engine::getInstance()->db->lastInsertId();
		}
		else {
			Log::put( print_r( Engine::getInstance()->db->errorInfo(), true) );
			return 0;
		}
	}
	
	
	/**
	 * @param array $data
	 * @return multitype:string 
	 */
	public function update( $data ) {
		$this->itrv_name = $data['itrv_name'];
		$this->a_vehicles = $data['vehicles'];
		$this->active = $data['active'];
		// $this->owner = Engine::getInstance()->user->id; // отключим, чтобы пользователь не менялся
		$this->squads_num = $data['squads_num'];
		$this->itrv_comment = $data['itrv_comment'];
		$this->plan = $data['plan'];
		$this->visability = $data['visability'];
		
		if ( $this->updateObjectToDB( array("itrv_id"/*, "owner"*/) )) {
			return true;
		}
		else {
			Log::put( print_r( Engine::getInstance()->db->errorInfo(), true) );
			return false;
		}
	}
	
	
	/**
	 * @param int $itrv_id
	 * @return Ambigous <NULL, boolean>
	 */
	public function load( $itrv_id ) {
		$this->itrv_id = $itrv_id;
		if ( $this->loadObjectFromDB( 'itrv_id') ) {
			$this->loadCandidates();
		}
		return false;
	}
	

	/**
	 * @param array $candidate
	 * @return boolean
	 */
	public function addCandidate( $vehicles ) {
		$db = Engine::getInstance()->db;
		$user = Engine::getInstance()->user;
		
		if ( isset( $this->_candidates[ $user->id ] ) ) {
			return true;
		}
		
		$this->_candidates[ $user->id ] = Candidate::Init(
				$user,
				$this->itrv_id,
				$vehicles
		);
	
		$sql = "INSERT INTO `". $db->tables("candidates") ."` (
				`user_id`, `personName`, `itrv_id`, `a_vehicles`) VALUES (
				'". addslashes( $user->id ) ."',
				'". addslashes($user->personName) ."',
				'". (0 + $this->itrv_id) ."',
				'". addslashes( serialize( $vehicles ) ) ."');";
		if ( $db->query( $sql ) ) {
			return true;
		}
		Log::put( "add candidate error". print_r($db->errorInfo(), true) );
		return false;
	}
	
	
	/**
	 * @throws ErrorException
	 * @return boolean
	 */
	public function loadCandidates() {
		$db = Engine::getInstance()->db;
		
		$sql = "SELECT * FROM `". $db->tables("candidates") ."` WHERE `itrv_id` = '". (0+$this->itrv_id) ."';";
		$res = $db->query( $sql );
		
		if (! $res ) {
			throw new ErrorException( "SQL Error fail. ". $sql );
		}
		
		$this->_candidates = array();
		$candidates = $res->fetchAll(PDO::FETCH_CLASS, 'Candidate');
		
		foreach ( $candidates as $row) {
			$row->a_vehicles = unserialize( stripslashes( $row->a_vehicles ) );
			$this->_candidates[ $row->user_id ] = $row;
		}
		return true;
	}
	
	
	/**
	 * @param array $vehicles
	 * @throws ErrorException
	 * @return NULL|Ambigous <multitype:, unknown>
	 */
	protected function loadVehicles( $vehicles = null) {
		$db = Engine::getInstance()->db;
		
		if (! $vehicles ) {
			$vehicles = $this->a_vehicles;
		}
		if (! is_string($vehicles[0]) ) {
			return null;
		}
		$sql = "SELECT * FROM `". $db->tables("vehicles") ."` as A
				LEFT JOIN `". $db->tables("vehicles_". LANG). "` as B
				ON A.`tank_id` = B.`tank_id`
				WHERE A.`tank_id` IN ('". implode("','", $vehicles) ."');";
		$res = $db->query( $sql );
		
		if (! $res ) {
			throw new ErrorException( "SQL Error fail. ". $sql );
		}
		
		return $res->fetchAll(PDO::FETCH_CLASS, 'WoTVehicle');
	}
	
	
	/**
	 * @return boolean
	 */
	public function isMember() {
		if ( isset($this->_candidates[ Engine::getInstance()->user->id ]) )
			return true;
		return false;
	}
	
	
	/**
	 * @param array $candidatesData: format( { $candidate_user_id } => { $status }, ... )
	 */
	public function candidatesUpdate( $candidatesData ) {
		$db = Engine::getInstance()->db;
		
		if (! is_array( $candidatesData )) {
			return false;
		}
		
		$sql = "UPDATE `". $db->tables("candidates") ."` SET 
				`status` = :status
				WHERE `user_id` = :user_id AND `itrv_id` = '". (0+$this->itrv_id) ."';";
		
		$pre = $db->prepare( $sql );
		if (! $pre ) {
			throw new ErrorException("Fail to prepare ". print_r( $db->errorInfo(), true));
		}
				
		foreach ( $candidatesData as $user_id => $status) {
			$pre->execute( array(
					':status' => (0+$status),
					':user_id' => addslashes($user_id),
			));
		}
		return true;
	}
	
	
	/**
	 * @return boolean
	 */
	public function isEditAllowed() {
		$engine = Engine::getInstance();
		// разрешено владельцу и мне
		return $engine->user->id === $this->owner || $engine->user->id === "3916664";
	}
	
	
	/**
	 * 
	 */
	public function getUsersStat( $battle_types = "all") {
		// тут должна быть загрузка статистики из кэша
		// .......
		// где в процессе создается массив ($candidate_id => array of uncached vehicles)
		
		// оформим пока заглушку
		$candidates = array();
		foreach ( $this->_candidates as $candidate_id => $candidate ) {
			$candidates[] = $candidate_id;
		}
		// не забыть добавить себя
		if ( ( ! $this->isMember() ) && (UserAuth::AUTH_SUCCESS === Engine::getInstance()->user->getStatus()) ) {
			$candidates[] = Engine::getInstance()->user->id;
		}
		
		$vehicles = array();
		foreach ( $this->a_vehicles as $tank_id => $vehicle ) {
			$vehicles[] = $tank_id;
		}
		// конец заглушки
		
		// бьем по площадям
		$this->_statData = UsersVehiclesStatStrict::loadVehiclesStatInfo( $candidates, $vehicles, "all" );
	}
} 