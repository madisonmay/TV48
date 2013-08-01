<?
	class InternalController extends AppController {

		public function beforeFilter() {
			parent::beforeFilter();
			$this->Auth->allow('*');
		}

		public function isAuthorized($user) {
		    return true;
		}

		public function lights() {
			$this->layout = 'ajax';
		}

		public function tv48() {
			$this->layout = 'ajax';
		}
	}
?>