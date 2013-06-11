function render_page() {
	//called on page load and window resize
	
	var window_width = $(window).width();

	//Varying numbers of columns for different window sizes
	if (window_width > 1400) {
	    var num_cols = 4;
	} else if (window_width > 1100) {
	    var num_cols = 3;
	} else {
	    var num_cols = 2;
	}

	//simple function to aid with string templating in javascript
	function template(string,data){
        return string.replace(/%(\w*)%/g,function(m,key){
          return data.hasOwnProperty(key)?data[key]:"";
        });
    }

    //setting options for public and restricted by default -- as the rest are pulled from a user list
	var options = "<option value='-1'>Restricted</option><option value='public'>Public</option>";
	var option = "<option value='%id%'>%name%</option>";

	//for each tenant, append an entry to the list of select options
	for (var i = 0; i < window.tenants.length; i++) {
		var tenant = window.tenants[i];
		var option_html = template(option, {'id': tenant['id'], 'name': tenant['name']});
		var options = options + option_html;
	}

	//basic html template for every room
	//right now light objects are used as a replacement, as we have not yet reorganized the database
	//perhaps a seperate page for light, power, and heat management is in order if room objects are
	//not created
	var room_template = "<div class='span%n% room-wrapper'>" +
	            "<div class='room-settings'>" +
	                "<div class='text-block'>" +
	                    "<span class='vert-text-align' contenteditable='false'>%location%</span>" +
	                "</div>" +
	                "<div id='settings-block'>" +
	                    "<img class='privacy' role='button' class='centered' src='images/%locked%.png'>" +
	                "</div>" +
	                "<hr class='thin-hr'>" +
	                "<div class='text-block'>" +
	                    "<select class='vert-text-align assigned-user'contenteditable='false'>" + options + 
	                    "</select>" +
	                "</div>" +
	            "</div>" +
	        "</div>";

	//delete existing content
	$('#content').html('');
	//set display to none to help prevent flickering
	$('#content').css('display', 'none');

	//build html up
	for (var i = 0; i < window.lights.length; i++) {

		//grab current light
		var light = window.lights[i];

		//create html to match the current light object
		var light_html = template(room_template, {'location': light['location'], 'locked': light['public'], 'n': 12/num_cols});

		//based on width of screen, set number of columns
		if (i % num_cols === 0) {
			//create a new row every num_columns additions
			$('#content').append('<div class="row-fluid">');
			$(".row-fluid ").last().append(light_html);

			//make sure the proper option is marked as selected
			if (light['userId']) {
				$(".vert-text-align").last().children("option[value='" + light['userId'] + "']").attr('selected', 'selected');
			} else if (light['public'] == 'locked') {
				$(".vert-text-align").last().children("option[value='-1']").attr('selected', 'selected');
			} else {
				$(".vert-text-align").last().children("option[value='public']").attr('selected', 'selected');
			}

		} else {
			//append to current row
			$(".row-fluid ").last().append(light_html);

			//make sure the proper option is marked as selected
			if (light['userId']) {
				$(".vert-text-align").last().children("option[value='" + light['userId'] + "']").attr('selected', 'selected');
			} else if (light['public'] == 'locked') {
				$(".vert-text-align").last().children("option[value='-1']").attr('selected', 'selected');
			} else {
				$(".vert-text-align").last().children("option[value='public']").attr('selected', 'selected');
			}
		}
	}

	//use combobox plugin
	$('.assigned-user').combobox();

	//default font was a bit ugly -- changed to Lato
	$('.assigned-user').css('font-face', 'Lato');

	//should eventually send request to server here and change item in database
	$('.privacy').on('click', function() {
		if ($(this).attr('src') === 'images/locked.png') {
			$(this).attr('src', 'images/public.png');
		} else {
			$(this).attr('src', 'images/locked.png');
		}
	});

	// Not currently functional!  Need to figure this problem out before saving to db
	$('.assign-user').change(function() {
		console.log("Logging selected value: ");
		console.log($('this :selected').text());
	});

	//display content after html string is composed and html is added to DOM
	$('#content').css('display', 'block');
};

$(document).ready(function() {
    render_page();
})

//make sure page resizing doesn't cause issues.  
$(window).resize(function() {
    render_page();
});