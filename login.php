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

<center>
	<img src='images/GSink-Banner.jpg' />
</center>
<div style='text-align: center; padding-left: 400px; font-size: 16px;'>
	<b><i>New user?</i></b>  Click <a style='color: #c959a0;' href='http://<?= DOMAIN ?>/?Action=SignUp'>here</a> to get started!
</div>
<table class='centered-table'>
	<tr>
		<td>
			<form method='post' action='<?= HAS_SSL ? 'https' : 'http' ?>://<?= DOMAIN ?>/index.php'>
				<input type='hidden' name='Action' value='Process-Login' />
				<table class='form-table' style='margin-top: 20px;'>
					<tr>
						<th colspan=2>Please Login:</th>
					</tr>

					<tr>
						<td id='explanation' colspan=2><span id='red'><?php if ( !empty( $_REQUEST[ 'Message' ] ) ) echo $_REQUEST['Message']; ?></span>&nbsp;</td>
					</tr>

					<tr>
						<td id='prompt'>Login:</td>
						<td id='input'><input type='text' name='Admin_Login' size=40 maxlength=40 /></td>
					</tr>

					<tr>
						<td id='prompt'>Password:</td>
						<td id='input'><input type='password' name='Admin_Password' size=40 maxlength=40 /></td>
					</tr>

					<tr>
						<td id='explanation' style='font-size: 10px;' colspan=2>(Forgot your password?  Click <a href='?Action=RecoverPasswordStep1'>here</a>.)</td>
					</tr>

					<tr>
						<td colspan=2 id='form-buttons'>
							<input type='image' src='images/Submit-Button.png' />
							<input type='image' src='images/Reset-Button.png' onclick='JavaScript: return ResetButton( this );' />
						</td>
					</tr>
				</table>
			</form>

			<center>
				Click <a href='?Action=SignUp'>here</a> to sign up for a new account.
			</center>
		</td>
		<td class='side-text'>
			<p>
Sign up for GSink today and will let you share your Google Contacts with one or an unlimited number of other Gmail or Google Apps users anywhere in the world!
			</p>
			<p> 
GSink does not store your contacts online so the information is transferred to your friends and co-workers safely and securely without retaining the information... none of your contacts are stored, they are just synced with other users for you.
			</p>
			<p>
			<a href='index.php?Action=MoreInfoLink'>More Info</a>
			</p>
		</td>
	</tr>
	<tr>
		<td colspan=2>
			<center>
				<img src='images/line_up_1.jpg' />
			</center>
		</td>
	</tr>
</table>