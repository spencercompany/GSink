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


        $db     	=       new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );

	$q		=	'SELECT c.* FROM ContactManager_Companies as c, ContactManager_PasswordResetCodes as prc WHERE c.Id=prc.Company_Id AND prc.Expiration >= NOW() AND prc.ResetCode = \'' . $_REQUEST[ 'ResetCode' ] . '\' LIMIT 1';
	$res		=	$db->query( $q );
	$row		=	$res->fetch_assoc();
	$res->close();

	$db->close();

	if ( ! $row )
	{
		echo '<script type=\'text/javascript\'>document.location=\'?Action=Login&Message=' . urlencode( 'Invalid or Expired Reset Code.' ) . '\';</script>' . "\n";
		exit;
	}
?>

<script type='text/javascript'>
	function ValidateForm()
	{
		var 	WarningMessages		=	[];

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

<form method='post'>
	<input type='hidden' name='Action' value='Process-RecoverPasswordStep2' />
	<input type='hidden' name='ResetCode' value='<?php echo $_REQUEST[ 'ResetCode' ] ?>' />
	<table class='form-table'>
		<tr>
			<th colspan=2>
				Reset Password
			</th>
		</tr>

		<tr>
			<td colspan=2>&nbsp;</td>
		</tr>
		
		<tr>
			<td id='prompt'>Login:</td>
			<td id='input'><?php echo $row[ 'Admin_Login' ]; ?></td>
		</tr>

		<tr>
			<td colspan=2>&nbsp;</td>
		</tr>
		
		<tr>
			<td id='prompt'>Desired New Password:</td>
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
				<button type='submit' onclick='JavaScript: return ValidateForm();'>Update</button>
				<button type='reset'>Reset</button>
			</td>
		</tr>
	</table>
</form>