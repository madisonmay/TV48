$(document).ready(function() {
  nv.dev = false;

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
          result.push({x: i + 1, y: array[i]});
      }
      return result;
  }

  function random_hex() {
    return '#'+Math.floor(Math.random()*16777215).toString(16);
  }

  function sortfunction(a, b){
    return (a[0] - b[0]) //causes an array to be sorted numerically and ascending
  }

  function difference(arr) {
    console.log(arr);
    var result = [];

    //temporary check to weed out streams that are not cumulative
    if (parseFloat(arr[arr.length-1]) > 1000) {
      for (var i=1; i<arr.length; i++) {
        result.push(arr[i] - arr[i-1]);
      }
      return result;
    }
  }

  var data = [];
  window.feeds.sort(function(a, b) {
    return a[0] - b[0];
  })

  //next values need to be converted from cumulative to a daily consumption
  for (var i=0; i<window.feeds.length; i++) {
    var deltas = difference(window.feeds[i]['values']);

    //temporary check to weed out streams that are not cumulative
    if (deltas) {
      var values = paired(deltas);
      if (values.length > 7) {
        values = values.slice(values.length-7, values.length);
      }
      data.push({values: values, key: window.feeds[i]['name'], color: random_hex()})
    }
  }

  nv.addGraph(function() {
    var chart = nv.models.multiBarChart();

    chart.xAxis
        .axisLabel('Day')
        .tickFormat(d3.format(',r'));

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