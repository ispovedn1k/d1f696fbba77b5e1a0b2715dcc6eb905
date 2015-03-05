<?php


class ControllerAway extends Controller {
	
	public function defaultAction() {
		$user = Engine::getInstance()->user;
		if ($user->getStatus() !== UserAuth::AUTH_SUCCESS) {
			Route::Relocate( Route::AUTH );
			return;
		}
		
		$model = new ModelAway();
		$data = $model->getAway($user->clan_id);
		$this->view->menu_pointer = "away";
		$this->view->display('away.html', $data);
	}
}