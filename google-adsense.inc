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


	function google_adsense( $ad_slot, $ad_width = 728, $ad_height = 90 )
	{
		$Google_AdSense		=	
'<script type="text/javascript"><!--
google_ad_client 	= "' . GOOGLE_AD_CLIENT . '";
/* Front Page Bottom */
google_ad_slot 		= "' . $ad_slot . '";
google_ad_width 	= ' . $ad_width . ';
google_ad_height 	= ' . $ad_height . ';
//-->
</script>
<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
';
		return $Google_AdSense;
	}
?>
