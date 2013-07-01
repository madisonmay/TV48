<?
	class RoomsController extends AppController {

		public function index() {
			$opts = array('fields' => array('id', 'name'),
			              'conditions' => array('Room.property_id' => $this->request->query['property'])
			);
			$this->set('rooms', $this->Room->find('list', $opts)); 
		}

		public function add() {
		    if ($this->request->is('post')) {
		        if (!$this->request->data['Room']['Users']) {
		        	//room does not have tenant -- therefore is available
		        	$this->request->data['Room']['available'] = 1;

		        	// check if room is public
		        	// ******************** UNTESTED ************************
		        	// Must add contract for each user that does not have a studio here
		        	
		        } else {
		        	//room has tenant
		        	$user_id = $this->request->data['Room']['Users'];

		        	//begin creation of contract object
		        	$this->request->data['Contract']['user_id'] = $user_id;


		        	// retrieving user object that matches room
		        	$user = $this->Room->User->find('first', array('conditions' => array('User.id' => $user_id)));

		        	// ************************ IMPORTANT *********************
		        	// Previous contract objects should be made deactivated if they are primary
		        	// Contracts which the user had pay permissions should be flipped if the new 
		        	// room is a studio.

		        	//search for active room set as primary for new room user
		        	$opts = array('conditions' => array('primary' => 1, 'user_id' => $user_id, 'deactivated' => 0));
		        	$old_contract = $this->Room->Contract->find('first', $opts);

		        	if ($old_contract) {
		        		date_default_timezone_set('Europe/Brussels');
		        		$datetime = date('F j, Y', time());
		        		$this->Room->Contract->id = $old_contract['Contract']['id'];
		        		$this->Room->Contract->saveField('deactivated', $datetime);

		        		$this->Room->id = $old_contract['Contract']['room_id'];
		        		$this->Room->saveField('available', 1);
		        	}

		        	//indicate that user has room
		        	$this->Room->User->id = $user_id;
		        	$this->Room->User->saveField('has_room', 1);

		        	//if the newly added room is a studio, also update that property
		        	if ($this->request->data['Room']['type'] == 'studio') {
		        		$this->Room->User->saveField('has_studio', 1);
		        	} else {
		        		$this->Room->User->saveField('has_studio', 0);
		        	}

		        	$opts = array('conditions' => array('user_id' => $user_id, 'active' => 1, 'primary' => 0));
		        	$contracts = $this->User->Contract->find('all', $opts);


		        	// ********************* UNTESTED ************************************
		        	//make sure studio owners are not paying for public rooms, but dorm owners are
		        	if ($this->request->data['User']['has_studio']) {
		        	    foreach ($contracts as $contract) {
		        	        $this->User->Contract->id = $contract['Contract']['id'];
		        	        $this->User->Contract->saveField('pay', 0);
		        	    }                            
		        	} else {
		        	    foreach ($contracts as $contract) {
		        	        $this->User->Contract->id = $contract['Contract']['id'];
		        	        $this->User->Contract->saveField('pay', 1);
		        	    }  
		        	}
		        	// *******************************************************************

		        }

		        $this->Room->create();
		        if ($this->Room->save($this->request->data)) {
		        	//addition of new room successful

		        	//if user submitted with room, add a new contract

		        	if ($this->request->data['Room']['Users']) {

		        		//set contract variables to values stored in user object
		        		$this->request->data['Contract']['room_id'] =  $this->Room->getInsertID();
		        		$this->request->data['Contract']['start_date'] =  $user['User']['start_date'];
		        		$this->request->data['Contract']['end_date'] =  $user['User']['end_date'];

		        		//could be abstracted to configuration file
		        		$this->request->data['Contract']['view'] = 1;
		        		$this->request->data['Contract']['pay'] = 1;
		        		$this->request->data['Contract']['modify'] = 1;
		        		$this->request->data['Contract']['primary'] = 1;

		        		$this->Room->Contract->create();
		        		if ($this->Room->Contract->save($this->request->data)) {
	        				//if save successful, send positive response
		        			$this->Session->write('flashWarning', 0);
		        			$this->Session->setFlash(__('Room added!'));
		        			$this->redirect('/home/manage');
		        		} else {
		        			//raise error
		        			$this->Session->write('flashWarning', 1);
		        			$this->Session->setFlash(__('An internal error occurred.  Please try again.'));	
		        		}
		        	}

		            $this->Session->write('flashWarning', 0);
		            $this->Session->setFlash(__('Room added!'));
		            $this->redirect('/home/manage');
			    } else {
			    	//something has gone horrible wrong
		        	$this->Session->write('flashWarning', 1);
		        	$this->Session->setFlash(__('An internal error occurred.  Please try again.'));	
		        }		   
		    } else {
		    	//get request -- render input form
				$opts = array('fields' => array('id', 'full_name'), 'conditions' => array('User.tenant' => 1));
				$this->set('users', $this->Room->User->find('list', $opts)); 
		    }

		}

		public function edit() {
			if ($this->request->is('get')) {
				//select tenant by user_id
				$room_id = $this->request->query['Rooms'];
				$this->set('tenants', $this->Room->Tenant->User->find('list', $opts)); 
			    $this->data = $this->Room->find('first', array('conditions' => array('Room.id' => $room_id)));
			}
		}
	}
?>