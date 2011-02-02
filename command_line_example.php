#!/usr/bin/php
<?php
# chmod 0777 command_line_example.php
# chmod 0777 seemes.php
error_reporting(0);
require('seemes.php');
if(!empty($argc) && strstr($argv[0], basename(__FILE__))){
	# Make sure we have a valid input file or we cannot do anything.
	if(!empty($argv[1]) && is_file($argv[1])){
		# If we have an offset use it, otherwise set it to 0, for Open Site Explorer use 7 (we start counting from 0).
		$offset = (isset($argv[2]) ? (int)$argv[2] : 0);
		# If we have an output file use it, otherwise set it to 'output.csv'.
		$output_file = (isset($argv[3]) ? $argv[3] : 'output.csv');
		echo 'Reading: ' , $argv[1] , "\n\n";

		# Open the File and get started reading the data.
		$csv_data = array();
		if(($handle = fopen($argv[1], 'r')) !== FALSE){
			while(($data = fgetcsv($handle, 1000, ',')) !== FALSE){
				# Populate the multidimensional array: add each line as an array to our larger container array.
				array_push($csv_data, $data);
			}
			# Close the File.
			fclose($handle);
		}

		# Loop through all the domains and make sure the URL seems somewhat valid.
		foreach($csv_data as $key => $value){
			if($key >= $offset){
				# Convert each domain to lowercases for convenience.
				$domain_name = strtolower($value[0]);
				# Open Site Explorer prefixes *., we need a real domain.
				if($domain_name[0] != 'h' && $domain_name[1] != 't'){
					$domain_name = 'http://' . substr($domain_name, 2);
				}
				# Begin checking the domain and inialize some variables.
				echo 'Checking ' , $domain_name , '...';
				$results = array();
				$seemes = new Seemes($domain_name);
				$page = $seemes->fetchUrl($domain_name, true);
				$header = $seemes->fetchHeaders($domain_name, true);
				$cms = '';
				# Check to see if we are online or offline and cached and report appropriately
				if($page == 'offline'){
					echo ' offline...';
				}
				else{
					# Set what we want to filter by, CMS only for now.
					$search_for = array('CMS');
					# Begin the searching...
					$results = $seemes->checkMetaTags(&$page, $results);
					$results = $seemes->checkScriptTags(&$page, $results, &$search_for);
					$results = $seemes->checkPageText(&$page, $results, &$search_for);
					$results = $seemes->checkPageHeaders(&$header, $results, &$search_for);
					$cms = implode(', ', $results);
					$analytics = $seemes->getAccounts(&$page, array(), array());
					$analytics = implode(', ', $analytics);
					if($cms[0] == ',') $cms = substr($cms, 2);
					if($analytics[0] == ',') $analytics = substr($analytics, 2);
				}
				# Add our found values back to our array so we can convert them to CSV later.
				# Each value in the array is another column, so here we are adding 2 columns, then updating our original container array.
				array_push($value, $cms);
				array_push($value, $analytics);
				$csv_data[$key] = $value;
				echo " done!\n";
			}
		}

		# Prepare the data and write it back out.
		$output_handler = fopen($output_file, 'w+');
		foreach($csv_data as $line){
			# Looping back through each line of the array converting it to CSV.
			fputcsv($output_handler, $line);
		}
		# Close the File.
		fclose($output_handler);

		die("Finished!\n");
	}
	else{
		$die  = "Error: Missing Input CSV File\n\n";
		$die .= "Usage: ./" . basename(__FILE__) . " input.csv offset output.csv\n";
		die($die);
	}
}