<div class="users form text-center">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo ('Sign up for an account'); ?></legend>
        <?php 
        	$password = array('class' => 'span3', 'placeholder' => '•••••••••••', 'label' => 'Password: ');
            $confirm_password = array('class' => 'span3', 'type' => 'password', 'label' => 'Confirm password: ',
                                      'placeholder' => '•••••••••••', "oninput" => "checkPassword(this)");
            echo $this->Form->input('password', $password);
            echo $this->Form->input('confirm_password', $confirm_password);
            echo $this->Form->input('id');
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