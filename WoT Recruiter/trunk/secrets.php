<?php

abstract class Secrets {
	const DB_HOST = "localhost";
	const DB_BASE = "wotteam"; // mysql base or sqlite file
	const DB_USER = "wotteam";
	const DB_PASS = "wotteam";
	const DB_TYPE = "mysql"; // mysql or sqlite
	const WG_ID = "bbda83fb368082f57902327276035eff";
	
	const SOLT = "qwerty123";
	
	
	public static function getDSN() {
		if ( "mysql" === self::DB_TYPE ) {
			return "mysql:host=" . self::DB_HOST . ";dbname=" .  self::DB_BASE;
		}
		if ( "sqlite" === self::DB_TYPE ) {
			return "sqlite:". self::DB_BASE . ".db";
		}
	}
	
	public static $blessed = array("3916664");
}