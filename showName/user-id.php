<?php

/**
 * Plugin Name: User ID
 * Plugin URI: 
 * Description: This Plugin shows the USER ID number using a shortcode.
 * Version: 3.0
 * Author: 
 * Author URI: 
 * License: 
 * Text Domain: 
 */
defined('ABSPATH') or die('What are you looking for?');
function show()
{
	ob_start();
	$current_user = wp_get_current_user();
	$current_roles = join(', ', $current_user->roles);
	//$data= $current_user->user_login;
	if ($current_user->ID == 0) { //checking for the existing user 

		echo "<span class='usr-id'>No logged user</span>";
	} else {
?>
		<div style="color:blue; font-size:40px">
			<span class="usr-id"><?php
									echo $current_user->user_login;
									?>
				<br>
				<?php
				echo $current_roles;
				?>
			</span>
		</div>
	<?php
	}
	//return $data;
	return ob_get_clean();
}

function login_css()
{
	?>
	<style type="text/css">
		#loginform #wp-submit {
			background-color: black;
			/* Replace 'your_color_here' with the desired color code or name */
			color: #fff;
		}
	</style>
	<script>
		
		// var inputElement = document.createElement("input");
		// inputElement.type ='text';
		// inputElement.id = 'myInput';
		// inputElement.name ='myInput';
		// inputElement.placeholder ='Enter text...';

		// var customDiv = document.getElementById('#custom_input');

		// // Check if the div with the ID "user" exists
		// if (customDiv) {
			
		// 	customDiv.appendChild(inputElement);
		// } else {
		// 	console.error('Div with the ID '+ +'not found.');
		// }
	</script>
<?php
}
add_shortcode('show_me_id', 'show');
add_action('login_head', 'login_css');
