<?php


class Candidate {
	public $cand_id;
	
	public $user_id;
	
	public $personName;
	
	public $itrv_id;
	
	/**
	 * Current status of candidate.
	 * -1 - banned
	 * 0 - not candidate
	 * N - Candadate of squad N
	 * 
	 * @var int
	 */
	public $status;
	
	public $a_vehicles;
	
	
	public static function Init(UserAuth $user = null, $itrv_id = 0, $a_vehicles = null, $status = 0) {
		$m = new Candidate();
		if ($user) {
			$m->user_id = $user->id;
			$m->personName = $user->personName;
		}
		$m->itrv_id = $itrv_id;
		$m->status = $status;
		$m->a_vehicles = $a_vehicles;
		
		return $m;
	}
}