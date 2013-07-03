<script>
    $(document).ready(function() {
        $("select").selectpicker();

        $('.type').change(function() {
            $('.lighting').addClass('hidden');
            $('.heating').addClass('hidden');
            $('.electricity').addClass('hidden');
            var selected = $(this).find(':selected').val();
            $('.' + selected).removeClass('hidden');
        });
    });


</script>
<style>
    .hidden  {
        display: none;
    }

</style>

<div class="form" style='text-align: center;'>
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('Sensor', array('action' => 'edit', 'type' => 'get')); ?>
    <fieldset>
        <legend><?php echo ('Manage Sensors'); ?></legend>
        <? 
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
            echo '<div style="margin-right: auto; margin-left: auto; float: none;" class="span3">';
            echo $this->Form->select('Sensors', $lighting, array('class' => 'span3 select lighting', 'label' => ''));
            echo '</div>';
            echo '<div style="margin-right: auto; margin-left: auto; float: none;" class="span3 hidden">';
            echo $this->Form->select('Sensors', $heating, array('class' => 'span3 select heating', 'label' => ''));
            echo '<div style="margin-right: auto; margin-left: auto; float: none;" class="span3 hidden">';
            echo $this->Form->select('Sensors', $electricity, array('class' => 'span3 select electricity', 'label' => ''));
            echo '</div>';
            $end = array('label' => 'Edit','class' => 'btn btn-success');
        ?>
    </fieldset>
<?php echo $this->Form->end($end); ?>

</div>