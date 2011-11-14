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

	function doQuery( $db, $q )
	{
		$res	=	$db->query( $q );
		$rec	=	$res->fetch_assoc();
		$res->close();
		return $rec;
	}

	$db     	=       new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );


	$q		=	'SELECT COUNT(*) as Count FROM ContactManager_Companies GROUP BY 1=1';
	$rec		=	doQuery( $db, $q );
	$Total		=	$rec[ 'Count' ];

	$q		=	'SELECT COUNT(*) as Count FROM ContactManager_Companies WHERE Last_Login_Date >= DATE_SUB( NOW(), INTERVAL 30 DAY )';
	$rec		=	doQuery( $db, $q );
	$LastMonth	=	$rec[ 'Count' ];

	$q		=	'SELECT COUNT(*) as Count FROM ContactManager_Companies WHERE Last_Login_Date >= DATE_SUB( NOW(), INTERVAL 30 DAY ) AND DATE( SignUp_Date ) != DATE( Last_Login_Date )';
	$rec		=	doQuery( $db, $q );
	$LastMonth1Plus	=	$rec[ 'Count' ];

	$db->close();

	echo $Total . " total active user records<br />\n";
	echo $LastMonth . " users have logged in at least once in the last month<br />\n";
	echo $LastMonth1Plus . " users have logged in at least once in the last month not counting their initial signup login.<br />\n";

?>