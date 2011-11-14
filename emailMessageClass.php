<?php
	/*
		Author: Christopher Spencer (christopher@spencercompany.com)
		Copyright (c) 2009-2010, The Spencer Company. All Rights Reserved. 
		
		GSink is free software: you can redistribute it and/or modify it under the terms of the 
		GNU General Public License as published by the Free Software Foundation, either 
		version 3 of the License, or (at your option) any later version.

 		GSink is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
		without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR 
		PURPOSE. See the GNU General Public License for more details.

 		You should have received a copy of the GNU General Public License along with GSink 
		If not, see the license here: http://www.gnu.org/copyleft/gpl.html.
	*/

	// this class attempts to simplfy all functionality having to do with Emails that should be definable on a per-company basis.
	// should completely abstract out all of the interface to the database, and does provide a shortcut method for sending the message
	// out using the email-functions.inc simple mailer.

	include_once( 'class.phpmailer.php' );

	class emailMessage
	{
		private $db		=	null;
		private $Data		=	Array();

		function __construct( $Email_Id, $Company_Id )
		{
			$this->db     		=       new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );

			$this->Data[ 'Email_Id' ]	=	$Email_Id;
			$this->Data[ 'Company_Id' ]	=	$Company_Id;

			$row			=	$this->fetchEmail( $Email_Id, $Company_Id );

			// this message doesn't yet exist for this company, fetch the default (Company_Id == 0)
			if ( !$row )
				$row		=	$this->fetchEmail( $Email_Id, 0 );

			if ( $row )
			{
				$this->SetFrom( $row[ 'From' ] );
				$this->SetSubject( $row[ 'Subject' ] );
				$this->SetMessage( $row[ 'Message' ] );
			}
		}

		function __destruct()
		{
			$this->db->close();
		}

		private function 	fetchEmail( $Email_Id, $Company_Id )
		{
			$q		=	'SELECT * FROM ContactManager_Emails WHERE Email_Id = \'' . $Email_Id . '\' AND Company_Id = \'' . $Company_Id . '\' LIMIT 1';
			$res		=	$this->db->query( $q );
			$row		=	$res->fetch_assoc();
			$res->close();

			if ( $row )
			{
				foreach ( $row as $key=>$val )
					$row[ $key ] 		=	preg_replace( '/\\\\(.)/', '\\1', $val );
			}

			return $row;
		}

		public function		SaveToDB()
		{
			$Data	=	Array();			// we don't want to keep the data in already escaped form because it's intended to be usable
			foreach ( $this->Data as $key=>$val )		// in web forms too		
				$Data[ $key ]		=	$this->db->real_escape_string( $val );
			$q	=	'REPLACE INTO ContactManager_Emails (`' . join( '`, `', array_keys( $Data ) ) . '`) VALUES (\'' . join( '\', \'', array_values( $Data ) ) . '\')';
			$this->db->query( $q );
			unset( $Data ); 
		}

		public function		GetFrom()
		{
			if ( !empty( $this->Data[ 'From' ] ) )
				return $this->Data[ 'From' ];
			else
				return null;
		}

		public function		GetSubject()
		{
			if ( !empty( $this->Data[ 'Subject' ] ) )
				return $this->Data[ 'Subject' ];
			else
				return null;
		}

		public function		GetMessage()
		{
			if ( !empty( $this->Data[ 'Message' ] ) )
				return $this->Data[ 'Message' ];
			else
				return null;
		}

		public function		SetFrom( $value )
		{
			$this->Data[ 'From' ] 		=	$value;
		}

		public function		SetSubject( $value )
		{
			$this->Data[ 'Subject' ]	=	$value;
		}

		public function		SetMessage( $value )
		{
			$this->Data[ 'Message' ]	=	$value;
		}

		public function		SetFromPost( $PostData )
		{
			$Fields		=	Array( 'From', 'Subject', 'Message' );
			
			foreach ( $Fields as $Field )			// not escaping the data here because we want to be able to use it in web forms, too
				if ( !empty( $PostData[ $Field ] ) )
					$this->Data[ $Field ] 		=	$PostData[ $Field ];
		}

		public function		SendEmail( $To )
		{
			if ( empty( $this->Data ) ) return;

			$mail = new PHPMailer(); 
			$mail->AddAddress( $To, 'GSink User' );
			$mail->Subject	=	$this->GetSubject();
			$mail->Body	=	$this->GetMessage();
			$mail->From	=	$this->GetFrom();
			$mail->Mailer	=	'smtp';
			
			$mail->Send();
		}
	}
?>
