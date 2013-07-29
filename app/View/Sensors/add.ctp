<script>
    $(function() {
        $("select").selectpicker();
    })
</script>
<? echo $this->Html->script('date'); ?>
<div class="sensors form text-center">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('Sensor'); ?>
    <fieldset>
        <legend><?php echo ('Add a sensor'); ?></legend>
        <?php 
            $name = array('class' => 'span3 t5', 'placeholder' => 'Kitchen Light', 'label' => 'Sensor name: ', 'required' => true);
            $channel = array('class' => 'span3', 'placeholder' => '12', 'label' => 'Arduino channel: ', 'type'=>'text');
            $xively_id = array('class' => 'span3', 'placeholder' => '27', 'label' => 'Xively id: ', 'type' => 'text');
            echo $this->Form->input('name', $name);
            echo $this->Form->input('channel', $channel);
            echo $this->Form->input('xively_id', $xively_id);
            echo '<div class="span3 type select-wrapper" >';
            echo $this->Form->select('type', array(
                    'lighting' => 'Lighting',
                    'heating' => 'Heating',
                    'electricity' => 'Electricity'
                ),
                array(
                    'class' => 'span3 select',
                    'label' => 'Sensor type: ',
                    'required' => true
                )
            );
            echo '</div>';
            echo '<div class="span3 select-wrapper">';
            echo $this->Form->select('Rooms', $rooms, array('class' => 'span3 select', 'label' => 'Room: '));
            echo '</div>';
            $end = array('label' => 'Add', 'class' => 'btn btn-success centered', 'style' => 'display: block;');
        ?>
    </fieldset>
<?php echo $this->Form->end($end); ?>
</div>