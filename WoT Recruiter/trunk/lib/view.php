<?php

class View {
	const CONT_AUTH_NEEDED = "auth_needed.html";
	const CONT_DEFAULT = "default.html";
	const ERROR_PAGE = "error.html";
	
	public $menu_pointer = "home";
	
	
	public function display($content_tmpl = View::CONT_DEFAULT, $data = null, $page_tmpl = "default_page.html") {
		if (! preg_match("/^\w+\.(?:html|json|xml)$/", $content_tmpl )) {
			throw new Exception("Wrong content view file template");
		}
		$content_tmpl = $content_tmpl .".php";
		
		if (! preg_match("/^\w+\.(?:html|json|xml)$/", $page_tmpl )) {
			throw new Exception("Wrong page view file template");
		}
		
		if ( file_exists( VIEWS_DIR . $page_tmpl .".php" ) ) {
			include( VIEWS_DIR . $page_tmpl .".php");
		}
	}
}