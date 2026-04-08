 jQuery(document).ready(function() {

$(function () {
    
    
  var opcje_cookie = {
   
   
    expires: 365
   
  }
	$("#demo1").jstree({ 

		 "themes": {
                    "theme": "apple",
                    "dots": false,
                    "icons": false
                    },

 		 "core": { 
                    "animation": 0,
                    "open_parents": true,
                    "initially_open": ["phtml_1"] //otworz glowne drzewo
                },
                "cookies": {    
                    "cookie_options" : opcje_cookie,
                    "override_ui" : true
                },

 "checkbox": { 
              
                  "override_ui" : true
//A boolean. Default is false.
//If set to true all selection will be handled by checkboxes. The checkbox plugin will map UI's get_selected function to its own get_checked function and overwrite the UI reselect function. It will also disable the select_node, deselect_node and deselect_all functions. If left as false nodes can be selected and checked independently. Set to true to allow the cookie plugin to automatically save a node's checked state. 
                },

		"plugins" : [ "themes", "html_data", "ui", "cookies", "checkbox" ]
	});






 });


});

