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
          <li class="active">
          <li><a href="source.php">Report</a></li>
          </li>
        </ul>
      </div>
    </nav>
  
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {	

$servername = "mars.cs.qc.cuny.edu";
$username = "raze2686";
$password = "23812686";
$db = "raze2686";

$mysqli = new mysqli($servername, $username, $password, $db);

// Check connection
if ($mysqli->connect_error) {
  die("Connection failed: " . $mysqli->connect_error);}


mysqli_autocommit($mysqli,TRUE);

$typed_source_url = ($_POST["source_url"]);
$typed_source_name = $mysqli->real_escape_string($_POST["source_name"]);
$typed_source_begin = strtoupper($_POST["source_begin"]);
$typed_source_end = strtoupper($_POST["source_end"]);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $typed_source_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$html = curl_exec($ch);

$dom = new DOMDocument();

@ $dom->loadHTML($html);

$tag_array = array('body');
$delimeter_array = array("!", "," , "'", "-" , "." , "‘" , "—" , "”" , "’", 
						";" , "“", "(" , ")" , ":" ,"?","<" , ">" , "{" , "}" , "[" , "]", "=");
$text = "";

for ($x = 0; $x < count($tag_array); $x++) {
  	
  	$tags = $dom->getElementsByTagName($tag_array[$x]); // DOMNodeList object
  	
  	foreach($tags as $tag) 
	{
    	$text .= $tag->textContent;
	}
}


$start_index = 0;
$end_index = count($text_array) - 1;



if (strcmp($typed_source_begin,"")!= 0) 
{
  $start_index = stripos($text, $typed_source_begin);
  $text = substr($text,$start_index);
}

if (strcmp($typed_source_end,"")!= 0) 
{
  $end_index = strripos($text, $typed_source_end);
  $len_end = strlen($typed_source_end);
  $text = substr($text,0, $end_index + $len_end);
}

for ($x = 0; $x < count($delimeter_array); $x++)
{
  $text = str_replace($delimeter_array[$x]," ",$text);
}

$singleSpace = preg_replace('/\s+/', ' ', $text);
$text_array = explode(" ", $singleSpace);


$word_freq = array();

foreach ($text_array as $word) {
 
   if (array_key_exists($word,$word_freq)) {
   		$word_freq[$word]++;
     
   }
   else
   {
   	if(strlen($word)> 30)//probably not a word
   		{
   			continue;
   		}
    $word_freq[$word] = 1;
    }
}



$typed_source_begin = $mysqli->real_escape_string($typed_source_begin);
$typed_source_end = $mysqli->real_escape_string($typed_source_end);

$sql = "INSERT INTO source(source_name, source_url, source_begin, source_end) VALUES('" . $typed_source_name . "', '" . $typed_source_url . "'" . ", " . "'" . $typed_source_begin . "'" . ", " . "'" . $typed_source_end . "'" . ");";

$result = $mysqli->query($sql);

if (!$result) {
   die("Error executing query: ($mysqli->errno) $mysqli->error<br>SQL = $sql");
}

$source_id = mysqli_insert_id($mysqli);

$query = "INSERT INTO occurence (word, freq, source_id, percentage) VALUES";

$word = array_keys($word_freq); 

for($i=0; $i < count($word_freq) - 1; ++$i) { 
	$word_count_per = ($word_freq[$word[$i]] / count($text_array)) * 100;
	
   	$value = "('" . $word[$i] . "','" . $word_freq[$word[$i]] . "','" . $source_id . "','" . $word_count_per . "'),";
   	$query .= $value; 
}

$last_element = count($word_freq) - 1;


$query .= "('" . $word[$last_element] . "','" . $word_freq[$word[$last_element]] . "','" . $source_id . "','" . $word_count_per . "');";

$result = $mysqli->query($query);


if (!$result) {
   die("Error executing query: ($mysqli->errno) $mysqli->error<br>SQL = $query");
}

mysqli_close($mysqli);

echo "<center style='background-color: crimson; color: #f1f1f1; font-weight: bold; font-size:40px;'>Parsing Successful!</center>";
echo "<p style='text-align:center; color: crimson;'>Loading Result.....</p>";

$link_query = 'https://venus.cs.qc.cuny.edu/~raze2686/cs355/occurence.php?source_id=';
$final_link_query = $link_query.$source_id;

echo "<meta http-equiv = 'refresh' content = '2; url = $final_link_query' />";
}




elseif($_SERVER['REQUEST_METHOD'] == 'GET') {
   echo "<h1 style='color: crimson;'>Parser Website</h1>";	
   
   echo "<form method='post' action='parser.php'>";
   
   echo "<div style='color: crimson;'>";
   
   echo "<p><label for='source_name'>Source Name:</label></p> <p><input type='text' placeholder='Enter Source Name' name='source_name' required/></p>";
   
   echo "<p><label for='source_url'>Source URL:</label></p>";
   echo "<textarea name='source_url' rows='1' cols='60' placeholder = 'Enter your Source URL' required></textarea>";
   echo "</br>";
   
   
   echo "<p><label for='source_begin'>Source Begin:</label><p><input type='text' placeholder='Start counting from here' name='source_begin'/></p>";
   echo "<p><label for='source_end'>Source End:</label><p><input type='text' placeholder='Stop counting at here' name='source_end'/></p>";
   echo "</div>";

   echo "<p><input type='submit' value='Parse'/></p>";
   echo "</form>";

}
?>
</html>
