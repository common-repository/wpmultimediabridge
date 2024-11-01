<?php
class Wpmb_Admin
{
	public static function admin_css_js()
	{
		// Loading custom script and style.
		wp_enqueue_script('wpmb-admin-script', WPMB_RESOURCES_URL . '/js/wpmb-admin-common.js', array('jquery'));
		wp_enqueue_style('wpmb-admin-style', WPMB_RESOURCES_URL . '/css/wpmb-admin-style.css');

                wp_localize_script('wpmb-admin-script', 'WpmbAjax', array('ajaxurl' => admin_url('admin-ajax.php' )));
	}
	
	public static function admin_menu()
	{
		if (is_admin()) {
			add_options_page( __( 'WpMultimediaBridge', 'wpmb' ), 'WpMultimediaBridge', 9, __FILE__, array(__CLASS__, 'general_settings') );
		}
	}
	
	public static function general_settings()
	{
		global $wpmb_options;
		
		if (isset($_POST['wpmb-submit'])) {
			$wpmb_options['wpmb_filter_content'] = isset($_POST['wpmb-filter-content']) ? "1" : "0";
			$wpmb_options['wpmb_filter_content_make_clickable'] = isset($_POST['wpmb-filter-content-make-clickable']) ? "1" : "0";
			
			$wpmb_options['wpmb_filter_comment'] = isset($_POST['wpmb-filter-comment']) ? "1" : "0";
			$wpmb_options['wpmb_filter_comment_make_clickable'] = isset($_POST['wpmb-filter-comment-make-clickable']) ? "1" : "0";
			
			$wpmb_options['wpmb_canvas_width'] = $_POST['wpmb-canvas-width'];
			$wpmb_options['wpmb_description_size'] = $_POST['wpmb-description-size'];
			
			$wpmb_options['wpmb_disable_post_revision'] = isset($_POST['wpmb-disable-post-revision']) ? "1" : "0";
			$wpmb_options['wpmb_disable_post_autosave'] = isset($_POST['wpmb-disable-post-autosave']) ? "1" : "0";
			$wpmb_options['wpmb_hide_imatrice_url'] = isset($_POST['wpmb-hide-imatrice-url']) ? "1" : "0";
			
			update_option('wpmb_options', $wpmb_options);
		}
		?>
		<form name="wpmb_settings" id="wpmb_settings" method="post" action="<?php $_SERVER['PHP_SELF'] ?>">
			<div class="wrap">
				<?php if (isset($_POST['wpmb-submit'])) : ?>
					<div id="message" class="updated fade">
						<p><?php _e('Settings successfully saved', 'wpmb'); ?></p>
					</div>
				<?php endif; ?>
				<h2><?php _e('WpMultimediaBridge Settings', 'wpmb'); ?></h2>
				<a href="#supporting"><?php _e('Click here for the list of supported sources', 'wpmb'); ?></a>
				<h3><?php _e('Versions', 'wpmb'); ?></h3>
				<p>
					<?php _e('Plugin:', 'wpmb'); ?>&nbsp;<?php echo Wpmb::WPMB_VERSION; ?>
					<br />
					<?php _e('Database:', 'wpmb'); ?>&nbsp;<?php echo Wpmb::WPMB_DB_VERSION; ?>
				</p>
				<p style="color:green;font-weight:bold;">
					<?php _e('WpMultimediaBridge will be soon completely redesigned with new themes (3), new features and it will be renamed to WpVib.', 'wpmb'); ?>
				</p>
				<p>
					<?php _e('If you discover a bug with <i>WpMultimediaBridge</i>, send us an email to <strong>support@imatrice.com</strong>. Thank you!', 'wpmb'); ?>
				</p>
				
				<h3><?php _e('Filters', 'wpmb'); ?></h3>
				<i><?php _e('Those filters will convert any* urls** into a Facebook-like element.', 'wpmb'); ?></i>
				<p>
					<label for="wpmb-filter-content">
						<input type="checkbox" value="1" name="wpmb-filter-content" id="wpmb-filter-content" 
							<?php echo (($wpmb_options['wpmb_filter_content']) ? 'checked="checked"' : ''); ?> style="margin:2px;" />
						<?php _e('Filter multimedia urls in posts content', 'wpmb'); ?>
					</label>
				</p>
				<p style="color:#999999;">
					<label for="wpmb-filter-comment">
						<input type="checkbox" value="1" name="wpmb-filter-comment" id="wpmb-filter-comment" disabled 
							<?php echo (($wpmb_options['wpmb_filter_comment']) ? 'checked="checked"' : ''); ?> style="margin:2px;" />
						<?php _e('Filter multimedia urls in comments content <i>(soon)</i>', 'wpmb'); ?>
					</label>
				</p>
				<i>
					<?php // echo sprintf(__('* Media supported by this plugin. <a href="%s">Click here</a> to see the list of supported media sources.', 'wpmb'), get_option('home').'/wp-admin/admin.php?page=wpmb-supported'); ?>
					<?php echo sprintf(__('* Multimedia urls supported by this plugin. For the version 1.0.0, only YouTube and Vimeo are supported. See the list at the bottom of this page.', 'wpmb'), get_option('home').'/wp-admin/admin.php?page=wpmb-supported'); ?>
					<br />
					<?php _e('** Except those already marked as clickable link or inserted in a paragraph.', 'wpmb'); ?>
				</i>
				
				<h3><?php _e('Wordpress Filters', 'wpmb'); ?></h3>
				<p>
					<label for="wpmb-filter-content-make-clickable">
						<input type="checkbox" value="1" name="wpmb-filter-content-make-clickable" id="wpmb-filter-content-make-clickable" 
							<?php echo (($wpmb_options['wpmb_filter_content_make_clickable']) ? 'checked="checked"' : ''); ?> style="margin:2px;" />
						<?php _e('Make other urls clickable in posts/pages content***', 'wpmb'); ?>
					</label>
				</p>
				<p style="color:#999999;">
					<label for="wpmb-filter-comment-make-clickable">
						<input type="checkbox" value="1" name="wpmb-filter-comment-make-clickable" id="wpmb-filter-comment-make-clickable" disabled 
							<?php echo (($wpmb_options['wpmb_filter_comment_make_clickable']) ? 'checked="checked"' : ''); ?> style="margin:2px;" />
						<?php _e('Make other urls clickable in comments content*** <i>(soon)</i>', 'wpmb'); ?>
					</label>
				</p>
				<i>
					<?php _e('*** This will happen after adding WpMultimediaBridge elements in the content', 'wpmb'); ?>
				</i>
				
				<h3><?php _e('Other settings', 'wpmb'); ?></h3>
				<p>
					<label for="wpmb-canvas-width">
						<?php _e('Width of multimedia canvas:', 'wpmb'); ?>
						<input type="text" name="wpmb-canvas-width" id="wpmb-canvas-width" style="width:80px;text-align:right;" 
							value="<?php echo $wpmb_options['wpmb_canvas_width']; ?>" />
					</label>
					<?php _e('pixels', 'wpmb'); ?>
					<i>
						<br />
						<?php _e('Leave empty for autosize. Minimum suggested of 420 pixels.', 'wpmb'); ?>
					</i>
				</p>
				<p>
					<label for="wpmb-description-size">
						<?php _e('Size of description in multimedia canvas:', 'wpmb'); ?>
						<input type="text" name="wpmb-description-size" id="wpmb-description-size" style="width:50px;text-align:right;" 
							value="<?php echo $wpmb_options['wpmb_description_size']; ?>" />
					</label>
					<?php _e('words', 'wpmb'); ?>
					<i>
						<br />
						<?php _e('Leave empty for no limit.', 'wpmb'); ?>
					</i>
				</p>
				
				<h3><?php _e('Advanced Options', 'wpmb'); ?></h3>
				<p>
					<label for="wpmb-disable-post-revision">
						<input type="checkbox" value="1" name="wpmb-disable-post-revision" id="wpmb-disable-post-revision"
							<?php echo (($wpmb_options['wpmb_disable_post_revision']) ? 'checked="checked"' : ''); ?> style="margin:2px;" />
						<?php _e('Disable Wordpress posts and pages revisions history', 'wpmb'); ?>
					</label>
					<i>
						<br />
						<?php _e('By disabling the Wordpress posts and pages revision history, you will increase the speed of this plugin on client side.<br />The effect will be visible for a blog that contains <strong>many thousand of posts</strong> with WpMultimediaBridge elements in it.', 'wpmb'); ?>
						<br />
						<?php _e('By default, this option is unchecked.', 'wpmb'); ?>
					</i>
				</p>
				<p>
					<label for="wpmb-disable-post-autosave">
						<input type="checkbox" value="1" name="wpmb-disable-post-autosave" id="wpmb-disable-post-autosave"
							<?php echo (($wpmb_options['wpmb_disable_post_autosave']) ? 'checked="checked"' : ''); ?> style="margin:2px;" />
						<?php _e('Disable Wordpress posts and pages autosave', 'wpmb'); ?>
					</label>
					<i>
						<br />
						<?php _e('Disable the Wordpress posts and pages autosave only if you encounter problem regarding autosave versus this plugin.', 'wpmb'); ?>
						<br />
						<?php _e('By default, this option is unchecked.', 'wpmb'); ?>
					</i>
				</p>
				<p style="color:#a10000;">
					<label for="wpmb-hide-imatrice-url">
						<input type="checkbox" value="1" name="wpmb-hide-imatrice-url" id="wpmb-hide-imatrice-url"
							<?php echo (($wpmb_options['wpmb_hide_imatrice_url']) ? 'checked="checked"' : ''); ?> style="margin:2px;" />
						<?php _e('Hide imatrice reference url', 'wpmb'); ?>
					</label>
					<i>
						<br />
						<?php _e('imatrice.com is the author of this plugin. By leaving this visibility to imatrice.com on your blog, you contribute to the success and evolution of this plugin. We thank you for that!', 'wpmb'); ?>
						<br />
						<?php _e('By default, this option is checked. So, the small ad is NOT visible.', 'wpmb'); ?>
					</i>
				</p>
				
				<p>
    				<input type="submit" name="wpmb-submit" id="wpmb-submit" value="<?php _e('Save Settings', 'wpmb'); ?>" class="button" />
    			</p>
			</div>
		</form>
		<br />
		<hr />
		<?php
		self::supported_media();
	}
	
