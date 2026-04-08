//$ sign in parentheses here: 
//http://stackoverflow.com/questions/10807200/jquery-uncaught-typeerror-property-of-object-object-window-is-not-a-funct
 jQuery(document).ready(function($) {

$(function () {
    
    
  var cookie_options = {

    expires: 365
   
  }
	$("#LangSourceTree").jstree({ 

		 "themes": {
                    "theme": "classic",
                    "dots": false,
                    "icons": false
                    },

 		 "core": { 
                    "animation": 0,
                    "open_parents": true,
                    "initially_open": ["phtml_1"] //otworz glowne drzewo polskie
                },
                "cookies": {    
                    "cookie_options" : cookie_options,
//                    "override_ui" : true
                },

 "checkbox": { 
              
               "override_ui" : true
//A boolean. Default is false.
//If set to true all selection will be handled by checkboxes. The checkbox plugin will map UI's get_selected function to its own get_checked function and overwrite the UI reselect function. It will also disable the select_node, deselect_node and deselect_all functions. If left as false nodes can be selected and checked independently. Set to true to allow the cookie plugin to automatically save a node's checked state. 
                },

		"plugins" : [ "themes", "html_data", "ui", "cookies", "checkbox" ]
	});






 });
  
//alert($.cookie('jstree_select'));
var language = window.navigator.userLanguage || window.navigator.language;
$( "<p>"+language+"</p>" ).insertBefore( "#LangSourceTree" );

jQuery('#phtml_67').removeClass("jstree-closed").addClass("jstree-open");
 
//var jqxhr = $.getJSON( "http://localhost/Memejet/?json=get_category_index", function() {
//  console.log( "success" );
//})
//  .done(function() {
//    console.log( "second success" );
//  })
//  .fail(function() {
//    console.log( "error" );
//  })
//  .always(function() {
//    console.log( "complete" );
//  });
//  
//  alert (jqxhr);

});

