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

  var App = {
    data: [],
    new_feeds: [],
    numDays: 7,
    feeds: JSON.parse(JSON.stringify(window.feeds))
  };


  function renderChart() {
    //more code to deal with non-cumulative streams
    App.data = [];
    App.new_feeds = [];
    App.feeds = JSON.parse(JSON.stringify(window.feeds));
    for (var i=0; i<App.feeds.length; i++) {
      App.feeds[i]['values'] = difference(App.feeds[i]['values']);
      if (App.feeds[i]['values']) {
        App.new_feeds.push(App.feeds[i]);
      }
    }
    var color = d3.scale.category20b();
    App.feeds = App.new_feeds;

    App.feeds.sort(function(a,b) {
      //should be simplified
      var last = a['values'].length - 1;
      return a['values'][last] > b['values'][last];
    })

    for (var i=0; i<App.feeds.length; i++) {
      var values = paired(App.feeds[i]['values']);
      if (values.length > App.numDays) {
        values = values.slice(values.length-App.numDays, values.length);
      }
      App.data.push({values: values, key: App.feeds[i]['name'], color: color(i)})
    }


    $('svg').html('');
    nv.addGraph(function() {
      var chart = nv.models.multiBarChart();

      chart.xAxis
          // .axisLabel('Day of the week');
          .axisLabel('');

      chart.yAxis
          .axisLabel('Energy Expenditure (KWh)')
          .tickFormat(d3.format('.02f'));

      chart.showControls(false).stacked(false);

      console.log("App.data: ", App.data);
      d3.select('#chart svg')
          .datum(App.data)
          .transition().duration(500)
          .call(chart);


      nv.utils.windowResize(chart.update);

      return chart;
    });  
  }

  renderChart();

  $('.numDays').change(function() {
    console.log($(this).val());
    App.numDays = parseInt($(this).val());
    renderChart();
    //send request to server
  });

});