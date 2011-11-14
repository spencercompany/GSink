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

	include_once( 'config.inc' );
	include_once( 'google-adsense.inc' );

	$No_HeaderFooter_Actions	=	Array( 
'CheckUniqueField', 'Process-SignUp', 'Process-Login', 'Process-UpdateAccounts', 'LogOut', 'ExecuteSync', 'Process-RecoverPasswordStep1', 'Process-RecoverPasswordStep2', 'Process-EditProfile', 'SetPassphraseCookie'
	);

	// actions not on this list will dump back to Login if there's no account in the session
	$No_Login_Actions		=	Array( 
			'Login', 'Process-Login', 'SignUp', 'Process-SignUp', 'CheckUniqueField', 'RecoverPasswordStep1', 'Process-RecoverPasswordStep1',
			'RecoverPasswordStep2', 'Process-RecoverPasswordStep2', 'MoreInfoLink'
	);	

	session_start();
	
	if( HAS_SSL )
	{
		if ( !empty( $_SESSION[ 'Account' ] ) && ( empty( $_SERVER[ 'HTTPS' ] ) || ( $_SERVER[ 'HTTPS' ] != 'on' ) ) )
		{
			Header( 'Location: https://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'SCRIPT_NAME' ] );
			exit;
		}
	}

	if ( isset( $_REQUEST['Action'] ) )	
		$Action		=	$_REQUEST['Action'];
	else					
	{
		if ( empty( $_SESSION[ 'Account' ] ) )				// if not logged in
			$Action		=	'Login';			// default to login form
		else								// if logged in
			$Action		=	'MainMenu';			// main menu
	}

	if ( !empty( $_SESSION[ 'Account' ] ) && empty( $_COOKIE[ 'Passphrase' ] ) && ( $Action != 'SetPassphraseCookie' ) )
		$Action			=	'GetPassphrase';
	
	if( $_GET[ 'Action' ] === 'LogOut' )
		$Action = 'LogOut';

	if ( empty( $_SESSION[ 'Account' ] ) && !in_array( $Action, $No_Login_Actions ) )
		$Action			=	'Login';

	$Page_Classes		=	$Action;

	if ( !in_array( $Action, $No_HeaderFooter_Actions ) )
		include_once( 'header.inc' );

	switch ( $Action )
	{
		case 'Login':
		default:
			include_once( 'login.php' );
		break;

		case 'LogOut':
			include_once( 'logout.php' );
		break;

		case 'Process-Login':
			include_once( 'process-login.php' );
		break;

		case 'ShowTerms':
			include_once( 'GSink_Terms_of_Service.html' );
		break;

		case 'MoreInfoLink':
			include_once( 'More_Info_Link.php' );
		break;

		case 'SignUp':
			include_once( 'signup.php' );
		break;

		case 'Process-SignUp':
			include_once( 'process-signup.php' );
		break;

		case 'EditProfile':
			include_once( 'editprofile.php' );
		break;

		case 'Process-EditProfile':
			include_oncE( 'process-editprofile.php' );
		break;

		case 'CheckUniqueField':
			include_once( 'checkuniquefield.php' );
		break;

		case 'MainMenu':
			include_once( 'mainmenu.php' );
		break;

		case 'Process-UpdateAccounts':
			include_once( 'process-updateaccounts.php' );
		break;

		case 'SyncUI':
			include_once( 'sync-ui.php' );
		break;

		case 'ExecuteSync':
			include_once( 'execute-sync.php' );
		break;

		case 'RecoverPasswordStep1':
			include_once( 'recover-password-step1.php' );
		break;

		case 'Process-RecoverPasswordStep1':
			include_once( 'process-recover-password-step1.php' );
		break;

		case 'RecoverPasswordStep2':
			include_once( 'recover-password-step2.php' );
		break;

		case 'Process-RecoverPasswordStep2':
			include_once( 'process-recover-password-step2.php' );
		break;

		case 'GetPassphrase':
			include_once( 'get-passphrase.inc' );
		break;

		case 'SetPassphraseCookie':
			include_once( 'set-passphrase-cookie.inc' );
		break;
	}

	if ( !in_array( $Action, $No_HeaderFooter_Actions ) )
		include_once( 'footer.inc' );

	session_commit();

?>