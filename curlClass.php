<?php
	/*
		Author: Katherine Harvest
		Copyright (c) 2008, Katherine Harvest. All Rights Reserved. 
		
		Distributed under GPL
	*/

	define( 'COOKIEFILE', 'cookiefile' );

	class curlClass 
	{
		private $ch;
		private $pageText;			// text of last page loaded
		private $lastPageURL = '';		// used for referer
		private $info = null;

		protected function	InitHandle( $url, $MethodSpecificHeaders = Array() )
		{
			$this->ch		=	curl_init();	

			curl_setopt( $this->ch, CURLOPT_URL, $url );
			curl_setopt( $this->ch, CURLOPT_HEADER, 0 );
			curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $this->ch, CURLOPT_FOLLOWLOCATION, 1 );
			curl_setopt( $this->ch, CURLOPT_AUTOREFERER, 1 );
			curl_setopt( $this->ch, CURLOPT_COOKIEFILE, COOKIEFILE );
			curl_setopt( $this->ch, CURLOPT_COOKIEJAR, COOKIEFILE );
			curl_setopt( $this->ch, CURLOPT_VERBOSE, DEBUG );

			$ExtraHeaders		=	Array(				// for some reason curl uses something weird by default, even though
				'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
				'Accept-Language: en-us,en;q=0.5',
				'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
				'Expect:'
			);
			if ( !empty( $MethodSpecificHeaders ) )
				$ExtraHeaders	=	array_merge( $ExtraHeaders, $MethodSpecificHeaders );
			curl_setopt( $this->ch, CURLOPT_HTTPHEADER, $ExtraHeaders );

			if ( !empty( $this->lastPageURL ) )
				curl_setopt( $this->ch, CURLOPT_REFERER, $this->lastPageURL );
			$this->lastPageURL	=	$url;
		}

		private	function	ExecHandle()
		{
			$this->pageText 	=	curl_exec( $this->ch );
			$this->info 		= 	curl_getinfo( $this->ch );
			curl_close( $this->ch );
			unset( $this->ch );
		}

		public function		DoGet( $url )
		{
			$this->InitHandle( $url );
			curl_setopt( $this->ch, CURLOPT_POST, 0 );
			$this->ExecHandle();
			return $this->pageText;	
		}

		public function		DoPost( $url, $data )
		{
			$ExtraHeaders		=	Array(				// for some reason curl uses something weird by default, even though
				'Content-Type: application/x-www-form-urlencoded'	// the docs say it uses x-www-form-urlencoded
			);

			$this->InitHandle( $url, $ExtraHeaders );
			curl_setopt( $this->ch, CURLOPT_POST, 1 );
			if ( is_array( $data ) )
				curl_setopt( $this->ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );
			else
				curl_setopt( $this->ch, CURLOPT_POSTFIELDS, $data );
			$this->ExecHandle();
			return $this->pageText;
		}

		public function		GetPageDOM()
		{
			$myDOM				=	new DOMDocument;
			$myDOM->formatOutput		=	true;
			$myDOM->preserveWhitespace	=	false;

			@$myDOM->loadHTML( $this->pageText );

			return $myDOM;
		}

		public function		GetXMLDOM()
		{
			$myDOM				=	new DOMDocument;
			$myDOM->formatOutput		=	true;
			$myDOM->preserveWhitespace	=	false;

			@$myDOM->loadXML( $this->pageText );

			return $myDOM;
		}

		public function		GetHTTPCode()
		{
			if ( isset( $this->info ) && isset( $this->info[ 'http_code' ] ) )
				return $this->info[ 'http_code' ];
			else
				return null;
		}
	}

?>
