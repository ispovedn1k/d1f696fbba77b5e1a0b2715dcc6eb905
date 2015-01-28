<?php


class UsersVehiclesStat {
	
	/**
	 * https://ru.wargaming.net/developers/api_reference/wot/tanks/stats/
	 */
	public 
//		$id,
		$tank_id,
		$account_id,
		$total_frags,
		$max_frags,
		$max_xp,
		$mark_of_mastery;
	
	
	public function InitWith($data) {
		$vars = get_class_vars( get_class() );
		
		foreach ( $vars as $k => $v ) {
			$this->$k = isset($data[ $k ]) ? $data[ $k ] : null;
		}
		
		$this->total_frags = $data['frags'];
	}
	
	
	public static function LoadVehicleStat() {
		
	}
	
	
	
}