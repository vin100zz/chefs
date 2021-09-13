<?php
function println($string_message = '')
{
	echo $string_message.PHP_EOL;
}
function beginHtml()
{
	println("<html>");
	println("<head>");
		println("<meta charset='utf-8'>");
		
		println("<title>Chefs d'État</title>");
		 
		// JQuery & JQuery UI
		println("<link rel='stylesheet' type='text/css' href='script/jquery-ui-1.10.1.custom/css/custom-theme/jquery-ui-1.10.1.custom.min.css' />"); 
		println("<script type='text/javascript' src='script/jquery-1.9.1.js'></script>");
		println("<script type='text/javascript' src='script/jquery-ui.js'></script>");
		 
		// DataTables
		println("<script type='text/javascript' charset='utf-8' src='script/DataTables-1.9.4/media/js/jquery.dataTables.js'></script>");
		println("<style type='text/css' title='currentStyle'>");
		println("@import 'script/DataTables-1.9.4/media/css/demo_table.css';");
		println("</style>");
		
		// Chosen
		println("<script src='script/chosen-master/chosen/chosen.jquery.js' type='text/javascript'></script>");
		println("<link rel='stylesheet' href='script/chosen-master/chosen/chosen.css' />");
		
		// Font
		println("<link href='https://fonts.googleapis.com/css?family=Russo+One' rel='stylesheet' type='text/css'>");
		
		// Flags
		println("<link rel='stylesheet' type='text/css' href='style/flags/stylesheets/flags16.css' />");
    println("<link rel='stylesheet' type='text/css' href='style/flags/stylesheets/flags32.css' />");
          
    // Bootstrap
    println("<script type='text/javascript' src='etats/skins/strapping/bootstrap/js/bootstrap.js'></script>");
		
		// JQuery Browser
		println("<script type='text/javascript' src='script/jquery.browser.min.js'></script>");
    
    // Timeline
    println("<link rel='stylesheet' type='text/css' href='script/timeline/timeline.css' />"); 
    println("<script type='text/javascript' src='script/timeline/timeline.js'></script>");

		// Chefs
    println("<link rel='stylesheet' type='text/css' href='style/common.css' />"); 
		println("<link rel='stylesheet' type='text/css' href='style/chefs.css' />"); 
		println("<script type='text/javascript' charset='utf-8' src='script/chefs.js'></script>");
		 
	println("</head>");
	println("<body>");
}
function endHtml()
{
	println("<div id='page-header'>");
    println("<div id='navigationbar'>");
      println("<a class='chefs' href='./'></a>");
      println("<a class='etats' href='etats/'></a>");
    println("</div>");
  println("</div>");
	
	println("<div id='actionbar'>");
    println("<ul>");
      println("<li id='action-filter' class='filter'><a id='button-filter'></a></li>");
      println("<li id='action-new' class='new'><a id='button-new'></a></li>");
    println("</ul>");
	println("</div>");
  
	println("</body>");
	println("</html>");
}
?>