<?php

abstract class Log {
	
	private static $_logMessages = array();
	private static $_logMessagesCount = 0;
	
	
	public static function put( $message ) {
		self::$_logMessages[] = $message;
		return self::$_logMessagesCount++;
	}
	
	
	public static function getLast() {
		return self::$_logMessages[ self::$_logMessagesCount-1 ];
	}
	
	
	public static function getNum() {
		return self::$_logMessagesCount;
	}
	
	
	public static function get( $message_id = 0) {
		if ( 0 === self::$_logMessagesCount ) {
			return null;
		}
		
		if ( $message_id < 0 || $message_id >= self::$_logMessagesCount ) {
			return null;
		}
		
		return self::$_logMessages[ $message_id ];
	}
	
	
	public static function getAll() {
		return self::$_logMessages;
	}
}