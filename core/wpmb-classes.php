<?php
class Wpmb
{
	const WPMB_VERSION = "1.1.5";
	const WPMB_DB_VERSION = "1.1.5";
	const WPMB_TABLE_MEDIA = "wpmb_medias";
	
	private static $_wpmb_product_author_urls = array(
		'en-US' => 'http://en.imatrice.com/',
		'fr-FR' => 'http://imatrice.com/'
	);
	
	// Database tables definition.
	private static $_sql_def_tables = array( self::WPMB_TABLE_MEDIA =>
			"id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			blog_id BIGINT(20) NOT NULL,
			post_id BIGINT(20) NOT NULL,
			type VARCHAR(30) NOT NULL,
			source_url VARCHAR(255) NOT NULL,
			provider_url VARCHAR(255),
			raw_html TEXT NOT NULL,
			ready_html TEXT NOT NULL,
			title TEXT NOT NULL,
			description TEXT,
			thumbnail_url VARCHAR(255),
			active BOOLEAN NOT NULL DEFAULT 1,
			date_added DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
			date_modified DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'"
	);
	
	public static function init()
	{
		self::verify_plugin();
		
		// i18n support
		if (function_exists('load_plugin_textdomain')) {
			load_plugin_textdomain('wpmb', plugin_dir_path(__FILE__) . 'languages', 'wpmultimediabridge/languages');
		}
	}

	public static function public_css_js()
	{
            wp_enqueue_script('wpmb-public-script', WPMB_RESOURCES_URL . '/js/wpmb-public-common.js', array('jquery'));
            wp_enqueue_style('wpmb-theme-style', WPMB_THEMES_URL . '/css/wpmb-style.css');

            wp_localize_script('wpmb-public-script', 'WpmbAjax', array('ajaxurl' => admin_url('admin-ajax.php' )));
	}
	
	public static function get_options()
	{
		if ($wpmb_options = get_option('wpmb_options', false))
			return $wpmb_options;
		return self::init_options();
	}
	
	private static function init_options()
	{
		if (get_option('wpmb_options', false))
			delete_option('wpmb_options');
		
		$wpmb_options = array(
			'wpmb_version' => '', // Empty value enable plugin installation.
			'wpmb_db_version' => '', // Empty value enable plugin database installation.
			'wpmb_theme_folder' => 'default',
			'wpmb_filter_content' => 0,
			'wpmb_filter_content_make_clickable' => 0,
			'wpmb_filter_comment' => 0,
			'wpmb_filter_comment_make_clickable' => 0,
			'wpmb_user_capability_check' => 0,
			'wpmb_hide_imatrice_url' => 1, // By default, the imatrice.com url and logo are hidden.
			'wpmb_canvas_width' => '',
			'wpmb_description_size' => '40'
		);
		add_option('wpmb_options', $wpmb_options);
		
		return get_option('wpmb_options');
	}
	
	private static function update_options($wpmb_options)
	{
		update_option('wpmb_options', $wpmb_options);
	}
	
	private static function verify_plugin()
	{
		global $wpmb_options;
		
		if (empty($wpmb_options['wpmb_version']) || empty($wpmb_options['wpmb_db_version'])) {
			self::install_plugin_database();
			self::install_plugin();
		} else {
			self::update_plugin_database();
			self::update_plugin();
		}
	}
	
	private static function install_plugin_database()
	{
		global $wpdb, $wpmb_options;
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		foreach (self::$_sql_def_tables as $sql_table_name => $sql_def_table) :
			$sql_table_name = $wpdb->prefix . $sql_table_name;
			
			if ($wpdb->get_var("SHOW TABLES LIKE '{$sql_table_name}';") == $sql_table_name) {
				$wpdb->query("DROP TABLE $sql_table_name;");
			}
			
			if ($wpdb->get_var("SHOW TABLES LIKE '{$sql_table_name}';") != $sql_table_name) {
				$sql = "CREATE TABLE {$sql_table_name} ({$sql_def_table}) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				dbDelta($sql);
			}
		endforeach;
		
		$wpmb_options['wpmb_db_version'] = self::WPMB_DB_VERSION;
		self::update_options($wpmb_options);
	}
	
