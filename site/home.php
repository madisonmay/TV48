<!-- Home.php Todo List:
Add in statistics/dashboard
Add in link to management page(s) -->

<!DOCTYPE html>
<html lang="en"></html>
<head>

    <?
        include("check.php");
    ?>

    <title>TV48 - Home</title>
    <title>TV48</title>
    <meta charset="utf-8">
    <script src="http://cdnjs.cloudflare.com/ajax/libs/d3/2.10.0/d3.v2.min.js"></script>
    <script src="nv.d3.js"></script>
    <? include('base.php'); ?>

    <style>
        .home-button {
            opacity: 0;
            cursor: default;
        }

        .hidden {
            display: none;
        }

        
        @media (min-width: 768px) {
            .row-fluid {
                min-width: 1000px;
            } 

            .hide-this {
                min-width: 1000px;
            }
        }

    </style>
    <!--[if lt IE 9]>
        <style>

            .hide-this {
                display: none;
            }

        </style>
    <![endif]-->
    <script src='scripts/home.js'></script>
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
    </script>
</head>
<body>
    <? include('header.php'); ?>
    TV48
    <? include('header2.php'); ?>
    <div class="row-fluid">
        <div class="span3">
            <a href='light.php'><div class="border">
                <div id="light" class="circle">
                    <div class="opaque">
                        <div class="label-text">Light</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
        <div class="span3">
            <a href='heat.php'><div class="border">
                <div id="heat" class="circle">
                    <div class="opaque">
                        <div class="label-text">Heat</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
        <div class="span3">
            <a href='power.php'><div class="border">
                <div id="power" class="circle">
                    <div class="opaque">
                        <div class="label-text">Power</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
        <div class="span3">
            <a href='management.php'><div class="border">
                <div id="management" class="circle">
                    <div class="opaque">
                        <div class="label-text">Manage</div>
                    </div>
                </div>
            </div></a>
            <div class="description">
            </div>
        </div>
    </div>
    <!-- <img src='images/left.png' style='width: 5%; float: left;'></img> -->
    <div id='chart' style='height: 350px; width: 85%; margin-right: auto; margin-left: auto;'>
        <svg></svg>
    <div>
    <!-- <img src='images/right.png' style='width: 5%; float: right;'></img> -->
    </div>
</body>