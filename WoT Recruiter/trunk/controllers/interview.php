<?php

class ControllerInterview extends Controller {
	
	
	/**
	 * First step. New team creation. Team name selection, vehicles selection
	 * Also, if got itrv_id it displays Interview editor
	 * @see Controller::defaultAction()
	 */
	public function defaultAction() {
		$engine = Engine::getInstance();
		
		if ($engine->user->getStatus() !== UserAuth::AUTH_SUCCESS) {
			Route::Relocate( Route::AUTH );
			return;
		}
		
		$itrv_id = 0+ $this->get('itrv_id');
		
		$model = new ModelInterview( $itrv_id );
		
		// проверяем, есть ли право на редактирование этой команды
		// разрешено редактировать владельцу, мне и создавать новые
		if ( $engine->user->id === $model->owner || $engine->user->id === "3916664" || $model->owner === 0) {	
			$this->view->display(
					"interview_editor.html",
					$model
			);
		}
		else {
			$this->view->display("error.html", array('code' => 500, 'msg' => "has no access") );
		}
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
		$id = $model->create( $data );
		
		$resp = array();
		// если все нормально записалось
		if (0 !== $id) {
			$resp['status'] = "ok";
			$resp['link'] = Route::LocalUrl("?cont=interview&action=show&itrv_id=". $id);
		} else {
			$resp['status'] = "fail";
			$resp['msg'] = "something got wrong! ". Log::getLast();
		}
		
		$this->view->display(
					'default.json',
					$resp,
					"default_page.json"
		);
	}
	
	
	/**
	 * 
	 */
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
		
		$data = $this->sanitisation();
		
		$model = new ModelInterview( $data['itrv_id'] );
		
		$resp = array();
		
		// проверяем, есть ли право на редактирование этой команды
		if ( $model->isEditAllowed() ) {
			if ( $model->update( $data ) ) {
				$resp['status'] = "ok";
				$resp['link'] = Route::LocalUrl("?cont=interview&action=show&itrv_id=". $model->itrv_id);
			}
			else {
				$resp['status'] = "fail";
				$resp['msg'] = "Try again later! ". Log::getLast();
			}
		}
		else {
			$resp['status'] = "fail";
			$resp['msg'] = "Access denied!";
		}
		
		
		$this->view->display(
				'default.json',
				$model,
				"default_page.json"
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
		$model->addCandidate( $data['vehicles'] );
		
		$model->getUsersStat();
		
		$this->view->display(
				"interview.html",
				$model
		);
	}
	
	
	/**
	 * 
	 */
	public function show() {
		$model = new ModelInterview( 0+ $this->get('itrv_id') );
		$model->getUsersStat();
		
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
		$res = array();
		
		if ( $model->isEditAllowed() ) {
			try {
				$upd = $model->candidatesUpdate( $data['candidates'] );
			}
			catch (Exception $e) {
				$upd = false;
				$res['status'] = "fail";
				$res['msg'] = $e->getMessage();
			}
			if ( $upd ) {
				$res['status'] = "ok";
			}
		}
		else {
			$res['status'] = "fail";
			$res['msg'] = "access denied";
		}

		$this->view->display(
				'default.json',
				$res,
				"default_page.json"
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
		
		$data['itrv_comment'] = addslashes( htmlentities( $this->get('itrv_comment'), ENT_QUOTES, "utf-8" ) );
		
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