	public static function supported_media()
	{
		?>
		<div class="wrap" style="margin-top:20px;">
			<h2><?php _e('List of supported sources', 'wpmb'); ?></h2>
			<a name="supporting"></a>
			<p><?php _e('Here is the list of supported sources by <i>WpMultimediaBridge</i> Plugin.', 'wpmb'); ?></p>
			
			<div style="padding-top:6px;padding-bottom:6px;margin-top:6px;margin-bottom:6px;border-bottom:1px solid #BBBBBB;border-top:1px solid #BBBBBB;">
				<div style="float:left;">
					<a href="http://www.youtube.com/" target="_blank"><img src="<?php echo WPMB_RESOURCES_URL; ?>/images/wpmb-youtube-logo-w140.png" title="YouTube" alt="YouTube" /></a>
				</div>
				<div style="float:left;padding-left:10px;">
					<strong>YouTube - Broadcast Yourself.</strong>
					<br /><a href="http://www.youtube.com/" target="_blank">http://www.youtube.com/</a>
				</div>
				<div class="wpmb-clear"></div>
				<div style="float:left;font-size:0.8em;color:#666666;margin-top:6px;padding-left:6px;padding-bottom:3px;margin-left:12px;border-left:4px solid #BBBBBB;">
					<?php _e('Required format: http://www.youtube.com/watch?v=[media key]', 'wpmb'); ?>
					<br />
					<?php _e('[media key] represents the unique identifier for a specific media.', 'wpmb'); ?>
				</div>
				<div class="wpmb-clear"></div>
			</div>
			
			<div style="padding-bottom:6px;margin-bottom:6px;border-bottom:1px solid #BBBBBB;">
				<div style="float:left;">
					<a href="http://vimeo.com/" target="_blank"><img src="<?php echo WPMB_RESOURCES_URL; ?>/images/wpmb-vimeo-logo-w140.png" title="Vimeo" alt="Vimeo" /></a>
				</div>
				<div style="float:left;padding-left:10px;">
					<strong>Vimeo, Video Sharing For You</strong>
					<br /><a href="http://vimeo.com/" target="_blank">http://vimeo.com/</a>
				</div>
				<div class="wpmb-clear"></div>
				<div style="float:left;font-size:0.8em;color:#666666;margin-top:6px;padding-left:6px;padding-bottom:3px;margin-left:12px;border-left:4px solid #BBBBBB;">
					<?php _e('Required format: http://vimeo.com/[media key]', 'wpmb'); ?>
					<br />
					<?php _e('[media key] represents the unique identifier for a specific media.', 'wpmb'); ?>
				</div>
				<div class="wpmb-clear"></div>
			</div>
			
			<p style="color:green;font-weight:bold;"><?php _e('More sources will be added soon. Thank you!', 'wpmb'); ?></p>
		</div>
		<?php 
	}
	
