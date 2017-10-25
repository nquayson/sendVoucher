<?php 
//All my basic functions here

function check_affected_rows($number){
	//$number is the number of rows to be checked if affected	
	if (mysql_affected_rows()==$number){
		echo 'Update successful';
	}else{
		echo 'update failed '. mysql_error();
	}
		
}


function check_access_granted(){
	//toDo Add more checks
	if (!isset($_POST['recharge'])){
		die('Forbidden! Access unauthorised');
	}	
}


function send_sms($destination,$user,$amount,$pin,$serial) {
	
    //$destination is expected to be in format 233207675000
	if (strlen($destination) != 12 ) {
		die ('Invalid Phone No.');
	}
	
	
    $source = 'iPayTM';
    $text = "{$user} has sent you a {$amount}ghc Voucher.\nPin: {$pin} \nSerial no: {$serial}";
        
    $content =  'action=sendsms'.
                '&user='.rawurlencode($username).
                '&password='.rawurlencode($password).
                '&to='.rawurlencode($destination).
                '&from='.rawurlencode($source).
                '&text='.rawurlencode($text);
    
    $smsglobal_response = file_get_contents('http://www.smsglobal.com.au/http-api.php?'.$content);
    //$smsglobal_response=$content;
	//echo $smsglobal_response;
    //Sample Response
    //OK: 0; Sent queued message ID: 04b4a8d4a5a02176 SMSGlobalMsgID:6613115713715266 
    
    $explode_response = explode('SMSGlobalMsgID:', $smsglobal_response);
    
    if(count($explode_response) == 2) { //Message Success
        $smsglobal_message_id = $explode_response[1];
        echo 'SMS sent';
    } else { //Message Failed
        echo 'SMS failed';
        
        //SMSGlobal Response
        echo $smsglobal_response;    
    }
}

?>