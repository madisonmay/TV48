<?php

    App::uses('AuthComponent', 'Controller/Component');
    class User extends AppModel {

        public $hasAndBelongsToMany = array('Room');
        public $hasMany = array('Contract', 'BalanceUpdate');

        public $virtualFields = array(    
            'full_name' => 'CONCAT(User.first_name, " ", User.last_name)'
        );

        public function beforeSave($options = array()) {
            parent::beforeSave();
            $email = $this->data['User']['email'];
            $user = $this->find('first', array('conditions' => array('email' => $email)));
            if ($user && $user['User']['id'] != $this->data['User']['id']) {
                $this->lastErrorMessage = 'A user with the email "' . $email . '" already exists.';
                return false;
            } 
            if (isset($this->data[$this->alias]['password'])) {
                $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
            }
            return true;
        }

        public $validate = array(
            'first_name' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A first name is required'
                )
            ),
            'last_name' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A last name is required'
                )
            ),
            'email' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A username is required'
                )
            )
        );
    }
?>