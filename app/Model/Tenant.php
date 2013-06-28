<?php

    App::uses('AuthComponent', 'Controller/Component');
    class Tenant extends AppModel {

        public $belongsTo = array('User', 'Property');
        public $hasAndBelongsToMany = array('Room');

        public function beforeSave($options = array()) {
            if (isset($this->data[$this->alias]['password'])) {
                $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
            }
            return true;
        }

        public $validate = array(
            'start_date' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A start date is required'
                )
            ),
            'end_date' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'An end date is required'
                )
            ),
            'property_id' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'Propery id is required'
                )
            )
        );
    }
?>