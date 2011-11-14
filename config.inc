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

	// database login parameters
    	define( 'DB_HOST', 		'localhost' );
        define( 'DB_NAME', 		'project_gsink' );
        define( 'DB_USER', 		'root' );
        define( 'DB_PASS', 		'1234r' );

	// not using this in current implementation
        define( 'SMTP', 		'' );
        define( 'NET_BUFFER_SIZE', 	1024 );

	// general settings and redirection
		error_reporting( 0 );
		
		define( 'HAS_SSL', false ); // IMPORTANT! SET THIS TO TRUE IN ORDER TO USE THE SSL PROTECTION OR MAKE SURE YOUR NETWORK IS PROTECTED!

		define( 'GOOGLE_AD_CLIENT', '' );
		
		define( 'DOMAIN', $_SERVER['HTTP_HOST'] );

		define( 'DEBUG', 0 );

		if ( !empty( $_SERVER[ 'HTTPS' ] ) && ( $_SERVER[ 'HTTPS' ] == 'on' ) )
			define( 'URL_PREFIX', 	'https://'	);
		else
			define( 'URL_PREFIX', 	'http://'	);
		

		define( 'MAIN_DIR', 	URL_PREFIX . $_SERVER[ 'HTTP_HOST' ] . dirname( $_SERVER[ 'SCRIPT_NAME' ] ) );
?>