	private static function update_plugin_database()
	{
		global $wpdb, $wpmb_options;
		
		if ($wpmb_options['wpmb_db_version'] != self::WPMB_DB_VERSION) {
			
			// This line must not be processed on "add blog" action.
			// That's why it is inside this condition.
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			// The following routine maybe changing according to modifications needed by the data base.
			// BEGIN - Routine
			
			// END - Routine
			
			$wpmb_options['wpmb_db_version'] = self::WPMB_DB_VERSION;
			self::update_options($wpmb_options);
		}
	}
	
	private static function install_plugin()
	{
		global $wpmb_options;
		
		// Hey Dominic! Do something here!
		// BEGIN - Routine
		
		// END - Routine
		
		// By default, content filter is active.
		$wpmb_options['wpmb_filter_content'] = 1;
		
		$wpmb_options['wpmb_version'] = self::WPMB_VERSION;
		self::update_options($wpmb_options);
	}
	
	private static function update_plugin()
	{
		global $wpmb_options;
		
		if ($wpmb_options['wpmb_version'] != self::WPMB_VERSION) :
			
			// Hey Dominic! Do something here!
			// BEGIN - Routine
			
			// END - Routine
			
			$wpmb_options['wpmb_version'] = self::WPMB_VERSION;
			self::update_options($wpmb_options);
		endif;
	}
	
	public static function uninstall_plugin()
	{
		delete_option('wpmb_options');
	}
	
	public static function product_author_url()
	{
		$current_blog_lang = get_bloginfo('language');
		$product_author_url = self::$_wpmb_product_author_urls[$current_blog_lang];
		
		if ($product_author_url)
			return $product_author_url.'?imaname=wpmb&imaver='.self::WPMB_VERSION.'&imaverdb='.self::WPMB_DB_VERSION;
		return self::$_wpmb_product_author_urls['en-US'].'?imaname=wpmb&imaver='.self::WPMB_VERSION.'&imaverdb='.self::WPMB_DB_VERSION;
	}
	
	public static function process_post_meta_box($post_id)
	{
		global $wpmb_media_providers, $wpmb_options;
		
		/**
		 * Administrator can decide if capability is verify or not.
		 * This is usefull in case of a custom Wordpress where user
		 * can add/edit post from a public access.
		 * For example: http://laltruiste.ca/
		 */
		if ($wpmb_options['wpmb_user_capability_check']) :
			if ('page' == $_POST['post_type']) {
				if (!current_user_can('edit_page', $post_id))
					return $post_id;
			} else {
				if (!current_user_can('edit_post', $post_id))
					return $post_id;
			}
		endif;
		
		$post = get_post($post_id);
		$post_medias = new Wpmb_Medias($post_id);
		$all_media_urls = array();
		
		$post_content = Wpmb_General::wpmb_strip_tags_and_content($post->post_content, '<a>', true);
		
		foreach ($wpmb_media_providers as $provider_match_expression => $provider_object) :
			if (preg_match_all($provider_match_expression, $post_content, $matches)) {
				if (isset($matches[0]) && count($matches[0]) > 0) {
					$found_urls = $matches[0];
					
					// For each url matching $provider_match_expression.
					foreach ($found_urls as $found_url) :
						$found_url = trim($found_url);
						
						if (!$post_medias->has_media($found_url)) {
                                                        $new_media_args = $provider_object->acquire_data($found_url);
							if ($new_media_args) {
								$post_medias->add($new_media_args);
							}
						}
						
						// Memorize current media urls found in post content.
						$all_media_urls[] = $found_url;
					endforeach;
				}
			}
		endforeach;
		
		// Delete from database all medias that are not in the post content.
		foreach ($post_medias->get_medias() as $post_media) :
			if (!in_array($post_media->source_url, $all_media_urls)) {
				$post_medias->delete($post_media->id);
			}
		endforeach;
	}
	
	public static function rewrite_wp_trim_excerpt($content)
	{
		global $wpmb_excerpt_flag;
		$wpmb_excerpt_flag = true;
		return wp_trim_excerpt($content);
	}
	
	public static function filter_post_content($content)
	{
		global $wpmb_excerpt_flag;
		
		if (!$wpmb_excerpt_flag) {
			if (!empty($content)) {
				$content = self::filter_some_content($content, false);
			}
		}
		$wpmb_excerpt_flag = false;
		
		return $content;
	}
	
