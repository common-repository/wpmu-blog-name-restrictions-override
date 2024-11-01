<?php

/******************************************************************************************************************
 
Plugin Name: Blog Name Restrictions Override 

Plugin URI:

Description: WordPress plugin for letting site admins allow users to bypass some of the built-in blog name rules

Version: 2.0

Author: Kevin Graeme & Deanna Schneider


Copyright:

    Copyright 2008 Board of Regents of the University of Wisconsin System
	Cooperative Extension Technology Services
	University of Wisconsin-Extension

            
*******************************************************************************************************************/

class cets_Blog_Name_Restrictions_Override {


function cets_blog_name_restrictions_override() {
	
	
	$defaults = array(
		'allowdashes'=>0,
		'minlength' => 4,
		'allowunderscores' => 0,
		'allownumeric' => 0,
		'allowupper' => 0
		);
	
	
	$validitychecks = get_site_option('cets_bnro_checks');
	if (!is_array($validitychecks)) add_site_option('cets_bnro_checks', $defaults);
	
	
	
	add_filter('wpmu_validate_blog_signup', array(&$this, 'validator'), 9, 1);
	add_action('wpmu_options', array(&$this, 'add_options'), 5, 0);
	add_action('update_wpmu_options', array(&$this, 'process_options'), 5, 0);
	
	}

function validator($result){
	
	
	$errors = $result['errors'];
	
	// we only want to filter if there is an error
	if (!is_object($errors)){
		return $result;
	}
	
	
	$checks = get_site_option('cets_bnro_checks');
	// create a new var to hold errors
	$newerrors = new WP_Error();

	// loop through the errors and look for the one we are concerned with
	foreach ($errors->errors as $key => $value) {
		// if the error is with the blog name, check to see which one
		if ($key == 'blogname'){
			foreach ($value as $subkey => $subvalue) {
				
				switch ($subvalue){
					case 'Only lowercase letters and numbers allowed':
						$allowedchars = '';
						if ($checks['allowdashes']== 1) $allowedchars .= '-';
						if ($checks['allowunderscores'] == 1) $allowedchars .= '_';
						if ($checks['allowupper'] == 1) $allowedchars .= 'A-Z';
						
						$allowed = '/[a-z0-9' . $allowedchars . ']+/';
						preg_match( $allowed, $result['blogname'], $maybe ); 
						if( $result['blogname'] != $maybe[0] ) {
							
							//still fails, so add an error to the object
							$newerrors->add('blogname', __("Only lowercase letters and numbers allowed"));
													
						}
						continue;
					//Prior to 3.0	
					case 'Blog name must be at least 4 characters':
						if( strlen( $result['blogname'] ) < $checks[minlength] && !is_site_admin() )
						$newerrors->add('blogname',  __("Blog name must be at least " . $checks[minlength] . " characters"));
						continue;
					// 3.0 Version
					case 'Site name must be at least 4 characters':
						if( strlen( $result['blogname'] ) < $checks[minlength] && !is_site_admin() )
						$newerrors->add('blogname',  __("Site name must be at least " . $checks[minlength] . " characters"));
						continue;
							
					// Prior to 3.0	
					case "Sorry, blog names may not contain the character '_'!": 
						if($checks['allowunderscores']!= 1) {
							$newerrors->add('blogname', __("Sorry, blog names may not contain the character '_'!"));
						}
						continue;
					// 3.0 version	
					case "Sorry, site names may not contain the character &#8220;_&#8221;!": 
						if($checks['allowunderscores']!= 1) {
							$newerrors->add('blogname', __("Sorry, site names may not contain the character '_'!"));
						}
						continue;
					// Prior to 3.0		
					case 'Sorry, blog names must have letters too!':
						if($checks['allownumeric'] != 1){
							$newerrors->add('blogname', __("Sorry, blog names must have letters too!"));
						}
						continue;
					// 3.0 version	
					case 'Sorry, site names must have letters too!':
						if($checks['allownumeric'] != 1){
							$newerrors->add('blogname', __("Sorry, site names must have letters too!"));
						}
						continue;		
					default:
						$newerrors->add('blogname', $subvalue);	
					
				}// end switch
				
				
				
				
				
		}
			
		}
		else {
			//Add all other errors into the error object, but they're in sub-arrays, so loop through to get the right stuff.
			foreach ($value as $subkey => $subvalue) {
				$newerrors->add($key, $subvalue);
			}
			
			
		}
		
		
		
		
		
		
	} 
	
	//unset the error object from the results & rest it with our new errors
	unset($result['errors']);
	$result['errors'] = $newerrors;
	
	return $result;
	}
	