	public static function admin_add_meta_box()
	{
		add_meta_box('wpmbMediaList', __('WpMultimediaBridge urls List', 'wpmb'), array(__CLASS__, 'admin_add_meta_box_media_list'), 'post', 'side', 'high');
		add_meta_box('wpmbMediaList', __('WpMultimediaBridge urls List', 'wpmb'), array(__CLASS__, 'admin_add_meta_box_media_list'), 'page', 'side', 'high');
	}
	
	public static function admin_add_meta_box_media_list()
	{
		global $post;
		
		$medias = new Wpmb_Medias($post->ID);
		$current_medias = $medias->get_medias();
		$nb_medias = count($current_medias);
		
		if ($nb_medias > 0) :
			?>
			<!-- BEGIN SCRIPT - WpMultimediaBridge Wordpress Plugin -->
			<script language="javascript" type="text/javascript">
			<!--
			wpmbMedia.labels = {
				"doneSaveMsg" : "<?php _e('This multimedia element was saved successfully.', 'wpmb'); ?>",
				"emptyMsg" : "<?php _e('Title cannot be empty. Try again.', 'wpmb'); ?>",
				"errorSaveMsg" : "<?php _e('There was a problem saving your multimedia element. Try again.', 'wpmb'); ?>",
				"doneResetMsg" : "<?php _e('This multimedia element was reseted successfully.', 'wpmb'); ?>",
				"errorResetMsg" : "<?php _e('There was a problem reseting data from source url. Try again later.', 'wpmb'); ?>",
				"errorActiveMsg" : "<?php _e('There was a problem changing status. Try again.', 'wpmb'); ?>",
				"doneActivateMsg" : "<?php _e('This multimedia element was activated successfully.', 'wpmb'); ?>",
				"doneDesactivateMsg" : "<?php _e('This multimedia element was desactivated successfully.', 'wpmb'); ?>",
				"postId" : <?php echo $post->ID; ?>
			}
			//-->
			</script>
			<!-- END SCRIPT - WpMultimediaBridge Wordpress Plugin -->
			<div id="wpmb-message"></div>
			<?php
			$i = 1;
			foreach ($current_medias as $post_media) :
				?>
				<div class="wpmb-canvas<?php echo (($i == $nb_medias) ? "" : " wpmb-canvas-border"); ?>">
					<input type="hidden" id="wpmb-data-media-id" name="wpmb-data-media-id" value="<?php echo $post_media->id; ?>" />
					<div class="wpmb-summary-zone">
						<div class="wpmb-expand-title<?php echo (($post_media->active) ? "" : " wpmb-expand-title-desactivated"); ?>" title="<?php _e('Click here to expand', 'wpmb'); ?>"><?php echo $post_media->title; ?><br /><?php echo $post_media->source_url; ?></div>
						<div class="wpmb-clear"></div>
					</div>
					<div class="wpmb-detail-zone">
						<div class="wpmb-data-zone">
							<label for="wpmb-data-title-<?php echo $post_media->id; ?>" class="wpmb-data-label"><?php _e('Title:', 'wpmb') ?></label><br />
		            		<input type="text" id="wpmb-data-title-<?php echo $post_media->id; ?>" class="wpmb-data-title wpmb-editable" value="<?php echo $post_media->title; ?>" />
						</div>
						<div class="wpmb-data-zone">
							<label for="wpmb-data-description-<?php echo $post_media->id; ?>" class="wpmb-data-label"><?php _e('Description:', 'wpmb') ?></label><br />
		            		<textarea id="wpmb-data-description-<?php echo $post_media->id; ?>" class="wpmb-data-description wpmb-editable" style="height:60px;"><?php echo $post_media->description; ?></textarea>
						</div>
						<div class="wpmb-data-zone">
							<label for="wpmb-data-source-url-<?php echo $post_media->id; ?>" class="wpmb-data-label"><?php _e('Multimedia Url:', 'wpmb') ?></label><br />
		            		<input type="text" id="wpmb-data-source-url-<?php echo $post_media->id; ?>" class="wpmb-data-source-url wpmb-not-editable" value="<?php echo $post_media->source_url; ?>" />
						</div>
						<div class="wpmb-button-zone">
							<div class="wpmb-collapse-button" title="<?php _e('Click here to collapse', 'wpmb'); ?>"></div>
							<div class="wpmb-save-button" title="<?php _e('Save changes', 'wpmb'); ?>"></div>
							<div class="wpmb-reset-button" title="<?php _e('Reset from source', 'wpmb'); ?>"></div>
							<div class="wpmb-desactivate-button" title="<?php _e('Desactivate', 'wpmb'); ?>"<?php echo (($post_media->active) ? '' : ' style="display:none;"'); ?>></div>
							<div class="wpmb-activate-button" title="<?php _e('Activate', 'wpmb'); ?>"<?php echo (($post_media->active) ? ' style="display:none;"' : ''); ?>></div>
							<div class="wpmb-clear"></div>
						</div>
					</div>
				</div>
				<?php
				$i++;
			endforeach;
		else :
			if ('page' == $post->post_type) :
				echo '<p>' . __('There is currently no media for this page', 'wpmb') . '</p>';
			else :
				echo '<p>' . __('There is currently no media for this post', 'wpmb') . '</p>';
			endif;
		endif;
	}
}
//wpmb-youtube-ready-black.png
class Wpmb_Admin_Ajax
{
	public static function save_media_data()
	{
		if (isset($_POST['post_id']) && isset($_POST['id']) && isset($_POST['title']) && isset($_POST['description'])) {
			$post_medias = new Wpmb_Medias($_POST['post_id']);
			
			if (empty($_POST['title'])) {
                                header( "Content-Type: application/json" );
                                echo json_encode(array('result' => 'empty'));
                                exit;
			}
			
			if ($post_medias->update(array(
				'id' => $_POST['id'],
				'title' => $_POST['title'],
				'description' => $_POST['description']
			))) {
                                header( "Content-Type: application/json" );
                                echo json_encode(array('result' => 'done'));
                                exit;
			}
		}

                header( "Content-Type: application/json" );
		echo json_encode(array('result' => 'error'));
		exit;
	}
	
