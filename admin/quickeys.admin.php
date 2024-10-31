<?php
/* QuicKeys Admin Page
 * -----------------
 * Date : 23/10/2012
 */


define(QUICKEYS_DIR, plugin_dir_url(__FILE__));

if (!empty( $_POST) && check_admin_referer('quickeys_update_options', 'quickeys_update_options_nonce')) {

	$keys['next'] = $_POST['key_next_code'];
	$keys['prev'] = $_POST['key_prev_code'];
	$keys['home'] = $_POST['key_home_code'];
	$keys['rand'] = $_POST['key_rand_code'];
	$keys['next_attachment'] = $_POST['key_next_attachment_code'];
	$keys['prev_attachment'] = $_POST['key_prev_attachment_code'];
	$keys['next_in_cat'] = $_POST['key_next_in_cat_code'];
	$keys['prev_in_cat'] = $_POST['key_prev_in_cat_code'];
	$keys['pids'] = ($_POST['quickeys_pids'] === '' ? $_POST['quickeys_pids_old'] : $_POST['quickeys_pids']);
	$is_in_page = (!empty($_POST['quickeys_in_page']) ? 1 : 0);
	
	$pids = $keys['pids'];
	for ($i=0; $i < $pids ; $i++) { 
		if (!empty($_POST['key_page_id_' . $i])) {
			$keys['page_key_' . $i][0] = $_POST['key_page_id_' . $i];
			$keys['page_key_' . $i][1] = $_POST['key_page_code_' . $i];
		}
		if (!empty($_POST['key_post_id_' . $i])) {
			$keys['post_key_' . $i][0] = $_POST['key_post_id_' . $i];
			$keys['post_key_' . $i][1] = $_POST['key_post_code_' . $i];
		}
		if (!empty($_POST['key_cat_id_' . $i])) {
			$keys['cat_key_' . $i][0] = $_POST['key_cat_id_' . $i];
			$keys['cat_key_' . $i][1] = $_POST['key_cat_code_' . $i];
		}
	}
	
	$alerts['next_prev'] = trim($_POST['quickeys_alert_next_prev']);
	$encap = trim($_POST['quickeys_encap_pids']);
	
	delete_option('quickeys_keys', $keys);
	update_option('quickeys_keys', $keys);
	delete_option('quickeys_alerts', $alerts);
	update_option('quickeys_alerts', $alerts);
	delete_option('quickeys_encap', $encap);
	update_option('quickeys_encap', $encap);
	

	update_option('quickeys_in_page',$is_in_page);

	echo '<div class="updated fade"><p><strong>' . __( 'Settings Saved.', 'quickeys' ) . "</strong></p></div>\n";
}

$keys = get_option('quickeys_keys');
$alerts = get_option('quickeys_alerts');
$encap = get_option('quickeys_encap');
$in_page = get_option('quickeys_in_page');
$in_page = (($in_page == 1) ? true : false);
?>
<div id="quickeys-admin" class="clearfix">
	<p class="page_title">QuicKeys | <span>Make Your Blog Keyboard Navigatable!</span></p>
	<div id="quickeys-content" class="left">
		<p>Setting it is easy :</p>
		<ul> 
			<li>Gray background means Core QuicKeys, They cannot be deleted, but can be changed. They can be disabled tho, by hitting the spacebar in the Key Recorder.</li>
			<li>Other backgrounds (right side) means you can delete and add as many as you want.</li>
			<li>All Hotkeys are listed on under Blog's Keymap. Once you finished adding/changing your keys, don't forget to <strong>Save Keymap!</strong></li>
			<li>QuicKeys is hooked to wp_footer(), so make sure you theme calls for this function!</li>
		</ul>
		<p class="info-box">You can use the following keys : a-z, 0-9 & arrow keys</p> 
		<p>An extended tutorial (with examples) can be found <a href="http://omniwp.com/plugins/quickeys-a-wordpress-plugin/" target="_blank">here</a>. If you miss a feature, feel free to drop us a line, we love make our plugins better for you.</p> 
	</div>
	<div id="quickeys-sidebar" class="right">
	</div>	
