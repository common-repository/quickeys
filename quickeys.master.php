<?php  
/* 
Plugin Name: WP Keyboard Navigation aka QuicKeys
Plugin URI: http://www.omniwp.com/plugins/quickeys-a-wordpress-plugin/ 
Description: Quickeyss lets you make your blog completely keyboard navigatable.
Version: 0.9
Author: Nimrod Tsabari / omniWP
Author URI: http://www.omniwp.com
*/  
/*  Copyright 2012 Nimrod Tsabari / omniWP  (email : yo@omniwp.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/?>
<?php

define('QUICKEYS_VER', '0.9');
define('QUICKEYS_DIR', plugin_dir_url( __FILE__ ));

/* Quickeys : Init */
/* -------------- */

function init_quickeys() {
	wp_register_style('quickeys-style', QUICKEYS_DIR . 'css/quickeys.css');
	wp_enqueue_style('quickeys-style');
	wp_register_script('quickeys-script', QUICKEYS_DIR . 'js/quickeys.min.js', array('jquery'));
	wp_enqueue_script('quickeys-script');
}

add_action('wp_enqueue_scripts', 'init_quickeys');

function quickeys_admin_load_scripts() {
	if(isset($_GET['page']) && $_GET['page']=='quickeys') {
		wp_register_style('quickeys_admin_css', QUICKEYS_DIR . '/admin/quickeys.admin.css', false, '1.0.0' );	    
		wp_register_script('quickeys_admin_js', QUICKEYS_DIR . '/admin/quickeys.admin.js', false, '1.0.0' );
        wp_enqueue_style('quickeys_admin_css');
        wp_enqueue_script('quickeys_admin_js');
	}
}

add_action('admin_enqueue_scripts', 'quickeys_admin_load_scripts');

/* Quickeys : Activation */
/* -------------------- */

define('QUICKEYS_NAME', 'QuicKeys');
define('QUICKEYS_SLUG', 'quickeys');

register_activation_hook(__file__,'omni_quickeys_admin_activate');
register_activation_hook(__file__,'quickeys_add_options');
add_action('admin_notices', 'omni_quickeys_admin_notices');	

function quickeys_add_options() {
	$k = get_option('quickeys_keys');
	$a = get_option('quickeys_alerts');
	$c = false;
	
	if ( !array_key_exists('next', $k ) ) { $k['next'] = '39'; $c = true; }
	if ( !array_key_exists('prev', $k ) ) { $k['prev'] = '37'; $c = true; }
	if ( !array_key_exists('home', $k ) ) { $k['home'] = '72'; $c = true; }
	if ( !array_key_exists('rand', $k ) ) { $k['rand'] = '82'; $c = true; }
	if ( !array_key_exists('next_in_cat', $k ) ) { $k['next_in_cat'] = '78'; $c = true; }
	if ( !array_key_exists('prev_in_cat', $k ) ) { $k['prev_in_cat'] = '80'; $c = true; }
	if ( !array_key_exists('pids', $k ) ) { $k['pids'] = '0'; $c = true; }
	if ( !array_key_exists('next_attachment', $k ) ) { $k['next_attachment'] = '88'; $c = true; }
	if ( !array_key_exists('prev_attachment', $k ) ) { $k['prev_attachment'] = '90'; $c = true; }
	
	if ( !array_key_exists('next_prev', $a ) ) { $a['next_prev'] = 'Nowhere to Go ...'; }
	
	update_option('quickeys_keys',$k);
	add_option('quickeys_in_page', false);
	update_option('quickeys_alerts', $a);
}

function omni_quickeys_admin_activate() {
	$reason = get_option('omni_plugin_reason');
	if ($reason == 'nothanks') { 
		update_option('omni_plugin_on_list',0);
	} else {		
		add_option('omni_plugin_on_list',0);
		add_option('omni_plugin_reason','');
	}
}

function omni_quickeys_admin_notices() {
	if ( get_option('omni_plugin_on_list') < 2 ){		
		echo "<div class='updated'><p>" . sprintf(__('<a href="%s">' . QUICKEYS_NAME . '</a> needs your attention.'), "options-general.php?page=" . QUICKEYS_SLUG). "</p></div>";
	}
} 

