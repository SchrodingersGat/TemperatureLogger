<!DOCTYPE html>
<html>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="http://code.highcharts.com/highcharts.js"></script>
</head>
<body>

<?php
error_reporting(E_ALL);
ini_set("display_errors",1);

include 'db.php';

$db = openTemperatureDatabase();

$sensors = getSensors($db);

$startDate = strtotime("today");
$endDate = strtotime("tomorrow");

$dateString = date("D d M Y",$startDate);

//Work out the date range to display data
if (isset($_GET["d"]))
{
    $day = intval($_GET["d"]);
    
    $offset = " -" . $day . " days";
    
    $startDate = strtotime("today " . $offset);
    $endDate = strtotime("tomorrow " . $offset);
    
    $dateString = date("D d M Y",$startDate);
}
else if (isset($_GET["w"]))
{
    $week = intval($_GET["w"]);
    
    if ($week < 0)
    {
        $week = 0;
    }
    
    $offset = "-" . $week . " weeks";
    
    $startDate = strtotime("Last Sunday", time());
//    $startDate = strtotime("this week", time());
    $startDate = strtotime($offset, $startDate);
    
    $endDate = strtotime("+ 1 week", $startDate);
    
//    $endDate = strtotime("next week", time());
//    $endDate = strtotime($offset, $endDate);
    
    $dateString = date("D d M Y",$startDate) . " to " . date("D d M Y",$endDate);
}
else if (isset($_GET["m"]))
{
    $month = intval($_GET["m"]);
    
    if ($month < 0)
    {
        $month = 0;
    }
    
    $offset = "- " . $month . " months";
    
    $startDate = strtotime(date("Y-m-01 00:00:00"));
    
    $startDate = strtotime($offset, $startDate);
    
    $endDate = strtotime("+1 month",$startDate);
    
    $dateString = date("F Y",$startDate);
}

//Format dates for the SQL data queries
$start = date("Y-m-d",$startDate);
$end = date("Y-m-d",$endDate);

//Highcharts requires timestamps to be in ms (rather than seconds)
$startDate = $startDate * 1000;
$endDate = $endDate * 1000;

?>

<div id="container" style="width:100%; height:80%;"></div>

<script>
$(function () {
    
    Highcharts.setOptions({
	global: {
		useUTC: false
	} });
    
    $('#container').highcharts({
        chart: {
            zoomType: 'x',
            type: 'spline'
        },
        title: {
            text: 'Temperature Data <?php print " - " . $dateString;?>'
        },
        xAxis: {
            type: 'datetime',
            dateTimeLabelFormats: { // don't display the dummy year
                month: '%e. %b',
                year: '%b'
            },
            min: <?php print $startDate ?>,
            max: <?php print $endDate ?>,
            //minRange: 60 * 60 * 1000, //1 hour
           // maxRange: 60 * 24 * 60 * 60 * 1000, //60 days
           
        },
        yAxis: {
            title: {
                text: 'Temperature C'
            }
        },
        legend: {
            enabled: true
        },
        plotOptions: {
            spline: {
                marker: {
                    enabled: true
                }
            },
            area: {
                fillColor: {
                    linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1},
                    stops: [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                },
                marker: {
                    radius: 2
                },
                lineWidth: 1,
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                threshold: null
            }
        },
        
        series: [
            

<?php
//Insert the temperature data

$sensor = $sensors->fetchArray();

if ($sensor)
{
    while (True)
    {
        $title = $sensor["Title"];
        $id = $sensor["rowid"];
        
        $series = temperaturePlot($db, $title, $id, $start, $end);
        
        print $series;
    
        $sensor = $sensors->fetchArray();
        
        if (!$sensor)
        {
            print("\n");
            break;
        }
        else
        {
            print ",\n";
        }
    }    
}

?>
]
});

});        

</script>

<?php
$db->close();
?>

</body>
</html>