<div id="quickeys-left" class="left">
	<form method="post" action="<?php echo get_bloginfo( 'wpurl' ).'/wp-admin/options-general.php?page=quickeys' ?>">
		<table class="quickeys_options_table quickeys-gray">
			<thead>
				<tr>
					<td colspan="2">
						<h2>Blog's Keymap</h2>
						<p>Keys in Gray are core and cannot be deleted, you can change them by typing the desired key in the Key Recorder field.</p>
						<div class="quickeys_checkbox">
							<input type="checkbox" name="quickeys_in_page" value="1" <?php checked($in_page); ?> /> Apply Next/Prev Post Keys to Pages as well
						</div>
						<div class="quickeys_checkbox">
							<span><strong>Alert Message :</strong> (Leave empty to disable)</span>
							<input type="text" name="quickeys_alert_next_prev" id="quickeys_alert_next_prev" class="quickeys_core_text" value="<?php echo $alerts['next_prev']; ?>" />
							<span>Alert messages shows up when there is nothing to show (eg, reaching the last post).</span>
						</div>
						<div class="quickeys_checkbox">
							<span><strong>Disable QuicKeys in :</strong> (Leave empty to enable site-wide)</span>
							<input type="text" name="quickeys_encap_pids" id="quickeys_encap_pids" class="quickeys_core_text" value="<?php echo $encap; ?>" />
							<span>Enter the Post/Page IDs you want to disable QuicKeys in ('home' for Home/Front Page, comma seperated).</span>
						</div>
					</td>
				</tr>
			</thead>
			<tbody id="quickeys_keymap">
				<tr class="keymap_head">
					<td class="left-col">
						<h3>Action</h3>
					</td>
					<td>
						<h4>Key / Recorder</h4>
					</td>
				</tr>
				
				<tr>
					<td class="left-col">
						<label for="key_next">Next Post (Chronological)</label>
					</td>
					<td>
						<input id="key_next" name="key_next" value="" type="text" class="quickeys-record-key" autocomplete="off" />
						<input id="key_next_code" name="key_next_code" value="<?php echo $keys['next']; ?>" type="hidden" class="quickeys-keep-key" />
					</td>
				</tr>
		
				<tr>
					<td class="left-col">
						<label for="key_prev">Prev Post (Chronological)</label>
					</td>
					<td>
						<input id="key_prev" name="key_prev" value="" type="text" class="quickeys-record-key" autocomplete="off" />
						<input id="key_prev_code" name="key_prev_code" value="<?php echo $keys['prev']; ?>" type="hidden" class="quickeys-keep-key" />
					</td>
				</tr>
		
				<tr>
					<td class="left-col">
						<label for="key_prev">Back Home</label>
					</td>
					<td>
						<input id="key_home" name="key_home" value="" type="text" class="quickeys-record-key" autocomplete="off" />
						<input id="key_home_code" name="key_home_code" value="<?php echo $keys['home']; ?>" type="hidden" class="quickeys-keep-key" />
					</td>
				</tr>

				<tr>
					<td class="left-col">
						<label for="key_prev">Random Post</label>
					</td>
					<td>
						<input id="key_rand" name="key_rand" value="" type="text" class="quickeys-record-key" autocomplete="off" />
						<input id="key_rand_code" name="key_rand_code" value="<?php echo $keys['rand']; ?>" type="hidden" class="quickeys-keep-key" />
					</td>
				</tr>
		
				<tr>
					<td class="left-col">
						<label for="key_prev">Next Post in Same Category</label>
					</td>
					<td>
						<input id="key_next_in_cat" name="key_next_in_cat" value="" type="text" class="quickeys-record-key" autocomplete="off" />
						<input id="key_next_in_cat_code" name="key_next_in_cat_code" value="<?php echo $keys['next_in_cat']; ?>" type="hidden" class="quickeys-keep-key" />
					</td>
				</tr>
		
				<tr>
					<td class="left-col">
						<label for="key_prev">Prev Post in Same Category</label>
					</td>
					<td>
						<input id="key_prev_in_cat" name="key_prev_in_cat" value="" type="text" class="quickeys-record-key" autocomplete="off" />
						<input id="key_prev_in_cat_code" name="key_prev_in_cat_code" value="<?php echo $keys['prev_in_cat']; ?>" type="hidden" class="quickeys-keep-key" />
					</td>
				</tr>
				
				<tr>
					<td class="left-col">
						<label for="key_prev">Next Attachment</label>
					</td>
					<td>
						<input id="key_next_attachment" name="key_next_attachment" value="" type="text" class="quickeys-record-key" autocomplete="off" />
						<input id="key_next_attachment_code" name="key_next_attachment_code" value="<?php echo $keys['next_attachment']; ?>" type="hidden" class="quickeys-keep-key" />
					</td>
				</tr>

				<tr>
					<td class="left-col">
						<label for="key_prev">Prev Attachment</label>
					</td>
					<td>
						<input id="key_prev_attachment" name="key_prev_attachment" value="" type="text" class="quickeys-record-key" autocomplete="off" />
						<input id="key_prev_attachment_code" name="key_prev_attachment_code" value="<?php echo $keys['prev_attachment']; ?>" type="hidden" class="quickeys-keep-key" />
					</td>
				</tr>

				<?php 
				$ppids = $keys['pids'];
				for ($i=0; $i < $ppids ; $i++) {
					if (array_key_exists('page_key_' . $i, $keys)) {
					?>
						<tr>
							<td class="left-col quickeys-blue-td">
								<label for="key_prev">Page</label>
								<select name="key_page_id_<?php echo $i; ?>" id="key_page_id_<?php echo $i; ?>">
								<?php	
									$query = new WP_Query(array('post_type' => 'page', 'post_status' => 'publish'));
									while ( $query->have_posts() ) : $query->the_post();
								?>
									<option value="<?php echo get_the_ID(); ?>" <?php if ($keys['page_key_' . $i][0] == get_the_ID()) echo 'selected="selected"'; ?>><?php echo get_the_title(); ?></option>
								<?php
									endwhile;			
									wp_reset_postdata();
								?>
								</select>
								<span class="quickeys-remove-button remove_tr">remove</span>
							</td>
							<td class="quickeys-blue-td">
								<input id="key_page_key_<?php echo $i; ?>" name="key_page_key_<?php echo $i; ?>" value="" type="text" class="quickeys-record-key" />
								<input id="key_page_code_<?php echo $i; ?>" name="key_page_code_<?php echo $i; ?>" value="<?php echo $keys['page_key_' . $i][1]; ?>" type="hidden" class="quickeys-keep-key" />
							</td>
						</tr>
				<?php
					}
					if (array_key_exists('post_key_' . $i, $keys)) {
					?>
						<tr>
							<td class="left-col quickeys-red-td">
								<label for="key_post_id">Post :</label>
								<input id="key_post_id_<?php echo $i; ?>" name="key_post_id_<?php echo $i; ?>" value="<?php echo $keys['post_key_' . $i][0]; ?>" type="text" class="quickeys-text-field" />
								<span><?php echo get_the_title($keys['post_key_'.$i][0]); ?></span>
								<span class="quickeys-remove-button remove_tr">remove</span>
							</td>
							<td class="quickeys-red-td">
								<input id="key_post_key_<?php echo $i; ?>" name="key_post_key_<?php echo $i; ?>" value="" type="text" class="quickeys-record-key" />
								<input id="key_post_code_<?php echo $i; ?>" name="key_post_code_<?php echo $i; ?>" value="<?php echo $keys['post_key_'.$i][1]; ?>" type="hidden" class="quickeys-keep-key" />
							</td>
						</tr>
					<?php
					}
					if (array_key_exists('cat_key_' . $i, $keys)) {
					?>
						<tr>
							<td class="left-col quickeys-green-td">
								<label for="key_prev">Category :</label>
								<select name="key_cat_id_<?php echo $i; ?>" id="key_cat_id_<?php echo $i; ?>">
								<?php	
									$cats = get_categories(); 
									foreach ($cats as $cat) {
										$cid = $cat->cat_ID;
										$cname = $cat->name;
									?>
									<option value="<?php echo $cid ?>" <?php if ($keys['cat_key_' . $i][0] == $cid) echo 'selected="selected"'; ?>><?php echo $cname; ?></option>
								<?php
									}
								?>
								</select>
								<span class="quickeys-remove-button remove_tr">remove</span>
							</td>
							<td class="quickeys-green-td">
								<input id="key_cat_key_<?php echo $i; ?>" name="key_cat_key_<?php echo $i; ?>" value="" type="text" class="quickeys-record-key" />
								<input id="key_cat_code_<?php echo $i; ?>" name="key_cat_code_<?php echo $i; ?>" value="<?php echo $keys['cat_key_' . $i][1]; ?>" type="hidden" class="quickeys-keep-key" />
							</td>
						</tr>
				<?php
					}
				}
				?>
			</tbody>
		</table>		
		<table class="quickeys_options_table quickeys_submit_section quickeys-gray">
			<tbody>
				<tr id="quickeys_error_tr">
					<td colspan="2">
						<div id="quickeys_error" class="error"></div>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2">
						<?php wp_nonce_field('quickeys_update_options', 'quickeys_update_options_nonce'); ?>
						<input name="quickeys_pids_old" id="quickeys_pids_old" type="hidden" value="<?php echo $keys['pids']; ?>">
						<input name="quickeys_pids" id="quickeys_pids" type="hidden" value="<?php echo $keys['pids']; ?>">
						<input id="quickeys_submit_keymap" type="submit" name="Submit" class="button-primary" value="Save Keymap"/>
					</td>
				</tr>
			</tfoot>
		</table>
	</form>
	<div id="quickeys-ajax-box"></div>
