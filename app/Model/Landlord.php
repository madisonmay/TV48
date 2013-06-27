<?php

    class Landlord extends AppModel {

        public hasMany = array("Property", "Tenant");
        public belongsTo = "Landlord";

    }
?>