<?php

abstract class Secrets {
	const DB_HOST = "localhost";
	const DB_BASE = "wotrecruiter"; // mysql base or sqlite file
	const DB_USER = "wotrecruiter";
	const DB_PASS = "wotrecruiterpass";
	const DB_TYPE = "sqlite"; // mysql or sqlite
	const WG_ID = "bbda83fb368082f57902327276035eff";
	
	
	public static function getDSN() {
		if ( "mysql" === self::DB_TYPE ) {
			return "mysql:host=" . self::DB_HOST . ";dbname=" .  self::DB_BASE;
		}
		if ( "sqlite" === self::DB_TYPE ) {
			return "sqlite:". self::DB_BASE . ".db";
		}
	}
}