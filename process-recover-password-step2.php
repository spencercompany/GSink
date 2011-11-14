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
 
    $db     	=       new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );

	// delete old, expired codes
	$q		=	'DELETE FROM ContactManager_Companies WHERE Expiration < NOW();';
	$db->query( $q );
	$q		=	'OPTIMIZE TABLE ContactManager_Companies;';
	$db->query( $q );

	// first confirm that the code is good.  otherwise people could post straight to this
	// processing program and change anyone's password.
	$q		=	'SELECT c.* FROM ContactManager_Companies as c, ContactManager_PasswordResetCodes as prc WHERE c.Id=prc.Company_Id AND prc.Expiration >= NOW() AND prc.ResetCode = \'' . $_REQUEST[ 'ResetCode' ] . '\' LIMIT 1';
	$res		=	$db->query( $q );
	$row		=	$res->fetch_assoc();
	$res->close();

	if ( ! $row )
	{
		// code is NOT good
		$db->close();

		Header( 'Location: ?Action=Login&Message=' . urlencode( 'Invalid or Expired Reset Code.' ) );
		exit;
	}

	$q	=	'UPDATE ContactManager_Companies SET Admin_Password_Hash = \'' . $db->real_escape_string( crypt( $_REQUEST[ 'Admin_Password' ] ) ) . '\' WHERE Id = \'' . $row[ 'Id' ] . '\' LIMIT 1';
	$db->query( $q );

	$db->close();

	Header( 'Location: ?Action=Login&Message=' . urlencode( 'Password updated.' ) );
?>

