<?php
//server vars
$debug = false;
$apiversion = "1.0.0";
$server = "localhost";
$user = "3dsclient";
$password = "test";
$database = "3dsrepo";
$repofqdn = "brunoturek.com";
$reponame = "Bruno Turek's Repo";
$repodesc = "Best repo in the world!";
//dbconn open
if($debug) {
print("1");
}
mysql_connect($server, $user, $password);
if($debug) {
print("2");
}
//now select a db
@mysql_select_db($database) or die("/error/Failed to select database");
if($debug) {
print("3");
print(" I got: name: " + $_GET["type"]);
}
//------Actual code-------
if($_GET["type"] == "name") {
 echo $reponame;
}
if($_GET["type"] == "desc") {
 echo $repodesc;
}
if($_GET["type"] == "search") {
	//getting ready to mysqli
	mysql_close();
	//starting mysqli
	$conn = new mysqli($server, $user, $password, $database);
	if ($conn->connect_error) {
    die("/error/" . $conn->connect_error);
	} 
	//all is set up, now let's send a query
	$thingtosearch = urldecode($_GET["find"]);
	$query = "SELECT id, name, shortdesc, iconurl FROM packages WHERE name LIKE '" . $thingtosearch . "'";
	$result = $conn->query($query);
	//searching
	if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "\"" . $row["id"]. "\",\"" . $row["name"]. "\",\"" . $row["shortdesc"] . "\",\"" . $row["iconurl"] . "\"";
    }
	} else {
    	echo "/none/";
	}
	//goodbye mysqli ;-; you will be missed
	$conn->close();
	}
//FINISH(for my own dumb brain)
if($_GET["type"] == "icons") {
	echo "default";
}
if($_GET["type"] == "apiversion") {
	echo apiversion;
}
if($debug) {
print("4");
}
//mysql_query($query) or die("/error/" + mysql_error()); NO
//if($debug) {
//print("5");
//}
//Goodbye database ;-;
mysql_close();
if($debug) {
print("5");
}
?>
