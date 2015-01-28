<?php


class ControllerUserStat extends Controller {
	
	public function execute() {
		$user_id = $this->get('user_id');
		$tanks_id = $this->get('tanks_id');
		
		$statInfo = UsersVehiclesStatStrict::loadVehiclesStatInfo( $user_id, $tanks_id );
		
		$this->view->display(
				'empty.json',
				$statInfo,
				"default.json"
		);
	}
}