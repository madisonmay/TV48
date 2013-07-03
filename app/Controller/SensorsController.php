<?
	class SensorsController extends AppController {

		public function index() {
			$this->set('cssIncludes', array());
			$this->set('jsIncludes', array());
			$lighting = array("conditions" => array("Sensor.type" => "lighting"));
			$heating = array("conditions" => array("Sensor.type" => "heating"));
			$electricity = array("conditions" => array("Sensor.type" => "electricity"));
			$this->set('lighting', $this->Sensor->find('all', $lighting)); 
			$this->set('heating', $this->Sensor->find('all', $heating)); 
			$this->set('electricity', $this->Sensor->find('all', $electricity)); 
		}

		public function add() {
			$rooms = $this->Sensor->Room->find('list');
			$this->set('rooms', $rooms);
		}
	}
?>