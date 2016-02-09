<?php
	$servername = "";
        $username = "";
        $password = "";
	$dbname = "myDB";
	
	function commandCheck ($command){
		
		global $servername, $username, $password;
		switch ($command[1]){

                case "--help":
                        helpDirectives();
                        break;  
                case "--file":

			$filename = $command[2];
			$username = $command[4];
			$password = $command[6];
			$servername = $command[8];

			initDB();
                        insertFile($filename);

                        break;

                case "--create_table":

			$username = $command[3];
                        $password = $command[5];
                        $servername = $command[7];

			initDB();
                        createTable();
                        break;

                case "--dry_run":

			$filename = $command[3];
                        $username = $command[5];
                        $password = $command[7];
			$servername = $command[9];
			
			initDB();
                        dryRun($filename);
                        break;

                default:
                        echo "Invalid command entered\n";
		}
	}

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
                while(! feof($file))
                {
                        $line = (fgetcsv($file));
                        $firstName = str_replace('\'', '\'\'', (preg_replace('/\s+/', '', (ucwords(strtolower($line[0]))))));
                        $lastName = str_replace('\'', '\'\'', (preg_replace('/\s+/', '', (ucwords(strtolower($line[1]))))));
                        $emailAddress = str_replace('\'', '\'\'', (preg_replace('/\s+/', '', ($line[2]))));

                        if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
                                echo "This ($emailAddress) email address is considered valid.\n";
                                $sql = "INSERT INTO users (name, surname, email) 
                                        VALUES ('$firstName', '$lastName', '$emailAddress')";
				
                                if ($conn->query($sql) === TRUE) {
                                        echo "New record created successfully\n";
                                }
                                else {
                                        echo "Error: " . $sql . "<br>" . $conn->error . "\n\n";
                                }
			
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
		while(! feof($file))
        	{
        		$line = (fgetcsv($file));
			$firstName = str_replace('\'', '\'\'', (preg_replace('/\s+/', '', (ucwords(strtolower($line[0]))))));
			$lastName = str_replace('\'', '\'\'', (preg_replace('/\s+/', '', (ucwords(strtolower($line[1]))))));
			$emailAddress = str_replace('\'', '\'\'', (preg_replace('/\s+/', '', ($line[2]))));

			if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
    				echo "This ($emailAddress) email address is considered valid.\n";
				$sql = "INSERT INTO users (name, surname, email) 
					VALUES ('$firstName', '$lastName', '$emailAddress')";
				
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


	function initDB(){

		global $servername, $username, $password;		

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

		$conn->close();

	}


	function createTable(){

		global $servername, $username, $password, $dbname;
		
		// Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error."\n\n");
                }
                echo "Connected successfully\n";
		
		// sql to create table
                $sql = "CREATE TABLE IF NOT EXISTS users (
                        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(30) NOT NULL,
                        surname VARCHAR(30) NOT NULL,
                        email VARCHAR(50) NOT NULL,
			insert_date TIMESTAMP,
                        UNIQUE (email)
                        )";

                if ($conn->query($sql) === TRUE) {
                        echo "Table users created successfully\n\n";
                }
                else {
                        echo "Error creating table: " . $conn->error . "\n\n";
                }

                $conn->close();

	}

	//initDB();
	commandCheck($argv);

?>
