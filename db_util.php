<?php

$debugOn = false;
date_default_timezone_set("America/Los_Angeles");


// connect to a mysql db
function wrapper_mysql_connect($environment) {


		$local_db = array(
			'host' => '127.0.0.1',
			'database' => 'link_analyzer',
			'username' => 'root',
			'password' => 'root');

    $dev_db = array(
        'host' => '10.89.113.134',
        'database' => 'link_analyzer',
        'username' => 'root',
        'password' => 'pangea');
    global $debugOn;

	$inuse_db = $dev_db;
	
	$host = $inuse_db['host'];
	$username = $inuse_db['username'];
	$password = $inuse_db['password'];
	$database = $inuse_db['database'];

    $dbConnection = @mysql_connect($host, $username, $password);
    if ($debugOn) {
    	print_r($inuse_db);
    	echo "\n";
    	print "Connecting to: Host: " . $host . " User: " . $username . " Password: " . $password .  "...\n";
    }
    
    if (!$dbConnection || !mysql_select_db($database,$dbConnection)) {
        if ($debugOn) {
            print "Error connecting to database: " . $database . "\n";
            print mysql_error();
            echo "\n";
            die;
        }
        //exit;
    }
    if ($debugOn) {
        print "Connected to: Host: " . $host . " User: " . $username . "Password: " . $password . " DB: " . $database ."\n";
    }
    return $dbConnection;
}

// run a query
function wrapper_mysql_query($sqlQuery, $dbConnection) {
    global $debugOn;

    if ($debugOn) {
        print '<br /><b>Query:</b> DB: ' . $dbConnection . ' SQL:<pre>' . $sqlQuery . '</pre>';
    }
    
    $result =  mysql_query($sqlQuery, $dbConnection);
    
    return $result;
}

?>