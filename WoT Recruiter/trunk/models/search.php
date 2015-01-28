<?php


class ModelSearch extends Model {
	
	
	public function getMine() {
		$engine = Engine::getInstance();
		$user_id = addslashes( $engine->user->id );
		$db = $engine->db;
		
		$sql = "SELECT * FROM `". $db->tables("interviews") ."` WHERE `owner` = '{$user_id}';";
		
		$res = $db->query( $sql );
		if (! $res ) {
			throw new ErrorException( "SQL Error fail. ". $sql );
		}
		
		return $res->fetchAll(PDO::FETCH_ASSOC);
	}
}