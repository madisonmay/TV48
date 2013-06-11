$(document).ready(function() {
  $('.border').mouseover(function() {
    $(this).stop(true).animate({"borderColor": "#46a546"}, 500);
    $(this).children('.circle').children('.opaque').stop(true)
      .animate({'opacity': '.9', '-moz-opacity': '.9', 'filter': 'Alpha(Opacity=90)',
               '-ms-filter': '"progid:DXImageTransform.Microsoft.Alpha(Opacity=90}"'}, 500);
  });

  $('.border').mouseout(function() {
    $(this).stop(true).animate({"borderColor": "#333333"}, 500);
    $(this).children('.circle').children('.opaque').stop(true)
      .animate({'opacity': '0', '-moz-opacity': '0', 'filter': 'Alpha(Opacity=0)',
               '-ms-filter': '"progid:DXImageTransform.Microsoft.Alpha(Opacity=0}"'}, 500);
  });
});