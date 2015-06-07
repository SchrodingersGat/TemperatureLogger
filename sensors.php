<html>
<body>

<?php
error_reporting(E_ALL);
ini_set("display_errors",1);

include 'db.php';

$db = openTemperatureDatabase();

$result = getSensors($db);

print "<table>";
//Header row

print "<tr><td>ID</td><td>Title</td><td># Readings</td></tr>";

while ($row = $result->fetchArray())
{
    print "<tr><td>";
    print $row["rowid"];
    print "</td><td>";
    print $row["Title"];
    print "</td><td>";
    
    $id = $row["rowid"];
    $count = sensorRecordCount($db,$id);
    print strval($count);
    print "</td></tr>";        
}

print "</table>";

$db->close();

?>

</body>
</html>