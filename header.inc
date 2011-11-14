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

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>GSink(sm) Contact Manager</title>
	<link rel='stylesheet' type='text/css' href='stylesheet.css' />
	<meta name='description' content='Synchronize Your Gmail and Google Apps Contacts to your entire organization' />
	<meta name='keywords' content='gmail sync contacts addressbook outlook' />
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<script type='text/javascript' src='ajax.js'></script>

	<!-- jQuery itself -->
	<script src='<?= URL_PREFIX ?>ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js' type='text/javascript'></script>

	<!-- for image form reset buttons -->
	<script type='text/javascript'>
		function ResetButton( button )
		{
			button.form.reset();
			return false;
		}
	</script>
</head>
<body<?= !empty( $Page_Classes ) ? ' class=\'' . $Page_Classes . '\'' : '' ?>>

<div class='bodyWrapper'>

<?php
	function HeaderLinks()
	{
		if ( empty( $_REQUEST[ 'Action' ] ) )	$_REQUEST[ 'Action' ] = '';
		if ( empty( $_SESSION[ 'Account' ] ) )
			return '&nbsp;';
		else
		{
			$Links			=	Array();
			$Links[ 'LogOut' ]	=	'Log Out';

			$Links[ 'EditProfile' ]		=	'Edit Profile';

			$Links[ 'MainMenu' ]		=	'Main Menu';

			$HTMLLinks		=	Array();

			foreach ( $Links as $key=>$val )
				$HTMLLinks[]	=	'<a href=\'?Action=' . $key . '\'>' . $val . '</a>';

			return '(' . $_SESSION[ 'Account' ][ 'Admin_Login' ] . ': ' . join( ' | ', $HTMLLinks ) . ')';
		}
	}
?>

<h1>
	<table width='100%'>
		<tr>
			<td class='header-links'>&nbsp;</td>
			<td>GSink Contact Manager</td>
			<td class='header-links'>
				<span style='font-size: 11px;'><?php echo HeaderLinks(); ?></span>
				&nbsp;
			</td>
		</tr>
	</table>
</h1>