<?php

class AWC_Redirect {
	private $awc_redirect_options;
	public $non_archived_posts = array();

	public function __construct() {
		if( is_admin() ){
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
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'awc_redirect_option_group' );
					do_settings_sections( 'awc-redirect-admin' );
					submit_button();
				?>
			</form>
		</div>
		<?php var_dump(get_option( 'awc_redirect_option_name' )); ?>
	<?php }

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
			'awc_cpt', // id
			'Custom Post Types', // title
			array( $this, 'awc_cpt_callback' ), // callback
			'awc-redirect-admin', // page
			'awc_redirect_setting_section' // section
		);
	}

	public function awc_redirect_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['awc_cpt'] ) ) {
			$sanitary_values['awc_cpt'] = $input['awc_cpt'];
		}

		return $sanitary_values;
	}

	public function awc_redirect_section_info() {
		
	}

	public function awc_cpt_callback() { 
		?> <select name="awc_redirect_option_name[awc_cpt]" id="awc_cpt" multiple>
			<?php echo $this->AWC_get_post_types(); ?>
		</select> <?php
	}

	// get all post types that are archived => true
	public function AWC_get_post_types(){
		// $post_types = $this->AWC_list_post_types();
		$args = array(
			'public'   => true,
			'_builtin' => false
		);
		
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		
		$post_types = $this->AWC_list_post_types();
		$op = '';
		 
		 foreach ( $post_types  as $post_type ) {
			$op .= $this->AWC_get_post_details( $post_type );
		 }
		 return $op;
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

	// loop through and spit out into <select>. If they are set, mark as selected.
	public function AWC_get_post_details( $post_type ){
		$op = get_post_type_object( $post_type );
		if( !$post_type || false === $op->has_archive ){
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

		$selected = (isset( $this->awc_redirect_options['awc_cpt'] ) && $this->awc_redirect_options['awc_cpt'] === $name ) ? 'selected' : '';

		$select_options = '
				<option value="' . $name . '" ' . $selected . '>' . $label . '</option>
        ';
        
		return $select_options;
	}

	public function AWC_template_redirect(){
		$post_types = $this->AWC_list_post_types();
		foreach( $post_types as $cpt ){
			if ( is_singular( $cpt ) && get_post_type_object( $cpt )->has_archive ) {
				$redirectLink = get_post_type_archive_link( $cpt );
				wp_redirect( $redirectLink, 302 );
				exit;
			}
		}
	}

}

$awc_redirect = new AWC_Redirect();