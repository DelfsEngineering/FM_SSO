<?php
/*
sso.php 

Author: C. Delfs 
Based on concepts by Mike Duncan

URL Parameters:
	mode			-	'write' to write the parames (via web viewer) 'read' to fetch
	file_uuid 		-	a UniqueID for a file name eg: 2345hnb52g454h5
	yourParam(s)	-	in Write mode these params are returned in read mode

Functionality explanation:
Called before fm_read.php from within FileMaker. This writes a temp file named with a UUID set by FileMaker. 
This same UUID will be passed to fm_read.php to pull the contents of the file later.
The temp file contains the session variable contents written from fm_link.php.
as well as any other needed environmental data


READ mode:
Data is returned in let format ready to be converted to $vars in FMP

*/

session_start();

// ============================= WRITE MODE ================================
if (isset($_GET['mode']) && $_GET['mode']== "write") {

	// store all GET parameters in a session 
	// Can add any additional needed bits here
	$_GET['LOGON_USER'] = $_SERVER['LOGON_USER']; //add the user to the session 
	
	
	
	if (isset($_GET) && count($_GET) > 0) {
		$_SESSION['get_requests'] = $_GET;
		}

	// only write to file if we get the correct parameter "file_uuid"
	if (isset($_GET['file_uuid']) && strlen($_GET['file_uuid']) > 0) {
		// save temp file with uuid from server.
		// add extension for writing
		$filename = $_GET['file_uuid'] . '.txt';

		// set session variables to a string to write
		//initialize string
		$string = '';

		// set each parameter as local variable
		foreach ($_SESSION['get_requests'] as $key => $value) {
			if ($key == 'fmurl' || $key == 'fmfile') {
				// do not store these two parameters
			} else {
				// store as variables
				$string .= '$' . urlencode($key) . ' = "' . urlencode($value) . '"; ';
			}
		}

		// write the file to temp
		$fn = dirname(__FILE__) . "/tmp/" . $filename;
		//echo $fn;
		if ($fn) {
			$f = fopen($fn, 'w+');
			if ($f) {
				$fw = fwrite($f, $string);
				if ($fw) {
					echo 'Success'; // . $fn
					// once file is written, destroy session
					session_destroy();
					
				} else {
					echo 'write error';
				}
			} else {
				echo 'open error';
			}
		} else {
			echo 'file error';
		}
	} else {
		exit('Missing file_uuid parm');
	}
	exit();


// ============================= READ MODE ================================
} elseif (isset($_GET['mode']) && $_GET['mode']== "read")  {
	// check for required parameter, should match the UUID passed in fm write file.
	if(isset($_GET['file_uuid']) && strlen($_GET['file_uuid'])>0 ){
		
		$fn = dirname(__FILE__) . "/tmp/" .$_GET['file_uuid'].'.txt';
		$fr = fopen($fn, "r") or die("Unable to open file!");
		echo fread($fr, filesize($fn));
		fclose($fr);
		unlink($fn);  // remove the file once read

	} else {
		exit('Error: No file.');  
	}

} else {
	echo "'mode' parameter missing" ;
}

?>