	public static function reset_media_data()
	{
		global $wpmb_media_providers;
		
		if (isset($_POST['post_id']) && isset($_POST['id'])) {
			$post_medias = new Wpmb_Medias($_POST['post_id']);
			$post_media = $post_medias->get_media($_POST['id']);
			
			if (!empty($post_media->source_url)) {
				foreach ($wpmb_media_providers as $provider_match_expression => $provider_object) :
					if (preg_match_all($provider_match_expression, $post_media->source_url, $matches)) {
						if (isset($matches[0]) && count($matches[0]) > 0) {
							$found_urls = $matches[0];
							
							foreach ($found_urls as $found_url) :
								$found_url = trim($found_url);
								$new_media_args = $provider_object->acquire_data($found_url);
                                                                
								if ($new_media_args) {
									if ($post_medias->update(array(
										'id' => $_POST['id'],
										'title' => $new_media_args['title'],
										'description' => $new_media_args['description']
									))) {
                                                                                header( "Content-Type: application/json" );
										echo json_encode(array(
											'title' => $new_media_args['title'],
											'description' => $new_media_args['description']
										));
										exit;
									}
								}
							endforeach;
						}
					}
				endforeach;
			}
		}

                header( "Content-Type: application/json" );
		echo json_encode(array('error' => '1'));
		exit;
	}
	
	public static function activate_media()
	{
		if (isset($_POST['post_id']) && isset($_POST['id'])) {
			$post_medias = new Wpmb_Medias($_POST['post_id']);
			$post_media = $post_medias->get_media($_POST['id']);
			
			if ($post_media->active) {
				if ($post_medias->update(array(
					'id' => $_POST['id'],
					'active' => 0
				))) {
                                        header( "Content-Type: application/json" );
                                        echo json_encode(array('result' => 'inactive'));
                                        exit;
				}
			} else {
				if ($post_medias->update(array(
					'id' => $_POST['id'],
					'active' => 1
				))) {
                                        header( "Content-Type: application/json" );
                                        echo json_encode(array('result' => 'active'));
                                        exit;
				}
			}
		}

                header( "Content-Type: application/json" );
                echo json_encode(array('result' => 'error'));
                exit;
	}
}
?>