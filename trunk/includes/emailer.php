<?php
//	$info['chrEmail'] = "jsummers@techitsolutions.com"; // Test E-mail Address, unrem to use.
	$info['chrFromEmail'] = "Rhythm <programmers@techitsolutions.com>";

	// This is added so that the Pear module can differentiate between HTML emails and plain text emails.
	//dtn: This is added so that the Pear module can differentiate between HTML emails and plain text emails.
	$er = error_reporting(0); 		//dtn: This is added in so that we don't get spammed with PEAR::isError() messages in our tail -f ..
	include_once('Mail.php');
	include_once('Mail/mime.php');

	$crlf = "\n";
	mb_language('en');
	mb_internal_encoding('UTF-8');
	
	$mime = new Mail_mime($crlf);	

	$subject = decode($info['chrSubject']);
	$subject = mb_convert_encoding($subject, 'UTF-8',"AUTO");
	$subject = mb_encode_mimeheader($subject);

	$hdrs = array('From'    => $info['chrFromEmail'],
				  'Subject' => $subject
			  );
	
	$mime->_build_params['text_encoding'] ='quoted-printable';
	$mime->_build_params['text_charset'] = "UTF-8";
	$mime->_build_params['html_charset'] = "UTF-8";

	$Message = decode($info['txtMsg']);
		
	$mime->setHTMLBody($Message);
		
	$body = $mime->get();
	$hdrs = $mime->headers($hdrs);
	$body = mb_convert_encoding($body, "UTF-8", "UTF-8"); 
	
	$mail =& Mail::factory('mail');
	$mail->send($info['chrEmail'], $hdrs, $body);
?>