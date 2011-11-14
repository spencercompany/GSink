<?php
/*
	Author: Christopher Spencer (christopher@spencercompany.com)
	Copyright (c) 2011, The Spencer Company. All Rights Reserved. 
	
	GSink is free software: you can redistribute it and/or modify it under the terms of the 
	GNU General Public License as published by the Free Software Foundation, either 
	version 3 of the License, or (at your option) any later version.

	GSink is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
	without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR 
	PURPOSE. See the GNU General Public License for more details.

	You should have received a copy of the GNU General Public License along with GSink 
	If not, see the license here: http://www.gnu.org/copyleft/gpl.html.
*/

	include_once( 'class.phpmailer.php' );

	function createRandomPassword() 
	{
		srand( ( double )microtime() * 1000000 );

		$chars 		= "abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$pass 		= '' ;

		while( strlen( $pass ) < 40 )
		{
			$num 	= rand() % strlen( $chars );
			$pass 	= $pass . substr( $chars, $num, 1 );
		}

		return $pass;
	}

        $db     	=       new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );

	$q		=	'SELECT Id, Admin_Login, Company_Name FROM ContactManager_Companies WHERE Email=\'' . $_REQUEST[ 'Email' ] . '\' LIMIT 1';
	$res		=	$db->query( $q );
	$row		=	$res->fetch_assoc();
	$res->close();

	if ( $row )
	{
		$Data			=	Array();
		$Data[ 'ResetCode' ]	=	createRandomPassword();
		$Data[ 'Company_Id' ]	=	$row[ 'Id' ];
		$Data[ 'Expiration' ]	=	date( 'Y-m-d H:i:s', time() + 60 * 60 * 12 );	// expire after 12 hours

		$q	=	'INSERT INTO ContactManager_PasswordResetCodes (' . join( ', ', array_keys( $Data ) ) . ') VALUES (\'' . join( '\', \'', array_values( $Data ) ) . '\')';
		$db->query( $q );

		$Link	=	'http://' . $_SERVER[ 'HTTP_HOST' ] . dirname( $_SERVER[ 'SCRIPT_NAME' ] ) . '/index.php?Action=RecoverPasswordStep2&ResetCode=' . $Data[ 'ResetCode' ];

		$To			=	$_REQUEST[ 'Email' ];
		$Subject		=	'Google Contacts Manager password reset link.';
		$Message		=	'

Dear ' . $row[ 'Admin_Login' ] . ',

You are receiving this message because someone (hopefully you) entered your Email on the \'lost password\' link of
Google Contact Manager.  Our records show that you are the contact administrator for ' . $row[ 'Company_Name' ] . '.

To reset your password, please follow the following link:

' . $Link . '

Thank you.
						';

		$mail = new PHPMailer();
		$mail->AddAddress( $To, 'GSink User' );
		$mail->Subject  =       $Subject;
		$mail->Body     =       $Message;
		$mail->Mailer   =       'smtp';
		
		$mail->Send();

		$db->close();

		Header( 'Location: ?Action=Login&Message=' . urlencode( 'Message sent.' ) );
	}
	else
	{
		$db->close();

		Header( 'Location: ?Action=Login&Message=' . urlencode( 'Email not found.' ) );
	}
?>
