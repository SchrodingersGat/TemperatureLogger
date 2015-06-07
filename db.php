<?php

error_reporting(E_ALL);

ini_set("display_errors",1);

function openTemperatureDatabase()
{
    $db = new SQLite3('/var/www/temperature.db');
    
    return $db;
}

function getLowerDateBound($format)
{

}

function getUpperDateBound($format)
{
    
}

function getSensors($db)
{
    $results = $db->query("SELECT rowid, Title from Sensors");
    
    return $results;
}

function formatTemperatureTable($temperatureData)
{
    $data = "[";
    
    $row = $temperatureData->fetchArray();
    
    if (!$row)
    {
        return "[]";
    }
    
    while (True)
    {
        $time = strtotime($row["Timestamp"]);
        
        $time = intval($time) * 1000;
        $time = strval($time);
        
        $data = $data . "[";
        $data = $data . $time . "," . $row["Temperature"];
        //$data = $data . "Date.UTC('" . $row["Timestamp"] . "')," . $row["Temperature"];
        $data = $data . "]";
     
        //Get the next row
        $row = $temperatureData->fetchArray();
        
        if (!$row)
        {
            $data = $data . "]";
            break;
        }
        else
        {
            $data = $data . ",\n\t\t";
        }
    }
    
    return $data;
}

function temperaturePlot($db, $title, $id, $t1, $t2)
{
    $data = getTemperatureData($db, $id, $t1, $t2);
    
    $series = "{\n";
    $series = $series . "\tname: '" . $title . "',\n";
    $series = $series . "\tdata: ";
    
    $series = $series . formatTemperatureTable($data) . "\n";
    
    $series = $series . "}";
    
    return $series;
}

function getTemperatureData($db, $id, $t1, $t2)
{
    $query = "select Timestamp, Temperature from Readings where SensorID = " . $id;
    $query = $query . " and Timestamp >= '" . $t1 . "' and Timestamp < '" . $t2 . "';";
    
    $results = $db->query($query);
    
    return $results;
}

function getVoltageData($db, $id, $t1, $t2)
{
    $query = "select Timestamp, Voltage from Readings where rowid = " . $id;
    $query = $query . " and Timestamp >= '" . $t1 . "' and Timestamp <= '" . $t2 . "';";
    
    $results = $db->query($query);
    
    return $results;
}

function getSensorID($db, $title)
{
    $query = "select rowid from Sensors where Title = '" . $title . "' limit 1;";
    
    $result = $db->query($query);
    
    $row = $result->fetchArray();
    
    if ($row == False)
    {
        return -1;
    }
    else
    {
        return $row["rowid"];
    }
}

function addTemperature($db, $title, $temperature)
{
    $id = getSensorID($db, $title);
    
    if ($id != -1) //Sensor existss in database
    {
        $query = "insert into Readings (SensorID, Temperature) ";
        $query = $query . "values (" . $id . "," . $temperature . ");";
        
        $result = $db->query($query);
    }
}

function addTemperatureAndVoltage($db, $title, $temperature, $voltage)
{
    $id = getSensorID($db, $title);
    
    if ($id != -1) //Sensor existss in database
    {
        $query = "insert into Readings (SensorID, Temperature, Voltage) ";
        $query = $query . "values (" . $id . "," . $temperature . "," . $voltage . ");";
        
        $result = $db->query($query);
    }
}

function doesSensorExist($db, $sensor)
{
    $sensor = filter_var($sensor);
    
    $query = "select exists (select 1 from Sensors where Title = '" . $sensor . "');";
    
    $result = $db->query($query);
    
    $row = $result->fetchArray();
    
    return $row[0] == 1;
}

function addSensor($db, $title)
{
    $query = "insert into Sensors (Title) values ('" . $title . "');";
    
    $result = $db->query($query);
}

function sensorRecordCount($db, $id)
{
    $query = "select count(Temperature) from Readings where SensorID = " . $id . ";";
    
    $result = $db->query($query);
    
    $row = $result->fetchArray();
    
    if ($row == False)
    {
        return 0;
    }
    else
    {
        return $row[0];
    }
}

?>

