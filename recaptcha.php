<?php
	// MANUAL - http://php.net/manual/en/reserved.variables.post.php
	//var_dump($_POST['msg']);
	$res;
	$array;
	$GLOBALS['status_code_http'] = '';
	$GLOBALS['status_code_https'] = '';
	$msg = "POST['msg']:\n<br>".$_POST['msg']; 
	/* data in $_POST server var contains HTTP POST variables: 
	An associative array of variables passed to the current script 
	via the HTTP POST method when using application/x-www-form-
	urlencoded or multipart/form-data as the HTTP Content-Type in 
	the request.

	$HTTP_POST_VARS contains the same initial information, but is not 
	a superglobal. (Note that $HTTP_POST_VARS and $_POST are different 
	variables and that PHP handles them as such)
	*/
	$msg2 = "POST['g-recaptcha-response']:\n<br>".$_POST['g-recaptcha-response'];
	print("POST super global var contents:<br>".$msg."\n<br>".$msg2."\n<br>");
	print("\n<br>POST record var dump contents:\n<br>");
	var_dump($_POST);
	print("\n<br>\n<br>GET record var dump contents:\n<br>");
	var_dump($_GET);
	print("\n<br>\n<br>");
	
	if(isset($_POST['g-recaptcha-response'])&& $_POST['g-recaptcha-response']){
		echo("post super-global var recaptcha field 'g-recaptcha-response' contains data ready to be sent (from 2nd request to external script):\n<br>");
		//print(chr(0x0D).chr(0x0A).$msg.chr(0x0D).chr(0x0A).$msg2);
		print("\n<br>".$msg."\n<br>"."<br>".$msg2."\n<br>");
	}
	
	function CheckCaptcha($googResp) {
		$fields_string = '';
		$fields = array(
			'secret' => '6LcB0VAUAAAAAMSXO8XjF1Weg-tctJqAIr2qyl1D',
			'response' => $googResp
		);
		$curl = @curl_init();
		@curl_setopt($curl, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
		@curl_setopt($curl, CURLOPT_POST, count($fields));
		@curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
		@curl_setopt($curl, CURLOPT_RETURNTRANSFER, True);
		
		$res = @curl_exec($curl);
		$GLOBALS['status_code_http'] = @curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$GLOBALS['status_code_https'] = @curl_getinfo($curl, CURLINFO_HTTPS_CODE);
		@curl_close($curl);
		
		$array = json_decode($res);
		return json_decode($res, true);
	}
	// Call CheckCaptcha (sends response from 2nd request batch back to goog for verif)
	$captcha_check = CheckCaptcha($_POST['g-recaptcha-response']);
	
	print_r("\n<br>google response (from 3rd request):\n<br>".$captcha_check);
	//print_r("\n<br>google json response detail:\n<br>captcha_check[0]:".$captcha_check[0]."\n<br>captcha_check[1]:".$captcha_check[1]);
	print("\n<br>google response detail:\n<br>");
	print("Type: ".gettype($captcha_check)."\n<br>content: ");
	//var_dump($captcha_check);
	print_r($captcha_check);
	/*foreach ($captcha_check['g-recaptcha-response'] as $address)
	{
	    echo "g-recaptcha-response item:". $address['address'] ."\n<br>";
	};
	*/
	if ($captcha_check['success']) {
		//If the user has checked the Captcha box
		print("\n<br>captcha contains success field set to true:\n<br>captcha_check['success']: ".$captcha_check['success']);
		print("\n<br>\n<br>other captcha response fieids:\n<br>captcha_check['challenge_ts']: ".$captcha_check['challenge_ts']);
		print("\n<br>\n<br>captcha_check['hostname']: ".$captcha_check['hostname']);
		print("\n<br>\n<br>Status codes: ".$GLOBALS['status_code_http']."\n<br>".$GLOBALS['status_code_https']."\n<br>");
		print("var dump GLOBALS['status_code_http:']\n<br>");
		var_dump($GLOBALS['status_code_http']);
		print("\n<br>var dump GLOBALS['status_code_https:']\n<br>");
		var_dump($GLOBALS['status_code_https']);
		/*foreach ($array['g-recaptcha-response'] as $address)
		{
		    echo "g-recaptcha-response item:". $address['address'] ."\n<br>";
		};
		*/
		print("\n<br>successful captcha check\n<br>");
	}
?>