<?php 	
	/**
	* Plugin Main Class
	*/
	class LA_Front_Edit 
	{
		
		function __construct()
		{
			add_action( "admin_menu", array($this,'front_editor_admin_options'));
			add_filter( 'the_title', array($this,'editable_title'));
			add_action( "wp_head", array($this,'add_enable_disable_button'));
			add_filter( 'the_content', array($this,'editable_content')); 
			add_action( 'wp_enqueue_scripts', array($this,'add_scripts'));
			add_action( 'wp_ajax_save_content_front', array($this, 'save_content_front') );
			add_action( 'admin_enqueue_scripts', array($this,'admin_enqueuing_scripts'));
			add_action('wp_ajax_la_save_front_editor', array($this, 'save_admin_options'));

		}

		function front_editor_admin_options(){
			add_menu_page( 'WP Quick Frontend Editor', 'WP Quick Frontend Editor', 'manage_options', 'front_editor', array($this,'front_editor_menu_page'), 'dashicons-welcome-write-blog');
		}

		function admin_enqueuing_scripts($slug){
			if ($slug == 'toplevel_page_front_editor') {
				wp_enqueue_script( 'admin-js', plugins_url( 'admin/admin.js', __FILE__ ));
				wp_localize_script( 'admin-js', 'laAjax', array( 'url' => admin_url( 'admin-ajax.php' )));
			}
		
		}

		function save_admin_options(){
			if (isset($_REQUEST)) {
				update_option( 'la_front_editor', $_REQUEST );
			}
			die(0);
		}

		function add_enable_disable_button(){ 
			$saved_options = get_option( 'la_front_editor' );
			
			$user = wp_get_current_user();
			$allroles = ( isset($saved_options['role']) ) ? $saved_options['role'] : array();
			//print_r($allroles);
			 if( array_intersect($allroles, $user->roles ) && is_single() && $saved_options['btnText']!=''){
			 	echo '<button style="position: fixed;top: 1px;left: 35%;z-index: 999000;" class="btn btn-sm btn-default activep"> <i class="fa fa-pencil"></i> '.$saved_options["btnText"].' </button>
					<button style="position: fixed;top: 1px;left: 35%;z-index: 999000;" class="btn btn-sm btn-danger deactive"> <i class="fa fa-shield"></i> '.$saved_options["disBtntext"].'</button>';
			 }
		}

		function front_editor_menu_page(){
			$saved_options = get_option( 'la_front_editor' );
			if (isset($saved_options['role'])) {
				$allroles = array_values($saved_options['role']);
			}
			?>

			<div class="wrap" id="fronteditor">
				<h2 style="margin: 20px 0;">WP Quick Frontend Editor Settings</h2>
				<hr>
				<?php if ($saved_options!='') {?>
					

				<table class="form-table">
					<tr>
						<td style="width:30%"><strong><?php _e( 'Enable Button Position', 'la-fronteditor' ); ?></strong> </td>
						<td style="width:30%">
							<select class="btnposition widefat">
								
								<option value="toolbar" <?php if ( $saved_options['position'] == 'toolbar' ) echo 'selected="selected"'; ?>>On Toolbar</option>
								<option value="abovecontent">Above Content(PRO Feature)</option>
								<option><?php _e( 'Fixed With Scroll (PRO Feature)', 'la-fronteditor' ); ?></option>
							</select>
						</td>
						<td style="width:40%">
							<p class="description"> <?php _e( 'Choose Position of "Enable Quick Frontend Editor Button"', 'la-fronteditor' ); ?></p>
						</td>
					</tr>
					<tr>
						<td><strong><?php _e( 'Enable Button Text', 'la-fronteditor' ); ?></strong> </td>
						<td>
							<input type="text" class="btntext widefat" value="<?php echo $saved_options['btnText']; ?>">
						</td>
						<td>
							<p class="description"> <?php _e( 'Enter Button Text.This will be shown on button on frontend.', 'la-fronteditor' ); ?><b>(Required)</b></p>
						</td>
					</tr>
					<tr>
						<td><strong><?php _e( 'Disable Button Text', 'la-fronteditor' ); ?></strong> </td>
						<td>
							<input type="text" class="disbtntext widefat" value="<?php echo $saved_options['disBtntext']; ?>">
						</td>
						<td>
							<p class="description"> <?php _e( 'Enter Disable Button Text.This will be shown on button on frontend.', 'la-fronteditor' ); ?><b>(Required)</b></p>
						</td>
					</tr>
					<tr> 
						<td><strong><?php _e( 'Who Can Edit? ( All registered roles available in PRO )', 'la-fronteditor' ); ?></strong> </td>
						<td>
							
							 <input type="checkbox" name="roles" class="admin" checked value="administrator" <?php if ( in_array( 'administrator', $allroles) ) echo 'checked="checked"'; ?>>Administrator <br>
							 <input type="checkbox" name="roles" class="editor" value="editor" <?php if ( in_array( 'editor', $allroles) ) echo 'checked="checked"'; ?>>Editor <br>
							 <input type="checkbox" name="roles" class="author" value="author" <?php if ( in_array( 'author', $allroles) ) echo 'checked="checked"'; ?>>Author <br>
							 <input type="checkbox" name="roles" class="contributor" value="contributor" <?php if ( in_array( 'contributor', $allroles) ) echo 'checked="checked"'; ?>>Contributor <br>
						</td>
						<td>
							<p class="description"> <?php _e( 'Choose who can edit post,pages,custom posts with editor', 'la-fronteditor' ); ?></p>
						</td>
					</tr>
					<tr>
						<td><strong><?php _e( 'Post Types to be Edited (PRO Feature)', 'la-fronteditor' ); ?></strong> </td>
						<td>
							
							<input type="checkbox" name="post-types" checked="true" class="post" value="post" >Posts <br>
							<input type="checkbox" name="post-types" class="page" value="page">Pages <br>
							<?php if (class_exists( 'woocommerce' )): ?>
								<input type="checkbox" name="post-types" class="product" value="author">Products <br>
							<?php endif ?>
							 
						</td>
						<td>
							<p class="description"> <?php _e( 'Choose which post types would be allowed to edit from frontend.With free version you can only edit posts.', 'la-fronteditor' ); ?></p>
						</td>
					</tr>
					<tr>
						<td><strong><?php _e( 'Allow User to Add Post? (PRO Feature)', 'la-fronteditor' ); ?></strong> </td>
						<td>
							<select class="addpost widefat">
								<option value="yes">Yes</option>
								<option value="no">No</option> 
							</select> 
						</td>
						<td>
							<p class="description"> <?php _e( 'Allow user to add post from frontend.', 'la-fronteditor' ); ?></p>
						</td>
					</tr>

					<tr class="addpostroles">
						<td><strong><?php _e( 'Who Can Add Post? (PRO Feature)', 'la-fronteditor' ); ?></strong> </td>
						<td>
							
							 <input type="checkbox" name="roles" class="admin" value="administrator" checked>Administrator <br>
							 <input type="checkbox" name="roles" class="editor" value="editor">Editor <br>
							 <input type="checkbox" name="roles" class="author" value="author">Author <br>
							 <input type="checkbox" name="roles" class="contributor" value="contributor">Contributor <br>
							 
						</td>
						<td>
							<p class="description"> <?php _e( 'Choose who can add post from from frontend.', 'la-fronteditor' ); ?></p>
						</td>
					</tr>
				</table>
				<?php } else{ ?>
 
				<table class="form-table">
					<tr>
						<td style="width:30%"><strong><?php _e( 'Enable Button Position', 'la-fronteditor' ); ?></strong> </td>
						<td style="width:30%">
							<select class="btnposition widefat">
								<option value="toolbar">On Toolbar</option>
								<option value="abovecontent">Above Content(PRO Feature)</option>
								<option>Fixed With Scroll (PRO Feature)</option>
							</select>
						</td>
						<td style="width:40%">
							<p class="description"> <?php _e( 'Choose Position of "Enable Quick Frontend Editor Button"', 'la-fronteditor' ); ?></p>
						</td>
					</tr>
					<tr>
						<td><strong><?php _e( 'Enable Button Text', 'la-fronteditor' ); ?></strong> </td>
						<td>
							<input type="text" class="btntext widefat" value="">
						</td>
						<td>
							<p class="description"> <?php _e( 'Enter Button Text.This will be shown on button on frontend.', 'la-fronteditor' ); ?><b>(Required)</b></p>
						</td>
					</tr>
					<tr>
						<td><strong><?php _e( 'Disable Button Text', 'la-fronteditor' ); ?></strong> </td>
						<td>
							<input type="text" class="disbtntext widefat" value="">
						</td>
						<td>
							<p class="description"> <?php _e( 'Enter Disable Button Text.This will be shown on button on frontend.', 'la-fronteditor' ); ?><b>(Required)</b></p>
						</td>
					</tr>
					<tr>
						<td><strong><?php _e( 'Who Can Edit? ( All registered roles available in PRO )', 'la-fronteditor' ); ?></strong> </td>
						<td>
							
							 <input type="checkbox" name="roles" class="admin" value="administrator" checked>Administrator <br>
							 <input type="checkbox" name="roles" class="editor" value="editor">Editor <br>
							 <input type="checkbox" name="roles" class="author" value="author">Author <br>
							 <input type="checkbox" name="roles" class="contributor" value="contributor">Contributor <br>
						</td>
						<td>
							<p class="description"> <?php _e( 'Choose who can edit post,pages,custom posts with editor', 'la-fronteditor' ); ?></p>
						</td>
					</tr>
					<tr>
						<td><strong><?php _e( 'Post Types to be Edited (PRO Feature)', 'la-fronteditor' ); ?></strong> </td>
						<td>
							
							<input type="checkbox" name="post-types" checked="true" class="post" value="post">Posts <br>
							<input type="checkbox" name="post-types" class="page" value="page">Pages <br>
							<?php if (class_exists( 'woocommerce' )): ?>
								<input type="checkbox" name="post-types" class="product" value="author">Products <br>
							<?php endif ?>
							 
						</td>
						<td>
							<p class="description"> <?php _e( 'Choose which post types would be allowed to edit from frontend.With free version you can only edit posts.', 'la-fronteditor' ); ?></p>
						</td>
					</tr>
					<tr>
						<td><strong><?php _e( 'Allow User to Add Post?', 'la-fronteditor' ); ?></strong> </td>
						<td>
							<select class="addpost widefat">
								<option value="yes">Yes</option>
								<option value="no">No</option> 
							</select> 
						</td>
						<td>
							<p class="description"> <?php _e( 'Allow user to add post from frontend.', 'la-fronteditor' ); ?></p>
						</td>
					</tr>

					<tr class="addpostroles">
						<td><strong><?php _e( 'Who Can Add Post?', 'la-fronteditor' ); ?></strong> </td>
						<td>
							
							 <input type="checkbox" name="roles" class="admin" value="administrator" checked>Administrator <br>
							 <input type="checkbox" name="roles" class="editor" value="editor">Editor <br>
							 <input type="checkbox" name="roles" class="author" value="author">Author <br>
							 <input type="checkbox" name="roles" class="contributor" value="contributor">Contributor <br>
							 
						</td>
						<td>
							<p class="description"> <?php _e( 'Choose who can add post from from frontend.', 'la-fronteditor' ); ?></p>
						</td>
					</tr>
				</table>
				<?php } ?>
				<hr style="margin-top: 20px;">
				<button class="saveoptions button button-primary" >Save Settings</button>
				<span id="la-loader"><img src="<?php echo plugin_dir_url( __FILE__ ); ?>images/ajax-loader.gif"></span>
				<span id="la-saved"><strong><?php _e( 'Settings Saved', 'la-postviewer' ); ?>!</strong></span>

				<a style="text-decoration:none;"  href="https://1.envato.market/34Brr" target="_blank"><h4 style="padding: 15px;background: #58abbe;color: #fff;margin-bottom: 0px;text-align:center;font-size:24px;"> Get Pro Version</h4></a><br>

				<a style="text-decoration:none;"  href="https://webdevocean.com/" target="_blank"><h4 style="padding: 15px;background: #c44747;color: #fff;margin-bottom: 0px;text-align:center;font-size:20px;font-style: italic;"> Need Customized Solution? Contact Us</h4></a><br>

			</div>
			<?php
		}
		function editable_title($title){
			$saved_options = get_option( 'la_front_editor' );
			$allroles = ( isset($saved_options['role']) ) ? $saved_options['role'] : array();
			$user = wp_get_current_user();
			
			 if( array_intersect($allroles, $user->roles )&& in_the_loop() ){
            	$title = '<span contenteditable="true" class="la-title">'.$title.'</span> <span class="text-muted hid bg-info" style="font-size: 12px;display: inline;">Click title to change</span>';
       		 } 
        	return $title;
		}


		function save_content_front(){ 

			extract($_REQUEST);
			print_r($_REQUEST);
			
				
			$thumb_id = get_post_thumbnail_id( $id );
			if ( isset($imagesrc) && $imagesrc != '' ) {
				set_post_thumbnail( $id, $imageid );
			} else{
				delete_post_thumbnail( $id );
			};

			
			  $my_post = array(
			      'ID'           => $id,
			      'post_title' => $title,
			      'post_content' => $content,
			  );

			wp_update_post( $my_post );
			die(0);
		}

		function editable_content($content){
			$saved_options = get_option( 'la_front_editor' );
			// var_dump($saved_options);
			$allroles = ( isset($saved_options['role']) ) ? $saved_options['role'] : array();
			$user = wp_get_current_user();
			 if( array_intersect($allroles, $user->roles ) && in_the_loop() && is_singular() ){
			 	global $post;
			 	$thumb = get_the_post_thumbnail($post->ID);
			 	$url =  get_post_thumbnail_id($post->ID);
			 	if ($saved_options['position']=='toolbar' && $saved_options['position']!='') {
			 		  $content ='
			 	<div class="wdo-outer">
					<div class="post-fea">
						      	
					</div>		 		  
					
					<div class="btn-toolbar" data-role="editor-toolbar" data-target="#editor">
				      <div class="btn-group">
				        <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="Font"><i class="fa fa-font"></i><b class="caret"></b></a>
				          <ul class="dropdown-menu">
				          </ul>
				        </div>
				      <div class="btn-group">
				        <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="Font Size"><i class="fa fa-text-height"></i>&nbsp;<b class="caret"></b></a>
				          <ul class="dropdown-menu">
				          <li><a data-edit="fontSize 5"><font size="5">Huge</font></a></li>
				          <li><a data-edit="fontSize 3"><font size="3">Normal</font></a></li>
				          <li><a data-edit="fontSize 1"><font size="1">Small</font></a></li>
				          </ul>
				      </div>
				      <div class="btn-group">
				        <a class="btn btn-default" data-edit="bold" title="Bold (Ctrl/Cmd+B)"><i class="fa fa-bold"></i></a>
				        <a class="btn btn-default" data-edit="italic" title="Italic (Ctrl/Cmd+I)"><i class="fa fa-italic"></i></a>
				        <a class="btn btn-default" data-edit="strikethrough" title="Strikethrough"><i class="fa fa-strikethrough"></i></a>
				        <a class="btn btn-default" data-edit="underline" title="Underline (Ctrl/Cmd+U)"><i class="fa fa-underline"></i></a>
				      </div>
				      <div class="btn-group">
				        <a class="btn btn-default" data-edit="insertunorderedlist" title="Bullet list"><i class="fa fa-list-ul"></i></a>
				        <a class="btn btn-default" data-edit="insertorderedlist" title="Number list"><i class="fa fa-list-ol"></i></a>
				        <a class="btn btn-default" data-edit="outdent" title="Reduce indent (Shift+Tab)"><i class="fa fa-indent"></i></a>
				        <a class="btn btn-default" data-edit="indent" title="Indent (Tab)"><i class="fa fa-dedent"></i></a>
				      </div>
				      <div class="btn-group">
				        <a class="btn btn-default" data-edit="justifyleft" title="Align Left (Ctrl/Cmd+L)"><i class="fa fa-align-left"></i></a>
				        <a class="btn btn-default" data-edit="justifycenter" title="Center (Ctrl/Cmd+E)"><i class="fa fa-align-center"></i></a>
				        <a class="btn btn-default" data-edit="justifyright" title="Align Right (Ctrl/Cmd+R)"><i class="fa fa-align-right"></i></a>
				        <a class="btn btn-default" data-edit="justifyfull" title="Justify (Ctrl/Cmd+J)"><i class="fa fa-align-justify"></i></a>
				      </div>
				      	<div class="btn-group">
						  <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="Hyperlink"><i class="fa fa-link"></i></a>
						    <div class="dropdown-menu input-append">
							    <input class="form-control" placeholder="URL" type="text" data-edit="createLink"/>
							    <button type="button">Add</button>
				        	</div>
				        	<a class="btn btn-default" data-edit="unlink" title="Remove Hyperlink"><i class="fa fa-scissors"></i></a>

				      	</div>
				      
				      	<div class="btn-group">
				        	<a class="btn btn-default" title="Insert picture (or just drag & drop)" id="upload-btn"><i class="fa fa-file-image-o"></i></a>
				        
				      	</div>
					    <div class="btn-group">
					        <a class="btn btn-default" data-edit="undo" title="Undo (Ctrl/Cmd+Z)"><i class="fa fa-undo"></i></a>
					        <a class="btn btn-default" data-edit="redo" title="Redo (Ctrl/Cmd+Y)"><i class="fa fa-repeat"></i></a>
					    </div>
				      	<div class="btn-group fea-btn" style="margin-top:10px;">
							<button  class="add btn btn-sm btn-primary"><i class="fa fa-picture-o"></i> Add Featured Image</button>
							<button  class="fea btn btn-sm btn-success"> <i class="fa fa-picture-o"></i> Change Featured Image</button>
							<button  class="remo btn btn-sm btn-danger"><i class="fa fa-times"></i> Remove Featured Image</button>
						</div>
				    </div>

				 
				    <div class="eneditor editor" >
				      '.$content.'
				    </div> <br>
					<div>
					 <img class="pull-right loader" src="'.plugins_url( "images/ajax-loader.gif", __FILE__  ).'" style="margin-left: 5px;"><button class="btn btn-primary pull-right" id="la-save">Save Changes</button> <span id="la-id" data-laid="'.$post->ID.'"></span>
					</div>
				</div>
				 ';
			 	}
	   	
       		 }
        	
			return $content;
		}
		function add_scripts(){
			wp_enqueue_style( 'bootstrap-css', plugins_url( 'css/bootstrap.min.css', __FILE__ ));
			wp_enqueue_style( 'font-awesome', plugins_url( 'font-awesome/css/font-awesome.min.css', __FILE__ ),array('bootstrap-css'));
			wp_enqueue_script( 'bootstrap-js', plugins_url( 'js/bootstrap.min.js', __FILE__ ),array('jquery'));
			wp_enqueue_script( 'hotkeys-js', plugins_url( 'js/jquery.hotkeys.js', __FILE__ ),array('jquery'));
			wp_enqueue_script( 'bootstrap-wysiwyg', plugins_url( 'js/bootstrap-wysiwyg.js', __FILE__ ),array('jquery','bootstrap-js'));
			wp_enqueue_script( 'la-custom-js', plugins_url( 'js/script.js', __FILE__ ),array('jquery','bootstrap-js','bootstrap-wysiwyg'));
			wp_localize_script( 'la-custom-js', 'laAjax', array( 'url' => admin_url( 'admin-ajax.php' )));
			wp_enqueue_media( );

		}

	}
 ?>