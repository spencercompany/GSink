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
	function SyncUI()
	{
		var bar		=	null;
		var percent	=	null;

		var syncing	=	null;
		var from	=	null;
		var to		=	null;

		var adds	=	null;
		var failedadds	=	null;
		var updates	=	null;
		var failedupds	=	null;

		var pageerror	=	null;

		var currenttime	=	null;
		var predictedendtime
				=	null;

		this.updateProgressBar		=	function( percentage )
		{
			if ( !this.bar )
				this.bar	=	document.getElementById( 'filled-progress-bar' );
			if ( !this.percent )
				this.percent	=	document.getElementById( 'progress-percent' );
	
			if ( ( percentage >= 0 ) && ( percentage <= 100 ) )
			{
				this.bar.style.width		=	( percentage * 3 ) + 'px';
				this.percent.innerHTML		=	percentage + '%';
			}
		}

		this.updateSyncing		=	function( text )
		{
			if ( !this.syncing )
				this.syncing		=	document.getElementById( 'syncing' );

			this.syncing.innerHTML		=	text;
		}

		this.updateFrom			=	function( text )
		{
			if ( !this.from )
				this.from		=	document.getElementById( 'from' );
			
			this.from.innerHTML	=	text;
		}

		this.updateTo			=	function( text )
		{
			if ( !this.to )
				this.to			=	document.getElementById( 'to' );
			this.to.innerHTML	=	text;

			// let's conveniently use this point to add a new row to the stats table, too
			var stats	=	document.getElementById( 'sync-stats-table' );
			if ( stats.firstChild.nodeName == 'TBODY' )	stats = stats.firstChild;

			var row		=	document.createElement( 'tr' );
			stats.appendChild( row );

			var account	=	document.createElement( 'td' );
			row.appendChild( account );
			var actName	=	document.createTextNode( text );
			account.appendChild( actName );
			account.setAttribute( 'id', 'stats-account' );

			this.adds	=	document.createElement( 'td' );
			row.appendChild( this.adds );
			var zero	=	document.createTextNode( '0' );
			this.adds.appendChild( zero );
			this.adds.setAttribute( 'id', 'stats-field' );

			this.failedadds	=	document.createElement( 'td' );
			row.appendChild( this.failedadds );
			var zero	=	document.createTextNode( '0' );
			this.failedadds.appendChild( zero );
			this.failedadds.setAttribute( 'id', 'stats-field' );

			this.updates	=	document.createElement( 'td' );
			row.appendChild( this.updates );
			var zero	=	document.createTextNode( '0' );
			this.updates.appendChild( zero );
			this.updates.setAttribute( 'id', 'stats-field' );

			this.failedupds	=	document.createElement( 'td' );
			row.appendChild( this.failedupds );
			var zero	=	document.createTextNode( '0' );
			this.failedupds.appendChild( zero );
			this.failedupds.setAttribute( 'id', 'stats-field' );
		}

		this.updateAdds			=	function( num )
		{
			if ( this.adds ) 	this.adds.innerHTML		=	num;
		}

		this.updateFailedAdds		=	function( num )
		{
			if ( this.failedadds )	this.failedadds.innerHTML	=	num;
		}

		this.updateUpdates		=	function( num )
		{
			if ( this.updates ) 	this.updates.innerHTML		=	num;
		}

		this.updateFailedUpdates	=	function( num )
		{
			if ( this.failedupds ) 	this.failedupds.innerHTML	=	num;
		}

		this.showReturnButton		=	function()
		{
			myButton		=	document.getElementById( 'return-button' );
			myButton.style.display	=	'';
		}

		this.showAccountError		=	function( text )
		{
			this.failedadds.parentNode.removeChild( this.failedadds );		this.failedadds		=	null;
			this.updates.parentNode.removeChild( this.updates );			this.updates		=	null;
			this.failedupds.parentNode.removeChild( this.failedupds );		this.failedupds		=	null;

			this.adds.setAttribute( 'colspan', 4 );
			this.adds.setAttribute( 'id', 'stats-errormsg' );
			this.adds.innerHTML	=	text;

			this.adds		=	null;
		}

		this.showPageError		=	function( text )
		{
			if ( !this.pageerror )
				this.pageerror		=	document.getElementById( 'page-errormsg' );

			this.pageerror.innerHTML	=	text;
		}

		this.showCurrentTime		=	function( text )
		{
			if ( !this.currenttime )
				this.currenttime	=	document.getElementById( 'current-time' );

			this.currenttime.innerHTML	=	text;
		}

		this.showPredictedEndTime	=	function( text )
		{
			if ( !this.predictedendtime )
				this.predictedendtime	=	document.getElementById( 'predicted-end-time' );

			this.predictedendtime.innerHTML	=	text;
		}
	}

	myUI	=	new SyncUI();
</script>

<table class='form-table'>
	<tr>
		<th colspan=3>Sync Progress</th>
	</tr>

	<tr>
		<td colspan=3 id='page-errormsg'>&nbsp;</td>
	</tr>

	<tr>
		<td class='sync-field'>Syncing:</td>
		<td class='sync-field'>From:</td>
		<td class='sync-field'>To:</td>
	</tr>

	<tr>
		<td class='sync-field' id='syncing'>&nbsp;</td>
		<td class='sync-field' id='from'>&nbsp;</td>
		<td class='sync-field' id='to'>&nbsp;</td>
	</tr>

	<tr>
		<td colspan=3>&nbsp;</td>
	</tr>

	<tr>
		<td colspan=3 class='explanation'>
			<table class='time-displays'>
				<tr>
					<th>Current Time</th>
					<th>Predicted End Time</th>
				</tr>
				<tr>
					<td id='current-time'>&nbsp;</td>
					<td id='predicted-end-time'>&nbsp;</td>
				</tr>
			</table>	
		</td>
	</tr>

	<tr>
		<td colspan=3>&nbsp;</td>
	</tr>

	<tr>
		<td colspan=3 class='explanation'>
			<table class='progress-with-percent'>
				<tr>
					<td>
						<div id='progress-bar'>
							<div id='filled-progress-bar'>
								&nbsp;
							</div>
						</div>
					</td>
					<td>
						<div id='progress-percent'>
							0%
						</div>
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td colspan=5>
			<table style='width: 100%; margin-top: 20px;' id='sync-stats-table'>
				<tr>
					<th id='account'>Account</th>
					<th>Adds</th>
					<th>Failed Adds</th>
					<th>Updates</th>
					<th>Failed Updates</th>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td colspan=3>&nbsp;</td>
	</tr>
</table>

<center>
	<img style='display: none;' id='return-button' onclick="JavaScript: document.location = '?Action=MainMenu';" src='images/MainMenu-Button.png' />
</center>

<iframe style='display: none; width: 640px; height: 480px;' src='?Action=ExecuteSync'></iframe>	<!-- the real action's HERE -->