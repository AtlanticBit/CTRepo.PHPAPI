<?php
	$mysqlserver = $_POST["mysqlserver"];
	$mysqluser = $_POST["mysqluser"];
	$mysqlpassword = $_POST["mysqlpassword"];
	$newusrname = $_POST["newusrname"];
	$newusrpass = $_POST["newusrpass"];
	$newdbname = $_POST["newdbname"];
	$repofqdn = $_POST["repofqdn"];
	$reponame = $_POST["reponame"];
	$repodesc = $_POST["repodesc"];
	//functions to make it all readable duh
	function inssql($sqltd)
	{
		$conn = new mysqli($mysqlserver, $mysqluser, $mysqlpassword);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// Create database
$sql = $sqltd;
if ($conn->query($sql) === TRUE) {
    echo "we won! provided sql done";
} else {
    echo "shit, this sucks:" . $conn->error;
}

$conn->close();
	}
	function rplc($FilePath, $OldText, $NewText) //replace in file
{
    $Result = array('status' => 'error', 'message' => '');
    if(file_exists($FilePath)===TRUE)
    {
        if(is_writeable($FilePath))
        {
            try
            {
                $FileContent = file_get_contents($FilePath);
                $FileContent = str_replace($OldText, $NewText, $FileContent);
                if(file_put_contents($FilePath, $FileContent) > 0)
                {
                    $Result["status"] = 'success';    
                }
                else
                {
                   $Result["message"] = 'Error while writing file'; 
                }
            }
            catch(Exception $e)
            {
                $Result["message"] = 'Error : '.$e; 
            }
        }
        else
        {
            $Result["message"] = 'File '.$FilePath.' is not writable !';       
        }
    }
}
    function downloadFile ($url, $path) {

  $newfname = $path;
  $file = fopen ($url, "rb");
  if ($file) {
    $newf = fopen ($newfname, "wb");

    if ($newf)
    while(!feof($file)) {
      fwrite($newf, fread($file, 1024 * 8 ), 1024 * 8 );
    }
  }

  if ($file) {
    fclose($file);
  }

  if ($newf) {
    fclose($newf);
  }
 }
 //now do everything in nice order
 	echo "downloading latest working phpapi... pls wait";
 	downloadFile("https://raw.githubusercontent.com/AtlanticBit/CTRepo.PHPAPI/master/workingphpapi.php", "./request.php");
 	echo "done, replacing names in downloaded file... pls wait";
 	rplc("./request.php", "SERVER", $mysqlserver);
 	rplc("./request.php", "UNAME", $newusrname);
 	rplc("./request.php", "PASS", $newusrpass);
 	rplc("./request.php", "DB", $newdbname);
 	rplc("./request.php", "FQDN", $repofqdn);
 	rplc("./request.php", "NAME", $reponame);
 	rplc("./request.php", "DESC", $repodesc);
 	echo "trying to create mysql user pls wait duh...";
 	inssql("CREATE USER " . $newusrname . ";");
 	echo "trying to set usr pass...";
 	inssql("SET PASSWORD FOR " . $newusrname . " = PASSWORD ('" . $newusrpass . "');");
 	echo "creating db...";
 	inssql("CREATE DATABASE " . $newdbname . ";");
 	echo "adding usr readonly privs...";
 	inssql("GRANT SELECT ON " . $newdbname .".* TO " . $newusrname . ";");
 	echo "downloading structure.sql(will be removed later)...";
 	downloadFile("https://raw.githubusercontent.com/AtlanticBit/CTRepo.PHPAPI/master/workingstructure.sql", "./structure.sql");
 	echo "replacing strings in structure.sql...";
 	rplc("./structure.sql", "DB2USE", $newdbname);
 	echo "exec structure.sql...";
 	inssql(file_get_contents("./structure.sql"));
 	echo "autodestruction of installscript in 3....2....1......";
 	unlink("./structure.sql");
 	unlink("./installscript.html");
 	unlink("./installscript.php");
?>
