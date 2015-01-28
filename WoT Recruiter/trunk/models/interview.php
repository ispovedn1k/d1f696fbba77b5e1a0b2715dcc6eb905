<?php


/**
 * @author Boris Pavlenko (aka Ispovedn1K) <borpavlenko@ispovedn1k.com>
 *
 */
class ModelInterview extends UniqDBObjectModel {
	public $itrv_id;
	
	public $itrv_name;
	
	public $a_vehicles;
	
	public $active;
	
	public $owner;
	
	public $squads_num;
	
	public $_candidates;
	
	public $_last_operation;
	
	
	public function __construct( $itrv_id = 0 ) {
		parent::__construct( Engine::getInstance()->db->tables("interviews") );
		
		if (0 === $itrv_id) {
			return;
		}
		
		$this->load($itrv_id);
	}
	
	
	public function execute() {
		$this->getVehiclesList();
		
		parent::execute();
	}
	
	
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
		
		$this->a_vehicles = $res->fetchAll(PDO::FETCH_CLASS, 'WoTVehicle');
		// @todo Надо ли тут записывать в этот массив?
		return $this->a_vehicles;
	}
	
	
	/**
	 * @param string $itrv_name
	 * @param array $vehicles
	 * @return multitype:string 
	 */
	public function create( $itrv_name, $vehicles ) {
		$this->itrv_name = $itrv_name ? $itrv_name : 'noname00';
		$this->a_vehicles = is_array($vehicles) ? $vehicles : array();
		$this->active = true;
		$this->owner = Engine::getInstance()->user->id;
		$this->squads_num = 1;
		
		if ( $this->insertObjectToDB(array("itrv_id")) ){
			return array(
					'last_action' => "created_successfully",
					'data' => Engine::getInstance()->db->lastInsertId(),		
			);
		}
		else {
			return array('last_action' => "creation_failed. ". print_r( Engine::getInstance()->db->errorInfo(), true));
		}
	}
	
	
	/**
	 * @param array $data
	 * @return multitype:string 
	 */
	public function update( $data ) {
		$this->itrv_name = $data['name'];
		$this->a_vehicles = $data['vehicles'];
		$this->itrv_id = $data['itrv_id'];
		$this->squads_num = $data['squads_num'];
		$this->active = $data['active'];
		$this->owner = Engine::getInstance()->user->id;
		
		if ( $this->updateObjectToDB( array("itrv_id", "owner") )) {
			return array('last_action' => "updated_successfully");
		}
		else {
			return array('last_action' => "update_failed");
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
			$this->a_vehicles = $this->loadVehicles();
		}
		return false;
	}
	

	/**
	 * @param array $candidate
	 * @return boolean
	 */
	public function addCandidate( $candidate ) {
		$db = Engine::getInstance()->db;
		$user = Engine::getInstance()->user;
		
		if ( isset( $this->_candidates[ $user->id ] ) ) {
			return true;
		}
		
		$this->_candidates[ $user->id ] = Candidate::Init(
				$user,
				$this->itrv_id,
				$candidate['vehicles']
		);
	
		$sql = "INSERT INTO `". $db->tables("candidates") ."` (
				`user_id`, `personName`, `itrv_id`, `a_vehicles`) VALUES (
				'". addslashes( $user->id ) ."',
				'". addslashes($user->personName) ."',
				'". (0 + $this->itrv_id) ."',
				'". addslashes( serialize( $candidate['vehicles'] ) ) ."');";
		if ( $db->query( $sql ) ) {
			return true;
		}
		$this->_last_operation = "add candidate error". print_r($db->errorInfo(), true);
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
			$a_vehicles = unserialize( $row->a_vehicles );
			$row->a_vehicles = $this->loadVehicles( $a_vehicles );
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
} 