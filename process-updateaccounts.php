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

    $db     =       new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );

	$Data	=	Array();
	$Deletes=	Array();

	include_once( 'cipher.inc' );

	$Cipher		=	new Cipher( $_REQUEST[ 'Passphrase' ] );

	foreach ( $_REQUEST as $key=>$val )
	{
		$key_parts	=	preg_split( '/:/', $key );
		//$key_parts	=	split( ':', $key );

		switch ( $key_parts[0] )
		{
			case 'GoogleAccount':
			case 'GooglePassword':
			case 'Account_Type':
				if ( !empty( $val ) )
					$Data[ $key_parts[ 1 ] ][ $key_parts[0] ]	=	$db->real_escape_string( $val );
			break;

			case 'Delete':
				$Deletes[]	=	$key_parts[1];
			break;
		}
	}

	foreach ( $Deletes as $Delete )
	{
		$q	=	'DELETE FROM ContactManager_Contacts WHERE Id=\'' . $Delete . '\' LIMIT 1';
		$db->query( $q );
		
		if ( isset( $Data[ $Delete ] ) )
			unset( $Data[ $Delete ] );
	}

	foreach ( $Data as $Id=>$Contact )
	{
		if ( !empty( $Contact[ 'GooglePassword' ] ) )
			$Contact[ 'GooglePassword' ]	=	$db->real_escape_string( $Cipher->encrypt( $Contact[ 'GooglePassword' ] ) ); 
		if ( $Id == 'New' )
		{
			if ( count( $Contact ) > 1 )
			{
				$Contact[ 'Company_Id' ]	=	$_SESSION[ 'Account' ][ 'Id' ];
				$q      =       'INSERT INTO ContactManager_Contacts (' . join( ', ', array_keys( $Contact ) ) . ') VALUES (\'' . join( '\', \'', array_values( $Contact ) ) . '\')';
				$db->query( $q );
			}
		}
		else
		{
			$Fields	=	Array();
			foreach ( $Contact as $Field=>$Value )
				$Fields[]	=	$Field . '=\'' . $Value . '\'';

			$q	=	'UPDATE ContactManager_Contacts SET ' . join( ', ', $Fields ) . ' WHERE Id=\'' . $Id . '\' LIMIT 1';
			$db->query( $q );
		}
	}

	$db->query( 'OPTIMIZE TABLE ContactManager_Contacts;' );

	$db->close();

	Header( 'Location: ?Action=MainMenu' );
?>