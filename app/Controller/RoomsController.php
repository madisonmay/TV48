<?
	class RoomsController extends AppController {

		public function index() {
			$opts = array('fields' => array('id', 'name'));
			$this->set('rooms', $this->Room->find('list', $opts)); 
		}

		public function add() {
			// ***********************************************************************
			// Make sure that admins and landlords are always given access 
			// Write a method that creates a contract between new room and those roles
			// ***********************************************************************
		    if ($this->request->is('post')) {
		        if ($this->request->data['Room']['Users']) {

		        	//room has tenant
		        	$user_id = $this->request->data['Room']['Users'];

		        	//begin creation of contract object
		        	$this->request->data['Contract']['user_id'] = $user_id;


		        	// retrieving user object that matches room
		        	$user = $this->Room->User->find('first', array('conditions' => array('User.id' => $user_id)));

		        	// ************************ IMPORTANT *********************
		        	// Previous contract objects should be deactivated if they are primary
		        	$this->removeOldUserContract($user_id);

		        	//if the newly added room is a studio, also update that property
		        	if ($this->request->data['Room']['type'] == 'studio') {
		        		$this->addRole($user_id, 'studio_owner');
		        		$this->removeRole($user_id, 'dorm_owner');
		        	} else {
		        		$this->addRole($user_id, 'dorm_owner');
		        		$this->removeRole($user_id, 'studio_owner');
		        	}

		        	$this->updateUserSecondaryContracts($user_id);

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

		        		//could be abstracted to configuration file (or at least compacted)
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
		        	} else {
		        		if ($this->request->data['Room']['type'] === 'public') {
		        			// check if room is public and add contracts between existing users
		        			$this->addSecondaryContracts($this->Room->getInsertID());
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
				//get request
				$this->set('users', $this->findByRole('tenant')); 

				$room_id = $this->request->query['Rooms'];
			    $this->data = $this->Room->find('first', array('conditions' => array('Room.id' => $room_id)));
				
			} else {
				//post request

				$room_id = $this->request->data['Room']['id'];
				$this->Room->id = $room_id;

				if ($this->Room->save($this->request->data)) {

					//room updated			
		        	//if user submitted with room, add a new contract


		        	// ***************** IMPORTANT ****************
		        	// refactor into a method 

		        	if ($this->request->data['Room']['Users']) {

		        		$user_id = $this->request->data['Room']['Users'];
		        		$user = $this->Room->User->findById($user_id);
		        		//set contract variables to values stored in user object
		        		$this->request->data['Contract']['user_id'] = $user_id;
		        		$this->request->data['Contract']['room_id'] =  $room_id;
		        		$this->request->data['Contract']['start_date'] =  $user['User']['start_date'];
		        		$this->request->data['Contract']['end_date'] =  $user['User']['end_date'];

		        		//could be abstracted to configuration file
		        		$this->request->data['Contract']['view'] = 1;
		        		$this->request->data['Contract']['pay'] = 1;
		        		$this->request->data['Contract']['modify'] = 1;
		        		$this->request->data['Contract']['primary'] = 1;

		        		//update user object
		        		if ($this->request->data['Room']['type'] == 'studio') {
		        			$this->addRole($user_id, 'studio_owner');
		        			$this->removeRole($user_id, 'dorm_owner');
		        		} else {		        			
		        			$this->addRole($user_id, 'dorm_owner');
		        			$this->removeRole($user_id, 'studio_owner');
		        		}

		        		$this->updateUserSecondaryContracts($user_id);
	        			$this->updateSecondaryContracts($room_id, $this->request->data['Room']['type']);

		        		$this->Room->Contract->create();
		        		if ($this->Room->Contract->save($this->request->data)) {
	        				//if save successful, send positive response

		        			$this->Session->write('flashWarning', 0);
		        			$this->Session->setFlash(__('Room saved!'));
		        			$this->redirect('/home/manage');
		        		} else {
		        			//raise error and exit (exit will be remove eventually)
		        			exit(0);
		        			$this->Session->write('flashWarning', 1);
		        			$this->Session->setFlash(__('An internal error occurred.  Please try again.'));	
		        		}
		        	}

		        	// *******************************************

					$this->updateSecondaryContracts($room_id, $this->request->data['Room']['type']);
					$this->Session->write('flashWarning', 0);
					$this->Session->setFlash(__('Room saved!'));
					$this->redirect('/home/manage');
				} else {
					//something has gone horrible wrong
					$this->Session->write('flashWarning', 1);
					$this->Session->setFlash(__('An internal error occurred.  Please try again.'));	
				}
			}
		}
	}
?>