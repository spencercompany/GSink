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


	// checks for an already existent value on a field that's supposed to be unique.
	// designed to be usable for both login and Email address.

	$FieldName	=	$_REQUEST[ 'FieldName' ];
	$FriendlyName	=	$_REQUEST[ 'FriendlyName' ];	// for use in UI messages

	if ( empty( $_REQUEST[ 'Value' ] ) )
	{
		echo "<span id='red'>" . $FriendlyName . " cannot be left blank.</span>";
		exit;
	}	

	$WhereParts	=	Array();

	$Value		=	$_REQUEST[ 'Value' ];		// value being checked
	
	$WhereParts[]	=	$FieldName . '=\'' . $Value . '\'';
	if ( !empty( $_SESSION[ 'Account' ] ) )
		$WhereParts[]	=	'Id != \'' . $_SESSION[ 'Account' ][ 'Id' ] . '\'';
	
        $db     =       new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );

	$q	=	'SELECT Id FROM ContactManager_Companies WHERE ' . join( ' AND ', $WhereParts ) . ' LIMIT 1';
	$res	=	$db->query( $q );
	$row	=	$res->fetch_assoc();

	if ( $row ) 	echo "<span id='red'>Already in use!</span>";
	else 		echo "<span id='green'>" . $FriendlyName . " OK.</span>";

	$res->close();

	$db->close();
	
?>
