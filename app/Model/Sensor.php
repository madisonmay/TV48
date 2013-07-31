<?php

    App::uses('AuthComponent', 'Controller/Component');
    class Sensor extends AppModel {

        public $belongsTo = array('Room');
        public $hasMany = array('Data' => array(
            'order' => 'Data.created ASC'
        ), 'BalanceUpdate');

        public function beforeSave() {
            parent::beforeSave();
            if (isset($this->data['Sensor']['xively_id'])) {
                $xively_id = $this->data['Sensor']['xively_id'];
                $sensor = $this->find('first', array('conditions' => array('xively_id' => $xively_id)));
                if ($sensor && $sensor['Sensor']['id'] != $this->data['Sensor']['id']) {
                    $this->lastErrorMessage = 'A sensor with xively_id "' . $xively_id . '" already exists.';
                    return false;
                } 
            }

            if (isset($this->data['Sensor']['name']) && isset($this->data['Sensor']['type'])) {
                $name = $this->data['Sensor']['name'];
                $type = $this->data['Sensor']['type'];
                $sensor = $this->find('first', array('conditions' => array('Sensor.name' => $name, 'Sensor.type'=>$type)));
                if ($sensor && $sensor['Sensor']['id'] != $this->data['Sensor']['id']) {
                    $this->lastErrorMessage = 'A sensor with name "' . $name . '" and type "' . $type . '" already exists.';
                    return false;
                } 
            }

            return true;
        }

        public $validate = array(
            'name' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A sensor is required'
                )
            ),
            'type' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A type is required'
                )
            ),
            'channel' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A channel is required'
                )
            ),
            'room_id' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'An associated room is required'
                )
            )
        );
    }
?>