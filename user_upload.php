<?php

	function commandCheck ($command){
		
		switch ($command[1]){

                case "--help":
                        helpDirectives();
                        break;  
                case "--file":
			$filename = $command[2];
                        insertFile($filename);
                        break;
                case "--create_table":
                        createTable();
                        break;
                case "--dry_run":
                        dryRun();
                        break;
                default:
                        echo "Invalid command entered\n";
		}
	}

	function helpDirectives(){

		echo "\n\n";
		echo "--file [csv  le name] – this is the name of the CSV to be parsed.\n\n";
		echo "--create_table – this will cause the MySQL users table to be built (and no further action will be taken).\n\n";
		echo "--dry_run – this will be used with the --file directive in the instance that we want to run the script but not insert into the DB. All other functions will be executed, but the database won't be altered.\n\n";
		echo "-u – MySQL username.\n\n";
		echo "-p – MySQL password.\n\n";
		echo "-h – MySQL host.\n\n";
		echo "--help – which will output the above list of directives with details.\n\n";

	}

	function createTable(){
		echo "From create table function\n";
	}

	function dryRun(){
		echo "From dry run function\n";
	}

	function insertFile($filename){
		echo "From insert $filename function\n";
		$file = fopen($filename,"r");
		
		while(! feof($file))
        	{
        		$line = (fgetcsv($file));
			echo "First name:  $line[0], Last name: $line[1], Email: $line[2]\n";
        	}

        	fclose($file);
	}

	function connectDB(){
	
		$servername = "localhost";
		$username = "root";
		$password = "password";

		// Create connection
		$conn = new mysqli($servername, $username, $password);

		// Check connection
		if ($conn->connect_error) {
    		die("Connection failed: " . $conn->connect_error."\n\n");
		}
		echo "Connected successfully\n";

		// Create database
		$sql = "CREATE DATABASE IF NOT EXISTS myDB";
		if ($conn->query($sql) === TRUE) {
    			echo "Database created successfully\n\n";
		} 
		else {
    			echo "Error creating database: " . $conn->error. "\n\n";
		}

		// sql to create table
		$sql = "CREATE TABLE IF NOT EXISTS users (
			id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			name VARCHAR(30) NOT NULL,
			surname VARCHAR(30) NOT NULL,
			email VARCHAR(50),
			insert_date TIMESTAMP,
			UNIQUE (email)
			)";

		if ($conn->query($sql) === TRUE) {
    			echo "Table users created successfully";
		} 
		else {
    			echo "Error creating table: " . $conn->error;
		}

		$conn->close();
	}

		
	commandCheck($argv);
	connectDB();
?>
