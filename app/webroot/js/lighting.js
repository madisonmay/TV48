
//namespace to avoid polluting global namespace
var App = {
  lights: window.lights
};

Array.prototype.sortByProp = function(p){
    return this.sort(function(a,b){
        return (a[p] > b[p]) ? 1 : (a[p] < b[p]) ? -1 : 0;
    });
}

function render_page() {

    $('.row-fluid').remove();

    var window_width = $(window).width();

    //Varying numbers of columns for different window sizes
    if (window_width > 1000) {
        var num_cols = 4;
    } else if (window_width > 650) {
        var num_cols = 3;
    } else {
        var num_cols = 2;
    }

    //initialize all icons and sliders
    for (var i = 0; i < App.lights.length; i++) {

        if ((i%(num_cols)) == 0) {
            $('body').append('<div class="row-fluid"></div>');
        }

        //get the essentials from the global window object
        var brightness = App.lights[i]['pwm']/500.0;
        var room_name = App.lights[i]['location'];
        var streamId = App.lights[i]['streamId'];

        //string for templating purposes
        var room = '<div class="span' + 12/num_cols + '">' +
            '<img class="centered bulb-off" src="/img/bulb_off.png"></img>' +
            '<img class="centered up bulb-on" src="/img/bulb_on.png"></img>' +
            '<div modified=0 streamId="' + streamId + '" id="slider' + i + '" style="width: 200px; display: block; margin-left: auto; margin-right: auto; margin-top: 128px"></div>' +
            '<div class="labels">' +
                '<div>' + room_name + '</div>' +
                '<div class="brightness">Brightness:</div>' +
                '<div class="amount"></div>' +
            '</div>' +
            '</div>';

        $(".row-fluid ").last().append(room);

        //bind slider function to slider
        $("#slider" + i.toString()).slider({
            value: brightness,
            min: 0,
            max: 100,
            step: 1,
            slide: function( event, ui ) {
                $(this).next('.labels').children(".amount").html( ui.value );
                $(this).prev(".bulb-on").css({'opacity': ui.value/100.0});
                $(this).prev(".bulb-off").css({'opacity': 1-ui.value/100.0});
                $(this).attr("modified", 1);
            }
        });

        //initial slider values
        var slider = $( "#slider" + i.toString());
        var pwm = slider.slider( "value" );
        slider.next('.labels').children(".amount").html(pwm);
        slider.prev(".bulb-on").css({'opacity': pwm/100.0});
        slider.prev(".bulb-off").css({'opacity': pwm/100.0});
    }

    //slight delay needed to fix flickering issue
    setTimeout(function(){$('body').css('display', 'block')}, 100);
}

$(document).ready(function() {

  App.lights.sortByProp('pwm');
  App.lights.reverse();

  render_page();
  reset_modified();
  //Used for testing
  // var num_sliders = 8;
  // var room_names = ['Bedroom', 'Bathroom', 'Kitchen', 'Dining Room', 'Living Room'];

  function update_all(value) {
    //set all sliders to the same value
    for (var i = 0; i < App.lights.length; i++) {
        var slider = $('#slider' + i.toString());
        slider.slider("value", value);
        slider.attr('modified', 1);
        slider.next('.labels').children(".amount").html(slider.slider( "value" ));
        slider.prev(".bulb-on").css({'opacity': slider.slider( "value" )/100.0});
        slider.prev(".bulb-off").css({'opacity': slider.slider( "value" )/100.0});
    }
  }

  function reset_modified() {
    //make sure none of the lights are registered as modified
    for (var i = 0; i < App.lights.length; i++) {
        var slider = $('#slider' + i.toString());
        slider.attr('modified', 0);
    }
  }

  $('.all-on').click(function() {
    //max pwm
    update_all(100);
  })

  $('.all-off').click(function() {
    //min pwm  
    update_all(0);
  })

  $('.sort-by').change(function() {
      //make sorting through entries a little easier on the user
      if ($(this).val() === 'pwm') {
          App.lights.sortByProp('pwm');
          App.lights.reverse();
      } else if ($(this).val() === 'location') {
          App.lights.sortByProp('location');
      }
      render_page();
  })

  $('.toggle-sort').click(function() {
      //reverse list
      App.lights.reverse();
      render_page();
  })

  function retrieve_all() {
      //retrieve all pwm values for pushing to database as string
      var values = [];
      for (var i = 0; i <= App.lights.length; i++) {
          var slider = $('#slider' + i.toString());
          var value = slider.next('.labels').children(".amount").html();
          var streamId = slider.attr('streamId');
          if (slider.attr('modified') === '1') {
            values.push({'streamId': streamId, 'pwm': value});
          }
      }
      return values
  }

  $('.update').click(function() {
      //only when update is clicked are the values sent to MySQL db
      var values = retrieve_all();
      reset_modified();
      $.post('/sensors/edit_lights', {'values': values}, function(data) {
          console.log(data);
      })
  })
});