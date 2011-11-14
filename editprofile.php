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

		var	From			=	document.getElementById( 'From-Input' );
		var 	Subject			=	document.getElementById( 'Subject-Input' );
		var 	Message			=	document.getElementById( 'Message-Input' );

		if ( From.value == '' )
			WarningMessages.push( "From field of Email cannot be left blank." );
		else if ( !CheckEmail( From.value ) )
			WarningMessages.push( "Please enter a valid Email address for the From field of the Email." );

		if ( Subject.value == '' )
			WarningMessages.push( "Subject line of Email cannot be left blank." );

		if ( Message.value.length < 200 ) 
			WarningMessages.push( "Email text must be at least 200 characters.  (Currently " + Message.value.length + ")" );

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
		if ( isset( $_SESSION[ 'Account' ][ $value ] ) )
			echo $_SESSION[ 'Account' ][ $value ];
	}

	if ( !empty( $_REQUEST[ 'Message' ] ) )
		echo '<center><span id=\'red\'>' . $_REQUEST[ 'Message' ] . '</span></center>' . "\n";
?>

<form method='post'>
	<input type='hidden' name='Action' value='Process-EditProfile' />
	<table class='form-table'>
		<tr>
			<th colspan=2>Edit Profile: <?php previousValue( 'Admin_Login' ); ?></th>
		</tr>

		<tr>
			<td colspan=2 id='explanation'><span id='red'><?php previousValue( 'Error' ); ?></span>&nbsp;</td>
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
			<td colspan=2 id='explanation'>
				(You must enter and re-enter a password in order to make any other profile changes.)
			</td>
		</tr>

		<tr>
			<td colspan=2>&nbsp;</td>
		</tr>
	</table>

<?php
	include_once( 'emailMessageClass.php' );

        $InvalidPasswordEmail   =       new emailMessage( 'INVALID_PASSWORD', $_SESSION[ 'Account' ][ 'Id' ] );
?>

	<table class='form-table' style='width: 75%;'>
		<tr>
			<th colspan=2>
				This Email will be sent to your users if Contact Sync fails to login to their account:
			</th>
		</tr>
		<tr>
			<td colspan=2>&nbsp;</td>
		</tr>
		<tr>
			<td id='prompt'>From:</td>
			<td id='input'><input type='text' name='From' id='From-Input' size=40 maxlength=40 value='<?php echo $InvalidPasswordEmail->GetFrom(); ?>' /></td>
		</tr>
		<tr>
			<td id='prompt'>Subject:</td>
			<td id='input'><input type='text' name='Subject' id='Subject-Input' size=80 maxlength=255 value='<?php echo htmlspecialchars( $InvalidPasswordEmail->GetSubject(), ENT_QUOTES ); ?>' /></td>
		</tr>
		<tr>
			<td colspan=2>&nbsp;</td>
		</tr>
		<tr>
			<td colspan=2>
				<textarea name='Message' id='Message-Input' style='width: 100%; height: 150px;' onkeypress='JavaScript: UpdateCharacterCount( this );'><?php echo $InvalidPasswordEmail->GetMessage(); ?></textarea>
			</td>
		</tr>
		<tr>
			<td colspan=2>&nbsp;</td>
		</tr>
	</table>

	<center>
		<input type='image' src='images/Update-Button.png' onclick='JavaScript: return validateForm();' />
		<input type='image' src='images/Reset-Button.png' onclick='JavaScript: return ResetButton( this );' />
	</center>
</form>
