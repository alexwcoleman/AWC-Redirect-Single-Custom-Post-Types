<?php

if ( ! defined( 'ABSPATH' ) ) exit;

 class AWC_Redirect {
	private $awc_redirect_options;
	public $non_archived_posts = array();

	public function __construct() {
		if( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'awc_redirect_add_plugin_page' ) );
			add_action( 'admin_init', array( $this, 'awc_redirect_page_init' ) );
		}
		add_action( 'template_redirect', array( $this, 'AWC_template_redirect') );
	}

	public function awc_redirect_add_plugin_page() {
		add_options_page(
			'AWC Redirect', // page_title
			'AWC Redirect', // menu_title
			'manage_options', // capability
			'awc-redirect', // menu_slug
			array( $this, 'awc_redirect_create_admin_page' ) // function
		);
	}

	public function awc_redirect_create_admin_page() {
		$this->awc_redirect_options = get_option( 'awc_redirect_option_name' ); ?>

		<div class="wrap">
			<h2>AWC Redirect</h2>
			<p><strong>Single Post Redirecting.</strong></p>
			<p>Choose to redirect a single Custom Post Type to its Archive Page. Only post types that have <code>has_archive => true</code> will show up.</p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'awc_redirect_option_group' );
					do_settings_sections( 'awc-redirect-admin' );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function awc_redirect_page_init() {
		register_setting(
			'awc_redirect_option_group', // option_group
			'awc_redirect_option_name', // option_name
			array( $this, 'awc_redirect_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'awc_redirect_setting_section', // id
			'Settings', // title
			array( $this, 'awc_redirect_section_info' ), // callback
			'awc-redirect-admin' // page
		);

		add_settings_field(
			'which_post_type_0', // id
			'Which Post Type?', // title
			//array( $this, 'which_post_type_0_callback' ), // callback
			array( $this, 'AWC_get_post_types'), // callback
			'awc-redirect-admin', // page
			'awc_redirect_setting_section' // section
		);
	}

	public function awc_redirect_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['which_post_type_0'] ) ) {
			$sanitary_values['which_post_type_0'] = $input['which_post_type_0'];
		}

		return $sanitary_values;
	}

	public function awc_redirect_section_info() {
		
	}

	public function AWC_get_post_types(){
		$post_types = $this->AWC_list_post_types();
		$op = '';
		 
		 foreach ( $post_types  as $post_type ) {
			$op .= $this->AWC_get_post_details( $post_type );
		 }
		 echo $op;
		 $this->test();
	}

	public function AWC_list_post_types(){
		$args = array(
			'public'   => true,
			'_builtin' => false
		 );
		 
		 $output = 'names'; // names or objects, note names is the default
		 $operator = 'and'; // 'and' or 'or'
		 $op = '';
		 
		 $post_types = get_post_types( $args, $output, $operator );
		 return $post_types;
	}

	public function AWC_get_post_details( $post_type ){
		if( !$post_type ){
			return;
		}
		$op = get_post_type_object( $post_type );
		if( false === $op->has_archive ){
			return;
		}
		$name = $op->name;
		$label = $op->label;
		$archive_link = get_post_type_archive_link( $post_type );

		// Had to do the following because array_push doesn't work on an empty variable... 
		// ...so set it with the first, push with the rest.
		if( empty( $this->non_archived_posts ) ){
			$this->non_archived_posts[] = $name;
		} else{
			array_push( $this->non_archived_posts, $name );
		}
		// var_dump($this->non_archived_posts);
		
		$post_info = [
			'name' => $op->name,
			'labe' => $op->label,
		];
		$post_checkbox = '<label><input type="checkbox" name="' . $name . '" value="' . $label . '">' . $label . '</label>
		<a href="' . $archive_link . '" target="_blank">Current ' . $op->label . ' Archive</a>
		<br>';
		// printf()
		return $post_checkbox;
	}

	public function test(){
		$post_types = $this->AWC_list_post_types();
		var_dump($post_types);
		// foreach( $post_types as $cpt ){
		// 	var_dump( $cpt );
		// }
	}

	public function AWC_template_redirect(){
		$post_types = $this->AWC_list_post_types();
		foreach( $post_types as $cpt ){
			if ( is_singular( $cpt ) && get_post_type_object( $cpt )->has_archive ) {
				// var_dump( "$cpt" );
				$redirectLink = get_post_type_archive_link( $cpt );
				wp_redirect( $redirectLink, 302 );
				exit;
			}
			// var_dump( $cpt );
		}
	}

}
// if ( is_admin() )
	$awc_redirect = new AWC_Redirect();

/* 
 * Retrieve this value with:
 * $awc_redirect_options = get_option( 'awc_redirect_option_name' ); // Array of All Options
 * $hello_0 = $awc_redirect_options['hello_0']; // hello
 */

//  $awc_redirect_options = get_option( 'awc_redirect_option_name' ); // Array of All Options
$awc_redirect_options = get_option( 'awc_redirect_option_name' ); // Array of All Options
 if( is_admin() ){
	 var_dump($awc_redirect_options);
 }