/*  Quickeys : Admin Part  */
/* --------------------- */
/* Inspired by Purwedi Kurniawan's SEO Searchterms Tagging 2 Pluging */

function quickeys_admin() {
	if (omni_quickeys_list_status()) include('admin/quickeys.admin.php'); 
}            

function quickeys_admin_init() {
	add_options_page("QuicKeys", "QuicKeys", 1, "quickeys", "quickeys_admin");
}

add_action('admin_menu', 'quickeys_admin_init');

function omni_quickeys_list_status() {
	$onlist = get_option('omni_plugin_on_list');
	$reason = get_option('omni_plugin_reason');
	if ( trim($_GET['onlist']) == 1 || $_GET['no'] == 1 ) {
		$onlist = 2;
		if ($_GET['onlist'] == 1) update_option('omni_plugin_reason','onlist');
		if ($_GET['no'] == 1) {
			 if ($reason != 'onlist') update_option('omni_plugin_reason','nothanks');
		}
		update_option('omni_plugin_on_list', $onlist);
	} 
	if ( ((trim($_GET['activate']) != '' && trim($_GET['from']) != '') || trim($_GET['activate_again']) != '') && $onlist != 2 ) { 
		update_option('omni_plugin_list_name', $_GET['name']);
		update_option('omni_plugin_list_email', $_GET['from']);
		$onlist = 1;
		update_option('omni_plugin_on_list', $onlist);
	}
	if ($onlist == '0') {
		omni_quickeys_register_form_1('quickeys_registration');
	} elseif ($onlist == '1') {
		$name = get_option('omni_plugin_list_name');
		$email = get_option('omni_plugin_list_email');
		omni_quickeys_do_list_form_2('quickeys_confirm',$name,$email);
	} elseif ($onlist == '2') {
		return true;
	}
}

function omni_quickeys_register_form_1($fname) {
	global $current_user;
	get_currentuserinfo();
	$name = $current_user->user_firstname;
	$email = $current_user->user_email;
?>
	<div class="register" style="width:50%; margin: 100px auto; border: 1px solid #BBB; padding: 20px;outline-offset: 2px;outline: 1px dashed #eee;box-shadow: 0 0 10px 2px #bbb;">
		<p class="box-title" style="margin: -20px; background: #489; padding: 20px; margin-bottom: 20px; border-bottom: 3px solid #267; color: #EEE; font-size: 30px; text-shadow: 1px 2px #267;">
			Please register the plugin...
		</p>
		<p>Registration is <strong style="font-size: 1.1em;">Free</strong> and only has to be done <strong style="font-size: 1.1em;">once</strong>. If you've register before or don't want to register, just click the "No Thank You!" button and you'll be redirected back to the Dashboard.</p>
		<p>In addition, you'll receive a a detailed tutorial on how to use the plugin and a complimentary subscription to our Email Newsletter which will give you a wealth of tips and advice on Blogging and Wordpress. Of course, you can unsubscribe anytime you want.</p>
		<p><?php omni_quickeys_registration_form($fname,$name,$email);?></p>
		<p style="background: #F8F8F8; border: 1px dotted #ddd; padding: 10px; border-radius: 5px; margin-top: 20px;"><strong>Disclaimer:</strong> Your contact information will be handled with the strictest of confidence and will never be sold or shared with anyone.</p>
	</div>	
<?php
}

