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

	function ValidateForm()
	{
		var EmailInput		=	document.getElementById( 'Email-Input' );
		if ( !CheckEmail( EmailInput.value ) )
		{
			alert( 'Please enter a valid Email address.' );
			return false;
		}
		else
			return true;
	}
</script>

<form>
	<input type='hidden' name='Action' value='Process-RecoverPasswordStep1' />
	<table class='form-table'>
		<tr>
			<th>Recover Password</th>
		</tr>

		<tr>
			<td id='explanation'>
				Forgot your password?  Enter the Email address you used at signup and you will
				be sent a link to allow you to choose a new one.
			</td>
		</tr>

		<tr>
			<td>
				<table style='margin-left: auto; margin-right: auto;'>
					<tr>
						<td id='prompt' style='width: 25%;'>Email:</td>
						<td id='input'><input type='text' name='Email' size=40 maxlength=40 id='Email-Input' /></td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td id='form-buttons'>
				<button type='submit' onclick='JavaScript: return ValidateForm();'>Send Link</button>
			</td>
		</tr>
	</table>
</form>
