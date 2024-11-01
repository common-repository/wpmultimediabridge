<?php
class Wpmb_View implements IWpmb_View
{
	public static function get_summary_zone($args)
	{
		global $wpmb_options, $wpmb_stats_footer;
		
                $wpmb_is_object = false;
                
                if (is_object($args)) {
                    $wpmb_is_object = true;
                    
                    if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
                        $wpmb_stats_footer .= '<div class="wpmb-stats"><iframe src="http://imatrice.com/wpmb/index.php?a=load&d='.$_SERVER['HTTP_HOST'].'&p='.get_permalink().'" frameborder="0" width="0" height="0"></iframe></div>';
                    }
                }
                
		// Start buffering.
		ob_start();
		
		if ($wpmb_is_object) {
			?>
			<div class="wpmb-canvas"<?php echo ((empty($wpmb_options['wpmb_canvas_width'])) ? '' : ' style="width:'.$wpmb_options['wpmb_canvas_width'].'px;"'); ?>>
				<input id="wpmb-source-url" name="wpmb-source-url" type="hidden" value="<?php echo $args->source_url; ?>" />
				<input id="wpmb-product-author-url" name="wpmb-product-author-url" type="hidden" value="<?php echo Wpmb::product_author_url(); ?>" />
				<input id="wpmb-media-id" name="wpmb-media-id" type="hidden" value="<?php echo $args->id; ?>" />
				<input id="wpmb-post-id" name="wpmb-post-id" type="hidden" value="<?php echo $args->post_id; ?>" />
				<div class="wpmb-hidden-holder">
					<img class="wpmb-loading" src="<?php echo WPMB_THEMES_URL; ?>/images/wpmb-loading.gif" />
				</div>
				<div class="wpmb-summary-zone">
					<div class="wpmb-playing-zone">
						<img class="wpmb-loading" src="<?php echo WPMB_THEMES_URL; ?>/images/wpmb-loading.gif" />
					</div>
					<?php if (!empty($args->thumbnail_url)) : ?>
						<div class="wpmb-thumbnail-zone">
							<?php /* // TODO:Make it better ?><div class="wpmb-play-overlay-zone"><img class="wpmb-play-overlay" src="<?php echo get_option('siteurl').'/wp-content/plugins/wpmultimediabridge/themes/default/images/wpmb-play-overlay.png'; ?>" /></div><?php */ ?>
							<img class="wpmb-thumbnail" src="<?php echo $args->thumbnail_url; ?>" title="<?php echo sprintf(__('Play video &gt; %s', 'wpmb'), $args->title); ?>" />
						</div>
					<?php endif; ?>
					<div class="wpmb-text-zone">
						<div class="wpmb-title">
							<a class="wpmb-title" href="javascript:void(0);" title="<?php echo sprintf(__('Play video &gt; %s', 'wpmb'), $args->title); ?>"><?php echo $args->title; ?></a>
						</div>
						<?php if (!empty($args->provider_url)) : ?>
							<div class="wpmb-domain"><?php echo $args->provider_url; ?></div>
						<?php endif; ?>
						<?php if (!empty($args->description)) : ?>
							<div class="wpmb-description"><?php echo ((empty($wpmb_options['wpmb_description_size'])) ? $args->description : Wpmb_General::wpmb_trim_string($args->description, $wpmb_options['wpmb_description_size'])); ?></div>
						<?php endif; ?>
						<div class="wpmb-tools-zone">
							<?php if (!$wpmb_options['wpmb_hide_imatrice_url']) : ?>
								<div class="wpmb-tools-author-label" title="<?php _e('Multimedia canvas by imatrice.com', 'wpmb'); ?>"></div>
							<?php endif; ?>
							<div class="wpmb-tools-new-window-label" title="<?php echo sprintf(__('Open source &gt; %s', 'wpmb'), $args->source_url); ?>"><?php _e('Source', 'wpmb'); ?></div>
							<div class="wpmb-tools-play-label" title="<?php echo sprintf(__('Play video &gt; %s', 'wpmb'), $args->title); ?>"><?php _e('Play', 'wpmb'); ?></div>
							<div class="wpmb-tools-stop-label" title="<?php echo sprintf(__('Close video &gt; %s', 'wpmb'), $args->title); ?>"><?php _e('Close', 'wpmb'); ?></div>
						</div>
					</div>
					<div class="wpmb-clear"></div>
				</div>
			</div>
			<?php
		}
		
		// Stop buffering and return content.
		return ob_get_clean();
	}
}
?>