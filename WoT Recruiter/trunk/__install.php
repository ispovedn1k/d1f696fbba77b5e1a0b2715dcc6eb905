<?php

require_once 'config.php';
require_once 'lib/functions.php';

echo PHP_EOL . "CONSoLE installation". PHP_EOL . PHP_EOL;
echo "==============================================". PHP_EOL . PHP_EOL;


try {
	$db = new DBConnector(Secrets::getDSN(), Secrets::DB_USER, Secrets::DB_PASS);
	
	$sql = "CREATE TABLE IF NOT EXISTS `". $db->tables("users") ."` (
			`id` INTEGER (10) NOT NULL,
			`personName` VARCHAR(64) NOT NULL,
			`lastLogin` TEXT NOT NULL,
			`blocked` BOOLEAN,
			`loginHash` VARCHAR(32) NOT NULL,
			`lastIp` VARCHAR(16),
			`lastUpdated` TEXT,
			`lastForceUpdated` TEXT,
			`access_token` VARCHAR(42),
			`expires` INTEGER(10),
			`clan_id` INTEGER(10) NOT NULL DEFAULT 0
		);";
	$sql .= "CREATE UNIQUE INDEX `id` ON `". $db->tables("users") ."` (`id`);";
	if (false === $db->query( $sql )) {
		throw new ErrorException( print_r( $db->errorInfo()) );
	}
	
	$sql = "CREATE TABLE IF NOT EXISTS `". $db->tables("interviews") ."` (
			`itrv_id` INTEGER PRIMARY KEY AUTOINCREMENT,
			`itrv_name` varchar(64) NOT NULL,
			`squads_num` int(3) NOT NULL DEFAULT 1,
			`a_vehicles` text NOT NULL,
			`active` BOOLEAN NOT NULL DEFAULT TRUE,
			`visability` VARCHAR(7) NOT NULL DEFAULT 'all',
			`secure` VARCHAR(32),
			`plan` VARCHAR (8),
			`itrv_comment` text,
			`owner` INTEGER (10) NOT NULL
		);";
	if (false === $db->query( $sql )) {
		throw new ErrorException( print_r( $db->errorInfo()) );
	}
	
	$sql = "CREATE TABLE IF NOT EXISTS `". $db->tables("vehicles") ."` (
			`tank_id` INTEGER(10) NOT NULL,
			`name` varchar(64) NOT NULL,
			`nation` varchar(32) NOT NULL,
			`type` varchar(24) NOT NULL,
			`lvl` int(2) NOT NULL,
			`contour_image` text NOT NULL,
			`image` text NOT NULL,
			`image_small` text NOT NULL,
			`is_premium` BOOLEAN NOT NULL DEFAULT FALSE
		);";
	$sql .= "CREATE UNIQUE INDEX `tank_id` on `". $db->tables("vehicles") ."` (`tank_id`);";
	if (false === $db->query( $sql )) {
		throw new ErrorException( print_r( $db->errorInfo()) );
	}
	
	$sql = "CREATE TABLE IF NOT EXISTS `". $db->tables("vehicles_". LANG) ."` (
			`tank_id` INTEGER(10) NOT NULL,
			`name_i18n` varchar(64) NOT NULL,
			`short_name_i18n` varchar(32) NOT NULL,
			`nation_i18n` varchar(32) NOT NULL,
			`type_i18n` varchar(24) NOT NULL
		);";
	if (false === $db->query( $sql )) {
		throw new ErrorException( print_r( $db->errorInfo()) );
	}
	
	$sql = "CREATE UNIQUE INDEX IF NOT EXISTS `tank_id` on `". $db->tables("vehicles_". LANG) ."` (`tank_id`);";
	if (false === $db->query( $sql )) {
		throw new ErrorException( print_r( $db->errorInfo()) );
	}
	
	$sql = "CREATE TABLE IF NOT EXISTS `". $db->tables("candidates") ."` (
			`cand_id` INTEGER PRIMARY KEY AUTOINCREMENT,
			`user_id` INTEGER (10) NOT NULL,
			`personName` VARCHAR(64) NOT NULL,
			`itrv_id` INTEGER,
			`a_vehicles` text NOT NULL,
			`status` int (3) NOT NULL DEFAULT 0
		);";
	$sql .= "CREATE UNIQUE INDEX `ui_candidate` ON `". $db->tables("candidates"). "` (`user_id`, `itrv_id`);";
	if (false === $db->query( $sql )) {
		throw new ErrorException( print_r( $db->errorInfo()) );
	}
	
	$sql = "CREATE TABLE IF NOT EXISTS `". $db->tables("users_vehicles"). "` (
			`tank_id`		INTEGER(10) NOT NULL,
			`account_id`	INTEGER(10) NOT NULL,
			`battle_type`	VARCHAR(16) NOT NULL,
			`total_frags`	TEXT,
			`max_frags`		INTEGER(10),
			`max_xp`		INTEGER(10),
			`mark_of_mastery`	INTEGER(1),
			`in_garage`		BOOLEAN,

			`spotted`		INTEGER(10),
			`hits`			INTEGER(10),
			`battle_avg_xp`	INTEGER(6),
			`draws`			INTEGER(10),
			`wins`			INTEGER(10),
			`losses`		INTEGER(10),
			`capture_points`	INTEGER(10),
			`battles`		INTEGER(10),
			`damage_dealt`	INTEGER(14),
			`hits_percents`	VARCHAR(8),
			`damage_received`	INTEGER(14),
			`shots`			INTEGER(10),
			`xp`			INTEGER(14),
			`frags`			INTEGER(10),
			`survived_battles`	INTEGER(10),
			`dropped_capture_points` INTEGER(10)
		);";
	$sql .= "CREATE UNIQUE INDEX IF NOT EXISTS `ui_vehinfo` ON `". $db->tables("users_vehicles"). "` (`tank_id`, `account_id`, `battle_type`);";
	if (false === $db->query( $sql )) {
		throw new ErrorException( print_r( $db->errorInfo()) );
	}
	
	$sql = "CREATE TABLE IF NOT EXISTS `". $db->tables("queue") ."` (
			`id`			INTEGER PRIMARY KEY AUTOINCREMENT,
			`callName`		TEXT NOT NULL,
			`params`		TEXT,
			`shots`			INTEGER,
			`done`			BOOLEAN,
			`execmicrotime`	INTEGER
		);";
	if (false === $db->query( $sql )) {
		throw new ErrorException( print_r( $db->errorInfo()) );
	}
	
	$sql = "CREATE TABLE IF NOT EXISTS `". $db->tables("options") ."` (
			`optName`	VARCHAR(64) NOT NULL,
			`value`		TEXT
		);";
	$sql .= "CREATE UNIQUE INDEX IF NOT EXISTS `ui_optname` ON `". $db->tables("options"). "` (`optName`);";
	if (false === $db->query( $sql )) {
		throw new ErrorException( print_r( $db->errorInfo()) );
	}
	
	$model = new ModelSyncVehicles();
	$model->execute();
	
	echo "DONE! Completed successfully!". PHP_EOL;
}
catch (Exception $e) {
	echo $e->getMessage() . PHP_EOL;
	echo "[ ". $e->getLine() . " ] : ". $e->getFile() . PHP_EOL;
}
