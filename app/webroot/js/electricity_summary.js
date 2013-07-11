$(document).ready(function() {
  nv.dev = false;

  function GetDates(startDate, daysToAdd) {
      var days = [];

      for (var i = 0; i <= daysToAdd; i++) {
          var currentDate = new Date();
          currentDate.setDate(startDate.getDate() + i);
          days.push(DayAsString(currentDate.getDay()));
      }

      return days;
  }

  function DayAsString(dayIndex) {
      var weekdays = new Array(7);
      weekdays[0] = "Sunday";
      weekdays[1] = "Monday";
      weekdays[2] = "Tuesday";
      weekdays[3] = "Wednesday";
      weekdays[4] = "Thursday";
      weekdays[5] = "Friday";
      weekdays[6] = "Saturday";

      return weekdays[dayIndex];
  }

  var startDate = new Date();
  var days = GetDates(startDate, 7);

  function sum(array) {
      var sum = 0;
      for (var i=0; i<array.length; i++) {
          sum = sum + array[i];
      }
      return sum;
  }

  function paired(array) {
      var result = [];
      for (var i = 0; i < array.length; i++) {
        offset = array.length - 8; 
        result.push({x: days[i-offset], y: array[i]});
      }
      return result;
  }

  function random_hex() {
    var result = '#'+Math.floor(Math.random()*16777215).toString(16);
    return result;
  }

  var getColorAtScalar = function (n, maxLength) {
       var n = n * 240 / (maxLength);
       return 'hsl(' + n + ',100%,50%)';
  }

  function difference(arr) {
    var result = [];

    //temporary check to weed out streams that are not cumulative
    if (parseFloat(arr[arr.length-1]) > 1000) {
      for (var i=1; i<arr.length; i++) {
        result.push(arr[i] - arr[i-1]);
      }
      return result;
    }
  }

  //more code to deal with non-cumulative streams
  var data = [];
  window.new_feeds = [];
  for (var i=0; i<window.feeds.length; i++) {
    window.feeds[i]['values'] = difference(window.feeds[i]['values']);
    if (window.feeds[i]['values']) {
      window.new_feeds.push(window.feeds[i]);
    }
  }
  window.feeds = window.new_feeds;

  window.feeds.sort(function(a,b) {
    //should be simplified
    var last = a['values'].length - 1;
    return a['values'][last] > b['values'][last];
  })

  for (var i=0; i<window.feeds.length; i++) {
    var values = paired(window.feeds[i]['values']);
    if (values.length > 7) {
      values = values.slice(values.length-7, values.length);
    }
    data.push({values: values, key: window.feeds[i]['name'], color: getColorAtScalar(i, window.feeds.length)})
  }

  nv.addGraph(function() {
    var chart = nv.models.multiBarChart();

    chart.xAxis
        .axisLabel('Day of the week');

    chart.yAxis
        .axisLabel('Energy Expenditure (KWh)')
        .tickFormat(d3.format('.02f'));

    chart.showControls(false).stacked(false);

    d3.select('#chart svg')
        .datum(data)
        .transition().duration(500)
        .call(chart);


    nv.utils.windowResize(chart.update);

    return chart;
  });
});