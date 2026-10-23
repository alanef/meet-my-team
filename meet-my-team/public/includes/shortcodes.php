<?php

/**
 * Meet My Team Shortcodes
 *
 * @package   Meet My Team
 * @author    Aaron Lee <aaron.lee@buooy.com>
 * @license   GPL-2.0+
 * @link      http://buooy.com
 * @copyright 2014 Buooy
 */
class Meet_My_Team_Shortcodes {


	/* Necessary Variables */
	protected $parent_container;
	protected $parent_container_class;
	protected $parent_container_id;
	protected $row_container_class;
	protected $item_container;
	protected $item_container_class;
	protected $cols;
	protected $center;
	protected $display_picture;
	protected $show_groups;
	protected $disable_modal_centering;
	/**
	 * @var mixed
	 */
	private $debug;
	/**
	 * @var mixed|string
	 */
	private $align;
	/**
	 * @var mixed|string
	 */
	private $enable_modal;

	public function __construct() {
	}


	/**
	 *    Extracts the shortcode atts
	 *
	 * @since 1.0.0
	 */
	public function extract_shortcodes( $atts ) {
		extract( shortcode_atts( array(

			'parent_container'       => 'div',
			'parent_container_class' => '',
			'parent_container_id'    => uniqid( 'mmt' ),

			'row_container_class' => 'row-fluid',

			'item_container'       => 'div',
			'item_container_class' => '',

			'cols' => '3',

			'align' => 'center',

			'display_picture' => 'true',

			'enable_modal' => 'true',

			'show_groups' => '',

			'disable_modal_centering' => 'false',

			'debug' => 'false',

		), $atts ) );

		// Container tags come from shortcode attributes, so only these are accepted
		$this->parent_container        = self::container_tag( $parent_container );
		$this->parent_container_id     = $parent_container_id;
		$this->parent_container_class  = $parent_container_class;
		$this->row_container_class     = $row_container_class;
		$this->item_container          = self::container_tag( $item_container );
		$this->show_groups             = $show_groups;
		// public.js only acts on 'true' (or 1) for these two
		$this->debug                   = in_array( strtolower( trim( $debug ) ), array( 'true', '1' ), true ) ? 'true' : 'false';
		$this->disable_modal_centering = 'true' === $disable_modal_centering ? 'true' : 'false';

		if ( $cols == "1" || $cols == "2" || $cols == "3" || $cols == "4" || $cols == "6" ) {
			$this->cols = $cols;
		} else {
			$this->cols = "3";
		}

		if ( $item_container_class == '' ) {
			$this->item_container_class = "col-mmt-" . ( 12 / $this->cols );
		} else {
			$this->item_container_class = $item_container_class;
		}

		// Documented values only. Each keeps the behaviour the old pass-through had: public.js centres
		// only on 'center', the picture shows only for 'true', and the modal is off for values jQuery
		// reads as false ('false', '', 0).
		$this->align = 'center' === $align ? 'center' : 'left';

		$this->display_picture = 'true' === $display_picture ? 'true' : 'false';

		$modal_off          = in_array( strtolower( trim( $enable_modal ) ), array( 'false', '' ), true ) || ( is_numeric( $enable_modal ) && 0 == $enable_modal );
		$this->enable_modal = $modal_off ? 'false' : 'true';

	}

	/**
	 * A container tag from a shortcode attribute, or 'div' if it isn't one of the allowed tags
	 *
	 * @param string $tag Requested tag name.
	 * @return string
	 */
	private static function container_tag( $tag ) {
		$tag = strtolower( trim( (string) $tag ) );

		return in_array( $tag, array( 'div', 'section', 'article', 'aside', 'ul', 'ol', 'li', 'span' ), true ) ? $tag : 'div';
	}

