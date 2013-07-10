$(document).ready(function() {
  window.timer_id = 0;
  window.reload_timer_id = 0;
  window.notepad_open = false;
  nv.dev = false;
  var average = [36, 35, 34, 21, 28, 28, 6, 9, 15, 31, 35, 24];
  var actual = [40, 36, 45];

  function sum(array) {
      var sum = 0;
      for (var i=0; i<array.length; i++) {
          sum = sum + array[i];
      }
      return sum;
  }

  function expected_absolute(actual, average) {
      var result = actual.slice(0);
      var difference = 0;
      for (var i = 0; i < actual.length; i++) {
          difference = difference + actual[i] - average[i];
      }
      var average_difference = difference/actual.length;
      for (var i = actual.length; i < average.length; i++) {
          var entry = average[i] + average_difference;
          if (entry > 0) {
              result.push(entry); 
          } else {
              result.push(0);
          }
      }
      console.log("Absolute: ", result);
      return result;
  }

  function expected_percentage(actual, average) {
      var result = actual.slice(0);
      var difference = 0;
      for (var i = 0; i < actual.length; i++) {
          difference = difference + actual[i]/average[i];
      }
      var average_difference = difference/actual.length;
      for (var i = actual.length; i < average.length; i++) {
          result.push(average[i]*average_difference);
      }
      console.log("Percentage: ", result);
      return result;
  }

  function expected_hybrid(actual, average) {
      var result = [];
      var percentage = expected_percentage(actual, average);
      var absolute = expected_absolute(actual, average);
      for (var i = 0; i<absolute.length; i++) {
          result.push((absolute[i] + percentage[i])/2);
      }
      console.log("Hybrid: ", result);
      return result;
  }

  function paired(array) {
      var result = [];
      for (var i = 0; i < array.length; i++) {
          result.push({x: i + 1, y: array[i]});
      }
      return result;
  }


  $(document).ready(function() {
      console.log("Actual: ", actual);
      console.log("Average: ", average);
      var absolute = expected_absolute(actual, average);
      var percentage = expected_percentage(actual, average);
      var hybrid = expected_hybrid(actual, average);

      var absolute_object = paired(absolute);
      var percentage_object = paired(percentage);
      var hybrid_object = paired(hybrid);
      var average_object = paired(average);

      var expected_yearly = sum(hybrid);
      var average_yearly = sum(average);

      nv.addGraph(function() {
        var chart = nv.models.multiBarChart();

        chart.xAxis
            .axisLabel('Month')
            .tickFormat(d3.format(',r'));

        chart.yAxis
            .axisLabel('Energy Expenditure (KWh)')
            .tickFormat(d3.format('.02f'));

        chart.showControls(false).stacked(false);

        d3.select('#chart svg')
            .datum([
                      {
                        values: absolute_object,
                        key: 'Absolute',
                        color: '#ff7f0e'
                      },
                      {
                        values: percentage_object,
                        key: 'Percentage',
                        color: '#2ca02c'
                      }, 
                      {
                        values: hybrid_object,
                        key: 'Hybrid',
                        color: '#2f4f4f'
                      }, 
                      {
                        values: average_object,
                        key: 'Average',
                        color: '#aa6600'
                      }, 
                   ])
            .transition().duration(500)
            .call(chart);


        nv.utils.windowResize(chart.update);

        return chart;
      });
  })

  function template(string,data){
      return string.replace(/%(\w*)%/g,function(m,key){
          return data.hasOwnProperty(key)?data[key]:"";
      });
  }

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

  $('#footer-tab').click(function() {
    if (window.notepad_open) {
      $(this).animate({'bottom': '-=520'}, 1000);
      $('#footer').animate({'bottom': '-=520'}, 1000);
      window.notepad_open = false;
    } else {
      $(this).animate({'bottom': '+=520'}, 1000);
      $('#footer').animate({'bottom': '+=520'}, 1000);
      window.notepad_open = true;
    }
  });

  $('#footer').keydown(function() {
    clearInterval(window.timer_id);
    clearInterval(window.reload_timer_id);

    //update after three seconds of not typing
    window.timer_id = setTimeout(function() {
      $.post('/home/notepad', {'content': $('#footer').html()}, function(response) {
        console.log(response);
      });
    }, 1500);
  });
});