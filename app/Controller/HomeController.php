<?
	class HomeController extends AppController {

		function beforeFilter() {
			parent::beforeFilter();
			$this->Auth->allow('index');
		}

		public function isAuthorized($user) {
			if (!in_array('landlord', $this->Session->read('User.roles')) && $this->action == 'manage') {
				return false;
			} 

			return true;
		}

		public function index() {
		}

		public function manage() {
			$this->set('cssIncludes', array());
			$this->set('jsIncludes', array()); 
		}
	}
?>
