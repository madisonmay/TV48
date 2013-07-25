<script>
	$(function() {
		$('.redirect').click(function () {
			var page = $(this).attr('url');
			window.location = page;
		});

		$('.border').mouseover(function() {
			var label = $(this).find('.label-text');
			label.html(label.attr('label-text'));
			$(this).stop(true).animate({"borderColor": "#46a546"}, 500);
			$(this).children('.circle').children('.opaque').stop(true)
				   .animate({'opacity': '.9', '-moz-opacity': '.9', 'filter': 'Alpha(Opacity=90)',
         			         '-ms-filter': '"progid:DXImageTransform.Microsoft.Alpha(Opacity=90}"'}, 500);
		});

		$('.border').mouseout(function() {
			var label = $(this).find('.label-text');
			label.html(label.attr('label-text'));
			$(this).stop(true).animate({"borderColor": "#333333"}, 500);
			$(this).children('.circle').children('.opaque').stop(true)
				   .animate({'opacity': '0', '-moz-opacity': '0', 'filter': 'Alpha(Opacity=0)',
         			         '-ms-filter': '"progid:DXImageTransform.Microsoft.Alpha(Opacity=0}"'}, 500);
		});

		$('.img-link').mouseover(function() {
			var border = $(this).parent().parent().prev().prev().children('.border');
			var label = border.find('.label-text');
			label.html($(this).attr('label-text'));
			border.stop(true).animate({"borderColor": "#46a546"}, 500);
			border.children('.circle').children('.opaque').stop(true)
		    	  .animate({'opacity': '.9', '-moz-opacity': '.9', 'filter': 'Alpha(Opacity=90)',
		          '-ms-filter': '"progid:DXImageTransform.Microsoft.Alpha(Opacity=90}"'}, 500);
		});

		$('.img-link').mouseout(function() {
			var border = $(this).parent().parent().prev().prev().children('.border');
			var label = border.find('.label-text');
			border.stop(true).animate({"borderColor": "#333333"}, 500);
			border.children('.circle').children('.opaque').stop(true)
				  .animate({'opacity': '0', '-moz-opacity': '0', 'filter': 'Alpha(Opacity=0)',
         		  '-ms-filter': '"progid:DXImageTransform.Microsoft.Alpha(Opacity=0}"'}, 500);
		});
	});
</script>

<div class="form" style='text-align: center;'>
<?php echo $this->Session->flash('auth'); ?>
<fieldset>
    <legend><?php echo ('Manage Rental Property'); ?></legend>
</fieldset>

<!-- <div style='text-align: center;'>
    <button type='button' class='btn redirect btn-success' url='/users/tenant'>Add Tenant</button>
    <button type='button' class='btn redirect btn-success' url='/rooms/add'>Add Room</button>
    <button type='button' class='btn redirect btn-success' url='/users'>Edit Tenants</button>
    <button type='button' class='btn redirect btn-success' url='/rooms'>Edit Rooms</button>
</div> -->
</div>

<div class="row-fluid">
    <div class="span6">
        <a href='/users/profiles'><div class="border">
            <div id="tenants" class="circle">
                <div class="opaque">
                    <div class="label-text" label-text='Tenants'>Tenants</div>
                </div>
            </div>
        </div></a>
        <div class="description">
        </div>
        <div class='links'>
        	<a href='/users/tenant'><img class='img-link' src='/img/add-this.png' label-text='Add'></a>
        	<a href='/users'><img class='img-link' src='/img/edit-this.png' label-text='Edit'></a>
        	<a href='/users/profiles'><img class='img-link' src='/img/view-this.png' label-text='View'></a>
            <a href='/users/billing'><img class='img-link' src='/img/bill-this.png' label-text='Deposit'></a>
        </div>
    </div>
    <div class="span6">
        <a href='/rooms'><div class="border">
            <div id="rooms" class="circle">
                <div class="opaque">
                    <div class="label-text" label-text='Rooms'>Rooms</div>
                </div>
            </div>
        </div></a>
        <div class="description">
        </div>
        <div class='links'>
        	<a href='/rooms/add'><img class='img-link' src='/img/add-this.png' label-text='Add'></a>
        	<a href='/rooms'><img class='img-link' src='/img/edit-this.png' label-text='Edit'></a>
        </div>
    </div>
</div>