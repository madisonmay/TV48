<?
	class BillingController extends AppController {

		function beforeFilter() {
			parent::beforeFilter();
			$this->Auth->allow('index');
		}

		public function isAuthorized($user) {
			if (in_array('landlord', $this->Session->read('User.roles'))) {
				return true;
			} 
			if (in_array('admin', $this->Session->read('User.roles'))) {
				return true;
			} 
			return false;
		}

		public function index() {
			
		}
	}
?>
