<script>
	$(function() {
		$('.redirect').click(function () {
			var page = $(this).attr('url');
			window.location = page;
		});
	});
</script>

<div class="form" style='text-align: center;'>
<?php echo $this->Session->flash('auth'); ?>
<fieldset>
    <legend><?php echo ('Manage Rental Property'); ?></legend>
</fieldset>
<div style='text-align: center;'>
    <button type='button' class='btn redirect btn-success' url='/users/tenant'>Add Tenant</button>
    <button type='button' class='btn redirect btn-success' url='/rooms/add'>Add Room</button>
    <button type='button' class='btn redirect btn-success' url='/users'>Edit Tenants</button>
    <button type='button' class='btn redirect btn-success' url='/rooms'>Edit Rooms</button>
</div>
</div>