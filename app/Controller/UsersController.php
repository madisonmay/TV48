<?php 
    // app/Controller/UsersController.php
    class UsersController extends AppController {

        public function beforeFilter() {
            parent::beforeFilter();
            $this->Auth->allow('add', 'login');
        }

        public function index() {
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
                $this->request->data['User']['landlord'] = 1;
                $this->request->data['User']['confirmation_code'] = $code;
                if ($this->User->save($this->request->data)) {

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

        public function edit($id = null) {
            $this->User->id = $id;
            if (!$this->User->exists()) {
                throw new NotFoundException(__('Invalid user'));
            }
            if ($this->request->is('post') || $this->request->is('put')) {
                if ($this->User->save($this->request->data)) {
                    $this->Session->write('flashWarning', 0);
                    $this->Session->setFlash(__('Profile saved'));
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->Session->write('flashWarning', 1);
                    $this->Session->setFlash(__('Your profile could not be saved. Please try again.'));
                }
            } else {
                $this->request->data = $this->User->read(null, $id);
                unset($this->request->data['User']['password']);
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
                    $this->redirect($this->Auth->redirect());
                } else {
                    $this->Session->write('flashWarning', 1);
                    $this->Session->setFlash(__('Invalid email or password, try again'));
                }
            }
        }

        public function logout() {
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
                $this->request->data['User']['tenant'] = 1;
                $this->request->data['User']['property_name'] = $user['property_name'];
                $this->request->data['User']['confirmation_code'] = $code;

                if ($this->request->data['User']['Rooms']) {
                    $this->request->data['User']['has_room'] = 1;
                    $room_id = $this->request->data['User']['Rooms'];
                    $room = $this->User->Room->find('first', array('conditions' => array('Room.id' => $room_id)));

                    //update room to reflect occupancy
                    $this->User->Room->id = $room_id;
                    $this->User->Room->saveField('available', 0);

                    //check for studio status and update user object
                    if ($room['Room']['type'] == 'studio') {
                        $this->request->data['User']['has_studio'] = 1;
                    } else {
                        $this->request->data['User']['has_studio'] = 0;
                    }

                    //start preparing contract object
                    $this->request->data['Contract']['room_id'] =  $room['Room']['id'];

                    //if the room had a previous user, deactivate old contract
                    $opts = array('conditions' => array('primary' => 1, 'room_id' => $room_id, 'deactivated' => 0));
                    $old_contract = $this->Room->Contract->find('first', $opts);

                    if ($old_contract) {
                        //deactivate old contract
                        date_default_timezone_set('Europe/Brussels');
                        $datetime = date('F j, Y', time());
                        $this->User->Contract->id = $old_contract['Contract']['id'];
                        $this->User->Contract->saveField('deactivated', $datetime);
                    }

                } else {
                    //no room found in HTTP Post
                    $this->request->data['User']['has_room'] = 0;
                }

                $this->User->create();
                if ($this->User->save($this->request->data)) {
                    if ($this->request->data['User']['Rooms']) {

                        //if room was submitted with user, update contract table
                        $this->request->data['Contract']['user_id'] =  $this->User->getInsertID();
                        $this->request->data['Contract']['start_date'] =  $this->request->data['User']['start_date'];
                        $this->request->data['Contract']['end_date'] =  $this->request->data['User']['end_date'];

                        //could be abstracted to configuration file
                        $this->request->data['Contract']['view'] = 1;
                        $this->request->data['Contract']['pay'] = 1;
                        $this->request->data['Contract']['modify'] = 1;
                        $this->request->data['Contract']['primary'] = 1;

                        // update all current contracts to reflect change
                        // only need to modify "public" contracts now when changed from studio -> dorm
                        // or from dorm -> studio since code above handled primary case
                        // ********************* UNTESTED **************************
                        $opts = array('conditions' => array('user_id' => $this->request->data['Contract']['user_id'], 'active' => 1, 'primary' => 0));
                        $contracts = $this->User->Contract->find('all', $opts);


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
                        // **********************************************************

                        if (!$this->User->Contract->save($this->request->data)) {
                            $this->Session->write('flashWarning', 1);
                            $this->Session->setFlash(__('An internal error occurred.  Please try again.')); 
                        } else {
                            $this->Session->write('flashWarning', 0);
                            $this->Session->setFlash(__('Tenant added!'));
                            $this->redirect(array('controller' => 'home', 'action' => 'manage'));
                        }
                    }
                    $this->Session->write('flashWarning', 0);
                    $this->Session->setFlash(__('Tenant added!'));
                    $this->redirect(array('controller' => 'home', 'action' => 'manage'));
                } else {
                    $this->Session->write('flashWarning', 1);
                    $this->Session->setFlash(__('An internal error occurred.  Please try again.')); 
                }
            }

            $opts = array('conditions' => array('available' => 1));
            $this->set('rooms', $this->User->Room->find('list', $opts));
        }
    }
?>