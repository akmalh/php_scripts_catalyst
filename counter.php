<?php
/*
###########################################################
# Author: Akmal Hossain
#
# Description:  The PHP script counts from 1 to 100.
#               For each number, it checks if it is
#               divisible by 3,5 and both. If any
#               conditions is true, it prints an allocated
#               message following the number. The script
#               implements the logic that a single number
#               can be divisble by one or both.
###########################################################
*/

	$counter = 1;	// Counter variable
	$tripleMsg = "triple";	// Message for numbers divisible by 3
	$fiverMsg = "fiver";	// Message for numbers divisible by 5
	$tripleFiverMsg = "triplefiver";	// Message for numbers divisible by both 3 and 5

	while ($counter <= 100)	// Counting 1 to 100
	{
		echo $counter;

		if (($counter%3) == 0 && ($counter%5) != 0)	// Condition to check divisble only by 3
		{
			echo " $tripleMsg";
		}

		if (($counter%3) != 0 && ($counter%5) == 0)	// Condition to check divisble only by 5
                {
                        echo " $fiverMsg";
                }

		if (($counter%3) == 0 && ($counter%5) == 0)	// Condition to check divisble by both 3 and 5
                {
                        echo " $tripleFiverMsg";
                }

		$counter += 1;
		echo "\n";
	}
?>
