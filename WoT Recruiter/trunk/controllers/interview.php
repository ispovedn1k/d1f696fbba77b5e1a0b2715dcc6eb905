<?php

class ControllerInterview extends Controller {
	
	
	/**
	 * First step. New team creation. Team name selection, vehicles selection
	 * @see Controller::defaultAction()
	 */
	public function defaultAction() {
		$engine = Engine::getInstance();
		
		if ($engine->user->getStatus() !== UserAuth::AUTH_SUCCESS) {
			Route::Relocate( Route::AUTH );
			return;
		}
		
		$model = new ModelInterview();
		$model->getAllVehiclesList();
		
		$this->view->display(
				"interview_editor.html",
				$model
		);
	}
	
	
	/**
	 * Second step. After [Create] button pressed
	 */
	public function create() {
		$engine = Engine::getInstance();
		
		if ($engine->user->getStatus() !== UserAuth::AUTH_SUCCESS) {
			Route::Relocate( Route::AUTH );
			return;
		}
		
		$data = $this->sanitisation();
		
		$model = new ModelInterview();
		$model->create( $data );
		
		$this->view->display(
				"interview_created.json",
				$model
		);
	}
	
	
	public function delete() {
		
	}
	
	
	/**
	 * 
	 */
	public function update() {
		$engine = Engine::getInstance();
		
		if ($engine->user->getStatus() !== UserAuth::AUTH_SUCCESS) {
			Route::Relocate( Route::AUTH );
			return;
		}
		
		$data = array(
				'itrv_id' => $this->get('itrv_id'),
				'name' => $this->get('name'),
				'vehicles' => $this->get('vehicles'),
				'squads_num' => $this->get('squads_num'),
				'active' => $this->get('active'),
		);
		
		$model = new ModelInterview();
		$model->update( $data );
		
		$this->view->display(
				"interview.html",
				$model
		);
	}
	
	
	public function close() {
		
	}
	

	/**
	 * 
	 */
	public function join() {
		$engine = Engine::getInstance();
		
		if ($engine->user->getStatus() !== UserAuth::AUTH_SUCCESS) {
			$this->view->display(View::CONT_AUTH_NEEDED);
			return;
		}
		
		$data = array(
				'itrv_id' => $this->get('itrv_id'),
				'vehicles' => $this->get('vehicles'),
		);
		
		$model = new ModelInterview( $data['itrv_id'] );
		$model->addCandidate( $data );
		
		$this->view->display(
				"interview.html",
				$model
		);
	}
	
	
	/**
	 * 
	 */
	public function vehiclesSelect() {
		$engine = Engine::getInstance();
		
		if ($engine->user->getStatus() !== UserAuth::AUTH_SUCCESS) {
			$this->view->display(View::CONT_AUTH_NEEDED);
			return;
		}
		
		$data = array(
				'itrv_id' => $this->get('itrv_id'),
		);
		
		$model = new ModelInterview( $data['itrv_id'] );

		$this->view->display(
				"vehicles.html",
				$model
		);
	}
	
	
	/**
	 * 
	 */
	public function show() {
		$model = new ModelInterview( 0+ $this->get('itrv_id') );
		$this->view->display(
				"interview.html",
				$model
		);
	}
	
	
	public function candidatesUpdate() {
		$engine = Engine::getInstance();
		
		if ($engine->user->getStatus() !== UserAuth::AUTH_SUCCESS) {
			$this->view->display(View::CONT_AUTH_NEEDED);
			return;
		}
		
		$data = array(
				'itrv_id' => $this->get('itrv_id'),
				'candidates' => $this->get('candidates'),
		);
		
		$model = new ModelInterview( $data['itrv_id'] );
		$res = $model->candidatesUpdate( $data['candidates'] );

		$this->view->display(
				'empty.json',
				array("status" => $res),
				"default.json"
		);
	}
	
	
	/**
	 * @return Ambigous <multitype:, string, number, multitype:multitype: string number >
	 */
	private function sanitisation() {
		$data = array();
		
		$data['itrv_name'] = addslashes( substr( $this->get('itrv_name'), 0, 63 ) );
		if ('' === $data['itrv_name']) {
			$data['itrv_name'] = 'noname00';
		}
		
		$visability = $this->get('visability');
		
		if ("clan" === $visability) {
			$data['visability'] = "clan";
		}
		elseif ("invite" === $visability) {
			$data['visability'] = "invite";
		}
		else {
			$data['visability'] = "all";
		}
		
		$data['itrv_id'] = 0+ $this->get('itrv_id');
		
		$data['itrv_comment'] = addslashes( htmlentities( $this->get('itrv_comment') ) );
		
		$data['squads_num'] = (0+ $this->get('squads_num')) | 1;
		
		$data['active'] = $this->get('active') == 0 ? 0 : 1;
		
		$data['plan'] = '';
		
		$data['vehicles'] = array();
		$vehicles = $this->get('vehicles');
		if ( is_array( $vehicles ) ) {
			foreach ( $vehicles as $tank_id => $vehicle ) {
				$tank_id = 0+ $tank_id;
				$data['vehicles'][ $tank_id ] = array();
				foreach ( $vehicle as $param => $value ) {
					$data['vehicles'][ $tank_id ][ $param ] = ((0+$value) == 0 ? '' : 0+$value);
				}
			}
		}
		
		return $data;
	}
}