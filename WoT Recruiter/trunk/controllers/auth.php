<?php


class ControllerAuth extends Controller {
	
	
	/**
	 * 
	 */
	public function logout() {
		if ( UserAuth::AUTH_SUCCESS === Engine::getInstance()->user->getStatus() ) {
			$model = new ModelAuth();
			$model->logout();
			Route::Relocate("index.php");
		}
	}
	
	
	/**
	 * (non-PHPdoc)
	 * @see Controller::defaultAction()
	 */
	public function defaultAction() {
		if ( UserAuth::AUTH_SUCCESS === Engine::getInstance()->user->getStatus() ) {
			Route::Relocate( "index.php" );
			return;
		}
		
		$error = false;
		if ( empty( $_GET['status'] ) ) {
			//генерируем ссылку и перенаправяем пользователя
			$link = ModelAuth::WGAuth2_GenerateRelink();
			if ($link) {
				Route::Relocate( $link );
				return;
			}
			else {
				$error = array(
								'code' => 500,
								'msg' => "Unable to get WG-auth link"
						);
			}
		}
		elseif ( isset($_GET['status']) && isset($_GET['access_token']) && isset($_GET['nickname']) && isset($_GET['account_id']) && isset($_GET['expires_at'])) {
			// если пользователь попал на страницу с параметрами, которые устанавливает метод auth/login
			if ("ok" != $_GET['status']){
				$error_code = 500;
				if (preg_match('/^[0-9]+$/u', $_GET['code'])){
					$error_code = $_GET['code'];
				}
				$error = array(
						'code' => $error_code,
						'msg' => "WG returned error"
				);
			}
			elseif ( $_GET['expires_at'] < time() ){
				$error = array(
						'code' => 200,
						'msg' => "access_token out of time"
				);
			}
			else{
				$model = new ModelAuth();
				$auth = $model->WGAuth2();
				if ($auth === UserAuth::AUTH_SUCCESS) {
					Route::Relocate( "index.php" );
					// успешное завершение работы
					return;
				}
				else {
					$error = array(
							'code' => 200,
							'msg' => $auth
					);
				}
			}
		}
		else{
			// в запросе чего-то не хватает?
			$error_code = 500;
			if (preg_match('/^[0-9]+$/u', $_GET['code'])){
				$error_code = $_GET['code'];
			}
			$error = array(
					'code' => $error_code,
					'msg' => "WG returned error"
			);
		}
		
		// и как мы только докатились до этой жизни...
		$this->view->display(View::ERROR_PAGE, $error);
	}
	
	
	/**
	 * Неиспользуемый метод
	 */
	private function WGAuth() {
		$res = null;
		if ( UserAuth::AUTH_SUCCESS !== Engine::getInstance()->user->getStatus() ) {
			$model = new ModelAuth();
			$res = $model->execute();
	
			if (UserAuth::AUTH_SUCCESS === $res ) {
				Route::Relocate( "index.php" );
				return;
			}
		}
	
		$this->view->display(View::CONT_DEFAULT, $res);
	}
}