function omni_quickeys_registration_form($fname,$uname,$uemail,$btn='Register',$hide=0, $activate_again='') {
	$wp_url = get_bloginfo('wpurl');
	$wp_url = (strpos($wp_url,'http://') === false) ? get_bloginfo('siteurl') : $wp_url;
	$thankyou_url = $wp_url.'/wp-admin/options-general.php?page='.$_GET['page'];
	$onlist_url   = $wp_url.'/wp-admin/options-general.php?page='.$_GET['page'].'&amp;onlist=1';
	$nothankyou_url   = $wp_url.'/wp-admin/options-general.php?page='.$_GET['page'].'&amp;no=1';
	?>
	
	<?php if ( $activate_again != 1 ) { ?>
	<script><!--
	function trim(str){ return str.replace(/(^\s+|\s+$)/g, ''); }
	function imo_validate_form() {
		var name = document.<?php echo $fname;?>.name;
		var email = document.<?php echo $fname;?>.from;
		var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		var err = ''
		if ( trim(name.value) == '' )
			err += '- Name Required\n';
		if ( reg.test(email.value) == false )
			err += '- Valid Email Required\n';
		if ( err != '' ) {
			alert(err);
			return false;
		}
		return true;
	}
	//-->
	</script>
	<?php } ?>
	<form name="<?php echo $fname;?>" method="post" action="http://www.aweber.com/scripts/addlead.pl" <?php if($activate_again!=1){;?>onsubmit="return imo_validate_form();"<?php }?> style="text-align:center;" >
		<input type="hidden" name="meta_web_form_id" value="1222167085" />
		<input type="hidden" name="listname" value="omniwp_plugins" />  
		<input type="hidden" name="redirect" value="<?php echo $thankyou_url;?>">
		<input type="hidden" name="meta_redirect_onlist" value="<?php echo $onlist_url;?>">
		<input type="hidden" name="meta_adtracking" value="omniwp_plugins_adtracking" />
		<input type="hidden" name="meta_message" value="1">
		<input type="hidden" name="meta_required" value="from,name">
		<input type="hidden" name="meta_forward_vars" value="1">	
		 <?php if ( $activate_again == 1 ) { ?> 	
			 <input type="hidden" name="activate_again" value="1">
		 <?php } ?>		 
		<?php if ( $hide == 1 ) { ?> 
			<input type="hidden" name="name" value="<?php echo $uname;?>">
			<input type="hidden" name="from" value="<?php echo $uemail;?>">
		<?php } else { ?>
			<p>Name: </td><td><input type="text" name="name" value="<?php echo $uname;?>" size="25" maxlength="150" />
			<br />Email: </td><td><input type="text" name="from" value="<?php echo $uemail;?>" size="25" maxlength="150" /></p>
		<?php } ?>
		<input class="button-primary" type="submit" name="activate" value="<?php echo $btn; ?>" style="font-size: 14px !important; padding: 5px 20px;" />
	</form>
    <form name="nothankyou" method="post" action="<?php echo $nothankyou_url;?>" style="text-align:center;">
	    <input class="button" type="submit" name="nothankyou" value="No Thank You!" />
    </form>
	<?php
}

function omni_quickeys_do_list_form_2($fname,$uname,$uemail) {
	$msg = 'You have not clicked on the confirmation link yet. A confirmation email has been sent to you again. Please check your email and click on the confirmation link to register the plugin.';
	if ( trim($_GET['activate_again']) != '' && $msg != '' ) {
		echo '<div id="message" class="updated fade"><p><strong>'.$msg.'</strong></p></div>';
	}
	?>
	<div class="register" style="width:50%; margin: 100px auto; border: 1px dotted #bbb; padding: 20px;">
		<p class="box-title" style="margin: -20px; background: #489; padding: 20px; margin-bottom: 20px; border-bottom: 3px solid #267; color: #EEE; font-size: 30px; text-shadow: 1px 2px #267;">Thank you...</p>
		<p>A confirmation email has just been sent to your email @ "<?php echo $uemail;?>". In order to register the plugin, check your email and click on the link in that email.</p>
		<p>Click on the button below to Verify and Activate the plugin.</p>
		<p><?php omni_quickeys_registration_form($fname.'_0',$uname,$uemail,'Verify and Activate',$hide=1,$activate_again=1);?></p>
		<p>Disclaimer: Your contact information will be handled with the strictest confidence and will never be sold or shared with third parties.</p>
	</div>	
	<?php
}


