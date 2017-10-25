<?php 
require_once("includes/connection.php");
	
	// if ($_POST){
		// echo "Debug: Some data has been posted";
		// foreach (array_keys($_POST) as $key) { 
			// $$key = $_POST[$key]; 
			// print "$key is ${$key}<br />"; 
		// } 
	// }
	
 	// START FORM PROCESSING
	if (isset($_POST['Submit'])) { // Form has been submitted.
		
			$username=$_POST['txtUserName'];
		 	$password=$_POST['txtPassword'];	
		
			// perform validations on the form data
			//here..
		
			// Check database to see if username and the password exist there.
			$query = "SELECT Id, username, Balance ";
			$query .= "FROM users ";
			$query .= "WHERE username = '{$username}' ";
			$query .= "AND password = '{$password}' ";
			$result_set = mysql_query($query);
			if (mysql_num_rows($result_set) == 1) {
				// username/password authenticated
				// and only 1 match SUCCESS!
				$found_user = mysql_fetch_array($result_set);
				
				//print_r($found_user);
				echo "success,{$username},{$found_user['Balance']}";
				

				//******Only required for testing purposes.
				//******REMOVE IN MAIN APP
				 echo '<br/> <br/>';
				 include ("Main.html");
				
			} else {
				// username/password combo was not found in the database
				//FAILURE!
				echo 'failure';
				
			}
				
	} else { // Form has not been submitted.
		$username = "";
		$password = "";
		$message="Unauthorised access: NULL";
		echo $message;
	}
?>
 	
 	
 