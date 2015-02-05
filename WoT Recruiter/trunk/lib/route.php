<?php

class Route {
	const AUTH = "?cont=auth";
	
	public static function Relocate( $location ) {
		header('Location: ' . $location);
	}
	
	
	public static function LocalUrl( $url ) {
		return $url;
	}
	
	
	public static function RemoteUrl( $url ) {
		return $url;
	}
}