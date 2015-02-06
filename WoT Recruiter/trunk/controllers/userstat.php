<?php


class ControllerUserStat extends Controller {
	
	
	public function defaultAction() {
		$user = Engine::getInstance()->user;
		if ( UserAuth::AUTH_SUCCESS === $user->getStatus() ) {
			if ( $user->isStatUp2Date() ) {
				$user->updatePlayerStat();
			}
		}
		
		$this->view->display('default.json', array('status' => "ok"), "default_page.json");
	}
	
	
	public function forceUpdate() {
		$user = Engine::getInstance()->user;
		if ( UserAuth::AUTH_SUCCESS === $user->getStatus() ) {
			$user->updatePlayerStat( true );
		}
		
		$this->view->display('default.json', array('status' => "ok"), "default_page.json");
	}
}

/** @todo
 * Обновление статистики пользователя должно производиться в фоновом режиме через
 * очередь. Обновление выполняется автоматически раз в две недели после авторизации
 * пользователя и подтверждения его access_token.
 * 
 * Кнопка "обновить статистику" должна добавлять пользователя в очередь обновления
 * статистики. Статистика не может быть обновлена чаще чем 1 раз в сутки, во избежания
 * спама и DDoS атак. Следует добавить поле о том, когда пользователь нажимал эту кнопку,
 * т.к. поле lastUpdated должно содержать актуальную информацию о том, когда статистику
 * действительно удалось обновить.
 * 
 * Об организации очередей. Очередей должно быть 2 или даже более. Самая срочная очередь - 
 * очередь авторизации пользователей. Очередь обновления статистики пока имеет наиболее
 * низкий приоритет. Одновременно к WG выполняется только один запрос. Ограничение WG
 * не более 5 запросов в секунду на ключ. Возможно, понадобится организовать ротацию
 * нескольких ключей для увелечения выполняемых запросов.
 */