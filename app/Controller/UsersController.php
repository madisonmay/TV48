<?php 
    // app/Controller/UsersController.php
    class UsersController extends AppController {

        public function beforeFilter() {
            parent::beforeFilter();
            $this->Auth->allow('add', 'login', 'confirm', 'tenant_confirm');
        }

        public function isAuthorized($user) {
            //if the user does not have landlord privileges
            if (!in_array('landlord', $this->Session->read('User.roles'))) {
                //the only actions exposed should be login and logout + confirmation pages, and own profile
                if (!in_array($this->action, array('login', 'logout', 'confirm', 'tenant_confirm', 'profile'))) {
                    return false;
                }

                if (in_array($this->action, array('profile'))) {
                    if ($this->request->query['id'] != $this->Auth->user('id')) {
                        return false;
                    }
                }
            }
            

            return true;
        }

        public function billing() {
            $users = $this->User->find('all');
            $users = $this->filterByRole($users, 'tenant');
            $this->set('users', $users);
            $this->set('title_for_layout', 'Tenant Balances');
        }

        public function update_balance() {
            if ($this->request->is('post')) {
                $user_id = (int) $this->request->data['id'];
                $delta = (float) $this->request->data['delta'];
                $user = $this->User->findById($user_id);
                $this->User->id = $user_id;
                if ($this->User->saveField('balance', $user['User']['balance'] + $delta)) {
                    echo $user['User']['balance'] + $delta;

                    //make BalanceUpdate object
                    $data = array();
                    $data['BalanceUpdate']['delta'] = $delta;
                    $data['BalanceUpdate']['balance'] = $user['User']['balance'] + $delta;
                    $data['BalanceUpdate']['user_id'] = $user_id;
                    $data['BalanceUpdate']['wh_delta'] = 0;
                    $data['BalanceUpdate']['wh'] = $user['User']['wh'];

                    $this->loadModel('BalanceUpdate');
                    $this->BalanceUpdate->create();
                    $this->BalanceUpdate->save($data);
                }
            }
            exit(0);
        }

        public function index() {
            $this->set('title_for_layout', 'Tenants');
            $this->set('users', $this->findByRole('tenant')); 
        }

        public function profile() {
            //display a single users profile
        }

        public function profiles() {
            //a summary of all tenants current standings
            $this->loadModel('Room');
            //filter the list of all users to get a list of all tenants
            $users = $this->User->find('all');
            $users = $this->filterByRole($users, 'tenant');
            //for each user
            for ($i = 0; $i < count($users); $i++) {
                //for each contract that the user (tenant) is part of 
                for ($j = 0; $j < count($users[$i]['Contract']); $j++) {
                    $contract = $users[$i]['Contract'][$j];
                    //if the contract is a primary contract
                    if ($contract['primary']) {
                        //and is currently in effect
                        if (!$contract['deactivated']) {
                            //set as primary contract for use in the view
                            $users[$i]['primary_contract'] = $contract;
                        }
                    }
                }

                //if the user is not currently assigned a room, fill an array with filler data
                if (!array_key_exists('primary_contract', $users[$i])) {
                    $users[$i]['primary_contract'] = array('start_date' => 'None', 'end_date' => 'None');
                    $users[$i]['Room'] = array('name' => 'None');
                } else {
                    //otherwise, also connect the user to a room
                    $room = $this->Room->findById($users[$i]['primary_contract']['room_id']);
                    $users[$i]['Room'] = $room['Room'];
                }

                //count up the amount of funds that a user has added to their account
                //we also need this for statistical/overview purposes
                $users[$i]['User']['funds_added'] = 0;
                foreach ($users[$i]['BalanceUpdate'] as $update) {
                    if (!$update['sensor_id']) {
                        $users[$i]['User']['funds_added'] += $update['delta'];
                    }
                }
            }
            $this->set('users', $users);
            $this->set('title_for_layout', 'Tenants Overview');
        }

        public function view($id = null) {
            $this->User->id = $id;
            if (!$this->User->exists()) {
                throw new NotFoundException(__('Invalid user'));
            }
            $this->set('user', $this->User->read(null, $id));
        }

        public function add() {
            $this->set('title_for_layout', 'Register');
            if ($this->request->is('post')) {
                $this->User->create();
                $code = rand();
                $this->request->data['User']['roles'] = json_encode(array("landlord"));
                $this->request->data['User']['confirmation_code'] = $code;
                if ($this->User->save($this->request->data)) {

                    $this->addAllRooms($this->User->getInsertID());
                    // If flashWarning is set to 0, the btn-success class is added to the resultant message.
                    // Otherwise, the btn-danger class is added.

                    $activate_url = "<a href=localhost/users/confirm?code=".$code."&email=".$this->request->data['User']['email'].">TV48 Confirmation</a>";
                    $name = $this->request->data['User']['first_name'];
                    $Email = new CakeEmail();
                    $Email->config('default');
                    $Email->from(array('core.tv48@gmail.com' => 'CORE TV48'));

                    // Eventually the recipient should be the person signing up.
                    // $Email->to($this->request->data['User']['email']);

                    $Email->to('madison.may@students.olin.edu');
                    $Email->template('confirm');
                    $Email->emailFormat('html');
                    $Email->subject('TV48 Email Confirmation');
                    $Email->viewVars(array('activate_url' => $activate_url,'name' => $name));
                    $Email->send();

                    $this->Session->write('flashWarning', 0);
                    $this->Session->setFlash(__('Registration successful!  Check your inbox for a confirmation email.'));
                    $this->redirect(array('controller' => 'home', 'action' => 'index'));
                } else {
                    $this->Session->write('flashWarning', 1);
                    $this->Session->setFlash(__('Errors occurred during registration.  Please try again.'));
                }
            }
        }


        public function delete($id = null) {
            if (!$this->request->is('post')) {
                throw new MethodNotAllowedException();
            }
            $this->User->id = $id;
            if (!$this->User->exists()) {
                throw new NotFoundException(__('Invalid user'));
            }
            if ($this->User->delete()) {
                $this->Session->write('flashWarning', 0);
                $this->Session->setFlash(__('User deleted'));
                $this->redirect(array('action' => 'index'));
            }
            $this->Session->write('flashWarning', 1);
            $this->Session->setFlash(__('User was not deleted'));
            $this->redirect(array('action' => 'index'));
        }

        public function login() {
            $this->set('title_for_layout', 'Login');
            if ($this->request->is('post')) {
                if ($this->Auth->login()) {
                    $user_id = $this->Auth->user('id');
                    $user = $this->User->findById($user_id);
                    if ($user['User']['confirmed']) {
                        $this->Session->write('User.roles', json_decode($user['User']['roles']));
                        $this->redirect($this->Auth->redirect());
                    } else {
                        $this->Session->destroy();
                        $this->Session->write('flashWarning', 1);
                        $this->Session->setFlash(__('Please confirm your account.'));
                    }
                } else {
                    $this->Session->write('flashWarning', 1);
                    $this->Session->setFlash(__('Invalid email or password, try again'));
                }
            }
        }


        public function confirm() {
            $this->set('title_for_layout', 'Registration Confirmation');
            $code = $this->request->query['code'];
            $email = $this->request->query['email'];
            $user = $this->User->find('first', array('conditions' => array('email' => $email)));
            $this->User->id = $user['User']['id'];
            if ($code == $user['User']['confirmation_code']) {
                $this->User->saveField('confirmed', 1);
                $this->Session->write('flashWarning', 0);
                $this->Session->setFlash(__('Account confirmed!'));
                $this->redirect(array('controller' => 'home', 'action' => 'index'));
            } else {
                $this->Session->write('flashWarning', 1);
                $this->Session->setFlash(__('Your confirmation code is not valid.  Please try again.'));
                $this->redirect(array('controller' => 'home', 'action' => 'index'));
            }
        }

        public function logout() {
            $this->Session->destroy();
            $this->redirect($this->Auth->logout());
        }

        public function tenant() {
            $this->set('title_for_layout', 'Add a Tenant');
            if ($this->request->is('post')) {
                $code = rand();

                $user = $this->Session->read("Auth.User");
                $this->request->data['User']['property_name'] = $user['property_name'];
                $this->request->data['User']['confirmation_code'] = $code;

                //initialize user roles
                $this->request->data['User']['roles'] = array("tenant");

                if ($this->request->data['User']['Rooms']) {
                    //if the user has been assigned a room

                    //retreive room_id
                    $room_id = $this->request->data['User']['Rooms'];

                    //retrieve room data
                    $room = $this->User->Room->find('first', array('conditions' => array('Room.id' => $room_id)));

                    //check for studio status and update user roles
                    if ($room['Room']['type'] == 'studio') {
                        array_push($this->request->data['User']['roles'], "studio_owner");

                    } else {
                        array_push($this->request->data['User']['roles'], "dorm_owner");
                    }

                    //start preparing contract object
                    $this->request->data['Contract']['room_id'] =  $room['Room']['id'];

                    $this->removeOldRoomContract($room_id);

                } 

                //encode data as string for saving to DB -- could also be done as X table
                $this->request->data['User']['roles'] = json_encode($this->request->data['User']['roles']);

                $this->User->create();
                if ($this->User->save($this->request->data)) {

                    $user_id = $this->User->getInsertID();

                    $this->loadModel('BalanceUpdate');
                    $this->BalanceUpdate->create();
                    $data = array('user_id' => $user_id, 
                                  'delta' => $this->request->data['User']['balance'],
                                  'balance' => $this->request->data['User']['balance']);
                    $this->BalanceUpdate->save($data);


                    $activate_url = "<a href=localhost/users/tenant_confirm?code=".$code."&email=".$this->request->data['User']['email'].">TV48 Confirmation</a>";
                    $name = $this->request->data['User']['first_name'];
                    $Email = new CakeEmail();
                    $Email->config('default');
                    $Email->from(array('core.tv48@gmail.com' => 'CORE TV48'));

                    // Eventually the recipient should be the person signing up.
                    // $Email->to($this->request->data['User']['email']);

                    $Email->to('madison.may@students.olin.edu');
                    $Email->template('confirm');
                    $Email->emailFormat('html');
                    $Email->subject('TV48 Email Confirmation');
                    $Email->viewVars(array('activate_url' => $activate_url,'name' => $name));
                    $Email->send();

                    //similar to SQL's lastInsertID();

                    if ($this->request->data['User']['Rooms']) {

                        //if room was submitted with user, update contract table
                        $this->request->data['Contract']['user_id'] = $user_id;
                        $this->request->data['Contract']['start_date'] = $this->request->data['User']['start_date'];
                        $this->request->data['Contract']['end_date'] = $this->request->data['User']['end_date'];

                        //could be abstracted to configuration file
                        $this->request->data['Contract']['view'] = 1;
                        $this->request->data['Contract']['pay'] = 1;
                        $this->request->data['Contract']['modify'] = 1;
                        $this->request->data['Contract']['primary'] = 1;


                        $this->User->Contract->create();
                        if ($this->User->Contract->save($this->request->data)) {

                            //add secondary contracts with public rooms
                            $this->addUserSecondaryContracts($user_id);

                            $this->Session->write('flashWarning', 0);
                            $this->Session->setFlash(__('Tenant added!'));
                            $this->redirect(array('controller' => 'home', 'action' => 'manage'));
                        } else {
                            $this->Session->write('flashWarning', 1);
                            $this->Session->setFlash(__('An internal error occurred.  Please try again.')); 
                            $this->redirect(array('controller' => 'home', 'action' => 'manage'));
                        }
                    }
                    //add secondary contracts with public rooms
                    $this->addUserSecondaryContracts($user_id);

                    $this->Session->write('flashWarning', 0);
                    $this->Session->setFlash(__('Tenant added!'));
                    $this->redirect(array('controller' => 'home', 'action' => 'manage'));
                } else {
                    $this->Session->write('flashWarning', 1);
                    $this->Session->setFlash(__('An internal error occurred.  Please try again.')); 
                }
            }

            $this->set('rooms', $this->findAvailableRooms());
        }

        public function tenant_confirm() {
            $this->set('title_for_layout', 'Registration Confirmation');
            if ($this->request->is('get')) {
                $email = $this->request->query['email'];
                $code = $this->request->query['code'];
                $user = $this->User->find('first', array('conditions' => array('email' => $email)));
                if ($code == $user['User']['confirmation_code']) {
                    $this->data = $user;
                } else {
                    $this->Session->write('flashWarning', 1);
                    $this->Session->setFlash(__('Incorrect confirmation code.  Please try again.'));
                    $this->redirect(array('controller' => 'users', 'action' => 'login')); 
                }
            } else {
                // Post request
                $this->User->id = $this->request->data['User']['id'];
                $this->User->saveField('confirmed', 1);
                $this->User->saveField('password', $this->request->data['User']['password']);
                $this->Session->write('flashWarning', 0);
                $this->Session->setFlash(__('Registration successful!'));
                $this->redirect(array('controller' => 'home', 'action' => 'index'));
            }

        }

        public function edit() {
            $this->set('title_for_layout', 'Edit Tenant');
            if ($this->request->is('get')) {
                //get request

                //find all non-public rooms
                $rooms = $this->User->Room->find('list', array('conditions' => array('type !=' => 'public')));
                $this->set('rooms', $rooms);

                //retrieve user data
                $user_id = $this->request->query['Users'];
                $this->data = $this->User->findById($user_id);
                
            } else {
                //post request
                $user_id = $this->request->data['User']['id'];
                $this->User->id = $user_id;

                if ($this->User->save($this->request->data)) {

                    //user updated
                    //if room submitted with user, add a new contract

                    if ($this->request->data['User']['Rooms']) {

                        $room_id = $this->request->data['Room']['Users'];
                        $room = $this->User->Room->findById($room_id);

                        //set contract variables to values stored in user object
                        $this->request->data['Contract']['room_id'] = $room_id;
                        $this->request->data['Contract']['user_id'] = $user_id;
                        $this->request->data['Contract']['start_date'] =  $this->request->data['User']['start_date'];
                        $this->request->data['Contract']['end_date'] =  $this->request->data['User']['end_date'];

                        //could be abstracted to configuration file
                        $this->request->data['Contract']['view'] = 1;
                        $this->request->data['Contract']['pay'] = 1;
                        $this->request->data['Contract']['modify'] = 1;
                        $this->request->data['Contract']['primary'] = 1;

                        //update user object
                        if ($room['Room']['type'] == 'studio') {
                            $this->addRole($user_id, 'studio_owner');
                            $this->removeRole($user_id, 'dorm_owner');
                        } else {
                            $this->addRole($user_id, 'dorm_owner');
                            $this->removeRole($user_id, 'studio_owner');
                        }

                        $this->removeOldUserContract($user_id);
                        $this->updateUserSecondaryContracts($user_id);

                        $this->User->Contract->create();
                        if ($this->Room->Contract->save($this->request->data)) {
                            //if save successful, send positive response

                            // I don't think this bit is needed while updating user
                            // In this case, I believe removeOldUserContract() takes care of the needed work
                            // $this->updateSecondaryContracts($room_id, $room['Room']['type']);

                            $this->Session->write('flashWarning', 0);
                            $this->Session->setFlash(__('Tenant saved!'));
                            $this->redirect('/home/manage');
                        } else {
                            // exit only during debugging test
                            exit(0);
                            $this->Session->write('flashWarning', 1);
                            $this->Session->setFlash(__('An internal error occurred.  Please try again.')); 
                        }
                    }

                    $this->Session->write('flashWarning', 0);
                    $this->Session->setFlash(__('Tenant saved!'));
                    $this->redirect('/home/manage');
                } else {
                    // exit only during debugging test
                    exit(0);
                    $this->Session->write('flashWarning', 1);
                    $this->Session->setFlash(__('An internal error occurred.  Please try again.')); 
                }
            }

        }
    }
?>