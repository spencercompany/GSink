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

	$Straight_Insert_Fields		=	Array( 'Admin_Login', 'Company_Name', 'Email' );
	$Data				=	Array();

        $db     =       new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );

        $q      =       'SELECT Id FROM ContactManager_Companies WHERE Admin_Login=\'' . $_REQUEST[ 'Admin_Login' ] . '\' LIMIT 1';
        $res    =       $db->query( $q );
        $row    =       $res->fetch_assoc();
	$res->close();

	if ( $row )
		$_REQUEST[ 'Error' ]	=	'This login name is already taken.';
	else
	{
		$q      =       'SELECT Id FROM ContactManager_Companies WHERE Email=\'' . $_REQUEST[ 'Email' ] . '\' LIMIT 1';
		$res    =       $db->query( $q );
		$row    =       $res->fetch_assoc();
		$res->close();

		if ( $row )
			$_REQUEST[ 'Error' ]	=	'This Email address is already taken.';
		else
		{
			foreach ( $Straight_Insert_Fields as $Field )
			{
				if ( isset( $_REQUEST[ $Field ] ) )
					$Data[ $Field ] 	=	$db->real_escape_string( $_REQUEST[ $Field ] );
			}

			$Data[ 'Admin_Password_Hash' ]		=	crypt( $_REQUEST[ 'Admin_Password' ] );
			$Data[ 'SignUp_Date' ]			=	date( 'Y-m-d H:i:s' );
			$Data[ 'Status' ]			=	'Inactive';

			$q	=	'INSERT INTO ContactManager_Companies (' . join( ', ', array_keys( $Data ) ) . ') VALUES (\'' . join( '\', \'', array_values( $Data ) ) . '\')';
			$db->query( $q );

			if ( !empty( $db->error ) )
				$_REQUEST[ 'Error']	=	$db->error;
		}
	}

	$db->close();

	if ( !empty( $_REQUEST['Error'] ) )
	{
		include_once( 'header.inc' );
		include_once( 'signup.php' );
		include_once( 'footer.inc' );
	}
	else
		Header( 'Location: ?Action=Login&Message=' . urlencode( "Account saved." ) );
?>
