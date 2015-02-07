<?php

class ModelAuth extends Model {
	
	public $validationResult;
	
	public $openid;
	
	
	public function execute() {
		return $this->WGAuth2();
		
		// parent::execute();
	}
	
	
	public static function WGAuth2_GenerateRelink() {
		// сначала попытаемся взять из кэша. Экономим половину запросов.
		// @todo: На сколько хватит сохраненной ссылки? Как долго оно будет работать? Как часто ее надо обновлять заново?
		$location = Engine::getInstance()->getOption("authRelink");
		if ( null !== $location ) {
			return $location;
		}
		
		$context = stream_context_create(
				array('http' =>
						array(
								'method'  => 'POST',
								'header'  => 'Content-type: application/x-www-form-urlencoded',
								'content' => http_build_query(
										array(
												'nofollow' => 1,
												'expires_at' => 300,
												'redirect_uri' => ROOT_URI."?cont=auth",
												'application_id' => Secrets::WG_ID
										)
								)
						)
				)
		);
		$data = json_decode(@file_get_contents('https://api.worldoftanks.ru/wot/auth/login/', false, $context),true);
		if ("ok" === $data['status']){
			// сохраним себе ссылку в кэш
			Engine::getInstance()->setOption("authRelink", $data['data']['location']);
			return $data['data']['location'];
		}
		else{
			return null;
		}
	}
	
	
	/**
	 * @return Ambigous <string, number>|string
	 */
	public function WGAuth2(){
		$context = stream_context_create(
				array('http' =>
					array(
						'method'  => 'POST',
						'header'  => 'Content-type: application/x-www-form-urlencoded',
						'content' => http_build_query(
							array(
								'expires_at' => 14*24*60*60,
								'access_token' => $_GET['access_token'],
								'application_id' => Secrets::WG_ID
							)
						)
					)
				)
		);
		//подтверждаем правдивость полученных параметров
		$data = json_decode(@file_get_contents('https://api.worldoftanks.ru/wot/auth/prolongate/', false, $context), true);

		if( $data['status']=="ok" ) {
			$user = Engine::getInstance()->user;

			$user->id = $data['data']['account_id'];;
			$user->personName = $_GET['nickname'];
			// @todo: надо ли тут сохранять так жестко? Или можно придумать что-то лучше?
			$this->saveAuth();
			$user->access_token = $data['data']['access_token'];
			$user->expires = $data['data']['expires_at'];
					
			return $this->saveAuth() ? UserAuth::AUTH_SUCCESS : "failed to save";
		}else{
			return "failed to confirm access";
		}
	}
	
	
	public function logout() {
		$access_token = Engine::getInstance()->user->access_token;
		
		$context = stream_context_create(
				array('http' =>
					array(
						'method'	=> 'POST',
						'header'	=> 'Content-type: application/x-www-form-urlencoded',
						'content' => http_build_query(
							array(
								'access_token' => $access_token,
								'application_id' => Secrets::WG_ID
							)
						)
					)
				)
		);
		@file_get_contents("https://api.worldoftanks.ru/wot/auth/logout/", false, $context);
		
		Engine::getInstance()->user->clearAuth();
	}
	
	
	/**
	 * @return number
	 */
	private function lightOpenIDAuth() {
		$this->openid = new LightOpenID( DOMAIN_NAME );
		
		if(! $this->openid->mode) {
			$this->openid->identity = "http://ru.wargaming.net/id/";
			$this->openid->optional = array('namePerson', 'namePerson/friendly');
			header('Location: ' . $this->openid->authUrl());
		}
		else {
			$this->validationResult = $this->openid->validate();
			if ( $this->validationResult ) {
				$user = Engine::getInstance()->user;
				$attributes = $this->openid->getAttributes();
				
				$user->id = $this->getUserId( $this->openid->data['openid_identity'] );
				$user->personName = $attributes['namePerson/friendly'];
				
				return $this->saveAuth() ? UserAuth::AUTH_SUCCESS : "failed to save";
			} else {
				return "failed to confirm access";
			}
		}
	}
	
	
	/**
	 * Сохраняет / обновляет данные успешной авторизации ВГ в базе данных
	 * для последующей куки-авторизации.
	 */
	private function saveAuth() {
		$user = Engine::getInstance()->user;
		
		$user->lastLogin = 'CURRENT_TIMESTAMP';
		$user->blocked = false;
		$user->lastIp = getenv('REMOTE_ADDR');
		$user->loginHash = $user->calcLoginHash();
		
		if ($user->isExist()) {
			$res = $user->updateAuth();
		} else {
			$res = $user->saveNewAuth();
		}
		
		$user->saveCookieAuth();
		return $res;
	}
	
	
	/**
	 * @param string $identity : Строка-идентификатор, которую вернул ВГ
	 * @throws Exception
	 * @return string : ID по которому сохраняются данные пользователя и можно найти этого пользователя у ВГ.
	 */
	private function getUserId( $identity ) {
		$matches = array();
		
		if (! preg_match("/^https:\/\/ru\.wargaming\.net\/id\/([\d]+)-\w+\/$/", $identity, $matches) )
			throw new Exception("Failed to get userID from WG response. Inline string: ". $identity);
		
		return $matches[1];
	}	
}