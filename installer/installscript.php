<?php
	$debug = false;
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
	if($debug) {
	echo "Data I got: <br />";	
	echo $mysqlserver . "<br />";
	echo $mysqluser . "<br />";
	echo $mysqlpassword . "<br />";
	echo $newusrname . "<br />";
	echo $newusrpass . "<br />";
	echo $newdbname . "<br />";
	echo $repofqdn . "<br />";
	echo $reponame . "<br />";
	echo $repodesc . "<br />";
	}
	public static function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("hot damnit this is not a dir lol <br />");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            self::deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}
		function multiinssql($sqltd)
{
global $mysqlserver, $mysqluser, $mysqlpassword;
$conn = new mysqli($mysqlserver, $mysqluser, $mysqlpassword);
// Check connection
if ($conn->connect_error) {
die("damnit something fucked up: " . $conn->connect_error);
}
// Create database
$sql = $sqltd;
if ($conn->multi_query($sql) === TRUE) {
echo "we won! provided sql done <br />";
} else {
echo "shit, this sucks: " . $conn->error . "<br />";
}
$conn->close();
}
	function inssql($sqltd)
	{
		global $mysqlserver, $mysqluser, $mysqlpassword;
		$conn = new mysqli($mysqlserver, $mysqluser, $mysqlpassword);
// Check connection
if ($conn->connect_error) {
    die("damnit something fucked up: " . $conn->connect_error);
} 

// Create database
$sql = $sqltd;
if ($conn->query($sql) === TRUE) {
    echo "we won! provided sql done <br />";
} else {
    echo "shit, this sucks: " . $conn->error . "<br />";
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
 	echo "creating tmp dir...";
 	mkdir("./tmp");
 	echo "downloading latest working phpapi... pls wait<br />";
 	downloadFile("https://raw.githubusercontent.com/AtlanticBit/CTRepo.PHPAPI/master/workingphpapi.zip", "./tmp/download.zip");
 	echo "extracting archive....";
 	$zip = new ZipArchive;
	if ($zip->open("./tmp/download.zip") === TRUE) {
    $zip->extractTo("./");
    $zip->close();
    echo "ok<br />";
	} else {
    echo "failed<br />";
	}
 	echo "done, replacing names in downloaded file... pls wait<br />";
 	rplc("./config.php", "SERVER","\"" . $mysqlserver . "\"");
 	rplc("./config.php", "UNAME","\"" . $newusrname . "\"");
 	rplc("./config.php", "PASS","\"" . $newusrpass . "\"");
 	rplc("./config.php", "DB","\"" . $newdbname . "\"");
 	rplc("./config.php", "FQDN","\"" .  $repofqdn . "\"");
 	rplc("./config.php", "NAME","\"" . $reponame . "\"");
 	rplc("./config.php", "DESC","\"" .  $repodesc . "\"");
 	echo "trying to create mysql user pls wait duh...<br />";
 	inssql("CREATE USER " . $newusrname . ";");
 	echo "trying to set usr pass... <br />";
 	inssql("SET PASSWORD FOR " . $newusrname . " = PASSWORD ('" . $newusrpass . "');");
 	echo "creating db... <br />";
 	inssql("CREATE DATABASE " . $newdbname . ";");
 	echo "adding usr readonly privs... <br />";
 	inssql("GRANT SELECT ON " . $newdbname .".* TO " . $newusrname . ";");
 	echo "replacing strings in structure.sql...<br />";
 	rplc("./structure.sql", "DB2USE","`" . $newdbname . "`");
 	echo "exec structure.sql...<br />";
 	multiinssql(file_get_contents("./structure.sql"));
 	echo "autodestruction of installscript in 3....2....1...... <br />";
 	unlink("./structure.sql");
 	unlink("./installscript.html");
 	unlink("./installscript.php");
 	deleteDir("./tmp");
?>
