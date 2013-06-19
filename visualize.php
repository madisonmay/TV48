<!DOCTYPE html>
<html lang="en">
<head>
    <title>TV48 - Visualization</title>
    <title>TV48</title>
    <meta charset="utf-8">
    <? include('base.php'); ?>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/d3/2.10.0/d3.v2.min.js"></script>
    <script src="nv.d3.js"></script>
    <style>

        .thin-wrapper {
            display: none;
        }

        div#preload { 
            display: none; 
        }

        body {
            text-align: center;
        }

        #chart svg {
          height: 675px;
          width: 1450px;
          margin-left: auto;
          margin-right: auto;
          margin-bottom: 50px;
        }

    </style>
    <!--[if lt IE 9]>
        <style>

            .hide-this {
                display: none;
            }

        </style>
    <![endif]-->
    <script>
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
                result.push({x: i+1, y: array[i]});
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
            setTimeout(function() {co2(expected_yearly/average_yearly);}, 500);

            nv.addGraph(function() {
              var chart = nv.models.lineChart();

              chart.xAxis
                  .axisLabel('Month')
                  .tickFormat(d3.format(',r'));

              chart.yAxis
                  .axisLabel('Energy Expenditure (KWh)')
                  .tickFormat(d3.format('.02f'));

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

        function co2(percent) {
            var cloud = "<div class='remove-me' style='display: inline-block; padding: 15px;'>" +
                             "<div class='gray-box' id='gray%count%' style='width: 512px; height: 305px; margin-right: auto; margin-left: auto; background-color: #333333; color: rgb(%red%, %green%, 0); font-size: 40px; padding-bottom: 0px; padding-top: 30px;'>" + 
                             "</div>" +
                             "<div style='text-align: center;'>" + 
                                "<img src='images/cloud3.png' style='margin-right: auto; margin-left: auto; margin-top: -397px;'>" +
                            "</div>" + 
                        "</div>";

            $('.remove-me').remove();
            var count = 1;
            var original_percent = percent;
            if (percent > 1 && percent < 1.25) {
                var red = 180;
                var green = 90;
            } else if (percent > 1.25) {
                var red = 220;
                var green = 20;
            } else if (percent < 1 && percent > .75) {
                var green = 130;
                var red = 90;                
            } else {
                var green = 200;
                var red = 90;
            }

            while (percent > 1) { 
                percent = percent - 1;
                $('body').append(template(cloud, {'count': count, 'red': parseInt(red), 'green': parseInt(green)}));
                count++;
            }

            $('body').append(template(cloud, {'count': count, 'red': parseInt(red), 'green': parseInt(green)}));

            var height = percent * 305;
            var offset = 305 - height;
            $('#gray' + count).css('display', 'none');
            $('#gray' + count).css('height', height + 'px');
            $('#gray' + count).css('margin-top', offset + 'px');
            $('#gray' + count).css('display', 'block');

            height = $('.gray-box').first().height() - 30; 
            $('.gray-box').first().append('<div style="margin-top:' + height/2 + 'px; font-size: 80px;">' + (original_percent * 100).toFixed() + '%</div>')
        }
    </script>
</head>
<body>
    <? include('header.php'); ?>
    CO2 Visualization
    <? include('header2.php'); ?>
    <div id='chart'>
        <svg></svg>
    <div>
</body>
</html>