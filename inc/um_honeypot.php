<?php
/* 
 * Honeypot custom validierung für Ultimate Member 
 * (https://wordpress.org/plugins/ultimate-member/)
 * Basierend auf: https://wordpress.org/support/topic/how-to-create-quiz/
 * In Ultimate Member hat das entsprechende Element den 'honigtopf' Meta Schlüssel.
 * Wird als barrierefreie Alternative zu Captchas eingesetzt, in den meisten Fällen völlig ausreichend.
 * (Bis auf koordinierte Bot Attacken)
 * 
 * 100% fertig
 * 
 */

function um_custom_validate_customfield( $args ) {
if ( isset( $args['honigtopf'] ) ) {
$mystring = "lastenrad";
if (  strcasecmp($args['honigtopf'],$mystring) != 0) {
$message = sprintf( __( 'Falscher Validierungswert', 'ultimate-member' ), $mystring );
UM()->form()->add_error( 'honigtopf', $message );
}
}
}
add_action( 'um_submit_form_errors_hook__registration', 'um_custom_validate_customfield', 99 );

/* Noch hier reingepfercht: https://www.champ.ninja/2020/05/show-passwords-feature-in-ultimate-member-forms/
 * Gibt einen schönen "show password" Button
*/

add_filter("um_confirm_user_password_form_edit_field","um_user_password_form_edit_field", 10, 2 );
add_filter("um_user_password_form_edit_field","um_user_password_form_edit_field", 10, 2 );
function um_user_password_form_edit_field( $output, $set_mode ){
    
    ob_start();
     ?>
    <div id='um-field-show-passwords-<?php echo $set_mode;?>' style='text-align:right;display:block;'>
    	<i class='um-faicon-eye-slash'></i>
    	<a href='#'><?php _e("Passwort anzeigen","ultimate-member"); ?></a>
    </div>
    <script type='text/javascript'>
	    jQuery('div[id="um-field-show-passwords-<?php echo $set_mode;?>"] a').click(function(){ 
		 
            var $parent = jQuery(this).parent("div"); 
            var $form = jQuery(".um-<?php echo $set_mode;?> .um-form");

		    $parent.find("i").toggleClass(function() {
		    	if ( jQuery( this ).hasClass( "um-faicon-eye-slash" ) ) {
	                $parent.find("a").text('<?php _e("Passwort verstecken","ultimate-member"); ?>');
		    		jQuery( this ).removeClass( "um-faicon-eye-slash" )
		    		$form.find(".um-field-password").find("input[type=password]").attr("type","text");
		    	   return "um-faicon-eye";
			    }
				 
				jQuery( this ).removeClass( "um-faicon-eye" );
				$parent.find("a").text('<?php _e("Passwort anzeigen","ultimate-member"); ?>');
			    $form.find(".um-field-password").find("input[type=text]").attr("type","password");
			  
                return "um-faicon-eye-slash";
			});

		    return false; 

		});
	</script>
    <?php 
	return $output.ob_get_clean();

}
?>