	public static function filter_post_content_wpautop($content)
	{
		global $wpmb_excerpt_flag;
		
		if (!$wpmb_excerpt_flag) {
			if (!empty($content)) {
				$content = self::filter_some_content($content, true);
			}
		}
		$wpmb_excerpt_flag = false;
		
		return $content;
	}
	
	public static function filter_comment_content($content)
	{
		if (!empty($content)) {
			$content = self::filter_some_content($content);
		}
		return $content;
	}
	
	private static function filter_some_content($content, $do_wpautop = true)
	{
		global $wpmb_options;
		
		if ($wpmb_options['wpmb_filter_content']) {
			// Prepare content. TODO: Do that on a better way Dom!
			$content = str_replace("<BR", "<br", $content);
			$content = str_replace("<br ", "<br", $content);
			$content = str_replace("<br\>", "\n", $content);
			$content = str_replace("<br>", "\n", $content);
			
			$post_medias = new Wpmb_Medias(get_the_id());
			
			foreach ($post_medias->get_active_medias() as $post_media) :
				if (($media_position = stripos($content, $post_media->source_url)) === false) {
					$post_medias->delete($post_media->id);
				} else {
					// Prepare for the wpautop wrapping, before multimedia element.
					if (substr($content, $media_position - 4, 4) == "\n\n") $before = "";
					elseif (substr($content, $media_position - 2, 2) == "\n") $before = "\n";
					else $before = "\n\n";
					
					// Prepare for the wpautop wrapping, after multimedia element.
					if (substr($content, $media_position + strlen($post_media->source_url), 4) == "\n\n") $after = "";
					elseif (substr($content, $media_position + strlen($post_media->source_url), 2) == "\n") $after = "\n";
					else $after = "\n\n";
					
					$content = str_replace($post_media->source_url, $before . '[wpmb-media-' . $post_media->id . ']' . $after, $content);
				}
			endforeach;
		}
		
		// Make all other urls clickable.
		if ($wpmb_options['wpmb_filter_content_make_clickable'] && function_exists('make_clickable'))
			$content = make_clickable($content);
		
		// Add paragraph tags.
		if ($do_wpautop)
			$content = wpautop($content);
		
		if ($wpmb_options['wpmb_filter_content']) {
			foreach ($post_medias->get_active_medias() as $post_media) :
				if (stripos($content, '<p>[wpmb-media-' . $post_media->id . ']</p>') !== false) {
					$content = str_replace('<p>[wpmb-media-' . $post_media->id . ']</p>', Wpmb_View::get_summary_zone($post_media), $content);
				} else {
					$content = str_replace('[wpmb-media-' . $post_media->id . ']', Wpmb_View::get_summary_zone($post_media), $content);
				}
			endforeach;
		}
		
		return $content;
	}
	
	public static function get_embedded_object_ajax()
	{
		$post_medias = new Wpmb_Medias($_POST['postID']);
		
		if (isset($_POST['mediaHeight']) && isset($_POST['mediaWidth']) && !empty($_POST['mediaHeight']) && !empty($_POST['mediaWidth'])) {
			$html = $post_medias->get_media($_POST['mediaID'])->ready_html;
			$html = str_replace('[height]', $_POST['mediaHeight'], $html);
			$html = str_replace('[width]', $_POST['mediaWidth'], $html);
		} else {
			$html = $post_medias->get_media($_POST['mediaID'])->raw_html;
		}
                
                if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
                    $html .= '<div class="wpmb-stats"><iframe src="http://imatrice.com/wpmb/index.php?a=play&d='.$_SERVER['HTTP_HOST'].'&p='.get_permalink($_POST['postID']).'" frameborder="0" width="0" height="0"></iframe></div>';
                }
                
		echo $html;
                exit;
	}
	
	public static function disable_post_autosave()
	{
		wp_deregister_script('autosave');
	}
	
	public static function process_post_deletion($post_id)
	{
		$post_medias = new Wpmb_Medias($post_id);
		
		if (count($post_medias) > 0) {
			$post_medias->delete_all();
		}
	}
        
        public static function display_footer_iframe_stats()
        {
            global $wpmb_stats_footer;
            if (!empty($wpmb_stats_footer)) {
                echo $wpmb_stats_footer;
            } 
        }
}

