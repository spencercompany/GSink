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

	$_SESSION	=	Array();

	session_destroy();

	setcookie( 'Passphrase', '', time() - ( 24 * 60 * 60 ) );	// set passphrase cookie to blank, expiration date 24 hours ago
									// hopefully this makes the browser drop the cookie.

	header( 'Location: http://' . $_SERVER[ 'HTTP_HOST' ] );
?>