	function add_options() {
		$checks = get_site_option('cets_bnro_checks');
		
		?>
		<h3>Blog Name Restrictions Settings</h3> 
		<div>Non-site admins are more restricted than site admins in the types of blog names they can create. These settings allow you to selectively override the built-in restrictions. Please be careful about what you are allowing and any ramifications this might have on your system.</div>
		<table class="form-table">
			<tbody>
			<tr valign="top"> 
				<th scope="row">Allow:</th> 
				<td>

					<label><input name="cets_bnro_allowdashes" id="cets_bnro_allowdashes"  value="1" type="checkbox" <?php if ($checks['allowdashes']== 1) echo 'checked="checked"'; ?>> Dashes</label> (Default: Not Allowed)
					<br>
					<label><input name="cets_bnro_allowunderscores" id="cets_bnro_allowunderscores"  value="1" type="checkbox" <?php if ($checks['allowunderscores']== 1) echo 'checked="checked"'; ?>> Underscores</label>  (Default: Not Allowed)
					<br>
					<label><input name="cets_bnro_allownumeric" id="cets_bnro_allownumeric"  value="1" type="checkbox" <?php if ($checks['allownumeric']== 1) echo 'checked="checked"'; ?>> All Numeric Names</label>  (Default: Not Allowed)
					<br>
					<label><input name="cets_bnro_allowupper" id="cets_bnro_allowupper"  value="1" type="checkbox" <?php if ($checks['allowupper']== 1) echo 'checked="checked"'; ?>> Upper Case Letters</label>  (Default: Not Allowed)
					<br>
				</td> 
			</tr> 
			<tr valign="top"> 
				<th scope="row">Minimum Length:</th> 
				<td>

					<input name="cets_bnro_minlength" style="width: 10%;" id="cets_bnro_minlenth" value="<?php echo $checks['minlength'];?>" size="10" type="text"><br/>
					(Default: 4)
				</td> 
			</tr> 
		</tbody></table>
	
	<?php
		
	}

	function process_options(){
		$defaults = array(
		'allowdashes'=>0,
		'minlength' => 4,
		'allowunderscores' => 0,
		'allownumeric' => 0,
		'allowupper' => 0
		);
	$defaults['allowdashes'] = ($_POST['cets_bnro_allowdashes'] == 1) ? 1 : 0;
	$defaults['minlength'] = (is_numeric($_POST['cets_bnro_minlength']) == true) ?  $_POST['cets_bnro_minlength'] : 4;
	$defaults['allowunderscores'] = ($_POST['cets_bnro_allowunderscores'] == 1) ? 1 : 0;
	$defaults['allownumeric'] = ($_POST['cets_bnro_allownumeric'] == 1) ? 1 : 0;
	$defaults['allowupper'] = ($_POST['cets_bnro_allowupper'] == 1) ? 1 : 0;
	
	update_site_option('cets_bnro_checks', $defaults);
	
	}



}// end class


add_action( 'plugins_loaded', create_function( '', 'global $cets_Blog_Name_Restrictions_Override; $cets_Blog_Name_Restrictions_Override = new cets_Blog_Name_Restrictions_Override();' ) );



?>
