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
		
		$this->view->display(
				"interview_new.html",
				$model->getAllVehiclesList()
		);
	}
	
	
	/**
	 * Second step. After [Create] button pressed
	 */
	public function create() {
		$engine = Engine::getInstance();
		
		if ($engine->user->getStatus() !== UserAuth::AUTH_SUCCESS) {
			$this->view->display(View::CONT_AUTH_NEEDED);
			return;
		}
		
		$int_name = $this->get('int_name');
		$vehicles = $this->get('vehicles');
		
		$model = new ModelInterview();
		
		$this->view->display(
				"interview_created.html",
				$model->create($int_name, $vehicles)
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
			$this->view->display(View::CONT_AUTH_NEEDED);
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
		$model = new ModelInterview( $this->get('itrv_id') );
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
}