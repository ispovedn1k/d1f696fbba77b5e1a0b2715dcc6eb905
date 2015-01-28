<?php

spl_autoload_register( 'classAutoloader' );


function classAutoloader( $className ) {
	if (! preg_match( "/^\w+$/", $className ) ) {
		throw new ErrorException("Bad class name: ". $className ."\n");
	}
	
	$className = strtolower( $className );
	
	$pos = strpos( $className, 'controller');
	if (0 === $pos) {
		$subname = substr( $className, 10);
		if (! $subname ) {
			$subname = 'controller';
		}
		$path = CONTROLLERS_DIR . $subname .".php";
		if ( file_exists( $path )) {
			require_once $path;
			return;
		} elseif (file_exists( LIB_DIR . $className . ".php" )) {
			require_once ( LIB_DIR . $className . ".php" );
			return;
		}
		else {
			throw new Exception("Unable to find file for controller '". $className ."'\n");
		}
	}
	
	$pos = strpos( $className, 'model');
	if (0 === $pos) {
		$subname = substr( $className, 5);
		if (! $subname ) {
			$subname = 'model';
		}
		$path = MODELS_DIR . $subname .".php";
		if ( file_exists( $path )) {
			require_once $path;
			return;
		} elseif (file_exists( LIB_DIR . $className . ".php" )) {
			require_once ( LIB_DIR . $className . ".php" );
			return;
		}
		else {
			throw new Exception("Unable to find file for model '". $className ."'\n");
		}
	}
	
	$path = LIB_DIR . $className . ".php";
	if ( file_exists( $path )) {
		require_once $path;
	}
	else {
		throw new Exception("Unable to find file for class '". $className ."'\n");
	}
}


/**
 * @param string $url
 * @param string $type
 * @param string $post_data
 * @param array $headers
 * @return mixed
 */
function curlRequest( $url,
		$type = 'GET',
		$post_data = '',
		$headers = array("Accept: application/xml")
) {
	$timeout = 100;
	$responseCURL = '';

	$errorInfo = array();

	try {
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			
		if (($type == 'POST') || ($type == 'HTTPS')) {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($ch, CURLOPT_HEADER, 0); // ----
			curl_setopt($ch, CURLOPT_POST, 1);
		}
			
		if ($type == 'HTTPS') {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		}
			
		if ( count ($headers) ) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		}
			
		$responseCURL = curl_exec($ch);
		$errorInfo['errno'] = curl_errno( $ch );
		curl_close($ch);
	}
	catch (Error $ex) {

	}
	return $responseCURL;
};


function i18n( $word, $lang = LANG ) {
	// @warning! Костыль!
	$inter = array (
			'ru' => array (
					//nations
					'ussr' => "СССР",
					'germany' => "Германия",
					'usa' => "США",
					'france' => "Франция",
					'uk' => "Великобритания",
					'china' => "Китай",
					'japan' => "Япония",
					'allNations' => "Все нации",
					
					//vehicles types
					'lightTank' => "Легкий Танк",
					'mediumTank' => "Средний Танк",
					'heavyTank' => "Тяжелый Танк",
					'AT-SPG' => "ПТ-САУ",
					'SPG' => "САУ",
					'allTypes' => "Все типы",
					
					//others
					'allLevels' => "Все уровни",
			),
			'en' => array (
					//nations
					'ussr' => "USSR",
					'germany' => "Germany",
					'usa' => "USA",
					'france' => "France",
					'uk' => "Great Britain",
					'china' => "China",
					'japan' => "Japan",
					'allNations' => "All Nations",
					
					//vehicles types
					'lightTank' => "Light Tank",
					'mediumTank' => "Medium Tank",
					'heavyTank' => "Heavy Tank",
					'AT-SPG' => "AT-SPG",
					'SPG' => "SPG",
					'allTypes' => "All types",
					
					//others
					'allLevels' => "All Levels",
			),
	);
	
	return $inter[LANG][$word];
}


//@warning! Костыль!
$nations = array(
		'ussr',
		'germany',
		'usa',
		'france',
		'china',
		'uk',
		'japan',
);

$vtypes = array(
		'lightTank',
		'mediumTank',
		'heavyTank',
		'AT-SPG',
		'SPG',
);