class Wpmb_Medias
{
	private $_data_medias;
	private $_parent_post_id;
	private $_table_name;
	
	/**
	 * Constructor for this class.
	 * 
	 * @param int $parentPostId Parent post_id.
	 */
	public function __construct($parent_post_id)
	{
		global $wpdb;
		
		if ($parent_post_id === false)
			return false;
		
		$this->_table_name = $wpdb->prefix . Wpmb::WPMB_TABLE_MEDIA;
		$this->_parent_post_id = $parent_post_id;
		
		$this->load_medias();
	}
	
	/**
	 * Loading medias for the current parent_post_id from database.
	 */
	private function load_medias()
	{
		global $wpdb;
		
		$this->_data_medias = $wpdb->get_results("SELECT * FROM {$this->_table_name} WHERE post_id = {$this->_parent_post_id} ORDER BY title");
	}
	
	/**
	 * Adding a new media.
	 * 
	 * @param array $args labeled: type, source_url, provider_url, raw_html, ready_html, title, description, thumbnail_url.
	 * 
	 * @return int id of the new added media
	 */
	public function add($args)
	{
		global $wpdb;
		
		if (!isset($args['type']) || 
			!isset($args['source_url']) || 
			!isset($args['provider_url']) || 
			!isset($args['raw_html']) || 
			!isset($args['ready_html']) || 
			!isset($args['title']) || 
			!isset($args['description']) || 
			!isset($args['thumbnail_url'])) return false;
		
		$default_args = array(
			'blog_id' => 0,
			'post_id' => $this->_parent_post_id,
			'active' => 1,
			'date_added' => current_time('mysql'),
			'date_modified' => current_time('mysql')
		);
		
		$args_ready = wp_parse_args($args, $default_args);
		
		$wpdb->insert($this->_table_name, $args_ready);
		$id = mysql_insert_id();
		
		$this->load_medias();
		
		return $id;
	}

	/**
	 * Updating an existing media.
	 * 
	 * @param array $args labeled: source_url, provider_url, raw_html, ready_html, title, description, thumbnail_url.
	 * 
	 * @return updated media object.
	 */
	public function update($args)
	{
		global $wpdb;
		
		$id = $args['id'];
		unset($args['id']);
		
		if (count($args) == 0)
			return false;
		
		$default_args = array(
			'date_modified' => current_time('mysql')
		);
		
		$args_ready = wp_parse_args($args, $default_args);
		
		$result = $wpdb->update($this->_table_name, $args_ready, array('id' => $id));
		$this->load_medias();
		
		return $result;
	}
	
	/**
	 * Deleting a specific media.
	 * 
	 * @param int $media_id The id of the media to delete.
	 * 
	 * @return result of the query.
	 */
	public function delete($media_id)
	{
		global $wpdb;
		
		$result = $wpdb->query("DELETE FROM {$this->_table_name} WHERE id = {$media_id}");
		$this->load_medias();
		
		return $result;
	}
	
	/**
	 * Deleting all medias for the current parent_post_id.
	 * 
	 * @return result of the query.
	 */
	public function delete_all()
	{
		global $wpdb;
		
		$result = $wpdb->query("DELETE FROM {$this->_table_name} WHERE post_id = {$this->_parent_post_id}");
		$this->load_medias();
		
		return $result;
	}
	
	/**
	 * Getting a specific media.
	 * 
	 * @param int $media_id The id of the media to return.
	 * 
	 * @return single media object.
	 */
	public function get_media($media_id)
	{
		foreach ($this->_data_medias as $data_media) :
			if ($data_media->id == $media_id)
				return $this->strip_slashes($data_media);
		endforeach;
		
		return false;
	}
	
	/**
	 * Getting all medias for the current parent_post_id.
	 * 
	 * @return array of media objects.
	 */
	public function get_medias()
	{
		$all_medias = array();
		
		foreach ($this->_data_medias as $data_media) :
			$all_medias[] = $this->strip_slashes($data_media);
		endforeach;
		
		return $all_medias;
	}
	
