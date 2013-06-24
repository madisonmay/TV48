<?
	class HomeController extends AppController {

		public function index() {
			$this->set('cssIncludes', array('home'));
			$this->set('jsIncludes', array('http://cdnjs.cloudflare.com/ajax/libs/d3/2.10.0/d3.v2.min.js', 'nv.d3', 'home')); 
		}
	}
?>
