<?php
/*
Plugin Name: WPMU copy options
Plugin URI:
Description: Copy any option from selected blog to other blogs in WPMU
Author: Dmitry
Version: 1.0.0
Network: true
*/

class WPMUCopyOptions {

	public $data = array();
	public $capability = 'manage_network_options';


	/* INIT */
	function __construct() {
		add_action( 'network_admin_menu', array( $this, 'plugin_pages' ) );

		$page = isset( $_GET[ 'page' ] ) ? $_GET[ 'page' ] : '';
		if( 'dmf-tools-WPMUCopyOptions' !== $page ) // stop function execution if not on plugin page
			return;

		## ќбъ€вл€ем константу инициализации нашего плагина
		DEFINE('CopyBlogOptionInWPMU', true);
		add_action( 'plugins_loaded', array( $this, 'myplugin_init'));
		add_action( 'admin_enqueue_scripts', array( $this, 'add_styles' ) );
	}

	//Add admin menu / page
	function plugin_pages() {
	   	add_menu_page('DmF Tools', 'DmF Tools', $this->capability, 'dmf-tools', array($this, 'main'));
		add_submenu_page( 'dmf-tools', __('Copy Blog Option In WPMU', 'wpmu-copy-options'), __('Copy Blog Option In WPMU', 'wpmu-copy-options'), $this->capability, 'dmf-tools-WPMUCopyOptions', array($this, 'dmf_tools_WPMUCopyOptions_main' ));
	}

	// localization
	function myplugin_init() {
		load_plugin_textdomain( 'wpmu-copy-options', false, dirname( plugin_basename( __FILE__ ) ). '/lang/' );
	}

	function add_styles() {
		wp_register_style( 'plugin-style', plugin_dir_url( __FILE__ ).'styles/style.css' );
		wp_enqueue_style( 'plugin-style' );

		/*wp_enqueue_script(
		'scripts',
		plugin_dir_url( __FILE__ ) . '/js/scripts.js',
		array( 'jquery' )
		);*/
	}

	/* INTERFACE */
	function main() {
		//echo '<div class="wrap" id="center-panel"><p class="r">DmF Tools</p></div>';
		echo '<p class="r">DmF Tools</p>';
	}


    function dmf_tools_WPMUCopyOptions_page_main_output()
    {
		if( !current_user_can( $this->capability ) ) {
			echo '<p>' . __( 'You do not have permissions to do that...', 'wpmu-copy-options' ) . '</p>'; // If accessed properly, this message doesn't appear.
			return;
		}

?>
		<div class="wrap">
			<h1><?php _e('Copy Blog Option In WPMU','wpmu-copy-options')?></h1>
			<form action="?page=dmf-tools-WPMUCopyOptions&action=showoptions" method="post" enctype="multipart/form-data">

				<p><h2 class="r"><?php _e('Source Blog', 'wpmu-copy-options'); ?></h2>
				<?php
					$blogs = get_bloglist();
				?>

				<select name="dmf_source_id" id="dmf_source_id" style="width:300px">
				<?php
					foreach ($blogs as $row) {
						$selected = '';

						if ($row['blog_id'] == "5")
							$selected = 'SELECTED';

						echo ('<option value="'.$row['blog_id'].'" '.$selected.'>'.$row['blog_id'].": ".$row['blogname'].'</option>');
					}
				?>
					</select>
				</p>

				<!--input id="source_id" name="source_id" type="hidden" value=""/-->
				<p class="submit no-border">
					  <input class="button" id="Submit_showOptions" name="Submit" value="<?php _e( 'Show options', 'wpmu-copy-options' ) ?>" type="submit" />
				</p>
			</form>

		</div>

<?php
    }



	/* BUSINESS LOGIC*/
	/**
	 * Execute actions
	 */
	function dmf_tools_WPMUCopyOptions_main() {
		global $wpdb;

		$page = isset( $_GET[ 'page' ] ) ? $_GET[ 'page' ] : '';
		if( 'dmf-tools-WPMUCopyOptions' !== $page ) // stop function execution if not on plugin page
			return;

		if( ! current_user_can( 'manage_network_options' ) ) { // check user permissions
			wp_die( 'Stopped' );
			exit;
		}

		//var_dump($_GET, $_POST);
		$action = isset( $_GET[ 'action' ] ) ? $_GET[ 'action' ] : '';
		switch( $action ) {

			case 'showoptions': // process action
				$this->data['vals'] = $wpdb->get_results("SELECT option_id, option_name, option_value FROM ".$wpdb->base_prefix . $_POST['dmf_source_id'] . "_options WHERE option_id != 1 ORDER BY option_id", ARRAY_A);
				include_once('views/show_optionlist.php');
                break;

			case 'confirm': // process action
				include_once('views/show_submitlist.php');
				break;

			case 'save': // process action
				$arr = array();
                $blogs = array();

                //var_dump($_POST);

				//filling an array of checked items
				foreach($_POST as $key=>$value)
				{
					if (substr($key, 0, 3) == "ch-")
					{
						if ($value == "on"){
							$arr[substr($key, 3)] = "";
						}
					}
					else
					{
						if (array_key_exists($key, $arr))
							$arr[$key] = $value;
					}

					if (substr($key, 0, 5) == "blog-")
						$blogs[] = substr($key, 5);
				}
//var_dump($blogs);
                //copy options
				echo '<div class="wrap" id="center-panel">';
				echo '<h2 class="r">'. __('Copy options log', 'copy-option-in-wpmu').'</h2>';
				foreach($arr as $key=>$value)
				{
					$value = str_replace('\\\\\\', '', $value);
					foreach($blogs as $blog_id)
					{
						$wpdb->update(
							$this->get_blog_OptionsTbName($blog_id),
							array('option_value' => $value),
							array('option_name' => $key),
							array('%s'),
							array('%s')
						);

						echo "blog: ".$blog_id." ".$key." => ".$value."<br>";
					}
				}
				break;

			default:
				$this->dmf_tools_WPMUCopyOptions_page_main_output();
		}
	}

	/* CLASS STATIC FUNCTIONS */
	function get_blog_OptionsTbName($blog_id)
	{
		global $wpdb;

		if ($blog_id == "1"){
			return $wpdb->base_prefix . 'options';}
		else
			return $wpdb->base_prefix . $blog_id . '_options';
	}
}


	/* STATIC FUNCTIONS */
	function get_bloglist() {
		global $wpdb;

		$blogs = $wpdb->get_results("SELECT blog_id, domain FROM $wpdb->blogs where blog_id !=1 ORDER BY blog_id", ARRAY_A);
		$ret = array();

		$i = 0;
		foreach ($blogs as $row) {
			$ret[$i]['blog_id'] = $row['blog_id'];
			$ret[$i]['domain'] = $row['domain'];
			$blog_tb = $wpdb->base_prefix . $row['blog_id'] . '_options';	// the wp id of the source database
			$blogname = $wpdb->get_var("SELECT option_value FROM $blog_tb WHERE option_id = 2");
			$ret[$i]['blogname'] = $blogname;
			$i++;
		}

		return $ret;
	}

$WPMUCopyOptions_ = new WPMUCopyOptions();

?>
