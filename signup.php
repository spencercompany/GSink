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
	function CheckEmail( Email )
	{
		var filter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

		return filter.test( Email );
	}

	function checkUniqueField( inputField, fieldName, friendlyName, resultCellId )
	{
		if ( ( fieldName == 'Email' ) && ( !CheckEmail( inputField.value ) ) )		// yeah, okay, I'm breaking abstraction.  So unlike me.
		{
			var resultCell		=	document.getElementById( resultCellId );
			resultCell.innerHTML	=	'<span id=\'red\'>Please enter a valid Email address.</span>';	
			return;
		}

		ajaxFunction( '?Action=CheckUniqueField&FieldName=' + fieldName + '&FriendlyName=' + friendlyName + '&Value=' + inputField.value,
			function( resultText )
			{
				var resultCell		=	document.getElementById( resultCellId );
				resultCell.innerHTML	=	resultText;
			}
		);
	}

	function validateForm()
	{
		var 	WarningMessages		=	[];

		var 	Admin_Login		=	document.getElementById( 'Admin_Login' );
		
		if ( Admin_Login.value == '' )
			WarningMessages.push( "Login cannot be left blank." );

		var 	Company_Name		=	document.getElementById( 'Company_Name' );

		if ( Company_Name.value == '' )
			WarningMessages.push( "Company name cannot be left blank." );

		var 	Email			=	document.getElementById( 'Email' );

		if ( Email.value == '' )
			WarningMessages.push( "Email cannot be left blank." );
		else if ( !CheckEmail( Email.value ) )
			WarningMessages.push( "Please enter a valid Email address." );			

		var 	Admin_Password		=	document.getElementById( 'Admin_Password' );
		var 	Repeat_Admin_Password	=	document.getElementById( 'Repeat_Admin_Password' );

		if ( Admin_Password.value == '' )
			WarningMessages.push( "Password cannot be left blank." );

		if ( Admin_Password.value != Repeat_Admin_Password.value )
			WarningMessages.push( "Passwords do not match!" );

		if ( WarningMessages.length > 0 )
		{
			alert( WarningMessages.join( "\n\n" ) );
			return false;
		}

		return true;
	}
</script>

<?php
	function previousValue( $value )
	{
		if ( isset( $_REQUEST[ $value ] ) )
			echo $_REQUEST[ $value ];
	}

	// make sure we aren't currently logged in...  could confuse 'existing username/email' checks.
	unset( $_SESSION[ 'Account' ] );
?>

<form method='post' action='<?= HAS_SSL ? 'https' : 'http' ?>://<?= DOMAIN ?>/index.php'>
	<input type='hidden' name='Action' value='Process-SignUp' />
	<table class='form-table'>
		<tr>
			<th colspan=2>New Account SignUp</th>
		</tr>

		<tr>
			<td colspan=2 id='explanation'><span id='red'><?php previousValue( 'Error' ); ?></span>&nbsp;</td>
		</tr>

		<tr>
			<td id='prompt'>Desired Login:</td>
			<td id='input'>
				<input onchange="JavaScript: checkUniqueField( this, 'Admin_Login', 'Login', 'Login-Check-Result' );" type='text' name='Admin_Login' id='Admin_Login' size=40 maxlength=40 value='<?php previousValue( 'Admin_Login' ); ?>' />
			</td>
		</tr>

		<tr>
			<td id='prompt'>&nbsp;</td>
			<td id='Login-Check-Result'></td>
		</tr>

		<tr>
			<td id='prompt'>Company Name:</td>
			<td id='input'><input type='text' name='Company_Name' id='Company_Name' size=40 maxlength=40 value='<?php previousValue( 'Company_Name' ); ?>' /></td>
		</tr>

		<tr>
			<td colspan=2>&nbsp;</td>
		</tr>

		<tr>
			<td id='prompt'>Your Email:</td>
			<td id='input'><input onchange="JavaScript: checkUniqueField( this, 'Email', 'Email', 'Email-Check-Result' );" type='text' name='Email' id='Email' size=40 maxlength=40 value='<?php previousValue( 'Email' ); ?>' /></td>
		</tr>

		<tr>
			<td colspan=2 id='explanation'>
				(For lost passwords.  Needs to be real!)
			</td>
		</tr>

		<tr>
			<td id='prompt'>&nbsp;</td>
			<td id='Email-Check-Result'></td>
		</tr>

		<tr>
			<td id='prompt'>Desired Password:</td>
			<td id='input'><input type='password' name='Admin_Password' id='Admin_Password' size=40 maxlength=40 /></td>
		</tr>

		<tr>
			<td id='prompt'>Re-type Password:</td>
			<td id='input'><input type='password' name='Repeat_Admin_Password' id='Repeat_Admin_Password' size=40 maxlength=40 /></td>
		</tr>

		<tr>
			<td colspan=2>&nbsp;</td>
		</tr>

		<tr>
			<td colspan=2 id='form-buttons'>
				<input type='image' src='images/Submit-Button.png' onclick='JavaScript: return validateForm();' />
				<input type='image' src='images/Reset-Button.png' onclick='JavaScript: return ResetButton( this );' />
			</td>
		</tr>
	</table>
</form>