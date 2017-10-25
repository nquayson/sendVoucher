<?Php 
require_once("includes/connection.php");
require_once("includes/functions.php");

//Recharge version 2. This one should works with device

	//Check for authorisation
	check_access_granted();
	
	$username= $_POST['txtUserName'];
	$password= $_POST['txtPassword'];
	$amount=   $_POST['amount'];
	$destination= $_POST['destination'];
 
		// Check database to see if username and the password exist there.
		//Consider using a session instead of another query
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
			$balance=$found_user['Balance'];
			
			//Checking whether user has sufficient balance
			if (($amount>0) && ($amount<=$balance))  { //|| (!$amount==0)
				//Send credit voucher here
				//retrieving 1 voucher
				$vquery = "SELECT id, statusid, pin, serial, price, datetime ";
				$vquery .= "FROM vouchers ";
				$vquery .= "WHERE price = {$amount} ";
				$vquery .= "AND statusid = 1 ";
				$vquery .= "ORDER BY statusid ASC ";
				$vquery .= "LIMIT 1 ";

				$vresult = mysql_query($vquery);
				
				if (mysql_num_rows($vresult) == 1) { //A voucher is available
					$found_voucher = mysql_fetch_array($vresult);

					//Send $found_voucher[pin] as sms
					//send_sms($destination,$user,$amount,$pin)
					send_sms($destination,$username,$found_voucher['price'],$found_voucher['pin'],$found_voucher['serial']);
					
					$sent_date= date('Y/m/d h:i:s a', time());
					
					//Notify
					//echo "{$amount}ghc of voucher {$found_voucher['id']}, has been successfully sent to {$destination}";
					
					//Update voucher statusid and status 
					//Note: Voucher statusid=0 means it's used, statusid=1 means available, 2 means unknown
					$vupdater= "UPDATE vouchers SET 
								statusid=0,
								datetime='{$sent_date}',
								status='used' 
								WHERE id={$found_voucher['id']} ";
								//update datetime to now
					$result=mysql_query($vupdater,$connection);
					//check affected rows .. i tire
					check_affected_rows(1);
										
					//Update balance in DB
					$newbalance=$balance-$amount;
					$updater= "UPDATE users SET 
								Balance={$newbalance} 
							WHERE username= '{$username}'"; 
					
					$result=mysql_query($updater,$connection);
					check_affected_rows(1);
					//Success Echo
					echo ',success,';
					echo "balance:{$newbalance}";
 				}else{
 					echo "Insufficient {$amount}ghc vouchers in our database";
				}				
			} else{
				//Insufficient balance
				echo 'Insufficient Credit/ Invalid amount';
			}						
		} else {
			// username/password combo was not found in the database
			//FAILURE!
			echo "Authentication failed!";
		}

?>