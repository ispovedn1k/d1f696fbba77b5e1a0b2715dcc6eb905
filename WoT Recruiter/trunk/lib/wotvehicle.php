<?php

class WoTVehicle {
	
	public $tank_id;
	
	public $name;
	
	public $nation;
	
	public $type;
	
	public $lvl;

	
	public function __toString() {
		return $this->tank_id;
	}
}