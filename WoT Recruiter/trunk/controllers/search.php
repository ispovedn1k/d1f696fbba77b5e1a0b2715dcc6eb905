<?php


class ControllerSearch extends Controller {
	
	public function find() {
		$model = new ModelSearch();
		
		$this->view->menu_pointer = "search";
		$this->view->display(
				"search_result.html",
				$model->getAll()
		);
	}
	
	
	public function mine() {
		$engine = Engine::getInstance();
		
		if ($engine->user->getStatus() !== UserAuth::AUTH_SUCCESS) {
			Route::Relocate( Route::AUTH );
			return;
		}
		
		$model = new ModelSearch();
		
		$this->view->menu_pointer = "mysquads";
		$this->view->display(
				"search_result.html",
				$model->getMine()
		);
	}
}