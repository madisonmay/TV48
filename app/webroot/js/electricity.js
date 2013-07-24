nv.dev = false;
var convert_time = {'seconds': 1, 'minutes': 60, 'hours': 3600, 'days': 86400,
    'weeks': 604800, 'months': 86400*30, 'years': 365*86400};

var convert_cost = {'Euro cents per Hour': .0085, 'Euros per Month': 8765 / 1200 * .0085, 'Euros per Year': 8765 / 100 * .0085,
                    'Grams of CO2 per Hour': .76,  'Kg of CO2 per Day': .76*24/1000, 'Kg of CO2 per Month': .76*24*30/1000, 
                    'Kg of CO2 per Year': .76*24*365/1000, 'Watts': 1};

function keys(obj) {
  var keys = [];
  for(var k in obj) {
    keys.push(k);
  }
  return keys;
}

function update_values(svg_id, ratio) {
  for (var i = 0; i < window.data[svg_id].length; i++) {
    window.data[svg_id][i]['y'] *= ratio; 
  }
}

function next_chart(svg_id) {
  update_values(svg_id, convert_cost[window.units]);

  //nvd3 magic -- see nvd3 docs for details
  svg_id = svg_id.toString();
  $('#chart').html('');
  $('#chart').append('<svg id="id'+svg_id+'"></svg>');
  nv.addGraph(function() {
    var chart = nv.models.lineChart().margin({left: 80, bottom: 50})
                  .tooltipContent(function(key, y, e, graph) { return '<h3>' + e + ' ' + window.units + '</h3>' })

    //chart formatting
    chart.xAxis
        .tickFormat(function(d) {
          return d3.time.format("%H:%M")(new Date(d));
         })

    // // Add in this code if you want to switch to a format with zoom.
    // chart.x2Axis
    //     .axisLabel('')
    //     .tickFormat(function(d) {
    //       return d3.time.format("%b %d")(new Date(d));
    //      });

    chart.yAxis
        .tickFormat(d3.format('.00f'));

    // // Add in this code if you want to switch to a format with zoom.
    // chart.y2Axis
    //     .tickFormat(d3.format('.00f'));

    //pass data to chart, specify transition length, initialize chart
    var chart_id = '#chart svg#' + 'id' + svg_id;
    d3.select(chart_id)
        .datum([{key: window.data_names[svg_id], values: window.data[svg_id].slice(0, window.data_length-1)}])
      .transition().duration(500)
        .call(chart);

    var w = parseFloat(d3.select(chart_id).style("width"));
    var h = parseFloat(d3.select(chart_id).style("height"));
    console.log(w, h);

    d3.select(chart_id)
      .append("text")
        .attr("x", w/2)
        .attr("y", 18)
        .attr("style", "font-size: 25px !important")
        .style("text-anchor", "middle")
        .text("Power Consumption");

    d3.select(chart_id)
      .append("text")
        .attr("x", w/2)
        .attr("y", h-5)
        .attr("style", "font-size: 20px !important")
        .style("text-anchor", "middle")
        .text("Time");

    d3.select(chart_id)
      .append("text")
        .attr("transform", "rotate(-90)")
        .attr("y", 40)
        .attr("x", 0 - (h/2))
        .attr("style", "font-size: 20px !important")
        .style("text-anchor", "middle")
        .text("Watts");

   //update chart on window resize
    nv.utils.windowResize(chart.update);
    return chart;
  });
}

//simple wrapper for plotting
function populate_graph(svg_id) {
  //for live updating -- only happens when duration of window is < 10 minutes
  window.new_time = window.times[svg_id][0];
  window.new_point = window.data[svg_id][0];

  //do not update chart if new information is not present
  if (window.last_time === undefined) {
    next_chart(svg_id);
  }

  //last point does not exist, definitely update chart
  else if (window.last_point === undefined) {
    next_chart(svg_id);
  }

  //times do not match -- indicating new information
  else if (window.new_time != window.last_time) {
    next_chart(svg_id);
  }

  //datapoints do not match -- indicating new information
  else if (window.new_point != window.lastPoint) {
    next_chart(svg_id);
  }

  //update times to reflect the current state
  window.last_time = window.new_time;
  window.last_point = window.new_point;
}

