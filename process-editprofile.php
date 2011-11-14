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

	include( 'emailMessageClass.php' );

	$email   	=       new emailMessage( 'INVALID_PASSWORD', $_SESSION[ 'Account' ][ 'Id' ] );
	$email->SetFromPost( $_REQUEST );
	$email->SaveToDB();

	// the profile settings
    $db     =       new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );

	$Data					=	Array();
	$Data[ 'Id' ]				=	$_SESSION[ 'Account' ][ 'Id' ];
	$Data[ 'Admin_Password_Hash' ]		=	$db->real_escape_string( crypt( $_REQUEST[ 'Admin_Password' ] ) );
	$Data[ 'Company_Name' ]			=	$db->real_escape_string( $_REQUEST[ 'Company_Name' ] );
	$Data[ 'Email' ]			=	$db->real_escape_string( $_REQUEST[ 'Email' ] );

	// must include Id field for this to work
	$q      =       'REPLACE INTO ContactManager_Emails (`' . join( '`, `', array_keys( $Data ) ) . '`) VALUES (\'' . join( '\', \'', array_values( $Data ) ) . '\')';

	$db->query( $q );
	
	$db->close();

	Header( 'Location: ?Action=EditProfile&Message=' . urlencode( 'Changes saved.' ) );
?>