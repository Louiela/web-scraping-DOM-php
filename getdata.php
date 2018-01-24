<?php 
header('Content-Type: text/html; charset=utf-8');


include("db_con.php");
// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
error_reporting(E_ERROR | E_WARNING | E_PARSE);


//require_once('wp-config.php');
include("dom_helper.php");
$url = 'http://www.skysports.com/premier-league-table';


error_reporting(E_ERROR | E_WARNING | E_PARSE);

$html = file_get_html($url);

$data  = array();

/*** a new dom object ***/ 
$dom = new domDocument; 

/*** load the html into the object ***/ 
libxml_use_internal_errors(true);
$dom->loadHTML($html); 

/*** discard white space ***/ 
$dom->preserveWhiteSpace = false; 

/*** the table by its tag name ***/ 
$tables = $dom->getElementsByTagName('table'); 

/*** get all rows from the table ***/ 
$rows = $tables->item(0)->getElementsByTagName('tr'); 

/*** loop over the table rows ***/ 
foreach ($rows as $row) {
    /*** get each column by tag name ***/ 
    $cols = $row->getElementsByTagName('td'); 

    /*** echo the values ***/ 
    $data['Number'] =  $cols->item(0)->nodeValue; 
    $data['Team'] = $cols->item(1)->nodeValue; 
    $data['Pl'] = $cols->item(2)->nodeValue; 
    $data['W'] = $cols->item(3)->nodeValue; 
    $data['D'] = $cols->item(4)->nodeValue; 
    $data['L'] = $cols->item(5)->nodeValue; 
    $data['F'] = $cols->item(6)->nodeValue; 
    $data['A'] = $cols->item(7)->nodeValue; 
    $data['GD'] = $cols->item(8)->nodeValue; 
    $data['Pts'] = $cols->item(9)->nodeValue; 
    $tabledata[] = $data;
}

function _group_by($array, $key) {
    $return = array();
    foreach($array as $val) {
        $return[$val[$key]][] = $val;
    }
    return $return;
}

$json_encode_serialize = serialize($tabledata);

$sql = "  INSERT INTO table_name (data) VALUES ('$json_encode_serialize')";
if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();

exit();