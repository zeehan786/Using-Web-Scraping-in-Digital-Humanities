<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="myStyle.css">
    <title>Bootstrap Example</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link
      rel="stylesheet"
      href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
    />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  </head>
<body style = "background-color: #ffffcc;">
    <nav class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">CSCI 355</a>
        </div>
        <ul class="nav navbar-nav">
          <li class="active"><a href="index.html">Home</a></li>
          <li><a href="parser.php">Website Parser</a></li>
          <li class="active">
          <li><a href="source.php">Report</a></li>
          </li>
        </ul>
      </div>
    </nav>


<?php

$servername = "mars.cs.qc.cuny.edu";
$username = "your_username";
$password = "your_password";
$db = "name_of_database";

// Create connection
$mysqli = new mysqli($servername, $username, $password, $db);

// Check connection
if ($mysqli->connect_error) {
  die("Connection failed: " . $mysqli->connect_error);
}
//echo "Connected successfully </br>\n";

$source_id = $_GET["source_id"];


$sql = "SELECT * FROM occurence WHERE source_id = ".$source_id." ORDER BY freq DESC;";


// Issue the query
$result = $mysqli->query($sql);
if (!$result) {
   die("Error executing query: ($mysqli->errno) $mysqli->error<br>SQL = $sql");
}




    
echo "<table style='width:70%'>";
	echo "<th> Source Id </th>";
	echo "<th> Word </th>";
	echo "<th> Freq </th>";
	echo "<th> Percentage </th>";	
	
	$i = 0;
	while ($row = $result->fetch_row()) 
	{
   		echo "<tr> <td> $row[3] </td> <td> $row[1] </td> <td> $row[2] </td> <td> $row[4] </td> </tr>";
   		$i++;
	}



echo "</table>";
   
    

?>

</body>
</html>
