<?php

    App::uses('AuthComponent', 'Controller/Component');
    class Room extends AppModel {

        public $hasAndBelongsToMany = array('User');
        public $hasMany = array('Contract', 'Sensor', 'BalanceUpdate');

        public function beforeSave() {
            parent::beforeSave();
            $room_name = $this->data['Room']['name'];
            $room = $this->find('first', array('conditions' => array('name' => $room_name)));
            if ($room && $room['Room']['id'] != $this->data['Room']['id']) {
                $this->lastErrorMessage = 'A room named ' . $room_name . ' already exists.';
                return false;
            } 
            return true;
        }

        public $validate = array(
            'name' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A room name is required'
                )
            ),
            'type' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A type is required'
                )
            )
        );
    }
?>