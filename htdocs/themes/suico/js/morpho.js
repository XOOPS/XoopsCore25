/*
 * Mor.pho.GEN.e.sis 3.x
 *
 * @package         themes
 * @since           2.4.x
 * @maintained      Afux <http://www.afux.org>
 *
 * @version         $Id
*/

/* text sizer */
	function fsize(size,unit,id){
	  var vfontsize = document.getElementById(id);
	  if(vfontsize){
	   vfontsize.style.fontSize = size + unit;
	  }
	}

var textsize = 1;
	function changetextsize(up){
	  if(up){
	   textsize = parseFloat(textsize)+0.05;
	  }else{
	   textsize =parseFloat(textsize)-0.05;
	  }
	}

/* show hide div id */
	function xoToggleBlock( id )
		{
        var value = (document.getElementById(id).style.display == 'none') ? 'block' : 'none';
		xoSetBlock( id, value );
        xoSetCookie( id, value );
		}
    function xoSetBlock( id, value )
		{
		document.getElementById(id).style.display = value;
		}
    function xoTrim( str )
		{
        return str.replace(/^\s+|\s+$/g, '') ;
		}
    function xoGetCookie( name )
    {
	 var cookieName = 'XoMorpho_' + name;
	 var cookie = document.cookie;

	 var cookieList = cookie.split( ";" );
		for( var idx in cookieList )
    {
	cookie = cookieList[idx].split( "=" );
		if ( xoTrim( cookie[0] ) == cookieName )
			{
		return( cookie[1] );
	  }
	 }
	return 'none';
		}
		function xoSetCookie( name, value )
		{
	 var cookieName = 'XoMorpho_' + name;
	 var expires = new Date();
	 expires.setTime( expires.getTime() + (365 * 24 * 60 * 60 * 1000));
	 document.cookie = cookieName + "=" + value + "; expires=" + expires + ";";
    }
/* show hide popup id */
	function XoHideDiv(id) {
		if (document.getElementById(id)) { // DOM3 = IE5, NS6
		document.getElementById(id).style.visibility = 'hidden';
		}
		else {
			if (document.layers) { // Netscape 4
			document.getElementById(id).visibility = 'hidden';
			}
			else { // IE 4
			document.all.getElementById(id).style.visibility = 'hidden';
			}
		}
	}
	
	function XoShowDiv(id) {
		if (document.getElementById(id)) { // DOM3 = IE5, NS6
		document.getElementById(id).style.visibility = 'visible';
		}
		else {
			if (document.layers) { // Netscape 4
			document.getElementById(id).visibility = 'visible';
			}
			else { // IE 4
			document.all.getElementById(id).style.visibility = 'visible';
			}
		}
	}