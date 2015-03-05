<?php


class ModelAway extends Model {
	
	public $members = array();
	
	
	public function getAway( $clanID ) {
		$context = stream_context_create(
				array('http' =>
						array(
								'method' => 'POST',
								'content' => http_build_query(
										array(
												'application_id' => Secrets::WG_ID,
												'clan_id' => $clanID,
												'fields' => "members"
										)
								)
						)
				)
		);
		$data = json_decode(@file_get_contents('http://api.worldoftanks.ru/wot/clan/info/', false, $context), true);
		
		if ("ok" !== $data['status']) {
			throw new Exception("failes to get clan info". print_r($data, true));
		}
		
		$acc_list = "";
		foreach( $data['data'][ $clanID ]["members"] as $member ) {
			$acc_list .= $member['account_id'] . ",";
			$this->members[ $member['account_id'] ] = $member;
		}
		
		$acc_list = substr($acc_list, 0, -1);
		
		$context = stream_context_create(
				array('http' =>
						array(
								'method' => 'POST',
								'content' => http_build_query(
										array(
												'application_id' => Secrets::WG_ID,
												'account_id' => $acc_list,
												'fields' => "last_battle_time"
										)
								)
						)
				)
		);
		$data = json_decode(@file_get_contents('http://api.worldoftanks.ru/wot/account/info/', false, $context), true);
		
		if ("ok" !== $data['status']) {
			throw new Exception("failes to get users info". print_r($data, true));
		}
		
		foreach( $data['data'] as $account_id => $info ) {
			$this->members[ $account_id ]['last_battle_time'] = $info['last_battle_time'];
		}
		
		return $this->members;
	}
}