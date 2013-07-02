<?
	class RoomsController extends AppController {

		public function index() {
			$opts = array('fields' => array('id', 'name'));
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
		        	// Previous contract objects should be deactivated if they are primary
		        	$this->removeOldUserContract($user_id);
		        	
		        	//indicate that user has room
		        	$this->Room->User->id = $user_id;
		        	$this->Room->User->saveField('has_room', 1);

		        	//if the newly added room is a studio, also update that property
		        	if ($this->request->data['Room']['type'] == 'studio') {
		        		$this->addRole($user_id, 'studio_owner');
		        	} else {
		        		$this->addRole($user_id, 'dorm_owner');
		        	}

		        	$this->updateSecondaryContracts($user_id);

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

				$users = $this->Room->User->find('all');
				$users = $this->filterByRole($users, "tenant");

				$user_list = array();

				foreach ($users as $user) {
					$user_list[$user['User']['id']] = $user['User']['full_name'];
				}

				$this->set('users', $user_list); 
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