</div>
<div id="quickeys-right" class="left">
		<table class="page_id_row quickeys-blue">
			<thead>
				<tr>
					<td colspan="2">
						<h2>Specific Page</h2>
						<p>Assign a key to a specific page. Choose from the drop-down then head to the Key Recorder (right cell in this table) and type your key!</p>
					</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="left-col">
						<label for="key_prev">Specific Page</label>
						<select name="key_page_id" id="key_page_id">
							<?php 
								$query = new WP_Query(array('post_type' => 'page', 'post_status' => 'publish'));
									while ( $query->have_posts() ) : $query->the_post();
								?>
									<option value="<?php echo get_the_ID(); ?>"><?php echo get_the_title(); ?></option>
								<?php
									endwhile;			
								?>
							<?php wp_reset_postdata(); ?>
						</select>
					</td>
					<td>
						<input id="key_page_key" name="key_page_key" value="" type="text" class="quickeys-record-key-temp" />
						<input id="key_page_code" name="key_page_code" value="" type="hidden" class="quickeys-keep-key-temp" />
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr id="add_page_id_row">
					<td colspan="2">
						<input name="quickeys_type_id" class="quickeys_type_id" type="hidden" value="page">
						<input type="button" value="Add Key" class="quickeys-add-pid-button">
					</td>
				</tr>
			</tfoot>
		</table>
		<table class="page_id_row quickeys-red">
			<thead>
				<tr>
					<td colspan="2">
						<h2>Specific Post</h2>
						<p>Assign a key to a specific post. Type the Post's ID then head to the Key Recorder (right cell in this table) and type your key!</p>
					</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="left-col">
						<label for="key_post_id">Type the Post's ID :</label>
						<input id="key_post_id" name="key_post_id" value="" type="text" class="quickeys-text-field" />
					</td>
					<td>
						<input id="key_post_key" name="key_post_key" value="" type="text" class="quickeys-record-key-temp" />
						<input id="key_post_code" name="key_post_code" value="" type="hidden" class="quickeys-keep-key-temp" />
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr id="add_page_id_row">
					<td colspan="2">
						<input name="quickeys_type_id" class="quickeys_type_id" type="hidden" value="post">
						<input type="button" value="Add Key" class="quickeys-add-pid-button">
					</td>
				</tr>
			</tfoot>
		</table>
		<table class="page_id_row quickeys-green">
			<thead>
				<tr>
					<td colspan="2">
						<h2>Specific Category</h2>
						<p>Assign a key to a specific category. Choose from the drop-down then head to the Key Recorder (right cell in this table) and type your key!</p>
					</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="left-col">
						<label for="key_prev">Specific Category</label>
						<select name="key_cat_id" id="key_cat_id">
							<?php 
								$cats = get_categories(); 
								foreach ($cats as $cat) {
									$cid = $cat->cat_ID;
									$cname = $cat->name;
								?>
									<option value="<?php echo $cid ?>"><?php echo $cname ?></option>
								<?php
								}
								?>
						</select>
					</td>
					<td>
						<input id="key_cat_key" name="key_cat_key" value="" type="text" class="quickeys-record-key-temp" />
						<input id="key_cat_code" name="key_cat_code" value="" type="hidden" class="quickeys-keep-key-temp" />
					</td>
				</tr>
			</tbody>
			<tfoot>			
				<tr id="add_page_id_row">
					<td colspan="2">
						<input name="quickeys_type_id" class="quickeys_type_id" type="hidden" value="cat">
						<input type="button" value="Add Key" class="quickeys-add-pid-button">
					</td>
				</tr>
			</tfoot>
		</table>
</div>
</div>
<?php
?>