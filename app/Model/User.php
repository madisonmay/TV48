<?php

    App::uses('AuthComponent', 'Controller/Component');
    class User extends AppModel {

        public $hasOne = array('Tenant');

        public $virtualFields = array(    
            'full_name' => 'CONCAT(User.first_name, " ", User.last_name)'
        );

        public function beforeSave($options = array()) {
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
            'username' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A username is required'
                )
            )
        );
    }
?>