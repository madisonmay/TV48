<script>

	function twoDecimals(value) {
		return Math.round(value * 100) / 100
	}

	$('input').keyup(function(e){
	    if (/\D/g.test(this.value)){
	        // Filter non-digits from input value.
	        this.value = this.value.replace(/\D/g, '');
	    }
	});

	$(document).ready(function() {
		$('.plus').click(function() {
			var delta = $(this).prev().val();
			var user_id = $(this).parent().attr('id');
			var balance = $(this).parent().children('div');
			$(this).prev().val('');
			$.post('/users/update_balance', {'delta': delta, 'id': user_id}, function(response) {
				var value = parseFloat(response);
				balance.html('€' + twoDecimals(value));
			})
		})

		$("input").keydown(function(event) {
			// Allow only backspace and delete
			if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode = 190) {
				// let it happen, don't do anything
			}
			else {
				// Ensure that it is a number and stop the keypress
				if (event.keyCode < 48 || event.keyCode > 57 ) {
					event.preventDefault();	
				}	
			}
		});
	})
</script>

<?php 
	foreach ($users as $user) {
		echo '<div id="' . $user['User']['id'] . '" class="billing-wrapper"> <b style="display: inline-block; text-align: right; width: 50%;">';
		echo $user['User']['full_name'] . ' :</b> ';
		echo '<div class="balance" style="display: inline-block; text-align: right; width: 120px; padding: 0px;">€';
		echo number_format($user['User']['balance'], 2);
		echo '</div><button class="btn euro-symbol" disabled>€</button>';
		echo '<input type="text" name="number" placeholder="Add funds" pattern="[0-9\.]*" class="span2" style="border-radius: 0px;">';
		echo '<button class="btn btn-success plus">+</button></div>';
		echo '<br>';
	}
?>