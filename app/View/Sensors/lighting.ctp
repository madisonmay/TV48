<script>
	$(function() {
	    $("select").selectpicker();
	})
</script>

<div class='top_group'>
    <button class='btn all-off'>All Off</button>
    <button class='btn btn-success update'>Update</button>
    <button class='btn all-on'>All On</button>
</div>
<div class='bottom_group'>
	<select class='sort-by'>
	      <option value='pwm'>Sort by brightness</option>
	      <option value='location'>Sort by name</option>
	</select>
	<!--     <button class='btn toggle-sort'>↑↓</button> -->
</div>