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

?>


<script type='text/javascript'>
	function SelectChanged( thisSelect )
	{
		// make sure we only have one authoritative at any time.  Set all other selectors
		// to non-authoritative whenever any selector is set to authoritative.
		if ( thisSelect.options[ thisSelect.selectedIndex ].value == 'Authoritative' )
		{
			thisSelect.parentNode.parentNode.id	=	'authoritative';	// for the colorization / style

			var selects	=	document.getElementsByTagName( 'select' );
			for ( i = 0; i < selects.length; i++ )
			{
				if ( selects[i] != thisSelect )	
				{
					selects[i].selectedIndex		=	1;
					selects[i].parentNode.parentNode.id	=	'';	// remove special style
				}
			}
		}
		else
			thisSelect.parentNode.parentNode.id	=	'';			// remove special style

		CheckForAuthoritative();
	}

        function CheckEmail( Email )
        {
                var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

                return filter.test( Email );
        }

	function ValidateForm()
	{
		var Messages	=	[];
		var inputs	=	document.getElementsByTagName( 'input' );
		for ( i = 0; i < inputs.length; i++ )
		{
			if ( inputs[i].type == 'text' )
			{
				var input_parts		=	inputs[i].name.split( ':' );
				
				if ( input_parts[1] != 'New' )
				{
					var checkbox		=	document.getElementById( 'Delete:' + input_parts[1] );

					if ( ! checkbox.checked )
					{
						if ( input_parts[0] == 'GoogleAccount'  )
						{
							if ( inputs[i].value == '' )
								Messages.push( 'Google Account cannot be left blank.  Use checkbox at left to delete.' );
							else if ( !CheckEmail( inputs[i].value ) )
								Messages.push( 'Google Account must be a valid Email address.' );
						}
						else if ( input_parts[0] == 'GooglePassword' )
						{
							if ( inputs[i].value == '' )
								Messages.push( 'Google Password cannot be left blank.  Use checkbox at left to delete.' );
						}
					}	// delete checkbox isn't checked
				}	// not a new user input
			}	// input is a text
		}	// for loop

		var 	NewGoogleAccount	=	document.getElementById( 'GoogleAccount' );
		var	NewGooglePassword	=	document.getElementById( 'GooglePassword' );

		if ( NewGoogleAccount.value == '' && NewGooglePassword.value != '' )
			Messages.push( "Cannot add new user without a Google Account." );
	
		if ( NewGoogleAccount.value != '' && NewGooglePassword.value == '' )
			Messages.push( "Cannot add new user without a password." );

		if ( NewGoogleAccount.value != '' && !CheckEmail( NewGoogleAccount.value ) )
			Messages.push( "Please enter a valid Email address for the new user's Google Account." );

                if ( Messages.length > 0 )
                {
                        alert( Messages.join( "\n\n" ) );
                        return false;
                }

		return true;
	}

	function CheckForAuthoritative()
	{
		var selects	=	document.getElementsByTagName( 'select' );
		var haveAuth	=	0;

		for ( i = 0; i < selects.length; i++ )
		{
			if ( selects[i].options[ selects[i].selectedIndex ].value == 'Authoritative' ) 
			{
				haveAuth	=	1;
				break;
			}
		}

		var WarningCell		=	document.getElementById( 'red' );
		if ( haveAuth ) 	WarningCell.innerHTML	=	'';
		else			WarningCell.innerHTML	=	'WARNING: Currently do not have an authoritative user!';
	}
</script>

<?php
	function MakeAccountTypeSelector( $Id, $Current_Value )
	{
		$Values		=	Array( 'Authoritative', 'Non-Authoritative' );

		$ret		=	'<select name=\'Account_Type:' . $Id . '\' onchange=\'JavaScript: SelectChanged( this );\'>';

		foreach ( $Values as $Value )
			$ret 	.=	'<option value=\'' . $Value . '\'' . ( $Value == $Current_Value ? ' selected=selected' : '' ) . '>' . $Value . '</option>';

		$ret		.=	'</select>';

		return $ret;
	}

	include_once( 'cipher.inc' );
	$Cipher	=	new Cipher( $_REQUEST[ 'Passphrase' ] );

        $db     =       new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );

	$q	=	'SELECT * FROM ContactManager_Contacts WHERE Company_Id = \'' . $_SESSION[ 'Account' ][ 'Id' ] . '\' ORDER BY Account_Type asc, GoogleAccount asc';
	$res	=	$db->query( $q );
?>

<form method='post' action="">
<input type='hidden' name='Action' value='Process-UpdateAccounts' />
<table class='form-table'>

	<tr>
		<th colspan=4 style='color: #148d3d;'>
			<?php echo $_SESSION[ 'Account' ][ 'Company_Name' ]; ?>
		</th>
	</tr>

	<tr>
		<td colspan=4 id='explanation'><span id='red'></span>&nbsp;</td>
	</tr>

	<tr>
		<th>Delete</th>
		<th>Google Account</th>
		<th>Google Password</th>
		<th>Account Type</th>
	</tr>

<?php
	while ( $row = $res->fetch_assoc() ) 
	{
?>
	<tr<?php if ( $row['Account_Type'] == 'Authoritative' ) echo ' id=\'authoritative\''; ?>>
		<td align=center><input type='checkbox' name='Delete:<?php echo $row['Id']; ?>' id='Delete:<?php echo $row['Id']; ?>' /></td>
		<td><input type='text' name='GoogleAccount:<?php echo $row['Id']; ?>' size=40 maxlength=40 value='<?php echo $row['GoogleAccount']; ?>' /></td>
		<td><input type='text' name='GooglePassword:<?php echo $row['Id']; ?>' size=40 maxlength=40 value='<?php echo $Cipher->decrypt( $row['GooglePassword'] ); ?>' /></td>
		<td><?php echo MakeAccountTypeSelector( $row['Id'], $row['Account_Type'] ); ?></td>
	</tr>
<?php
	}
?>

	<tr>
		<td colspan=4>&nbsp;</td>
	</tr>

	<tr>
		<td colspan=4 id='explanation'>Add new users below:</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td><input type='text' name='GoogleAccount:New' id='GoogleAccount' size=40 maxlength=40 value='' /></td>
		<td><input type='text' name='GooglePassword:New' id='GooglePassword' size=40 maxlength=40 value='' /></td>
		<td><?php echo MakeAccountTypeSelector( 'New', 'Non-Authoritative' ); ?></td>
	</tr>

	<tr>
		<td colspan=4>&nbsp;</td>
	</tr>

	<tr>
		<td colspan=4 id='form-buttons'>
			<input type='image' src='images/Update-Button.png' onclick='JavaScript: return ValidateForm();' />
			<input type='image' src='images/Reset-Button.png' onclick='JavaScript: return ResetButton( this );' />
		</td>
	</tr>

</table>
</form>

<center>
	<p>
		Seeing garbage as the account passwords?  <a href='?Action=GetPassphrase'>Re-enter your encryption passphrase!</a>
	</p>
	<img src='images/Sync-Button.png' onclick="JavaScript: document.location='?Action=SyncUI';" />
</center>

<?php
	$res->close();
	$db->close();
?>

<script type='text/javascript'>
	$( function() {
		CheckForAuthoritative();
	});
</script>