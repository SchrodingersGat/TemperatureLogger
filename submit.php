
<?php

//Blank page that takes in GET variables and adds temperature data

error_reporting(E_ALL);
ini_set("display_errors",1);

include "db.php";

$db = openTemperatureDatabase();

if (isset($_GET["s"]))
{
    $sensor = $_GET["s"];
    
    $sensor = filter_var($sensor);
    
    getSensorID($db, $sensor);
    
    $voltage = "";
    $temperature = "";
    
    $vExists = isset($_GET["v"]);
    $tExists = isset($_GET["t"]);
    
    if ($vExists)
    {
        $voltage = $_GET["v"];
    }
    
    if ($tExists)
    {
        $temperature = $_GET["t"];
    }
    
    if ($tExists and $vExists)
    {
        addTemperatureAndVoltage($db, $sensor, $temperature, $voltage);
        print "Success";
    }
    else if ($tExists)
    {
        addTemperature($db, $sensor, $temperature);
        print "Success";
    }
}


$db->close();

?>
