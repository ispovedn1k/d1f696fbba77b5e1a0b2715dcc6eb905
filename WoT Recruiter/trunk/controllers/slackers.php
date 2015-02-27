<?php

class ControllerSlackers extends Controller {
	
	/**
	 * прием и добавление информации
	 */
	public function append() {
		$data = $this->sanitisation();
		
		if ( isset($data['slackers'] ) ) {
			$model = new ModelSlackers();
			$model->update( $data );
		}
	}
	
	
	public function show() {
		$user = Engine::getInstance()->user;
		
		if ($user->getStatus() !== UserAuth::AUTH_SUCCESS) {
			Route::Relocate( Route::AUTH );
			return;
		}
		
		$model = new ModelSlackers();
		if ( $user->isBlessed() ) {
			
			$clanID = isset($_GET['clanID']) ? 0+ $_GET['clanID'] : $user->clan_id;
		} else {
			$clanID = $user->clan_id;
		}
		$data = $model->getPerMonth( $clanID );
		
		$this->view->menu_pointer = "fort";
		$this->view->display("slackers.html", $data);
	}
	
	
	private function sanitisation() {
		$data = array();

		$data['clanID'] = 0+ $_POST['clanID'];
		$data['playerID'] = 0+ $_POST['playerID'];
		$data['clanTag'] = htmlentities( $_POST['clanTag'] );
		$data['playerName'] = htmlentities( $_POST['playerName'] );
		$slackers = json_decode( $_POST['slackers'] );
		
		if ( isset($slackers[0])) {
			foreach ($slackers as $slacker) {
				$slacker->playerID = 0+ $slacker->playerID;
				$slacker->week = 0+ $slacker->week;
				$slacker->total = 0+ $slacker->total;
				$slacker->playerName = htmlentities( $slacker->playerName );
				
				$data['slackers'][ $slacker->playerID ] = $slacker;
			}
		}
		
		return $data;
	}
}