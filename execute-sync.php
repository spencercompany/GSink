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


	header( "Cache-Control: no-cache, must-revalidate" ); // HTTP/1.1
	header( "Expires: Sat, 26 Jul 1997 05:00:00 GMT" ); // Date in the past

	include_once( 'emailMessageClass.php' );
	include_once( 'curlClass.php' );
	include_once( 'cipher.inc' );

	// ***************************************** INTERFACE TO JAVASCRIPT UI IN PARENT WINDOW *********************************************

	class SyncUI
	{
		private function	outputJS( $method, $value )
		{
			echo "<script type='text/javascript'>parent.myUI." . $method . "( " . $value . " );</script>\n\n";
			ob_flush(); flush(); 
		}

		public function 	updateProgress( $percentage, $StartTS )
		{
			$this->outputJS( 'updateProgressBar', $percentage );

			$CurrentTS		=	time();
			$this->showCurrentTime( date( 'H:i:s', $CurrentTS ) );

			if ( $percentage > 0 )
			{
				$TimeSoFar		=	$CurrentTS - $StartTS;
				$PredictedTotalTime	=	( $TimeSoFar * 100 ) / $percentage;
				$PredictedEndTS		=	$StartTS + $PredictedTotalTime;

				$this->showPredictedEndTime( date( 'H:i:s', $PredictedEndTS ) );
			}
		}

		public function		updateSyncing( $text )
		{
			$this->outputJS( 'updateSyncing', "'$text'" );
		}

		public function		updateFrom( $text )
		{
			$this->outputJS( 'updateFrom', "'$text'" );
		}

		public function		updateTo( $text )
		{
			$this->outputJS( 'updateTo', "'$text'" );
		}

		public function		updateAdds( $num )
		{
			$this->outputJS( 'updateAdds', $num );
		}

		public function		updateFailedAdds( $num )
		{
			$this->outputJS( 'updateFailedAdds', $num );
		}

		public function		updateUpdates( $num )
		{
			$this->outputJS( 'updateUpdates', $num );
		}

		public function		updateFailedUpdates( $num )
		{
			$this->outputJS( 'updateFailedUpdates', $num );
		}

		public function		showReturnButton()
		{
			$this->outputJS( 'showReturnButton', '' );
		}

		public function		showAccountError( $text )
		{
			$this->outputJS( 'showAccountError', "'$text'" );
		}

		public function		showPageError( $text )
		{
			$this->outputJS( 'showPageError', "'$text'" );
		}

		public function		showCurrentTime( $text )
		{
			$this->outputJS( 'showCurrentTime', "'$text'" );
		}

		public function		showPredictedEndTime( $text )
		{
			$this->outputJS( 'showPredictedEndTime', "'$text'" );
		}
	}

	// ***************************************** ABSTRACT CLASS FOR GOOGLE CONTACTS CALLS *********************************************

	class GoogleContactsClass extends curlClass
	{
		var 	$appId				=	'GoogleContactManager-0.1';
		var 	$AuthToken			=	null;
		var	$Account			=	null;
		var	$ContactManager_Contacts_Id	=	null;
		//var 	$Contacts			=	Array();
		var	$ContentType			=	null;
		var 	$IfMatch			=	null;
		var 	$XHTTPMethodOverride		=	null;
		var 	$db				=	null;
		var 	$Contacts_Query_Resource	=	null;

		function	__construct() 
		{
			$this->db     	=       new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );
		}

		function	__destruct()
		{
			$q		=	'DELETE FROM ContactManager_ListEntries WHERE ContactManager_Contacts_Id = \'' . $this->ContactManager_Contacts_Id . '\'';
			$this->db->query( $q );
			$q		=	'OPTIMIZE TABLE ContactManager_ListEntries';
			$this->db->query( $q );
			$this->db->close();
		}

                function        InitHandle( $url, $MethodSpecificHeaders = Array() )
		{	
			if ( !empty( $this->AuthToken ) )
				$MethodSpecificHeaders[]	=	'Authorization: GoogleLogin auth=' . $this->AuthToken;
				
				//$MethodSpecificHeaders[]	= "GData-Version: 2";

			// blank out headers being replaced
			$NewHeaders			=	Array();
			foreach ( $MethodSpecificHeaders as $Header )
				if ( ! 	( 
						( ( $this->ContentType ) && ( substr( $Header, 0, 13 ) == 'Content-Type:' ) ) 
						|| 
						( ( $this->IfMatch ) && ( substr( $Header, 0, 9 ) == 'If-Match:' ) ) 
						||
						( ( $this->XHTTPMethodOverride ) && ( substr( $Header, 0, 23 ) == 'X-HTTP-Method-Override:' ) )
					) 
				)
					$NewHeaders[]	=	$Header;
			$MethodSpecificHeaders		=	$NewHeaders;

			if ( !empty( $this->ContentType ) )
			{
				$MethodSpecificHeaders[]	=	'Content-Type: ' . $this->ContentType;
				$this->ContentType		=	null;
			}

			if ( !empty( $this->IfMatch ) )
			{
				$MethodSpecificHeaders[]	=	'If-Match: ' . $this->IfMatch;
				$this->IfMatch			=	null;
			}

			if ( !empty( $this->XHTTPMethodOverride ) )
			{
				$MethodSpecificHeaders[]	=	'X-HTTP-Method-Override: ' . $this->XHTTPMethodOverride;
				$this->XHTTPMethodOverride	=	null;
			}

			parent::InitHandle( $url, $MethodSpecificHeaders );
		}

		function Authenticate( $Account, $Password )
		{
			// destroy data for any previous account we dealt with, we're switching to a new account!
			$this->AuthToken	=	null;
			$this->Account		=	null;
			$this->Contacts		=	Array();

			$Data		=	Array();

			$Data[ 'accountType' ]		=	'HOSTED_OR_GOOGLE';
			$Data[ 'Email' ]		=	$Account;
			$Data[ 'Passwd' ]		=	$Password;
			$Data[ 'service' ]		=	'cp';
			$Data[ 'source' ]		=	$this->appId;
			
			
			$text		=	$this->DoPost( 'https://www.google.com/accounts/ClientLogin', $Data );

			$Tokens		=	preg_split( "/\n/", $text );
			foreach ( $Tokens as $Token )
			{
				$TokenParts	=	preg_split( '/=/', $Token );
				if ( $TokenParts[0] == 'Auth' )
				{
					$this->AuthToken	=	$TokenParts[1];
					break;
				}
			}

			if ( !empty( $this->AuthToken ) )
			{
				$this->Account	=	$Account;

				$q		=	'SELECT Id FROM ContactManager_Contacts WHERE GoogleAccount=\'' . $Account . '\' LIMIT 1';
				$res		=	$this->db->query( $q );
				$row		=	$res->fetch_assoc();
				$res->close();

				$this->ContactManager_Contacts_Id	=	$row[ 'Id' ];

				return true;
			}

			return false;
		}

		private function ProcessContactProperty( $node, $depth = 1 )
		{
			// turns a DOM node (and all its children) into my internal representation
			if ( $node->nodeName == '#text' ) 	return $node->nodeValue;

			$Property			=	Array();

			//$Property[ 'Value' ]		=	$node->nodeValue;
			$Property[ 'Name' ]		=	$node->nodeName;
			if ( $node->attributes )
				foreach ( $node->attributes as $Attribute )
					$Property[ 'Attributes' ][ $Attribute->name ]	=	$Attribute->value;

			$child		=	$node->firstChild;
			while ( $child )
			{
				$Property[ 'children' ][]			=	$this->ProcessContactProperty( $child, $depth + 1 );
				$child						=	$child->nextSibling;
			}

			return $Property;
		}

		private function BuildContactProperty( $DOMDocument, $Property )
		{
			// turns my internal representation of a DOM node back into an actual DOM node for use in $DOMDocument
			if ( !is_array( $Property ) ) 	
				return $DOMDocument->createTextNode( $Property );	

			$ret		=	$DOMDocument->createElement( $Property[ 'Name' ] );
			if ( !empty( $Property[ 'Attributes' ] ) )
				foreach ( $Property[ 'Attributes' ] as $Name=>$Value )
					$ret->setAttribute( $Name, $Value );

			if ( !empty( $Property[ 'children' ] ) )
				foreach ( $Property[ 'children' ] as $PropData )
					$ret->appendChild( $this->BuildContactProperty( $DOMDocument, $PropData ) );

			return $ret;
		}

		function GetAllContacts()
		{
			global	$myUI;
			$Address 	=	null;

			$Processed	=	0;
			do
			{
				$feed	=	'http://www.google.com/m8/feeds/contacts/' . urlencode( $this->Account ) . '/full?max-results=1000&start-index=' . ( $Processed + 1 );
				$text	=	$this->DoGet( $feed );
				$DOM	=	$this->GetXMLDOM();

				$Entries	=	$DOM->getElementsByTagName( 'entry' );
				foreach ( $Entries as $Entry )
				{
					//echo "<xmp>" . $Entry->ownerDocument->saveXML( $Entry ) . "</xmp>\n";
					$FullEntry	=	& $Entry;
					$Id		=	$Entry->getElementsByTagName( 'id' )->item(0)->nodeValue;
					$Title		=	$Entry->getElementsByTagName( 'title' )->item(0)->nodeValue;

					$Links		=	$Entry->getElementsByTagName( 'link' );		// get this contact's edit link
					unset( $EditLink );	
					foreach ( $Links as $Link )
					{
						if ( $Link->getAttribute( 'rel' ) == 'edit' )
						{
							$EditLink	=	$Link->getAttribute( 'href' );
							break;
						}
					}

					// start out by getting this off the editlink, then if there's a gd:extendedProperty
					// with a value set, overwrite this fron that.
					$Original_Account_Id		=	basename( $EditLink );

					// Make an array of *all* properties in the gd: namespace, so we can carry them over in the sync.
					$Properties	=	Array();
					$child		=	$Entry->firstChild;
					while ( $child )
					{
						if ( ( $child->nodeName == 'gd:extendedProperty' ) && ( $child->getAttribute( 'name' ) == 'original.account.id' ) )
							// This contact was written during a previous sync.  Make sure to keep syncing it to the same
							// source contact.
							$Original_Account_Id		=	$child->getAttribute( 'value' );
						else
						{
							$parts		=	preg_split( '/:/', $child->nodeName );
							if ( ( $parts[0] == 'gd' ) || ( $parts[ 0 ] == 'content' ) )
							{
								$Property			=	$this->ProcessContactProperty( $child );
								$Properties[]		 	=	$Property;
							}
						}

						$child		=	$child->nextSibling;
					}

					$Emails		=	$Entry->getElementsByTagName( 'email' );
					foreach ( $Emails as $Email )		// get a primary Email.  This will uniquely identify this contact across contact lists.
					{
						$Address	=	$Email->getAttribute( 'address' );
						$Primary	=	$Email->getAttribute( 'primary' );

						// This is where a contact actually gets added to the list of contacts.
						// This means contacts without a primary Email are essentially ignored.
						if ( $Primary == 'true' )
							break;
					}	// foreach ( email addresses of this contact )
					
					// DONE FETCHING ALL CONTACT DATA -- insert into database
					$Contact			=	compact( 'Title', 'Address', 'Original_Account_Id', 'EditLink', 'Properties', 'FullEntry' );
					//$this->Contacts[ $Address ]	=	& $Contact;

					// insert into the database
					$Data					=	Array();
					$Data[ 'ContactManager_Contacts_Id' ]	=	$this->ContactManager_Contacts_Id;
					$Data[ 'Address' ]			=	$this->db->real_escape_string( $Address );
					$Data[ 'Original_Account_Id' ]		=	$this->db->real_escape_string( $Original_Account_Id );
					$Data[ 'Serialized_Entry' ]		=	$this->db->real_escape_string( serialize( $Contact ) );
					$tmp	=	$FullEntry->getAttribute( 'xmlns' );
					if ( empty( $tmp ) )	// insert these here.  without them in the text, loadXML complains when we
					{			// try to rebuild the DOM out of the text in the dataase.
						$FullEntry->setAttribute( 'xmlns', 	'http://www.w3.org/2005/Atom' );
						$FullEntry->setAttribute( 'xmlns:gd', 	'http://schemas.google.com/g/2005' );
					}
					$tmp	=	$FullEntry->getAttribute( 'xmlns:gContact' );
					if ( empty( $tmp ) )
						$FullEntry->setAttribute( 'xmlns:gContact',
											'http://schemas.google.com/contact/2008' );
					$Data[ 'Entry_HTML' ]			=	$this->db->real_escape_string( $FullEntry->ownerDocument->saveXML( $FullEntry ) );

					$q	=	'INSERT INTO ContactManager_ListEntries (' . join( ', ', array_keys( $Data ) ) . ') VALUES (\'' . join( '\', \'', array_values( $Data ) ) . '\')';
					$this->db->query( $q );

				}	// foreach ( individual contact entries )
				$Processed	+=	$Entries->length;

				$myUI->ShowPageError( 'Fetched ' . $Processed . ' contacts from ' . $this->Account . ' ...' );
			} // HTTP GET requests loop
			while ( $Entries->length > 0 );

			return;
		}	// GetAllContacts

		function PrintContacts( $Title )
		{
			echo "<table class='form-table'>\n";
			echo "\t<tr>\n\t\t<th colspan=2>" . $Title . "</th>\n\t</tr>\n";
			echo "\t<tr>\n\t\t<th>Title</th>\n\t\t<th>Email</th>\n\t</tr>\n";
			foreach ( $Contacts as $Contact ) 
			{
				echo "\t<tr>\n\t\t<td>" . $Contact['Title'] . "</td>\n\t\t<td>" . $Contact['Address'] . "</td>\n\t</tr>\n";	
				ob_flush();
			}
			echo "</table>\n";
		}

		private function FindContactFromField( $Field, $Value )		// finds a particular contact in a list of contacts
		{
			$q	=	'SELECT Serialized_Entry, Entry_HTML FROM ContactManager_ListEntries WHERE ContactManager_Contacts_Id = \'' . $this->ContactManager_Contacts_Id . '\' AND `' . $Field . '` = \'' . $this->db->real_escape_string( $Value ) . '\' LIMIT 1';
			$res	=	$this->db->query( $q );
			$row	=	$res->fetch_assoc();
			$res->close();

			if ( ! $row )	return null;

			$Data			=	unserialize( $row[ 'Serialized_Entry' ] );
			$tmp			=	new DOMDocument;	
			$tmp->loadXML( $row[ 'Entry_HTML' ] );
			$Data[ 'FullEntry' ] 	=	$tmp->documentElement;

			return $Data;	
		}

		function FindContactFromAddress( $Address )
		{
			return $this->FindContactFromField( 'Address', $Address );
		}

		function FindContactFromId( $Id )
		{
			return $this->FindContactFromField( 'Original_Account_Id', $Id );
		}

		function FindContactFromBestData( $Id, $Address )
		{
			$Contact	=	$this->FindContactFromId( $Id );
			if ( empty( $Contact ) && !empty( $Address ) )
				$Contact	=	$this->FindContactFromAddress( $Address );

			return $Contact;
		}

		function GetContactCount()
		{
			$q	=	'SELECT COUNT(*) as Count FROM ContactManager_ListEntries WHERE ContactManager_Contacts_Id = \'' . $this->ContactManager_Contacts_Id . '\' GROUP BY ContactManager_Contacts_Id';
			$res	=	$this->db->query( $q );
			$row	=	$res->fetch_assoc();
			$res->close();

			return intval( $row[ 'Count' ] );
		}

		function GetNextContact()
		{
			//	var 	$Contacts_Query_Resource	=	null;

			if ( $this->Contacts_Query_Resource === null )
			{
				$q					=	'SELECT Serialized_Entry, Entry_HTML FROM ContactManager_ListEntries WHERE ContactManager_Contacts_Id = \'' . $this->ContactManager_Contacts_Id . '\'';
				$this->Contacts_Query_Resource		=	$this->db->query( $q );
			}

			$row		=	$this->Contacts_Query_Resource->fetch_assoc();
			if ( ! $row )
			{
				$this->Contacts_Query_Resource->close();
				$this->Contacts_Query_Resource		=	null;
				return null;
			}
			else
			{
				$Data			=	unserialize( $row[ 'Serialized_Entry' ] );
				$tmp			=	new DOMDocument;	$tmp->loadXML( $row[ 'Entry_HTML' ] );
				$Data[ 'FullEntry' ] 	=	$tmp->documentElement;

				return $Data;
			}
		}

		function AddContact( $ContactDetails )		// Adds a new contact to the account this class is currently connected to
		{
			global $db;


			$Entry		=	new DOMDocument;
			
			// main entry element (and its attributes)
			$EntEl		=	$Entry->createElement( 'atom:entry' );
			$Entry->appendChild( $EntEl );

			$Attr		=	$Entry->createAttribute( 'xmlns:atom' );
			$EntEl->appendChild( $Attr );
			$Attr->appendChild( $Entry->createTextNode( 'http://www.w3.org/2005/Atom' ) );

			$Attr		=	$Entry->createAttribute( 'xmlns:gd' );
			$EntEl->appendChild( $Attr );
			$Attr->appendChild( $Entry->createTextNode( 'http://schemas.google.com/g/2005' ) );

			// Category (and its attributes)
			$Category	=	$Entry->createElement( 'atom:category' );
			$EntEl->appendChild( $Category );

			$Attr		=	$Entry->createAttribute( 'scheme' );
			$Category->appendChild( $Attr );
			$Attr->appendChild( $Entry->createTextNode( 'http://schemas.google.com/g/2005#kind' ) );

			$Attr		=	$Entry->createAttribute( 'term' );
			$Category->appendChild( $Attr );
			$Attr->appendChild( $Entry->createTextNode( 'http://schemas.google.com/contact/2008#contact' ) );

			// Title element
			$Title		=	$Entry->createElement( 'atom:title' );
			$EntEl->appendChild( $Title );
			
			$Attr		=	$Entry->createAttribute( 'type' );
			$Title->appendChild( $Attr );
			$Attr->appendChild( $Entry->createTextNode( 'text' ) );

			$Title->appendChild( $Entry->createTextNode( $ContactDetails[ 'Title' ] ) );

			// Content element (required by Atom feed standard?)
			if ( ! $this->CheckForPropertyInArray( 'content', $ContactDetails[ 'Properties' ] ) )
			{
				$Content	=	$Entry->createElement( 'atom:content' );
				$EntEl->appendChild( $Content );

				$Attr		=	$Entry->createAttribute( 'type' );
				$Content->appendChild( $Attr );
				$Attr->appendChild( $Entry->createTextNode( 'text' ) );

				$Content->appendChild( $Entry->createTextNode( 'Notes' ) );
			}

			$this->AddGDProperties( $ContactDetails[ 'Properties' ], basename( $ContactDetails[ 'EditLink' ] ), $EntEl, true );

			// Post to the contact feed.
			$Entry->formatOutput	=	true;
			$this->ContentType	=	'application/atom+xml';
			$response		=	$this->DoPost( 'http://www.google.com/m8/feeds/contacts/' . $this->Account . '/full', $Entry->saveXML( $EntEl ) );


/*
For testing:
			$Entry->formatOutput	=	true;
			echo "add: " . $Entry->saveXML( $EntEl ) . "\n";
			echo "response: " . $response . "\n";

			$Entry->formatOutput	=	true;

			$db->query( "
				INSERT INTO
					ContactManager_Logs
				SET
					Type = 'added',
					FullEntry = '" . $Entry->saveXML( $EntEl ) . "',
					Response = '" . $response . "'
			" );
*/

			$code = $this->GetHTTPCode();
			
			$status = $code == '201'; // 201 CREATED

/*
For testing:
			$this->Log( $response, 'add', $status, $code );
*/

			return $status;		
		}
		

		function CheckForPropertyInArray( $Property_Name, $Property_Array )
		{
			// gets passed an array of properties in my internal representation and a property name
			// returns TRUE if array contains a property with that name

			foreach ( $Property_Array as $Property )
				if ( $Property[ 'Name' ] == $Property_Name )	return true;

			return false;
		}

		function AddGDProperties( $PropertyData, $Original_Account_Id, $ParentNode, $IsAdd = false )		
		{
			// Construct DOM elements for all the properties in PropertyData and insert them under ParentNode
			// Used by both Add and Update.

			$NewProp	=	$ParentNode->ownerDocument->createElement( 'gd:extendedProperty' );
			$ParentNode->appendChild( $NewProp );
			$NewProp->setAttribute( 'name', 	'original.account.id' );
			$NewProp->setAttribute( 'value', 	$Original_Account_Id );

			foreach ( $PropertyData as $PropData )
			{
				if ( $IsAdd )
				{
					// things that only need to happen on Adds.  content node needs to be in atom namespace.
					if ( $PropData[ 'Name' ] == 'content' ) 	$PropData[ 'Name' ] = 'atom:content';
				}
				$NewProp	=	$ParentNode->ownerDocument->createElement( $PropData[ 'Name' ] );
				$ParentNode->appendChild( $NewProp );

				if ( !empty( $PropData[ 'children' ] ) )
					foreach ( $PropData[ 'children' ] as $Name=>$ChildPropData )
						$NewProp->appendChild( $this->BuildContactProperty( $ParentNode->ownerDocument, $ChildPropData ) );

				/*
				if ( !empty( $PropData[ 'Value' ] ) )
					$NewProp->appendChild( $ParentNode->ownerDocument->createTextNode( $PropData[ 'Value' ] ) );
				*/

				if ( !empty( $PropData[ 'Attributes' ] ) )
					foreach ( $PropData[ 'Attributes' ] as $Name=>$Value )
					{
						$Attr		=	$ParentNode->ownerDocument->createAttribute( $Name );
						$Attr->appendChild( $ParentNode->ownerDocument->createTextNode( $Value ) );
						$NewProp->appendChild( $Attr );
					}
			}
		}

		function UpdateContact( $From, $To )		// Update a contact in the contact list this class is currently attached to
		{
			global $db;
			
		
			// change out the title elements
			$FromTitle	=	$From[ 'FullEntry' ]->getElementsByTagName( 'title' )->item(0);
			$ToTitle	=	$To[ 'FullEntry' ]->getElementsByTagName( 'title' )->item(0);
			
			
			$FromTitle->parentNode->replaceChild( $FromTitle->ownerDocument->importNode( $ToTitle, true ), $FromTitle );

			// remove all current gd namespace elements.  will reconstruct them from authoritative entry
			$child		=	$From[ 'FullEntry' ]->firstChild;
			while ( $child )
			{
				$parts		=	preg_split( '/:/', $child->nodeName );
				$nextSibling	=	$child->nextSibling;

				if ( ( $parts[0] == 'gd' ) || ( $parts[ 0 ] == 'content' ) )
					$child->parentNode->removeChild( $child );
				
				$child		=	$nextSibling;
			}

			// recreate the gd elements from the authoritative
			$this->AddGDProperties( $To[ 'Properties' ], basename( $From[ 'EditLink' ] ), $From[ 'FullEntry' ] );
			// namespaces -- documentation wasn't clear we needed these, had to research it
			/*
			$Attr           =       $From[ 'FullEntry' ]->ownerDocument->createAttribute( 'xmlns' );
			$From[ 'FullEntry' ]->appendChild( $Attr );
			$Attr->appendChild( $From[ 'FullEntry' ]->ownerDocument->createTextNode( 'http://www.w3.org/2005/Atom' ) );

			$Attr           =       $From[ 'FullEntry' ]->ownerDocument->createAttribute( 'xmlns:gd' );
			$From[ 'FullEntry' ]->appendChild( $Attr );
			$Attr->appendChild( $From[ 'FullEntry' ]->ownerDocument->createTextNode( 'http://schemas.google.com/g/2005' ) );
			*/
			
			// Post to the edit link.
			$From[ 'FullEntry' ]->ownerDocument->formatOutput	=	true;
			$this->ContentType	=	'application/atom+xml';
			$this->IfMatch		=	'*';
			$this->XHTTPMethodOverride	=	'PUT';
			$response		=	$this->DoPost( $From[ 'EditLink' ], $From[ 'FullEntry' ]->ownerDocument->saveXML( $From[ 'FullEntry' ] ) );

			/*
			$From[ 'FullEntry' ]->ownerDocument->formatOutput	=	true;
			echo "update: " . $From[ 'FullEntry' ]->ownerDocument->saveXML( $From[ 'FullEntry' ] ) . "\n\n";
			echo "response: " . $response . "\n\n";
			*/


			$From[ 'FullEntry' ]->ownerDocument->formatOutput	=	true;

/*
Testing:
			$db->query( "
				INSERT INTO 
					ContactManager_Logs 
				SET 
					Type = 'updated',
					FullEntry = '" . $From[ 'FullEntry' ]->ownerDocument->saveXML( $From[ 'FullEntry' ] ) . "',
					Response = '" . $response . "' 
			" );
//			
*/
			$code = $this->GetHTTPCode();
			
			$status = $code == '200'; // 200 UPDATED

/*
Testing:
			$this->Log( $response, 'update', $status, $code );	
*/			
			return $status;
		}
		
		
		
		public function Log( $response, $type, $status, $code )
		{
			global $db;
		
			$response = preg_replace( "/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response );
			
			$obj = simplexml_load_string( $response );
			
			$q = "
				INSERT INTO
					ContactManager_Logs
				SET
					Code = '{$code}',
					Response = '"  . ( $status ? '' : addslashes( htmlspecialchars( $response ) ) ) . "',
					Type = '{$type}',
					Account = '{$this->Account}',
					Status = '" . ( $status ? 'success' : 'failure' ) . "',
					Address = '" . $obj->gdemail->attributes()->address . "'
			";
			
			$db->query( $q );
		}
		

                function GetAllGroups()
                {
			$feed   =       'http://www.google.com/m8/feeds/groups/' . urlencode( $this->Account ) . '/full?max-results=1000';
			$text	=	$this->DoGet( $feed );
			$DOM	=	$this->GetXMLDOM();

			echo $DOM->saveXML();
			
		}
		
	}	// GoogleContactsClass	

	// ***************************************** MAIN CODE *********************************************

	set_time_limit( 0 );		// larger contact lists can take forever
	
	
	$InvalidPasswordEmail	=	new emailMessage( 'INVALID_PASSWORD', $_SESSION[ 'Account' ][ 'Id' ] );

	
	$Cipher			=	new Cipher( $_REQUEST[ 'Passphrase' ] );

	$db     =       new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );

	$q	=	'SELECT COUNT(*) as Count FROM ContactManager_Contacts WHERE Company_Id = \'' . $_SESSION[ 'Account' ][ 'Id' ] . '\' AND Account_Type = \'Non-Authoritative\' GROUP BY Company_Id';
	$res	=	$db->query( $q );
	$row	=	$res->fetch_assoc();
	$res->close();

	$NonAuthoritative_Users		=	intval( $row[ 'Count' ] );

	$q	=	'SELECT * FROM ContactManager_Contacts WHERE Company_Id = \'' . $_SESSION[ 'Account' ][ 'Id' ] . '\' AND Account_Type = \'Authoritative\' LIMIT 1';
	$res	=	$db->query( $q );
	$row	=	$res->fetch_assoc();
	$res->close();

	$Messages	=	Array();

	//$myGoogleClass	=	new GoogleContactsClass;
	$myUI			=	new SyncUI;
	$myUI->updateFrom( $row[ 'GoogleAccount' ] );
	$AuthoritativeContacts	=	new GoogleContactsClass;


	if ( $AuthoritativeContacts->Authenticate( $row[ 'GoogleAccount' ], $Cipher->decrypt( $row[ 'GooglePassword' ] ) ) )
	{
		$q		=	'UPDATE ContactManager_Contacts SET Last_Update = \'' . date( 'Y-m-d H:i:s' ) . '\' WHERE Id = \'' . $row[ 'Id' ] . '\' LIMIT 1';
		$db->query( $q );

		// get the authoritative contacts
		$myUI->showPageError( 'Fetching authoritative contact list...' );
		$AuthoritativeContacts->GetAllContacts();
		$myUI->showPageError( '&nbsp;' );
		
		//$myGoogleClass->PrintContacts( $AuthoritativeContacts, "Authoritative Contact List" );

		// total number of updates we need to do is the number of contacts in the authoritative contact list
		// multiplied by the number of contact lists they have to be synced to.  Used for calculating percent done.
		$Num_Total_Updates	=	$NonAuthoritative_Users * $AuthoritativeContacts->GetContactCount();
		$Num_Updates_Done	=	0;

		$StartTS		=	time();

		// start looping through the non-authoritative accounts and uploading contacts that don't exist to them
		$q	=	'SELECT * FROM ContactManager_Contacts WHERE Company_Id = \'' . $_SESSION[ 'Account' ][ 'Id' ] . '\' AND Account_Type = \'Non-Authoritative\' ORDER BY GoogleAccount asc';
		$res	=	$db->query( $q );
		while ( $row	=	$res->fetch_assoc() )
		{
			$myUI->updateTo( $row[ 'GoogleAccount' ] );

			$UnauthoritativeContacts	=	new GoogleContactsClass;
			
			if ( $UnauthoritativeContacts->Authenticate( $row[ 'GoogleAccount' ], $Cipher->decrypt( $row[ 'GooglePassword' ] ) ) )
			{
				$myUI->showPageError( 'Fetching ' . $row[ 'GoogleAccount' ] . '\\\'s current contact list...' );
				$Contacts	=	$UnauthoritativeContacts->GetAllContacts();
				$myUI->showPageError( '&nbsp;' );
				$Added		=	0;
				$AddFailed	=	0;
				$Updated	=	0;
				$UpdateFailed	=	0;

				while ( $AuthoritativeContact = $AuthoritativeContacts->GetNextContact() )
				{
					$myUI->updateSyncing( $AuthoritativeContact[ 'Address' ] );
					//$UnAuthoritativeContact		=	$UnauthoritativeContacts->FindContactFromAddress( $AuthoritativeContact[ 'Address' ] );
					$UnAuthoritativeContact		=	$UnauthoritativeContacts->FindContactFromBestData( $AuthoritativeContact[ 'Original_Account_Id' ], $AuthoritativeContact[ 'Address' ] );
					if ( $UnAuthoritativeContact )	// already have this contact, do an update
					{
						if ( $UnauthoritativeContacts->UpdateContact( $UnAuthoritativeContact, $AuthoritativeContact ) ) 
						{
							$Updated++;
							$myUI->updateUpdates( $Updated );
						}
						else
						{
							//echo "\t<li>Failed to update " . $AuthoritativeContact[ 'Address' ] . ".</li>\n";
							$UpdateFailed++;
							$myUI->updateFailedUpdates( $UpdateFailed );
						}
					}
					else				// don't have this contact, do an add.
					{
						if ( $UnauthoritativeContacts->AddContact( $AuthoritativeContact ) )
						{
							$Added++;
							$myUI->updateAdds( $Added );
						}
						else
						{
							//echo "\t<li>Failed to add " . $AuthoritativeContact[ 'Address' ] . ".  (Authoritative Contact's primary Email address probably appears as secondary address in another non-authoritative contact.)</li>\n";
							$AddFailed++;
							$myUI->updateFailedAdds( $AddFailed );
						}
					}

					$Num_Updates_Done++;	
					if ( $Num_Total_Updates <= 0 ) 
						$Percent 	=	100;
					else
						$Percent	=	round( ( $Num_Updates_Done * 100 ) / $Num_Total_Updates );
					$myUI->updateProgress( $Percent, $StartTS );
				}

				$q		=	'UPDATE ContactManager_Contacts SET Last_Update = \'' . date( 'Y-m-d H:i:s' ) . '\' WHERE Id = \'' . $row[ 'Id' ] . '\' LIMIT 1';
				$db->query( $q );
			}
			else
			{
				$myUI->showAccountError( 'Unable to authenticate.  User\\\'s stored password is invalid?' );
				$InvalidPasswordEmail->SendEmail( $row[ 'GoogleAccount' ] );

				// for the sake of the percentage counter, this essentially means we're skipping a number of updates
				// equal to the number of contacts in the authoritative list.  this is the number of updates that would
				// have needed to be performed on the account we just failed to connect to.
				$Num_Updates_Done	+= 	$AuthoritativeContacts->GetContactCount();
				if ( $Num_Total_Updates <= 0 ) 
					$Percent 	=	100;
				else
					$Percent	=	round( ( $Num_Updates_Done * 100 ) / $Num_Total_Updates );
				$myUI->updateProgress( $Percent, $StartTS );
			}

		}

		$res->close();
	}
	else
	{
		$myUI->showPageError( 'Unable to authenticate to authoritative account.  User\\\'s stored password is invalid?' );
		$InvalidPasswordEmail->SendEmail( $row[ 'GoogleAccount' ] );
	}

	$myUI->showReturnButton();		// reveal the "return to main menu" button.

	exit;
?>