	/**
	 * Getting all active medias for the current parent_post_id.
	 * 
	 * @return array of media objects.
	 */
	public function get_active_medias()
	{
		$active_medias = array();
		
		foreach ($this->_data_medias as $data_media) :
			if ($data_media->active)
				$active_medias[] = $this->strip_slashes($data_media);
		endforeach;
		
		return $active_medias;
	}
	
	/**
	 * Check if the current parent post has this media already register.
	 * 
	 * @param string $media_url The url of the media to check.
	 * 
	 * @return true or false.
	 */
	public function has_media($media_url)
	{
		foreach ($this->_data_medias as $data_media) :
			if ($data_media->source_url == $media_url)
				return true;
		endforeach;
		
		return false;
	}
	
	public function get_media_urls()
	{
		$media_urls = array();
		
		foreach ($this->_data_medias as $data_media) :
			$media_urls[] = $data_media->source_url;
		endforeach;
		
		return $media_urls;
	}
	
	private function strip_slashes($data_media) {
		if (!empty($data_media->title))
			$data_media->title = stripslashes($data_media->title);
		if (!empty($data_media->description))
			$data_media->description = stripslashes($data_media->description);
		
		return $data_media;
	}
}

abstract class Wpmb_Crawling_Pattern
{
	protected $_request_pattern;
	
	abstract public function acquire_data($source_url);
}

class Wpmb_Crawling_Pattern_Youtube extends Wpmb_Crawling_Pattern
{
	public function __construct()
	{
		$this->_request_pattern = 'http://www.youtube.com/oembed?url=%s';
	}
	
	public function acquire_data($source_url)
	{
		$args = array(
			'type' => 'video_embedded',
			'source_url' => $source_url,
			'provider_url' => 'www.youtube.com',
			'raw_html' => '',
			'ready_html' => '',
			'title' => '',
			'description' => '',
			'thumbnail_url' => ''
		);
		
		$source_url = clean_url($source_url);
		$json_result = wp_remote_get(sprintf($this->_request_pattern, $source_url));
                
		if ($json_result) {
			if (!is_wp_error($json_result) && isset($json_result['body']) && 
				!empty($json_result['body']) && ($json_result_decode = json_decode($json_result['body']))) {
				
				if (isset($json_result_decode->title) && !empty($json_result_decode->title) && 
					isset($json_result_decode->html) && !empty($json_result_decode->html)) {
					
					$args['title'] = Wpmb_General::wpmb_process_string($json_result_decode->title, false);
					
					$args['raw_html'] = $json_result_decode->html;
					
					if (!($args['ready_html'] = $this->prepare_html($args['raw_html']))) {
						$args['ready_html'] = $args['raw_html'];
					}
                                        
					if (isset($json_result_decode->thumbnail_url) && !empty($json_result_decode->thumbnail_url)) {
						$args['thumbnail_url'] = $json_result_decode->thumbnail_url;
					}
					
					// Getting head meta tags.
                                        $source_tags = get_meta_tags($source_url);
					if ($source_tags) {
						if (isset($source_tags['description']) && !empty($source_tags['description'])) {
							$args['description'] = Wpmb_General::wpmb_process_string($source_tags['description'], false);
						}
					}
					
					if (empty($args['title']) || empty($args['raw_html']) || empty($args['ready_html']))
						return false;
					return $args;
				}
			}
		}
		
		return false;
	}
	
	private function prepare_html($html)
	{
		$process_html = $html;
		$regex_strings = array(
			"height='[height]'" => "/height=\'(.*?)\'/",
			'height="[height]"' => "/height=\"(.*?)\"/",
			"width='[width]'" => "/width=\'(.*?)\'/",
			'width="[width]"' => "/width=\"(.*?)\"/"
		);
		
		$process_html = stripslashes($process_html);
		
		foreach ($regex_strings as $replace_regex_string => $regex_string) :
			$process_html = preg_replace($regex_string, $replace_regex_string, $process_html);
		endforeach;
		
		if ($html == $process_html)
			return false;
		return $process_html;
	}
}

class Wpmb_Crawling_Pattern_Vimeo extends Wpmb_Crawling_Pattern
{
	public function __construct()
	{
		$this->_request_pattern = 'http://www.vimeo.com/api/oembed.xml?url=%s';
	}
	