	/**
	 * Create Shortcodes
	 *
	 * @since    1.0.0
	 */
	public function display( $atts ) {

		// Extracts the shortcodes 
		$this->extract_shortcodes( $atts );

		// Gets the team members
		$team_members_list = $this->get_team_members();

		// Create the mmt display
		$mmt = "<" . tag_escape( $this->parent_container ) . " 
						data-debug='" . esc_attr( $this->debug ) . "'
						data-align='" . esc_attr( $this->align ) . "' 
						data-enable-modal = '" . esc_attr( $this->enable_modal ) . "' 
						data-disable-modal-centering='" . esc_attr( $this->disable_modal_centering ) . "'
						class='mmt_container " . esc_attr( $this->parent_container_class ) . "' id='" . esc_attr( $this->parent_container_id ) . "'>";

		$team_members = array();
		foreach ( $team_members_list as $index => $member ) {

			if ( $index % ( $this->cols ) == 0 ) {
				$mmt .= '<div class="mmt_row ' . esc_attr( $this->row_container_class ) . '">';
			}

			$member_details                 = array();
			$member_details['id']           = $member->ID;
			$member_details['name']         = $member->post_title;
			$member_details['designation']  = get_post_meta( $member->ID, 'mmt_designation', true );
			$member_details['email']        = get_post_meta( $member->ID, 'mmt_email', true );
			$member_details['bio_picture']  = get_post_meta( $member->ID, 'mmt_bio_picture', true );
			$member_details['biography']    = apply_filters( 'the_content', get_post_meta( $member->ID, 'mmt_biography', true ) );
			$member_details['personal_url'] = get_post_meta( $member->ID, 'mmt_url', true );


			$mmt .= $this->build_single_member( $member_details );

			if ( $index % ( $this->cols ) == ( $this->cols - 1 ) ) {
				$mmt .= '</div><!-- ' . esc_html( $this->row_container_class ) . ' -->';
			}

		}

		$mmt .= "</" . tag_escape( $this->parent_container ) . ">";

		return $mmt;

	}

	/**
	 *    Extracts the list of team members
	 */
	private function get_team_members() {

		$cats     = explode( ',', $this->show_groups );
		$cat_list = '';
		foreach ( $cats as $cat ) {
			if ( get_cat_ID( $cat ) != 0 ) {
				$cat_list .= get_cat_ID( $cat ) . ",";
			}
		}

		$args = array(
			'post_type'      => 'team_members',
			'posts_per_page' => - 1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'category'       => $cat_list,
			//'post_status'      	=> 	'publish'
		);

		$team_members = get_posts( $args );

		return $team_members;

	}

	/**
	 *    Builds a single member
	 */
	private function build_single_member( $details ) {

		// Build the Modal
		$id    = (int) $details['id'];
		$modal = '	<div id="mmt_member_' . $id . '" class="reveal-modal">';

		if ( $details['bio_picture'] != '' ) {
			$modal .= '<div class="mmt_bio_picture"><img src="' . esc_url( $details['bio_picture'] ) . '"></div>';
		}

		$modal .= '<h4>' . force_balance_tags( wp_kses_post( $details['name'] ) ) . '</h4>
						<h6>' . esc_html( $details['designation'] ) . '</h6>';

		if ( $details['email'] != '' ) {
			$modal .= '<a href="' . esc_url( 'mailto:' . $details['email'] ) . '" target="_blank">' . esc_html( $details['email'] ) . '</a>';
		}

		$modal .= '<div>' . wp_kses_post( $details['biography'] ) . '</div>';

		if ( $details['personal_url'] != '' ) {
			$modal .= '<a href="' . esc_url( $details['personal_url'] ) . '" target="_blank">Personal Link</a>';
		}

		$modal .= '		<a class="close-reveal-modal">&#215;</a>
					</div>';

		// Build the initial display
		$display = '<' . tag_escape( $this->item_container ) . ' data-reveal-id="mmt_member_' . $id . '" class="mmt ' . esc_attr( $this->item_container_class ) . '">';
		if ( $this->display_picture == 'true' && $details['bio_picture'] != '' ) {
			$display .= '<div class="mmt_member_img"><img src="' . esc_url( $details['bio_picture'] ) . '" alt="' . esc_attr( wp_strip_all_tags( $details['name'] ) ) . '" ></div>';
		}
		$display .= '<h4>' . force_balance_tags( wp_kses_post( $details['name'] ) ) . '</h4>';
		$display .= '<h6>' . esc_html( $details['designation'] ) . '</h6>';
		$display .= '</' . tag_escape( $this->item_container ) . '>';

		return $modal . $display;
	}


}