//* Handling Ajax */
function quickeys_ajax_add_new_pid_field() {
	$idtype = $_POST['idtype'];
	$pids = $_POST['pids'];
	$pkc = $_POST['pkc'];
	$ppid = $_POST['ppid'];

	$html = '';

	switch ($idtype) {
		case 'page':
			$id_name = "key_page_id_" . $pids;
			$pid_name = "key_page_key_" . $pids;
			$code_name = "key_page_code_" . $pids;
		
			$pids++;
		
			$html .= '<tr><td class="left-col quickeys-blue-td"><label for="key_prev">Page :</label>';
			$html .= '<select name="' . $id_name . '" id="' . $id_name . '">';
			
			$query = new WP_Query(array('post_type' => 'page', 'post_status' => 'publish'));
				while ( $query->have_posts() ) : $query->the_post();
				
				$html .= '<option value="' .  get_the_ID() . '" ';
				
				if ($ppid == get_the_ID()) $html .= 'selected="selected"';
				$html .= '>' . get_the_title() . '</option>';
			endwhile;			
			
			wp_reset_postdata();
		
			$html .= '</select><span class="quickeys-remove-button remove_tr">remove</span></td><td class="quickeys-blue-td">
								<input id="' . $pid_name . '" name="' . $pid_name . '" value="" type="text" class="quickeys-record-key" />
								<input id="' . $code_name . '" name="' . $code_name . '" value="' . $pkc . '" type="hidden" class="quickeys-keep-key" />
							</td>
						</tr>';
			break;
		
		case 'post':
			$id_name = "key_post_id_" . $pids;
			$pid_name = "key_post_key_" . $pids;
			$code_name = "key_post_code_" . $pids;
			
			$p_title = get_the_title($ppid);
			
			if ($p_title === '') {
				$p_title = "Couldn't find post :/";
			} else {
				$p_title = '(' .substr($p_title,0,40) . ' ...)';
			}
			
			$pids++;
			
			$html .= '<tr><td class="left-col quickeys-red-td"><label for="key_post_id quickeys-red-td">Post :</label>';
			$html .= '<input id="' . $id_name . '" name="' . $id_name . '" value="' . $ppid . '" type="text" class="quickeys-text-field" /><span>' . $p_title . '</span><span class="quickeys-remove-button remove_tr">remove</span>
					</td>
					<td class="quickeys-red-td">
						<input id="' . $pid_name . '" name="' . $pid_name . '" value="" type="text" class="quickeys-record-key" />
						<input id="' . $code_name . '" name="' . $code_name . '" value="' . $pkc . '" type="hidden" class="quickeys-keep-key" />
					</td>
				</tr>';
		break;
		case 'cat':
			$id_name = "key_cat_id_" . $pids;
			$pid_name = "key_cat_key_" . $pids;
			$code_name = "key_cat_code_" . $pids;
		
			$pids++;
		
			$html .= '<tr><td class="left-col quickeys-green-td"><label for="key_prev">Category :</label>';
			$html .= '<select name="' . $id_name . '" id="' . $id_name . '">';
			
			$cats = get_categories(); 
			foreach ($cats as $cat) {
				$cid = $cat->cat_ID;
				$cname = $cat->name;
				$html .= '<option value="' .  $cid . '" ';
				
				if ($ppid == $cid) $html .= 'selected="selected"';
				$html .= '>' . $cname . '</option>';
			}			
	
			$html .= '</select><span class="quickeys-remove-button remove_tr">remove</span></td><td class="quickeys-green-td">
								<input id="' . $pid_name . '" name="' . $pid_name . '" value="" type="text" class="quickeys-record-key" />
								<input id="' . $code_name . '" name="' . $code_name . '" value="' . $pkc . '" type="hidden" class="quickeys-keep-key" />
							</td>
						</tr>';
			break;
			
		default:
			
			break;
	}


	die($html);
}

add_action('wp_ajax_quickeys_ajax_add_new_pid_field', 'quickeys_ajax_add_new_pid_field');


