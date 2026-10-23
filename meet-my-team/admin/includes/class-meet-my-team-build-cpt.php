<?php
/**
 * Meet My Team
 *
 * @package   Meet My Team
 * @author    Aaron Lee <aaron.lee@buooy.com>
 * @license   GPL-2.0+
 * @link      http://buooy.com
 * @copyright 2014 Buooy
 */
class Meet_My_Team_Build_Cpt {

	/* All Necessary Variables */
	protected $post_type 	= 'team_members';
	protected $text_domain 	= 'meet-my-team';
	protected $post_name 	= 'Team Members';
	protected $menu_name 	= 'Meet My Team';
	protected $parent_item 	= 'Parent Team Member';
	protected $items_name 	= 'Members';
	protected $item_name 	= 'Member';
	protected $meta_box_title = 'Additional Details';
	protected $description  = 'Team members is a custom post type for Meet My Team';

	protected $tax_name_lower = 'group';
	protected $tax_name_upper = 'Group';

	/* Custom Fields */
	public $mmt_meta_boxes = array();



	public function __construct() {
	}

	/**
	 * 	-	Builds the Meet My team Custom Post Type
	 *	
	 *	POST TYPE: team_members
	 *
	 * @since    1.0.0
	 */
	public function build_custom_post_type() {
		
		// Builds the team_member post type
		$labels = array(
			'name'                => _x( 'Team Members', 'Post Type General Name', 'meet-my-team' ),
			'singular_name'       => _x( 'Team Members', 'Post Type Singular Name', 'meet-my-team' ),
			'menu_name'           => __( 'Meet My Team', 'meet-my-team' ),
			'parent_item_colon'   => __( 'Parent Team Member', 'meet-my-team' ),
			'all_items'           => __( 'All Members', 'meet-my-team' ),
			'view_item'           => __( 'View Member', 'meet-my-team' ),
			'add_new_item'        => __( 'Add New Member', 'meet-my-team' ),
			'add_new'             => __( 'Add New', 'meet-my-team' ),
			'edit_item'           => __( 'Edit Member', 'meet-my-team' ),
			'update_item'         => __( 'Update Member', 'meet-my-team' ),
			'search_items'        => __( 'Search Member', 'meet-my-team' ),
			'not_found'           => __( 'Not found', 'meet-my-team' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'meet-my-team' ),
		);
		$args = array(
			'label'               => __( 'Team Members', 'meet-my-team' ),
			'description'         => __( 'Team members is a custom post type for Meet My Team', 'meet-my-team' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'page-attributes' ),
			'taxonomies'          => array( "category" ),
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 70,
			'menu_icon'           => 'dashicons-groups',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);
		register_post_type( $this->post_type, $args );

	}


	/**
	 * Initialize the metabox class.
	 */
	public function cmb_initialize_mmt_meta_boxes() {

		if ( ! class_exists( 'cmb_Meta_Box' ) ){
			require_once 'Custom-Metaboxes-and-Fields/init.php';
		}

	}


	/**
	 * Define the metabox and field configurations.
	 *
	 * @param  array $meta_boxes
	 * @return array
	 */
	public function mmt_metaboxes() {

		global $meta_boxes;

		$prefix = 'mmt_'; // Prefix for all fields

	    $meta_boxes[$this->post_type.'_metabox'] = array(
	        'id' 		=> 	$this->post_type.'_metabox',
	        'title' 	=> 	$this->post_name.' Details',
	        'pages' 	=> 	array( $this->post_type ), // post type
	        'context' 	=> 'normal',
	        'priority' 	=> 'high',
	        'show_names' => true, // Show field names on the left
	        'fields' => array(

	        	/* Bio Picture */
	            array(
	                'name' 	=> 'Bio Picture',
	                'desc' 	=> 'Member\'s Bio Picture',
	                'id'	=> $prefix . 'bio_picture',
	                'type' 	=> 'file',
	                'save_id' => false, // save ID using true
	                'options' => array('url', 'attachment'),
	            ),

	        	/* Designation */
	            array(
	                'name' 	=> 'Designation',
	                'desc' 	=> 'Member\'s Designation',
	                'id'	=> $prefix.'designation',
	                'type' 	=> 'text',
	                'options' => array(),
	            ),

	            /* Email */
	            array(
	                'name' 	=> 'Email',
	                'desc' 	=> 'Member\'s Email',
	                'id'	=> $prefix . 'email',
	                'type' 	=> 'text',
	                'options' => array(),
	            ),

	            /* Biography */
	            array(
	                'name' 	=> 'Biography',
	                'desc' 	=> 'Member\'s Biography',
	                'id'	=> $prefix . 'biography',
	                'type' 	=> 'wysiwyg',
	                'options' => array(),
	            ),

	            /* Personal URL */
	            array(
	                'name' 	=> 'Personal URL',
	                'desc' 	=> 'Member\'s URL',
	                'id'	=> $prefix . 'url',
	                'type' 	=> 'text',
	                'options' => array(),
	            ),
	        ),
	    );

		return $meta_boxes;
	}

}