<?php


class ControllerSearch extends Controller {
	
	public function find() {
		$this->view->display("indevel.html");
	}
	
	
	public function mine() {
		$engine = Engine::getInstance();
		
		if ($engine->user->getStatus() !== UserAuth::AUTH_SUCCESS) {
			Route::Relocate( Route::AUTH );
			return;
		}
		
		$model = new ModelSearch();
		
		$this->view->display(
				"search_result.html",
				$model->getMine()
		);
	}
}