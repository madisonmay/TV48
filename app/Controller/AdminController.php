<?
	class AdminController extends AppController {

		public function index() {
			$this->set('title_for_layout', 'Admin Panel');
			$this->set('cssIncludes', array());
			$this->set('jsIncludes', array()); 
		}
	}
?>