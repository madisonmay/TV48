<script>
    $(function() {
        $(".date").blur(function() {
            var date = Date.parse($(this).val()).toString('MMMM d, yyyy')
            $(this).val(date);
        });

        $("select").selectpicker();
    })
</script>
<? echo $this->Html->script('date'); ?>
<div class="sensors form" style='text-align: center;'>
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('Sensor'); ?>
    <fieldset>
        <legend><?php echo ('Add a sensor'); ?></legend>
        <?php 
            $name = array('class' => 'span3', 'placeholder' => 'Sensor name...', 'label' => '', 'required' => true);
            $channel = array('class' => 'span3', 'placeholder' => 'Arduino channel...', 'label' => '', 'type'=>'text');
            $xively_id = array('class' => 'span3', 'placeholder' => 'Xively id...', 'label' => '', 'type' => 'text');
            echo $this->Form->input('name', $name);
            echo $this->Form->input('channel', $channel);
            echo $this->Form->input('xively_id', $xively_id);
            echo '<div style="margin-right: auto; margin-left: auto; float: none; margin-top: 6px; margin-bottom: 6px;" class="span3">';
            echo $this->Form->select('Rooms', $rooms, array('class' => 'span3 select', 'label' => ''));
            echo '</div>';
            echo '<div style="margin-right: auto; margin-left: auto; float: none;" class="span3 type" >';
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
            echo '</div>';
            $end = array('label' => 'Add', 'class' => 'btn btn-success');
        ?>
    </fieldset>
<?php echo $this->Form->end($end); ?>
</div>