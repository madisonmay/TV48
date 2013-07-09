<style>
	#cal-heatmap {
		margin-top: 200px; 
		display: block; 
		width: 1164px; 
		margin-left: auto; 
		margin-right: auto;
		/*border: 2px solid black;*/
	}
</style>

<div id="cal-heatmap"></div>
<link rel='stylesheet' href='/css/cal-heatmap.css'>
<script src='/js/cal-heatmap.min.js'></script>
<script type="text/javascript">
$(document).ready(function() {
	$.ajax({
	    url: '/files/datas-years.json'
	}).done(function (data) {
	    ///////////////////////////////////////////////////
	    // Edit from here    

		var calendar = new CalHeatMap();
		calendar.init({
			data: data,
			start: new Date(2000, 0),
			domain : "year",
			subDomain : "day",
			range : 1,
			cellsize: 20,
			scale: [40, 60, 80, 100]
		});

	    // End edition    
	    ///////////////////////////////////////////////////
	});
})
</script>