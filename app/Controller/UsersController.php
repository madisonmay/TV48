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
                if ($this->User->save($this->request->data)) {

                    // If flashWarning is set to 0, the btn-success class is added to the resultant message.
                    // Otherwise, the btn-danger class is added.
                    $code = rand();
                    $this->Session->write('flashWarning', 0);
                    $this->Session->setFlash(__('Thanks for registering! Please check your inbox for a confirmation email.'));

                    //Email composition process -- will not work locally because of mail server configuration
                    $to=$this->request->data['User']['username'];

                    $subject = 'CORE registration';

                    $message = "
                        <html>
                            <head>
                                <title>CORE Registration</title>
                            </head>
                            <body>
                                <h1>Thanks for registering, ".$this->request->data['User']['first_name']."</h1>
                                <p>Your information is as follows:</p>
                                <p>Username: ".$this->request->data['User']['username']."</p>
                                <br>
                                <p>Click the link below to confirm your registration:</p>
                                <a href=http://www.thinkcore.be/TV48/confirmation.php?code=".$code."&email=".$this->request->data['User']['first_name'].">";

                    // To send HTML mail, the Content-type header must be set
                    $headers  = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                    // Additional headers
                    $headers .= 'From: CORE_cvba-so' . "\r\n";

                    // Mail it
                    mail($to, $subject, $message, $headers);

                    // Redirect user to hompage
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
                    $this->Session->setFlash(__('Invalid username or password, try again'));
                }
            }
        }

        public function logout() {
            $this->redirect($this->Auth->logout());
        }
    }
?>