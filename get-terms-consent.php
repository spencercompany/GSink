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

<div>
</div>

<h2>Please read the following license agreement carefully, then indicate at the bottom whether you agree or decline the terms:</h2>

<?php
	include_once( 'GSink_Terms_of_Service.html' );
?>

<center>
	<button type='button' onclick='JavaScript: document.location="index.php?Action=LogOut";'>I <span style='color: red;'>Decline</span></button>
	<button type='button' onclick='JavaScript: document.location="index.php?Action=SignUp";'>I Accept</button>
</center>
