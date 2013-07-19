$(document).ready(function() {
	//very sketchy, but currently necessary
	var pos = parseFloat($('.nv-x g g .nv-axislabel').attr('x')) - 30;
	setTimeout(function() {$('.nv-x g g .nv-axislabel').attr('x', pos)}, 100);
})