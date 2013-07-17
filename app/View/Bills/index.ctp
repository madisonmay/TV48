<div class="billing form text-center">
<?php echo $this->Form->create(); ?>
    <fieldset>
        <legend><?php echo ('Generate Invoices'); ?></legend>
        <?php 
        	$electricity_cost = array('class' => 'span3', 'label' => '', 'placeholder' => 'Electricity cost...', 'type' => 'text', 'required' => true);
        	echo $this->Form->input('electricity_cost', $electricity_cost);
        	$heating_cost = array('class' => 'span3', 'label' => '', 'placeholder' => 'Heating cost...', 'type' => 'text', 'required' => true);
        	echo $this->Form->input('heating_cost', $heating_cost);
            $start_date = array('class' => 'span3', 'label' => '', 'placeholder' => 'Start date...', 'required' => true);
            echo $this->Form->input('start_date', $start_date);
            $end_date = array('class' => 'span3', 'label' => '', 'placeholder' => 'End date...');
            echo $this->Form->input('end_date', $end_date);
	        $options = array('label' => 'Process',
							 'class' => 'btn btn-success');
    	?>
    </fieldset>
<?php echo $this->Form->end($options); ?>
</div>