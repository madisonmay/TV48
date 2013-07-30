<script>
    $(document).ready(function() {
        $('#UserPrice').bind('keypress', function (event) {
            var regex = new RegExp("[0-9\.]+");
            var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
            if (!regex.test(key)) {
               event.preventDefault();
               return false;
            }
        });
    });
</script>

<div class="users form text-center">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo ('Sign up for an account'); ?></legend>
        <?php 
            $first_name = array('class' => 'span3', 'placeholder' => 'John', 'label' => 'First name:');
            $last_name = array('class' => 'span3', 'placeholder' => 'Doe', 'label' => 'Last name:');
            $email = array('class' => 'span3', 'placeholder' => 'john.doe@example.com', 'label' => 'Email: ', 'type' => 'email');
        	$password = array('class' => 'span3', 'placeholder' => '•••••••••••', 'label' => 'Password: ');
            $confirm_password = array('class' => 'span3', 'type' => 'password', 'label' => 'Confirm password: ',
                                      'placeholder' => '•••••••••••', "oninput" => "checkPassword(this)");
            $property_name = array('class' => 'span3', 'type' => 'text', 'label' => 'Property name: ', 'placeholder' => 'TV48');
            $price_per_kwh = array('class' => 'span3', 'type' => 'text', 'label' => 'Price per kWh: ', 'placeholder' => '.20');
            echo $this->Form->input('first_name', $first_name);
            echo $this->Form->input('last_name', $last_name);
            echo $this->Form->input('email', $email);
            echo $this->Form->input('password', $password);
            echo $this->Form->input('confirm_password', $confirm_password);
            echo $this->Form->input('property_name', $property_name);
            echo $this->Form->input('price', $price_per_kwh);
	        $end = array('label' => 'Register',
							 'class' => 'btn btn-success');
    	?>
    </fieldset>
<?php echo $this->Form->end($end); ?>
</div>
<script>
    function checkPassword(input) {
        console.log('checking...')
        if (input.value != document.getElementById('UserPassword').value) {
            input.setCustomValidity('Passwords do not match.');
        } else {
            // input is valid -- reset the error message
            input.setCustomValidity('');
        }
    }

    $(document).ready(function () {
       $("#UserConfirmPassword").keyup(checkPassword);
    });
</script>