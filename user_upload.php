<?php
/*
###########################################################
# Author: Akmal Hossain
#
# Description:  The PHP script inserts data from csv file
#               to SQL database. The script takes command
#               line arguments as directives. Each directive
#               need to be followed by database username,
#               password and host name which has the database.
#               The script avoids insertion of  duplication
#		data and invalid email addresses.
###########################################################
*/


	$servername = "";	// Global variaable for host name
        $username = "";		// Global variable for SQL username
        $password = "";		// Global variable for SQL password
	$dbname = "DB";	// Global variable to hold the database name
	

	/*
	 commandCheck() function takes the user argument 
	 and checks which directive has been entered by user
	*/

	function commandCheck ($command){
		
		global $servername, $username, $password;

		// Swicth case checking input argument

		switch ($command[1]){

                case "--help":

                        helpDirectives();
                        break; 
 
                case "--file":

			$filename = $command[2];

			if($command[3] == "-u")
			{
				$username = $command[4];
			}
			elseif($command[3] == "-p")
			{
				$password = $command[4];
			}
			elseif($command[3] == "-h")
			{
				$servername = $command[4];
			}
			else
			{
				exit ("Invalid user or host argument\n");
			}

			if($command[5] == "-u")
                        {
                                $username = $command[6];
                        }
                        elseif($command[5] == "-p")
                        {
                                $password = $command[6];
                        }
                        elseif($command[5] == "-h")
                        {
                                $servername = $command[6];
                        }
                        else
                        {
                                exit ("Invalid user or host argument\n");
                        }

			if($command[7] == "-u")
                        {
                                $username = $command[8];
                        }
                        elseif($command[7] == "-p")
                        {
                                $password = $command[8];
                        }
                        elseif($command[7] == "-h")
                        {
                                $servername = $command[8];
                        }
                        else
                        {
                                exit ("Invalid user or host argument\n");
                        }


			initDB();
                        insertFile($filename);

                        break;

                case "--create_table":

			if($command[2] == "-u")
                        {
                                $username = $command[3];
                        }
                        elseif($command[2] == "-p")
                        {
                                $password = $command[3];
                        }
                        elseif($command[2] == "-h")
                        {
                                $servername = $command[3];
                        }
                        else
                        {
                                exit ("Invalid user or host argument\n");
                        }

                        if($command[4] == "-u")
                        {
                                $username = $command[5];
                        }
                        elseif($command[4] == "-p")
                        {
                                $password = $command[5];
                        }
                        elseif($command[4] == "-h")
                        {
                                $servername = $command[5];
                        }
                        else
                        {
                                exit ("Invalid user or host argument\n");
                        }

			if($command[6] == "-u")
                        {
                                $username = $command[7];
                        }
                        elseif($command[6] == "-p")
                        {
                                $password = $command[7];
                        }
                        elseif($command[6] == "-h")
                        {
                                $servername = $command[7];
                        }
                        else
                        {
                                exit ("Invalid user or host argument\n");
                        }

			initDB();
                        createTable();

                        break;

                case "--dry_run":

			$filename = $command[3];
                        
			if($command[4] == "-u")
                        {
                                $username = $command[5];
                        }
                        elseif($command[4] == "-p")
                        {
                                $password = $command[5];
                        }
                        elseif($command[4] == "-h")
                        {
                                $servername = $command[5];
                        }
                        else
                        {
                                exit ("Invalid user or host argument\n");
                        }

			if($command[6] == "-u")
                        {
                                $username = $command[7];
                        }
                        elseif($command[6] == "-p")
                        {
                                $password = $command[7];
                        }
                        elseif($command[6] == "-h")
                        {
                                $servername = $command[7];
                        }
                        else
                        {
                                exit ("Invalid user or host argument\n");
                        }

                        if($command[8] == "-u")
                        {
                                $username = $command[9];
                        }
                        elseif($command[8] == "-p")
                        {
                                $password = $command[9];
                        }
                        elseif($command[8] == "-h")
                        {
                                $servername = $command[9];
                        }
                        else
                        {
                                exit ("Invalid user or host argument\n");
                        }
			
			initDB();
                        dryRun($filename);

                        break;

                default:
                        echo "Invalid command entered\n";
		}
	}
	

	/*
         helpDirectives() function is invoked when user enters --help. 
         It prints out definition of each command line argument option.
        */

	function helpDirectives(){

		echo "\n";
		echo "--file [csv  le name] – this is the name of the CSV to be parsed.\n\n";
		echo "--create_table – this will cause the MySQL users table to be built (and no further action will be taken).\n\n";
		echo "--dry_run – this will be used with the --file directive in the instance that we want to run the script but not insert into the DB. All other functions will be executed, but the database won't be altered.\n\n";
		echo "-u – MySQL username.\n\n";
		echo "-p – MySQL password.\n\n";
		echo "-h – MySQL host.\n\n";
		echo "--help – which will output the above list of directives with details.\n";

	}


	/*
         dryRun() function is invoked when user enters --dry_run.
         It checks data insertion to the database without commiting 
	 the changes (Rollback).
        */

	function dryRun($filename){
		
		global $servername, $username, $password, $dbname;

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error."\n\n");
                }
                echo "Connected successfully\n";

                echo "Turning Auto Commit off for Dry Run\n";

		$conn->autocommit(FALSE);

                $file = fopen($filename,"r");
                
		// Parsing the CSV file
		while(! feof($file))
                {
			// Preprocessing data to meet standard before insertion	
                        $line = (fgetcsv($file));
                        $firstName = str_replace('\'', '\'\'', (preg_replace('/\s+/', '', (ucwords(strtolower($line[0]))))));
                        $lastName = str_replace('\'', '\'\'', (preg_replace('/\s+/', '', (ucwords(strtolower($line[1]))))));
                        $emailAddress = str_replace('\'', '\'\'', (preg_replace('/\s+/', '', ($line[2]))));
			
			// Checking email address validity
                        if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) 
			{

                                echo "This ($emailAddress) email address is considered valid.\n";

                                $sql = "INSERT INTO users (name, surname, email) 
                                        VALUES ('$firstName', '$lastName', '$emailAddress')";
				
				// Execute SQL statement
                                if ($conn->query($sql) === TRUE) {
                                        echo "New record created successfully\n";
                                }
                                else {
                                        echo "Error: " . $sql . "<br>" . $conn->error . "\n\n";
                                }
			
				// Rollback changes
				$conn->rollback();
				echo "New record insertion rolled back\n";

                        }
                        else
                        {
                                echo "($emailAddress) is not valid\n";
                        }

                }

                fclose($file);
		$conn->autocommit(TRUE); 
                $conn->close();
	}

	
	/*
         insertFile() function is invoked when --file directive in entered.
         It parses the input CSV file and saved all valid data to the database
	 for a authorized user.
        */

	function insertFile($filename){

		global $servername, $username, $password, $dbname;

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error."\n\n");
                }
                echo "Connected successfully\n";

		echo "Inserting data from $filename to DB\n";

		$file = fopen($filename,"r");

		// Parsing the CSV file
		while(! feof($file))
        	{
			// Preprocessing data to meet standard before insertion
        		$line = (fgetcsv($file));
			$firstName = str_replace('\'', '\'\'', (preg_replace('/\s+/', '', (ucwords(strtolower($line[0]))))));
			$lastName = str_replace('\'', '\'\'', (preg_replace('/\s+/', '', (ucwords(strtolower($line[1]))))));
			$emailAddress = str_replace('\'', '\'\'', (preg_replace('/\s+/', '', ($line[2]))));
			
			// Checking email address validity
			if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) 
			{

    				echo "This ($emailAddress) email address is considered valid.\n";
				
				$sql = "INSERT INTO users (name, surname, email) 
					VALUES ('$firstName', '$lastName', '$emailAddress')";
				
				// Execute SQL statement				
				if ($conn->query($sql) === TRUE) {
    					echo "New record created successfully\n";
				} 
				else {
    					echo "Error: " . $sql . "<br>" . $conn->error . "\n\n";
				}
				
			}			
			else
			{
				echo "($emailAddress) is not valid\n";
			}
			
        	}

        	fclose($file);
		$conn->close();
	}


	/*
         initDB() function is invoked when any of the directive entered except --help.
         It creates the database if it doesn't exists and checks connection.
        */

	function initDB(){

		global $servername, $username, $password, $dbname;		

		// Create connection
                $conn = new mysqli($servername, $username, $password);

                // Check connection
                if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error."\n\n");
                }
                echo "Connected successfully\n";

                // Create database
                $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
                if ($conn->query($sql) === TRUE) {
                        echo "Database created successfully\n\n";
                }
                else {
                        echo "Error creating database: " . $conn->error. "\n\n";
                }

		$conn->close();

	}


	/*
         createTable() function is invoked when --create_table directive is entered.
         It creates "users" table inside already created database in initDB().
        */

	function createTable(){

		global $servername, $username, $password, $dbname;
		
		// Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error."\n\n");
                }
                echo "Connected successfully\n";
		
		// SQL statement defining "users" table
                $sql = "CREATE TABLE IF NOT EXISTS users (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(30) NOT NULL,
                        surname VARCHAR(30) NOT NULL,
                        email VARCHAR(50) NOT NULL,
			insert_date TIMESTAMP,
                        UNIQUE (email)
                        )";

		// Creating table
                if ($conn->query($sql) === TRUE) {
                        echo "Table users created successfully\n\n";
                }
                else {
                        echo "Error creating table: " . $conn->error . "\n\n";
                }

                $conn->close();

	}


	// Starting point of script
	commandCheck($argv);

?>
