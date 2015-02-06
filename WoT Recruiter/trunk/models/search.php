<?php


class ModelSearch extends Model {
	
	
	public function getMine() {
		$engine = Engine::getInstance();
		$user_id = $engine->user->id;
		$db = $engine->db;
		
		$sql = "SELECT * FROM `". $db->tables("interviews") ."` WHERE `owner` = '{$user_id}';";
		
		$res = $db->query( $sql );
		if (! $res ) {
			throw new ErrorException( "SQL Error fail. ". $sql );
		}
		
		return $res->fetchAll(PDO::FETCH_ASSOC);
	}
	
	
	public function getAll() {
		$engine = Engine::getInstance();
		$db = $engine->db;
		
		$sql = "SELECT * FROM `". $db->tables("interviews") ."`;";
		
		$res = $db->query( $sql );
		if (! $res ) {
			throw new ErrorException( "SQL Error fail. ". $sql );
		}
		
		return $res->fetchAll(PDO::FETCH_ASSOC);
	}
}