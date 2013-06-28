<script>
	$(function() {
		$('.redirect').click(function () {
			var page = $(this).attr('url') + '?property=' + String(<? echo $property['Property']['id']; ?>);
			window.location = page;
		});

	});
</script>

<div class="property form" style='text-align: center;'>
<?php echo $this->Session->flash('auth'); ?>
<fieldset>
    <legend><?php echo ('Modify Rental Property'); ?></legend>
    <button type='button' class='btn span4 center-pad' value=<? echo $property['Property']['id']; ?>><? echo $property['Property']['name']; ?></button>
</fieldset>
<div style='text-align: center;'>
    <button type='button' class='btn redirect btn-success' url='/tenants/add'>Add Tenant</button>
    <button type='button' class='btn redirect btn-success' url='/rooms/add'>Add Room</button>
    <button type='button' class='btn redirect btn-success' url='/tenants'>Edit Tenants</button>
    <button type='button' class='btn redirect btn-success' url='/rooms'>Edit Rooms</button>
</div>
</div>