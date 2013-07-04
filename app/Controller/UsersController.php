<?php 
    // app/Controller/UsersController.php
    class UsersController extends AppController {

        public function beforeFilter() {
            parent::beforeFilter();
            $this->Auth->allow('add', 'login');
        }

        public function isAuthorized($user) {
            //if the user does not have landlord privileges
            if (!in_array('landlord', $this->Session->read('User.roles'))) {
                //the only actions exposed should be login and logout
                if (!in_array($this->action, array('login', 'logout'))) {
                    return false;
                }
            }
            

            return true;
        }

        public function index() {
            $this->set('users', $this->findByRole('tenant')); 
        }

        public function view($id = null) {
            $this->User->id = $id;
            if (!$this->User->exists()) {
                throw new NotFoundException(__('Invalid user'));
            }
            $this->set('user', $this->User->read(null, $id));
        }

        public function add() {
            if ($this->request->is('post')) {
                $this->User->create();
                $code = rand();
                $this->request->data['User']['roles'] = json_encode(array("landlord"));
                $this->request->data['User']['confirmation_code'] = $code;
                if ($this->User->save($this->request->data)) {

                    $this->addAllRooms($this->User->getInsertID());
                    // If flashWarning is set to 0, the btn-success class is added to the resultant message.
                    // Otherwise, the btn-danger class is added.
                    $this->Session->write('flashWarning', 0);
                    $this->Session->setFlash(__('Thanks for registering! Please check your inbox for a confirmation email.'));

                    // //Email composition process -- will not work locally because of mail server configuration
                    // $to=$this->request->data['User']['email'];

                    // $subject = 'CORE registration';

                    // $message = "
                    //     <html>
                    //         <head>
                    //             <title>CORE Registration</title>
                    //         </head>
                    //         <body>
                    //             <h1>Thanks for registering, ".$this->request->data['User']['first_name']."</h1>
                    //             <p>Your information is as follows:</p>
                    //             <p>Username: ".$this->request->data['User']['email']."</p>
                    //             <br>
                    //             <p>Click the link below to confirm your registration:</p>
                    //             <a href=http://www.thinkcore.be/TV48/confirmation.php?code=".$code."&email=".$this->request->data['User']['first_name'].">";

                    // // To send HTML mail, the Content-type header must be set
                    // $headers  = 'MIME-Version: 1.0' . "\r\n";
                    // $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                    // // Additional headers
                    // $headers .= 'From: CORE_cvba-so' . "\r\n";

                    // // Mail it
                    // mail($to, $subject, $message, $headers);

                    // Redirect user to homepage
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
            if ($this->request->is('post')) {
                if ($this->Auth->login()) {
                    $user_id = $this->Auth->user('id');
                    $user = $this->User->findById($user_id);
                    $this->Session->write('User.roles', json_decode($user['User']['roles']));
                    $this->redirect($this->Auth->redirect());
                } else {
                    $this->Session->write('flashWarning', 1);
                    $this->Session->setFlash(__('Invalid email or password, try again'));
                }
            }
        }

        public function logout() {
            $this->Session->destroy();
            $this->redirect($this->Auth->logout());
        }

        public function tenant() {
            if ($this->request->is('post')) {
                $code = rand();

                // ******************* IMPORTANT ************************
                // Will eventually need to send out an email to the tenant
                // containing a link for the tenant to complete their registration
                // and choose a password

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

                    //similar to SQL's lastInsertID();
                    $user_id = $this->User->getInsertID();

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

        public function edit() {
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
                            $this->Session->setFlash(__('Room saved!'));
                            $this->redirect('/home/manage');
                        } else {
                            // exit only during debugging test
                            exit(0);
                            $this->Session->write('flashWarning', 1);
                            $this->Session->setFlash(__('An internal error occurred.  Please try again.')); 
                        }
                    }

                    $this->Session->write('flashWarning', 0);
                    $this->Session->setFlash(__('Room saved!'));
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