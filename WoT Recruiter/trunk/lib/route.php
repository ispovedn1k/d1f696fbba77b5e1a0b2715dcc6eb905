<?php

class Route {
	const AUTH = "?cont=auth";
	
	public static function Relocate( $location ) {
		header('Location: ' . $location);
	}
}