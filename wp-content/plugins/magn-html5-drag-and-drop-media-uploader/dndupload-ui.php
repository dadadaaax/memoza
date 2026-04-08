<?php

function dndmedia_show_ui_settings_page() {

    $categories = get_categories();
    ?>
    <div class="wrap">
        <h2>Magn Drag and Drop Upload</h2>
      

        <h3>General Settings</h3>

        <form name="dndmedia_options" method="POST" action="options.php">
    <?php //wp_nonce_field('update-options');  ?>
            <?php settings_fields('dndmedia-settings-group'); ?>
            <input type="hidden" name="dndmedia_form_action" value="save">

            <div><input type="checkbox" id="dndmedia_sendtoeditor" name="dndmedia_sendtoeditor" value="1" <?php echo (get_option('dndmedia_sendtoeditor') ? "checked" : "") ?>> Auto publish into editor after successful upload</div>
            <div><input type="checkbox" id="dndmedia_attachment" name="dndmedia_attachment" value="1" <?php echo (get_option('dndmedia_attachment') ? "checked" : "") ?> disabled="disabled"> Auto create attachment (recommended)</div>
            <div>Default attachment size: 
                <select name="dndmedia_attachment_size" id="dndmedia_attachment_size">
                    <option value=""></option>
    <?php
    $dndmedia_sizes = get_intermediate_image_sizes();
    foreach ($dndmedia_sizes as $size_name => $size_attrs) {
        // Get the image source, width, height, and whether it's intermediate.
        // $image = wp_get_attachment_image_src( get_the_ID(), $size );
        // Add the link to the array if there's an image and if $is_intermediate (4th array value) is true or full size.
        //if ( !empty( $image ) && ( true == $image[3] || 'full' == $size ) ) $links[] = "<a class='image-size-link' href='{$image[0]}'>{$image[1]} &times; {$image[2]}</a>";
        //$size_attrs_str = $size_name.' - w:'. $size_attrs['width'] . ', h:' . $size_attrs['height'] . ', crop:' . $size_attrs['crop'];
        $size_attrs_str = $size_attrs;
        $size_name = $size_attrs;
        $selected = ( get_option('dndmedia_attachment_size') == $size_name ? "selected" : "" );
        echo '<option value="' . $size_name . '" ' . $selected . '>' . $size_attrs_str . '</option>';
    }
    ?>
                    <option value="">full</option>
                </select>
            </div>

            <div><input type="checkbox" id="dndmedia_dropstyle" name="dndmedia_dropstyle" value="gmail" <?php echo (get_option('dndmedia_dropstyle') ? "checked" : "") ?>> Use Gmail drop files style</div>


            <p class="submit">
                <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
        </form>
    </div><!-- end wrap-->
    <?
}

// end wpsync_show_ui_settings_page

function dndmedia_edit_form_advanced_ui() {
    ?>

    <div id="drop-box-overlay"> 

        <div id="drop-box-jsupload" >
            Wrzuć tu plik
        </div>

    </div> 


    <?php
}

function dndmedia_show_metabox_ui() {
    $this_plugin_url = WP_PLUGIN_URL . '/' . str_replace(basename(__FILE__), "", plugin_basename(__FILE__));

    $dndmedia_dropstyle = get_option('dndmedia_dropstyle');
    $dndmedia_dropstyle = 'gmail';
    ?>


    <script type="text/javascript">
        <!--
    <?php if (!empty($dndmedia_dropstyle)): ?>
            dndmedia_dropstyle = '<?= $dndmedia_dropstyle ?>';
    <?php endif; ?>
    	
    <?php if (!empty($dndmedia_scrollto)): ?>
            dndmedia_scrollto = '<?= $dndmedia_scrollto ?>';
    <?php endif; ?>
        -->
            
            
           
        //podpinamy akcje - zmiana w select file powoduje odsloniecie guzika Wrzuc Qpę
        //TODO@: - powinna od razu powodować wysłanie formy
        jQuery(document).ready(function(){
                    
                  
                  
            jQuery("#qpa_file_select").bind("change", function() 
            {
                    
                  
                jQuery('#old_submit_qpa_file_button').show();

                jQuery('#load_instrukcja0').hide();
                jQuery('#load_instrukcja1').hide();
                jQuery('#load_instrukcja2').hide();
                 jQuery("#drop-box-overlay-gmail").hide();
            });
                   
            //auto upload after select http://stackoverflow.com/questions/12540953/property-submit-of-object-htmlformelement-is-not-a-function
            //nigdy nie nazywaj elementow formy (ani id ani name "submit" - to sie miesza z nazwa fukcji submit();!!!
//            jQuery('#qpa_file_select').change(function() {
//                jQuery('#qpa_new_post_form').submit();
//            });


                   
                   
              
                   
        });
         
            
    </script>






    <div id="browser-warning" class="warning" style="display: none; "> 

        Prawdopodobnie używasz przestarzałej przeglądarki. Q-pa najlepiej hula na Chromie i Firefoxie. </div> 
    <div class="clearfix"> </div>

    <div id="load_instrukcja1" class="dndmedia_information" > 1. Przeciągnij plik *.jpg myszką i upuść nad zielonym polem</div>

    <div id="drop-box-overlay-gmail-wrapper"  style="display:none;">
    </div>

    <div id="drop-box-overlay-gmail" style=""> 

        <div id="drop-box-jsupload-gmail" >
            
        </div>

        <div id="qpa_tmp_qpa_file"> 


        </div>


    </div> 

    <div id="dndmedia_meta_box">


        <div id="dndmedia_status">

    <!--			<div id="upload-status-progressbar" style="display:none; float:left; width:30px; height: 30px;"><img src="<?php echo $this_plugin_url . '/images/loader.gif' ?>" /></div> -->

            <div style="clear:both;"></div>
        </div>

        <div id="dndmedia_files">
        </div>


        <div class="clearfix"></div>

        <div id="load_instrukcja2" class="dndmedia_inputarea">
            <div  class="dndmedia_information" > lub kliknij i wklej link (URL) </div>
            <button class="qpa-edit-buttons">
                <a href="javascript:void(0)" id="dndmedia_importurl">Wklej link (URL) do pliku </a>
            </button>
        </div>

        <div class="clearfix"></div>

        <div id="old_choosefile" class="dndmedia_old_input"> lub wybierz plik <br>
            <!--                   ten select daje nazwe wpisowi w $_FILES-->
            <input  id="qpa_file_select" class="dndmedia_information" type="file" name="qpa_file" >

            <input id="old_submit_qpa_file_button" name="old_submit_qpa_file_button"  type="submit" value="Wyślij plik" tabindex="40" style="display:none"/>
        </div> 	



    </div>


    <?php
}
