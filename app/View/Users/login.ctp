<div class="users form text-center">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo ('Please enter your email address and password'); ?></legend>
        <?php 
        	$username = array('class' => 'span3', 'label' => '', 'placeholder' => 'Email...');
        	echo $this->Form->input('email', $username);
        	$password = array('class' => 'span3', 'label' => '', 'placeholder' => 'Password...');
        	echo $this->Form->input('password', $password);
            if (isset($reset)) {
                echo "<div class=centered style='margin-bottom: 10px;'>";
                echo "<a href='/users/password_reset'>Forgot your password?</a>";
                echo "</div>";
            }
	        $options = array('label' => 'Login',
							 'class' => 'btn btn-success');
    	?>
    </fieldset>
<?php echo $this->Form->end($options); ?>
</div>
