function render_page() {



	var window_width = $(window).width();

	//Varying numbers of columns for different window sizes
	if (window_width > 1400) {
	    var num_cols = 4;
	} else if (window_width > 1100) {
	    var num_cols = 3;
	} else {
	    var num_cols = 2;
	}

	console.log(window_width, num_cols);

	function template(string,data){
        return string.replace(/%(\w*)%/g,function(m,key){
          return data.hasOwnProperty(key)?data[key]:"";
        });
    }


	var options = "<option value='-1'>Restricted</option><option value='public'>Public</option>";
	var option = "<option value='%id%'>%name%</option>";

	for (var i = 0; i < window.tenants.length; i++) {
		var tenant = window.tenants[i];
		var option_html = template(option, {'id': tenant['id'], 'name': tenant['name']});
		var options = options + option_html;
	}

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

	$('#content').html('');
	$('#content').css('display', 'none');

	for (var i = 0; i < window.lights.length; i++) {
		var light = window.lights[i];
		var light_html = template(room_template, {'location': light['location'], 'locked': light['public'], 'n': 12/num_cols});
		if (i % num_cols === 0) {
			$('#content').append('<div class="row-fluid">');
			$(".row-fluid ").last().append(light_html);
			if (light['userId']) {
				$(".vert-text-align").last().children("option[value='" + light['userId'] + "']").attr('selected', 'selected');
			} else if (light['public'] == 'locked') {
				$(".vert-text-align").last().children("option[value='-1']").attr('selected', 'selected');
			} else {
				$(".vert-text-align").last().children("option[value='public']").attr('selected', 'selected');
			}
		} else {
			$(".row-fluid ").last().append(light_html);
			if (light['userId']) {
				$(".vert-text-align").last().children("option[value='" + light['userId'] + "']").attr('selected', 'selected');
			} else if (light['public'] == 'locked') {
				$(".vert-text-align").last().children("option[value='-1']").attr('selected', 'selected');
			} else {
				$(".vert-text-align").last().children("option[value='public']").attr('selected', 'selected');
			}
		}
	}

	$('.assigned-user').combobox();
	$('.assigned-user').css('font-face', 'Lato');

	$('.privacy').on('click', function() {
		if ($(this).attr('src') === 'images/locked.png') {
			// $(this).attr('src', 'images/public.png');
		} else {
			$(this).attr('src', 'images/locked.png');
		}
	});

	$('.assign-user').change(function() {
		console.log("Logging selected value: ");
		console.log($('this :selected').text());
	});

	$('#content').css('display', 'block');
};

$(document).ready(function() {
    render_page();
})

$(window).resize(function() {
    render_page();
});