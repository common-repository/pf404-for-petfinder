<?php
/*
 * Plugin Name: PF404 for PetFinder
 * Description: Overides your 404 page template and shows dogs, cats, and more in need of a new home.
 * Author: Steve Meyer
 * Version: 0.1.0
 *
 */
if ( ! defined( 'WPINC' ) ) {
	die('What are looking?');
}
if (!class_exists('petfinder404')) {

	class petfinder404 {
		var $error = false;
		function set_error($new_error) { $this->error = $new_error; }
		function __construct() {			
			register_activation_hook(__FILE__, array($this, 'activate'));
			register_deactivation_hook(__FILE__, array($this, 'deactivate'));		
			$this->actions_and_filters();
			//Refresh animals every 24 hours
			$this->lookup_lastsync();
		}


		static function get_table_name() {
			global $wpdb;
			return $wpdb->prefix . 'petfinder_404';
		}

		private function actions_and_filters() {
			function change_404_template() {
				global $post;
				$template = plugin_dir_path(__FILE__) . 'page-404.php';
				return $template;
			}
			add_filter('404_template', 'change_404_template');

			function petfinder_404_enqueue_script() {   
				if (is_404()) {
			    	wp_enqueue_script( 'masonary', plugin_dir_url( __FILE__ ) . 'js/masonry.pkgd.min.js', array('jquery'), '1.0' );
			    	wp_enqueue_script( 'imagesloaded', plugin_dir_url( __FILE__ ) . 'js/imagesloaded.pkgd.min.js', array('jquery'), '1.0' );

			    	wp_enqueue_style('pf404-styles', plugin_dir_url( __FILE__ ) . 'css/style.css' );
				}
			}
			add_action('wp_enqueue_scripts', 'petfinder_404_enqueue_script');

			
			function pf404_custom_admin_notice() { 

				if(!get_option('pf404_options') || !get_option('pf404_options')['pf404_field_apikey'] || get_option('pf404_options')['pf404_field_explicitperm'] !== "Yes") {
				?>				
				<div class="notice notice-error is-dismissible">
					<p><?php _e('PF404 for PetFinder requires an API KEY and Link Permission granted. <a href="' . admin_url('options-general.php?page=pf404') . '" >more info</a>', 'pf404'); ?></p>
				</div>
				
			<?php } }
			add_action('admin_notices', 'pf404_custom_admin_notice');

		}

		// Add the rewrite rule and flush
		public function activate() {
			$this->create_table();
		}

		// Creates the database table for the plugin.		
		public function create_table() {
			global $wpdb;
			$charset_collate = $wpdb->get_charset_collate();
			$table_name = self::get_table_name();

			if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {
				$sql = "CREATE TABLE $table_name (
				  id mediumint(12) NOT NULL AUTO_INCREMENT,
                  pf_id varchar(20) NOT NULL,
                  pf_image varchar(200) DEFAULT '' NOT NULL,
                  pf_name varchar(55) DEFAULT '',
                  pf_synctime datetime DEFAULT NULL,
                  PRIMARY KEY (id)
                ) $charset_collate;";

				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta($sql);
				$this->load_api_data();
			}
		}

		public static function load_api_data($apikey = '', $animal = '') {

			petfinder404::delete_table();
			if (empty($apikey)){
				$apikey = get_option('pf404_options')['pf404_field_apikey'];
			}
			if(!$apikey) {return;}

			if (empty($animal)){
				$animal = get_option('pf404_options')['pf404_field_animal'];
			}
			$data = array('key' => $apikey, 'animal' => $animal, 'output' => 'basic', 'format' => 'json');
			global $wpdb;
			$table_name = self::get_table_name();
			for ($i = 0; $i < 18; $i++) {

				$url = 'http://api.petfinder.com/pet.getRandom';

				if ($data) {
					$url = sprintf("%s?%s", $url, http_build_query($data));
				}			

				$response = wp_remote_get( $url );
				$result = wp_remote_retrieve_body( $response );

				$jdecode = json_decode($result);
				if ($jdecode->petfinder) {
					$name = $jdecode->petfinder->pet->name->{'$t'};
					$petid = $jdecode->petfinder->pet->id->{'$t'};

					foreach ($jdecode->petfinder->pet->media->photos->photo as $key => $value) {
						if ($value->{'@size'} === 'pn') {
							$img = $value->{'$t'};
						}
					}
					$date = date('Y-m-d\TH:i:sP');

					$sql = $wpdb->prepare(
						"INSERT IGNORE INTO $table_name (pf_id, pf_image, pf_name, pf_synctime)
					          VALUES (%s,%s,%s,%s);", $petid, $img, $name, $date
					);

					$wpdb->query($sql);
				} else {
					$this->set_error(true);
					return;
				}

			}
		}

		public static function delete_table() {
			global $wpdb;
			$table_name = self::get_table_name();
			$sql = "Delete From $table_name";

			$wpdb->query($sql);
		}

		
		public function lookup_pets() {
			global $wpdb;
			$table_name = self::get_table_name();
			$sql = "SELECT * FROM $table_name";

			return $wpdb->get_results($sql);
		}

		public function lookup_lastsync() {
			global $wpdb;
			$table_name = self::get_table_name();
			$sql = "SELECT pf_synctime FROM $table_name Limit 1";

			$synced =  $wpdb->get_var($sql);

			if($synced) {
				$date = date('Y-m-d\TH:i:sP');
				$date = date_create($date);
				$syncdate  = date_create($synced);
				$date->modify('-1 day');

				//Last pulled animals more than 24 hours ago
				if($syncdate < $date ){
					$this->load_api_data();				
				}
			} else {
				$this->load_api_data();					
			}
		}		

	}

	$petfinder404 = new petfinder404();


}
require_once( dirname( __FILE__ ) . '/settings.php' );
?>

