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
            echo '</div>';
            echo '<div class="span3 type select-wrapper" >';
            echo $this->Form->select('type', array(
                    'lighting' => 'Lighting',
                    'heating' => 'Heating',
                    'electricity' => 'Electricity'
                ),
                array(
                    'class' => 'span3 select',
                    'label' => '',
                    'required' => true
                )
            );
            $name = array('class' => 'span3', 'placeholder' => 'Sensor name...', 'label' => '', 'required' => true);
            $channel = array('class' => 'span3', 'placeholder' => 'Arduino channel...', 'label' => '', 'type'=>'text');
            $xively_id = array('class' => 'span3', 'placeholder' => 'Xively id...', 'label' => '', 'type' => 'text');
            echo '<div class="span3 select-wrapper">';
            echo $this->Form->select('Rooms', $rooms, array('class' => 'span3 select', 'label' => ''));
            echo '</div>';
            echo $this->Form->input('name', $name);
            echo $this->Form->input('channel', $channel);
            echo $this->Form->input('xively_id', $xively_id);
            $end = array('label' => 'Add', 'class' => 'btn btn-success centered', 'style' => 'display: block;');
        ?>
    </fieldset>
<?php echo $this->Form->end($end); ?>
</div>