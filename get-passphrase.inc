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
	function VerifyForm()
	{
		var Pass1Inp	=	document.getElementById( 'pass1' );
		var Pass2Inp	=	document.getElementById( 'pass2' );

		if ( Pass1Inp.value != Pass2Inp.value ) 
			alert( 'Pass phrases do not match!' );

		if ( Pass1Inp.value == '' )
			alert( 'Passphrase cannot be blank.' );

		return ( Pass1Inp.value == Pass2Inp.value ) && ( Pass1Inp.value != '' ) && ( Pass2Inp.value != '' );
	}
</script>
<h2>Enter Encryption Pass-Phrase</h2>
<div class='explanation'>
	<table>
		<tr>
			<td>
				<p>
				In order to sync contacts between different Google Accounts, this site needs to store the passwords for the various accounts that are to be accessed during the sync.  For security reasons, we only store the passwords in an encrypted format.  That way, even in the unlikely event that some exceedingly talented hacker manages to compromise servers, they still won't learn your passwords.
				</p>
				<p>
				In order to properly encrypt and decrypt your passwords, we ask that you enter a security pass-phrase of your choice.  This pass-phrase should ideally be an actual phrase, not just a single word.  The longer it is, the more well protected your passwords are.  But please pick something that will be easy for you to remember and do not write it down anywhere.  Since the pass-phrase is not stored, it will be impossible for you to retrieve it if you forget it!  In the event that happens, you'll need to re-enter all the passwords for 
the accounts you're syncing.
				</p>
				<? 
				if( HAS_SSL )
				{ ?>
				<p>
				The pass-phrase you enter will be stored <i>ONLY</i> in a cookie inside your web browser that will be set to expire after 30 minutes, or once you've logged out of the site.  Once the cookie expires, you'll be asked to enter the pass-phrase again.  This cookie will be configured so that your browser will only transmit its value back to the site over a connection which is itself encrypted, and your 				logged-in connections to our site as encrypted with industry standard Secure Socket Layer encryption.  The Secure Socket Layer encryption also protects the connections between your web browser and our servers over which the account passwords are sent back and forth as you're entering them.
				</p>
				<? 
				} ?>
				<p>
				The pass-phrase will be used every time you hit the Update button on the syncing-accounts list editor (to encrypt the passwords as they're stored) and every time you start a sync operation to decrypt them as they're sent to Google.  In order for the sync to work correctly, you must consistently use the same pass-phrase.  (Capitalization, spelling and punctuation all count!)
				</p>
			</td>
			<td style='text-align: left;'>
				<img src='images/small_lock.jpg' />
			</td>
		</tr>
	</table>
</div>
<center>
	<form method='post' action='index.php?Action=SetPassphraseCookie'>
		<table>
			<tr>
				<td>Enter your pass-phrase: </td>
				<td><input type='password' name='Passphrase' id='pass1' size=25 maxlength=40 /></td>
			</tr>
			<tr>
				<td>Confirm your pass-phrase: </td>
				<td><input type='password' name='Confirm-Passphrase' id='pass2' size=25 maxlength=40 /></td>
			</tr>
			<tr>
				<td colspan=2 style='text-align: right;'>
					<input type='image' src='images/Submit-Button.png' onclick='JavaScript: return VerifyForm();' />
				</td>
			</tr>
		</table>
	</form>
</center>