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

	unset( $_SESSION[ 'Account' ] );
	
        $db     =       new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );

	$q	=	'SELECT * FROM ContactManager_Companies WHERE Admin_Login = \'' . $_REQUEST[ 'Admin_Login' ] . '\' LIMIT 1';
	$res	=	$db->query( $q );
	$row	=	$res->fetch_assoc();
	$res->close();	

	if ( crypt( $_REQUEST[ 'Admin_Password' ], $row[ 'Admin_Password_Hash' ] ) == $row[ 'Admin_Password_Hash' ] )
	{
		$q	=	'UPDATE ContactManager_Companies SET Last_Login_Date = \'' . date( 'Y-m-d H:i:s' ) . '\' WHERE Id=\'' . $row[ 'Id' ] . '\' LIMIT 1';
		$db->query( $q );

		$_SESSION[ 'Account' ]		=	$row;		
	}

	$db->close();

	if ( !empty( $_SESSION[ 'Account' ] ) )
		Header( 'Location: ?Action=MainMenu' );
	else
		Header( 'Location: ?Action=Login&Message=' . urlencode( 'Login failed.' ) );
?>