// Next Post URL
function quickeys_get_next_url() {
	$next = get_adjacent_post(false, '', false);
	$next_url = '';
	if ( $next ) $next_url = get_permalink($next);
	return $next_url;	
}
// Prev Post URL
function quickeys_get_prev_url() {
	$prev = get_adjacent_post(false, '', true);
	$prev_url = '';
	if ( $prev ) $prev_url = get_permalink($prev);
	return $prev_url;	
}
// Next Post URL in Category
function quickeys_get_next_in_cat_url() {
	$next = get_adjacent_post(true, '', false);
	$next_url = '';
	if ( $next ) $next_url = get_permalink($next);
	return $next_url;	
}
// Prev Post URL in Category
function quickeys_get_prev_in_cat_url() {
	$prev = get_adjacent_post(true, '', true);
	$prev_url = '';
	if ( $prev ) $prev_url = get_permalink($prev);
	return $prev_url;	
}
// Random Post URL
function quickeys_get_rand_url() {
	$args = array( 'numberposts' => 1, 'orderby' => 'rand' );
	$rand_post = get_posts( $args );
	foreach( $rand_post as $post ) :
		$rand_url = get_permalink($post->ID);
	endforeach;
	return $rand_url;	
}
function quickeys_get_home_url() {
	return home_url();	
}
// Get post/page url by ID
function quickeys_get_url_by_ID($pid) {
	return get_permalink($pid);
}
function quickeys_get_cat_url_by_ID($cid) {
	return get_category_link($cid);
}
function quickeys_get_next_attachment_url() {
	global $post; 
	
	$attachments = array_values(get_children( array('post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID') ));

	foreach ($attachments as $k => $attachment)
  		if ($attachment->ID == $post->ID)
    break;
 
	$next_url =  isset($attachments[$k+1]) ? get_permalink($attachments[$k+1]->ID) : '';
	
	return $next_url;
}
function quickeys_get_prev_attachment_url() {
	global $post; 
	
	$attachments = array_values(get_children( array('post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID') ));

	foreach ($attachments as $k => $attachment)
  		if ($attachment->ID == $post->ID)
    break;
 
 	if ($k>0) {
		$prev_url =  isset($attachments[$k-1]) ? get_permalink($attachments[$k-1]->ID) : '';
	}
	
	return $prev_url;
}

function quickeys_head() {
	if (!is_admin()) {
		$enable = TRUE;
		$encap = get_option('quickeys_encap');
		$encaps = explode(',',$encap);
		foreach ($encaps as $enpid) {
			if (get_the_ID() == trim($enpid)) $enable = FALSE; 
			if ((is_home() || is_front_page()) && (trim($enpid) == 'home')) $enable = FALSE;
		}
		
		if ($enable) {
			$keys = get_option('quickeys_keys');
			$alerts = get_option('quickeys_alerts');
			$in_page = get_option('quickeys_in_page');
			$keys_string = '';
			$urls_string = '';
			$is_post = (is_single() ? 1 : 0);
			$is_page = (is_page() ? 1 : 0);
			$is_attachment = (is_attachment() ? 1 : 0);
			
			foreach ($keys as $key=>$val) {
				if ($key != 'pids') {
					$keys_string .= '||'; 
					if ((strpos($key, 'page_key') !== false) || (strpos($key, 'post_key') !== false) || (strpos($key, 'cat_key') !== false)) {
						$keys_string .= ($val[1] !== '32' ? $val[1] : 'none');
					} else {
						$keys_string .= ($val !== '32' ? $val : 'none');
					}
					$urls_string .= '||';
					if ((strpos($key, 'page_key') !== false) || (strpos($key, 'post_key') !== false)) {
						$url_temp = quickeys_get_url_by_ID($val[0]);
					} else if (strpos($key, 'cat_key') !== false) {
						$url_temp = quickeys_get_cat_url_by_ID($val[0]);
					} else {
						$url_temp = call_user_func(quickeys_get_ . $key . _url); 
					}
					$urls_string .= ($url_temp == '') ? 'none' : $url_temp;
				}
			}
	
			$keys_string = substr($keys_string, 2);
			$urls_string = substr($urls_string, 2);
			
			$html = '';
			$html .= '<script type="text/javascript">';
			$html .= 'quickeys_init("' . $keys_string . '","' . $urls_string . '",' . $is_post . ',' . $is_page . ',' . $is_attachment . ',' . $in_page . ',"' . $alerts['next_prev'] . '");';
			$html .= '</script>';
			
			echo $html;
		}
	}
}

add_action('wp_footer', 'quickeys_head');
?>