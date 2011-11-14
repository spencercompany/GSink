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


	class Cipher 
	{
		private $securekey, $iv;

		function __construct( $textkey ) 
		{
			$this->securekey 	= hash( 'sha256', $textkey, TRUE );
			$this->iv 		= mcrypt_create_iv( 32, MCRYPT_DEV_URANDOM );
		}

		function encrypt( $input ) 
		{
			return base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, $this->securekey, $input, MCRYPT_MODE_ECB, $this->iv ) );
		}

		function decrypt( $input ) 
		{
			return trim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, $this->securekey, base64_decode( $input ), MCRYPT_MODE_ECB, $this->iv ) );
		}
	}
?>
