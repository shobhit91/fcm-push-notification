<?php
/*Template Name:[home]*/
get_header();?>
<div class="home_page">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
			<div class="col-md-6 classMargin ios_form">
				<center><h4>iOS Push Notification Tester</h4></center>
				<form action="" method="post" id="ios_form" enctype="multipart/form-data">
					<div class="custom-control marginBottom">
						<label class="custom-control-label col-md-5">Upload PEM fle</label>
						<input type="file" name="ios_certificate" id="ios_certificate" class="col-md-7" accept=".pem" >
					</div>
					<div class="custom-control field">
						<input type="text" name="ios_password" placeholder="Password or Passphrase" class="form-control" required>
					</div>
					<div class="custom-control field">
						<input type="text" name="ios_device_id" placeholder="Multiple device id's seprated by comma" class="form-control" required>
					</div>
					<div class="col-md-12">
						<div class="custom-control col-md-6">
						  <label class="custom-control-label" for="ios_development">Development</label>
						  <input type="radio" name="iOS_mode" value="development" id="ios_development">
						</div>	
						<div class="custom-control col-md-6">
						  <label class="custom-control-label" for="ios_production">Production</label>
						  <input type="radio" name="iOS_mode" value="production" id="ios_production">
						</div>
					</div>
					<div class="custom-control">
						<label class="custom-control-label">Notification Text</label>
						<textarea name="ios_content" class="form-control" placeholder="Enter Push Notification Message"></textarea>
					</div>
					<div class="col-md-12">
						<div class="custom-control col-md-6">
							 <label class="custom-control-label" for="ios_text">Text</label>
							 <input type="radio" class="custom-control-input" name="text_type" value="text" id="ios_text">
						</div>
						<div class="custom-control col-md-6">
						  <label class="custom-control-label" for="json-type">JSON</label>
						  <input type="radio" class="custom-control-input" name="text_type" value="json" id="json-type">
						</div>
					</div>
					<div class="custom-control">
						<input type="submit" name="ios_send" class="btn btn-primary btn-sm btn-block ios_send" value="Send">
					</div>
				</form>
			</div>
			<div class="col-md-6 classMargin android_form">
					<center><h4>Android Notification Tester</h4></center>
					<form action="" method="post" id="android_form">
					<div class="custom-control field">
						<input type="text" name="androd_api_key" placeholder="Enter API Key" class="form-control" required>
					</div>
					<div class="custom-control field android_device_ids">
						<input type="text" name="android_device_id" placeholder="Multiple device id's seprated by comma" class="form-control android_device_ids" required>
					</div>
					<div class="custom-control">
						<label class="custom-control-label">Notification Text</label>
						<textarea name="android_content" class="form-control gcm_textarea" placeholder="Enter Push Notification Message" ></textarea>
					</div>
					<div class="col-md-12">
						<div class="custom-control col-md-6">
							 <label class="custom-control-label" for="android_text">Text</label>
							 <input type="radio" class="custom-control-input" name="android_type" value="text" id="android_text">
						</div>
						<div class="custom-control col-md-6">
						  <label class="custom-control-label" for="android-json">JSON</label>
						  <input type="radio" class="custom-control-input" name="android_type" value="json" id="android-json">
						</div>
					</div>
					<div class="custom-control">
						<input type="submit" name="android_send" class="btn btn-primary btn-sm btn-block" value="Send">
					</div>
				</form>
			</div>
			</div>
		</div>
	</div>
</div>
<?php 
if(isset($_POST['ios_send'])){
	$device_id = $_POST['ios_device_id'];
	$ios_password= $_POST['ios_password'] ? $_POST['ios_password'] : '12345';
	$ios_certificate= $_POST['ios_certificate'];
	$text_type= $_POST['text_type'] ? $_POST['text_type'] : 'text' ;
	$iOS_mode= $_POST['iOS_mode']? $_POST['iOS_mode'] : 'development' ;

	if($text_type=='text'){
		$ios_content= $_POST['ios_content'];
	}
	if($text_type=='json'){
		$ios_content= $_POST['ios_content'];
	}
	
	if($iOS_mode=='production'){
		$url = 'ssl://gateway.push.apple.com:2195';
	}elseif($iOS_mode=='development'){
		$url = 'ssl://gateway.sandbox.push.apple.com:2195';
	}else{
		$url = 'ssl://gateway.sandbox.push.apple.com:2195';
	}

	if (move_uploaded_file($_FILES['ios_certificate']['tmp_name'], __DIR__.'../../certificates/'. 'aa.pem')) {
		$ios_certificate = __DIR__.'/certificates/'. 'aa.pem';
		$deviceIdExp=explode(',', $device_id);
		$deviceIDCount = count($deviceIdExp);
		
		for($i=0; $i<$deviceIDCount; $i++){
			$deviceToken = $deviceIdExp[$i];
			//$deviceToken = $device_id;
			$ctx = stream_context_create();
			$certificatePath = $ios_certificate;
			
			// Certificates1.pem is your certificate file
			stream_context_set_option($ctx, 'ssl', 'local_cert', $certificatePath);
			stream_context_set_option($ctx, 'ssl', 'passphrase', $ios_password);
		   
			// Open a connection to the APNS server
			$fp = stream_socket_client($url, $err,
				$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
			if (!$fp)
				exit("Failed to connect: $err $errstr" . PHP_EOL);
			// Create the payload body
			$body['aps'] = array(
				'alert' => array(
					'title' => 'Notification from pushTry.Tech',
					'body' => $ios_content,
				 ),
				'sound' => 'default'
			);
			// Encode the payload as JSON
			$payload = json_encode($body);
			// Build the binary notification
			$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
			// Send it to the server
			$result = fwrite($fp, $msg, strlen($msg));
		}
			// Close the connection to the server
			fclose($fp);
			if (!$result)
				echo '<div class="alert alert-danger">Message not delivered</div>' . PHP_EOL;
			else
				echo  '<div class="alert alert-success">Message successfully delivered</div>' . PHP_EOL;
	} else {
		echo '<div class="alert alert-danger">File was not uploaded</div>';
	}
}

if(isset($_POST['android_send'])){
	$androd_api_key = $_POST['androd_api_key'];
	$android_device_id = $_POST['android_device_id'];
	$android_content = $_POST['android_content'];
	$android_device_id = $_POST['android_device_id'];
	
	define( 'API_ACCESS_KEY', $androd_api_key);
	   
	$headers = array
	(
		'Content-Type: application/json',
		'Authorization: key=' . API_ACCESS_KEY
	);
	
	$deviceIdExp=explode(',', $android_device_id);
	$deviceIDCount = count($deviceIdExp);
	
	for($i=0; $i<$deviceIDCount; $i++){  
	
		$fields = array(
		 'to' => $android_device_id[$i] ,
		 'priority' => "high",
		 'data' =>[
			 "notification"=>"Nusik",
			 "title" => 'Notification from pushTry.Tech',
			 "body" =>$android_content ? $android_content : '',
		 ]
		);
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		curl_close( $ch );
	}
}
get_footer();?>