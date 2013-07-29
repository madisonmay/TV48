<script>
    $(function() {
        $(".date").blur(function() {
            var date = Date.parse($(this).val()).toString('MMMM d, yyyy')
            $(this).val(date);
        });
        
        $('.double').bind('keypress', function (event) {
            var regex = new RegExp("[0-9\.]+");
            var key = String.fromCharCode(!event.charCode ? event.which : event.charCode);
            if (!regex.test(key)) {
               event.preventDefault();
               return false;
            }
        });

        $("select").selectpicker();
        $("select").selectpicker('val', '<?php echo $primary_contract["room_id"]; ?>')
    })
</script>
<? echo $this->Html->script('date'); ?>
<div class="users form text-center">
<?php echo $this->Session->flash('auth'); ?>
<?php echo $this->Form->create('User'); ?>
    <fieldset>
        <legend><?php echo ('Edit tenant'); ?></legend>
        <?php 
            $first_name = array('class' => 'span3', 'placeholder' => 'John', 'label' => 'First name: ', 'required' => true);
            $last_name = array('class' => 'span3', 'placeholder' => 'Doe', 'label' => 'Last name: ','required' => true);
            $username = array('class' => 'span3', 'placeholder' => 'john.doe@example.com', 'label' => 'Email: ',
                              'type' => 'email', 'required' => true);
            $start_date = array('class' => 'span3 date', 'placeholder' => 'August 1, 2013', 'label' => 'Start date: ',
                                'type' => 'text', 'required' => true);
            $end_date = array('class' => 'span3 date', 'placeholder' => 'May 1, 2014', 'label' => 'End date: ',
                              'type' => 'text', 'required' => true);
            $balance = array('class' => 'span3', 'placeholder' => '124.12', 'label' => 'Balance: ', 'type' => 'text');
            $price = array('class' => 'span3 double', 'type' => 'text', 'label' => 'Price per kWh: ', 
                           'placeholder' => '.20', 'pattern' => '[0-9\.]+');
            echo $this->Form->input('first_name', $first_name);
            echo $this->Form->input('last_name', $last_name);
            echo $this->Form->input('email', $username);
            echo $this->Form->input('start_date', $start_date);
            echo $this->Form->input('end_date', $end_date);
            echo $this->Form->input('balance', $balance);
            echo $this->Form->input('price', $price);
            echo '<div class="span3 select-wrapper">';
            echo $this->Form->select('Rooms', $rooms, array('class' => 'span3 select', 'label' => 'Room: '));
            echo '</div>';
            echo $this->Form->input('id');
            $end = array('label' => 'Save', 'class' => 'btn btn-success');
        ?>
    </fieldset>
<?php echo $this->Form->end($end); ?>
</div>