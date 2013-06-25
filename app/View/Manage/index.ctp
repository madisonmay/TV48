<div class="property form" style='text-align: center;'>
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('Property'); ?>
    <fieldset>
        <legend><?php echo ('Manage Rental Properties'); ?></legend>
        <?
            echo $this->Form->input('name', array('class' => 'span3', 'placeholder' => 'Property name...', 'label' => ''));
            $end = array('label' => 'Edit','class' => 'btn btn-success');
    	?>
    </fieldset>
<?php echo $this->Form->end($end); ?>