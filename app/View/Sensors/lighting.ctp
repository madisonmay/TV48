<script>
	$(function() {
	    $("select").selectpicker();
	})
</script>

<style>
    /*body initially hidden to prevent flickering*/
    body {
        display: none;
    }

    .update {
    	width: 0px;
    	border-width: 0px;
    	padding: 0px;
    	margin: 0px;
    }

    .switch {
    	position: relative;
    	z-index: 100000;
    	cursor: pointer;
    }

    .off {
    	left: 50px;
    	top: 85px;
    }

    .on {
    	left: 250px;
    	top: 85px;
    }

</style>

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