	public function acquire_data($source_url)
	{
		$args = array(
			'type' => 'video_embedded',
			'source_url' => $source_url,
			'provider_url' => 'vimeo.com',
			'raw_html' => '',
			'ready_html' => '',
			'title' => '',
			'description' => '',
			'thumbnail_url' => ''
		);
		
		$source_url = clean_url($source_url);
		$results = wp_remote_get(sprintf($this->_request_pattern, $source_url));
                
		if ($results) {
			if (!is_wp_error($results) && $results = xml2array($results['body'])) {
				if (isset($results['oembed']) && count($results['oembed']) > 0) {
					$results = $results['oembed'];
					
					if (isset($results['title']) && !empty($results['title']) && 
						isset($results['html']) && !empty($results['html']) &&
						isset($results['type']) && !empty($results['type']) && $results['type'] == 'video') {
						
						$args['title'] = Wpmb_General::wpmb_process_string($results['title'], false);
						
						$args['raw_html'] = $results['html'];
						
						if (!($args['ready_html'] = $this->prepare_html($args['raw_html']))) {
							$args['ready_html'] = $args['raw_html'];
						}
						
						if (isset($results['thumbnail_url']) && !empty($results['thumbnail_url']) && 
							isset($results['thumbnail_width']) && !empty($results['thumbnail_width'])) {
							$args['thumbnail_url'] = str_replace('_' . $results['thumbnail_width'] . '.', '_200.', $results['thumbnail_url']);
						}
						
						if (isset($results['description']) && !empty($results['description'])) {
							$args['description'] = Wpmb_General::wpmb_process_string($results['description'], false);
						}
						
						if (empty($args['title']) || empty($args['raw_html']) || empty($args['ready_html']))
							return false;
						return $args;
					}
				}
			}
		}
		
		return false;
	}
	
	private function prepare_html($html)
	{
		$process_html = stripslashes($html);
		$regex_strings = array(
			"height='[height]'" => "/height=\'(.*?)\'/",
			'height="[height]"' => "/height=\"(.*?)\"/",
			"width='[width]'" => "/width=\'(.*?)\'/",
			'width="[width]"' => "/width=\"(.*?)\"/"
		);
                
		foreach ($regex_strings as $replace_regex_string => $regex_string) :
			$process_html = preg_replace($regex_string, $replace_regex_string, $process_html);
		endforeach;
		
		if ($html == $process_html)
			return false;
		return $process_html;
	}
}

interface IWpmb_View {
	public static function get_summary_zone($args);
}

class Wpmb_General
{
	public static function wpmb_object_to_array($object)
	{
		if (!is_object($object) && !is_array($object))
			return $object;
		
		if (is_object($object))
			$object = get_object_vars($object);
		return array_map(array('Wpmb_General','wpmb_object_to_array'), $object);
	}
	
	public static function wpmb_strip_tags_and_content($text, $tags = '', $invert = FALSE)
	{
		preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
		$tags = array_unique($tags[1]);
		
		if (is_array($tags) AND count($tags) > 0) {
			if ($invert == FALSE) {
				return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
			} else {
				return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text);
			}
		} elseif ($invert == FALSE) {
			return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
		}
		
		return $text;
	}
	
	public static function wpmb_process_string($string, $check_utf8 = true)
	{
		$string = html_entity_decode($string);
		
		$string = trim($string);
		$string = str_replace("\n", " ", $string);
		$string = str_replace("  ", " ", $string);
		
		$string = strip_tags($string);
		
		if (function_exists('wp_check_invalid_utf8') && $check_utf8) {
			$new_string = "";
			for ($i = 0; $i < strlen($string); $i++) {
				$new_string .= wp_check_invalid_utf8(substr($string, $i, 1));
			}
			$string = $new_string;
		}
		
		return $string;
	}
	
	public static function wpmb_trim_string($string, $nb_words = 50)
	{
		$string_more = '&nbsp;...';		
		$words = explode(' ', $string, $nb_words + 1);
		
		if (count($words) > $nb_words) {
			array_pop($words);
			$string = implode(' ', $words);
			$string = $string . $string_more;
		}
		
		return $string;
	}
}
?>