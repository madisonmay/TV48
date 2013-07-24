function obj_size(obj) {
    var count = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) count++;
    }
    return count;
};

$(document).ready(function() {
  nv.dev = false;
  var App = {
    data: []
  }

  count = -1*obj_size(window.data);
  for (key in window.data) {
    count++;
    App.data.push({x: count, y: window.data[key]})
  }

  nv.addGraph(function() {
    var chart = nv.models.lineChart().margin({left: 80, bottom: 50});

    chart.xAxis
        .axisLabel('Day')
        .tickFormat(function(d) {
          var today = new Date();
          var day = new Date();
          day.setDate(today.getDate()+d);
          return d3.time.format("%b %e")(day);
        })

    chart.yAxis
        .axisLabel('Energy Expenditure (Wh)')
        .tickFormat(d3.format('.02f'));

    var chart_id = '#chart svg';

    d3.select(chart_id)
        .datum([{key: "Energy Consumption", values: App.data}])
        .transition().duration(500)
        .call(chart);

    var w = parseFloat(d3.select(chart_id).style("width"));
    var h = parseFloat(d3.select(chart_id).style("height"));
    console.log(w, h);

    d3.select(chart_id)
      .append("text")
        .attr("x", 160)
        .attr("y", 18)
        .attr('id', 'chart_title')
        .attr("style", "font-size: 25px !important; color: #34495e; fill: #34495e;")
        .style("text-anchor", "middle")
        .text("Electricity Use");



    nv.utils.windowResize(chart.update);

    //position x label properly on window resize
    nv.utils.windowResize(function() {
      var x_val = $('.nv-x .nvd3 g .nv-axislabel').attr('x'); 
      $('.nv-x .nvd3 g .nv-axislabel').attr('x', x_val - 35);
    })

    var x_val = $('.nv-x .nvd3 g .nv-axislabel').attr('x'); 
    $('.nv-x .nvd3 g .nv-axislabel').attr('x', x_val - 35);

    return chart;
  });
})  
