<script>
    $(document).ready(function() {
        $("select").selectpicker();

        var types = ['lighting', 'heating', 'electricity'];

        $('.type').change(function() {
            $('.lighting').addClass('hidden');
            $('.heating').addClass('hidden');
            $('.electricity').addClass('hidden');
            var selected = $(this).find(':selected').val();
            for (var i=0; i<types.length; i++) {
                if (types[i] != selected) {
                    $('.' + selected).val('');
                }
            }
            $('.' + selected).removeClass('hidden');
        });
    });


</script>
<style>
    .hidden  {
        display: none;
    }

</style>

<div class="form text-center">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('Sensor', array('action' => 'edit', 'type' => 'get')); ?>
    <fieldset>
        <legend><?php echo ('Manage Sensors'); ?></legend>
        <? 
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
            echo '</div>';
            echo '<div class="span3 select-wrapper hidden lighting">';
            echo $this->Form->select('Lighting', $lighting, array('class' => 'span3 select lighting', 'label' => ''));
            echo '</div>';
            echo '<div class="span3 select-wrapper hidden heating">';
            echo $this->Form->select('Heating', $heating, array('class' => 'span3 select heating', 'label' => ''));
            echo '<div class="span3 select-wrapper hidden electricity">';
            echo $this->Form->select('Electricity', $electricity, array('class' => 'span3 select electricity', 'label' => ''));
            echo '</div>';
            $end = array('label' => 'Edit','class' => 'btn btn-success');
        ?>
    </fieldset>
<?php echo $this->Form->end($end); ?>

</div>

<hr>
<a href='/sensors/add' class='centered text-center'><button class='btn btn-success'>Add a new sensor</button></a>