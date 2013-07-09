<?php
    class Data extends AppModel {

        public $belongsTo = array('Sensor');
        
        public $validate = array(
            'sensor_id' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A sensor id is required'
                )
            ),
            'value' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A value is required'
                )
            ),
            'date' => array(
                'required' => array(
                    'rule' => array('notEmpty'),
                    'message' => 'A date is required'
                )
            )
        );
    }
?>