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
	});
</script>

<div class="form" style='text-align: center;'>
<?php echo $this->Session->flash('auth'); ?>
<fieldset>
    <legend><?php echo ('Select Visualization'); ?></legend>
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
        <a href='/sensors/heating_summary'><div class="border">
            <div id="barchart" class="circle circular-icon" style="background-image: url('/img/barchart.png')">
                <div class="opaque">
                    <div class="label-text" label-text='History'>History</div>
                </div>
            </div>
        </div></a>
        <div class="description">
            A day by day look at each room's heating demand.
        </div>
    </div>
    <div class="span6">
        <a href='/sensors/heating_piechart'><div class="border">
            <div id="piechart" class="circle circular-icon" style="background-image: url('/img/piechart.png')">
                <div class="opaque">
                    <div class="label-text" label-text='Piechart'>Piechart</div>
                </div>
            </div>
        </div></a>
        <div class="description">
            A breakdown by room of the house's heat consumption.
        </div>
    </div>
</div>