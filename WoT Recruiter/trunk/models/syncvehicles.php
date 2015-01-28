<?php

class ModelSyncVehicles extends Model {
	
	public function execute() {
		$this->syncVehicles( Secrets::WG_ID );
	}
	
	
	private function syncVehicles( $WG_App_id ) {
		$db = Engine::getInstance()->db;
		
		$url = "http://api.worldoftanks.ru/wot/encyclopedia/tanks/?application_id={$WG_App_id}&language=". LANG;
		
		$response = curlRequest( $url );
		
		$vehicles = json_decode( $response );
		
		if ($vehicles->count) {
			$sql = "INSERT INTO `". $db->tables("vehicles") ."` (
						`tank_id`, `name`, `nation`, `type`, `lvl`,
						`contour_image`, `image`, `image_small`, `is_premium`
					) VALUES (
						:tank_id, :name, :nation, :type, :lvl,
						:contour_image, :image, :image_small, :is_premium
					);";
			$pre = $db->prepare( $sql );
			foreach ( $vehicles->data as $vehicle ) {
				$pre->execute( array(
						':tank_id' => $vehicle->tank_id,
						':name' => $vehicle->name,
						':nation' => $vehicle->nation,
						':type' => $vehicle->type,
						':lvl' => $vehicle->level,
						':contour_image' => $vehicle->contour_image,
						':image' => $vehicle->image,
						':image_small' => $vehicle->image_small,
						':is_premium' => $vehicle->is_premium,
				));
		
				echo $vehicle->tank_id ." ". $vehicle->name ." ". $vehicle->nation ." ".
						$vehicle->type. " ". $vehicle->level . PHP_EOL;
			}
			
			$sql = "INSERT INTO `". $db->tables("vehicles_". LANG) ."` (
						`tank_id`, `name_i18n`, `short_name_i18n`, `nation_i18n`, `type_i18n`
					) VALUES (
						:tank_id, :name_i18n, :short_name_i18n, :nation_i18n, :type_i18n
					);";
			$pre = $db->prepare( $sql );
			foreach ( $vehicles->data as $vehicle ) {
				$pre->execute( array (
						':tank_id' => $vehicle->tank_id,
						':name_i18n' => $vehicle->name_i18n,
						':short_name_i18n' => $vehicle->short_name_i18n,
						':nation_i18n' => $vehicle->nation_i18n,
						':type_i18n' => $vehicle->type_i18n,
				));
				
				echo $vehicle->tank_id ." ". $vehicle->name_i18n ." ". $vehicle->short_name_i18n ." ". $vehicle->nation_i18n ." ".
						$vehicle->type_i18n. PHP_EOL;
			}//*/
		}
	}
}