function render_page(svg_id) {

  //check DOM elements for relevant values
  var feed_val = $('#feed').val();
  var units = $('#units').val();
  var duration = $('#duration').val();
  var seconds = convert_time[units] * duration;

  //called on page load -- sets params based on clients computer
  //and calls helper functions to render data
  var height = $(window).innerHeight() - 150;
  var width = $(window).width();

  //avoid flickering by changing properties and then making chart visible
  $('#chart').css('height', height*.85);
  $('#chart').css('width', width - 65);
  $('#chart').css('display', 'block');

  //remove feed html to prepare for addition of new feed value
  $('#feed').html('');
  //Populate feed select with datastream values
  //May eventually want to compose string and then add it all at once to
  //speed up the process a bit.
  for (var i = 0; i < data_ids.length; i++) {
    if (!$('option[value=' + window.data_ids[i] + ']').length) {
      $('#feed').append('<option value="' + window.data_ids[i] + '">' + window.data_names[window.data_ids[i]] + '</option>');
    }
  }

  //update feed value and populate graph with the appropriate information
  $('#feed').val(feed_val);
  populate_graph(svg_id);

  //When the feed, duration, or units change, update the graph
}

//called when the desired time period or feed changes
function update_graph() {

  //make loading icon visible -- it's been there the whole time
  $('#loading').css('display', 'block');
  $('#settings').css('display', 'none');

  //as indicated, called when graph state must be updated
  //variables from sliders and inputs are pulled
  var feed = $('#feed').val();
  var units = $('#units').val();
  var duration = $('#duration').val();
  var duration_in_seconds = convert_time[units] * parseFloat(duration);

  //a bit of processing to ensure that the date range is not too large
  if (duration_in_seconds > timeDiff) {
    var delta = Math.ceil(window.timeDiff / convert_time[units]);
    $('#duration').val(delta);
  }

  //Live mode -- updates every 5 seconds and refreshes the graph
  //Refresh isn't noticeable unless the graph has actually been updated
  if (duration_in_seconds < 600) {
    setTimeout(function() {update_graph();}, 5000);
  }

  //more variables are grabbed from DOM elements
  var streamId = $('#feed').val().toString();
  var duration = $('#duration').val().toString() + $('#units').val().toString();
  //send jquery post request
  $.post('/sensors/refresh', {'streamId': streamId, 'duration': duration}, function(data) {
    var data = JSON.parse(data);
    //data attached to window object to act as global vars
    window.times = data.times;
    window.data = data.data;
    window.data_length = data.data_length;

    //data is reformatted -- will eventually remove and streamline this step
    prepare_data();

    //graph is populated and rerendered
    populate_graph(streamId);
    $('#loading').css('display', 'none');
    $('#settings').css('display', 'block');
  });
}

//format conversion -- perhaps could be done server side
function prepare_data() {
  //this is the format that nv.d3 accepts
  for(var stream in window.data) {
    for (var i = 0; i < 100; i++) {
        window.data[stream][i] = {x: window.times[stream][i], y: parseInt(window.data[stream][i], 10)};
    }
  }
}

//bind function handlers to events that should change the set of points plotted

$('#feed').change(function() {
  if (window.previous_value != $(this).val()) {
    window.previous_value = $(this).val()
    update_graph();
  }
});

$('#duration').change(function() {
  update_graph();
});

$('#units').change(function() {
  update_graph();
});

$('#duration').keypress(function(e) {
  if(e.keyCode == 13) {
    $('#duration').trigger('change');
  }
})


$('.unit-change').click(function() {
  //simple change in graph scale
  var svg_id = $('#feed').val().toString();
  update_values(svg_id, 1.0/convert_cost[window.units]);
  window.units = $('#graph-units').val();
  render_page(svg_id);
  $('#myModal').modal('hide');
});

$(document).ready(function() {
  // $('select').fadeTo(0, 0);
  //calculate difference between the time when the feed was created and the current time
  var date_created = new Date(created);
  var current_date = new Date();

  //make global var to save this property
  window.units = 'Watts';
  window.timeDiff = (current_date.getTime() - date_created.getTime())/1000;

  $('#units').val("hours");
  $('#duration').val("6");
  $('#feed').val(keys(window.data)[0]);
  window.previous_value = keys(window.data)[0];
  $('select').selectpicker();

  //hacky fix to resolve display issues with selectpicker
  $('.select').fadeTo(0, 0);
  setTimeout(function() {
    $('.select').fadeTo(0, 1);}, 400);

  prepare_data();
  render_page(keys(window.data)[0]);
});

//dynamic plot resizing based on screen size
$(window).resize(function() {
  var svg_id = $('#feed').val().toString();
  render_page(svg_id);
})

$(window).ready(function(){
  $('#feed').selectpicker('val', keys(window.data)[0]);
})

$(window).resize(function() {
  $('#feed').selectpicker('val', keys(window.data)[0]);
})