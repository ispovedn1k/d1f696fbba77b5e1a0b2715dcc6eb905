<?php

class ControllerInstall extends Controller {
	
	public function install() {
		$model = new ModelSyncVehicles();
		$model->execute();
	}
}