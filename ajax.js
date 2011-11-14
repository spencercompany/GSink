function ajaxFunction( requestURL, callbackFunction )
{
	var xmlHttp;

	try
	{
		// Firefox, Opera 8.0+, Safari
		xmlHttp		=	new XMLHttpRequest();
	}
	catch (e)
	{
		// Internet Explorer
		try
		{
			xmlHttp		=	new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			try
			{
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}

	xmlHttp.onreadystatechange = function()
	{
		if ( xmlHttp.readyState == 4 )
			callbackFunction( xmlHttp.responseText );
	}

	xmlHttp.open( "GET", requestURL, true );
	xmlHttp.send( null );
	
	return xmlHttp;
}
