<?php

namespace GFPDF\Plugins\Watermark\Watermark\Options;

use GFPDF\Helper\Helper_Misc;
use GFPDF\Helper\Helper_Trait_Logger;

/**
 * @package     Gravity PDF Watermark
 * @copyright   Copyright (c) 2020, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class AddWatermarkFields
 *
 * @package GFPDF\Plugins\Watermark\TextWatermark\Options
 */
class AddWatermarkFields {

	/**
	 * @since 1.0
	 */
	use Helper_Trait_Logger;

	/**
	 * @var Helper_Misc
	 *
	 * @since 1.0
	 */
	private $misc;

	/**
	 * AddTextWatermarkFields constructor.
	 *
	 * @param Helper_Misc $misc
	 *
	 * @since 1.0
	 */
	public function __construct( Helper_Misc $misc ) {
		$this->misc = $misc;
	}

	/**
	 * Initialise our module
	 *
	 * @since 1.0
	 */
	public function init() {
		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * @since 1.0
	 */
	public function add_actions() {
		add_action( 'admin_enqueue_scripts', [ $this, 'load_admin_assets' ] );
	}

	/**
	 * @since 1.0
	 */
	public function add_filters() {
		add_filter( 'gfpdf_form_settings_appearance', [ $this, 'add_options' ], 9999 );

	}

	/**
	 * Include the Text Watermark settings
	 *
	 * @param array $settings
	 *
	 * @return array
	 *
	 * @since 1.0
	 */
	public function add_options( $settings ) {

		$display = apply_filters( 'gfpdf_display_text_watermark', true, $settings );

		if ( $display ) {

			$options = \GPDFAPI::get_options_class();

			$watermark_settings = [
				'watermark_toggle'    => [
					'id'      => 'watermark_toggle',
					'name'    => esc_html__( 'Watermark', 'gravity-pdf-watermark' ),
					'desc'    => esc_html__( 'Enable PDF Watermark', 'gravity-pdf-watermark' ),
					'type'    => 'checkbox',
					'tooltip' => '<h6>' . esc_html__( 'Watermark', 'gravity-pdf-watermark' ) . '</h6>' . esc_html__( 'Toggle to display a text- or image-based watermark in the PDF (or both). When enabled, PDF/A-1b and PDF/X-1a formats are automatically disabled (regardless of the PDF Format you choose).', 'gravity-pdf-watermark' ),
				],

				'watermark_image'     => [
					'id'      => 'watermark_image',
					'name'    => esc_html__( 'Image Watermark', 'gravity-pdf-watermark' ),
					'type'    => 'upload',
					'class'   => 'gfpdf-watermark',
					'tooltip' => '<h6>' . esc_html__( 'Image Watermark', 'gravity-pdf-watermark' ) . '</h6>' . esc_html__( 'For the best results, ensure the image is the same dimensions as the Paper Size and use a transparent background.', 'gravity-pdf-watermark' ),
				],

				'watermark_text'      => [
					'id'         => 'watermark_text',
					'type'       => 'text',
					'name'       => esc_html__( 'Text Watermark', 'gravity-pdf-watermark' ),
					'class'      => 'gfpdf-watermark',
					'tooltip'    => '<h6>' . esc_html__( 'Text Watermark', 'gravity-pdf-watermark' ) . '</h6>' . esc_html__( 'Provided the font supports it, any valid UTF-8 character can be used. HTML tags are not supported.', 'gravity-pdf-watermark' ),
					'inputClass' => 'merge-tag-support mt-hide_all_fields', /* add merge tag support */
				],

				'watermark_text_font' => [
					'id'         => 'watermark_text_font',
					'name'       => esc_html__( 'Font', 'gravity-pdf-watermark' ),
					'type'       => 'select',
					'options'    => $options->get_installed_fonts(),
					'std'        => $options->get_option( 'default_font' ),
					'inputClass' => 'large',
					'chosen'     => version_compare( PDF_EXTENDED_VERSION, '6.0.0-beta1', '<' ),
					'class'      => 'gfpdf-watermark',
				],

				'watermark_opacity'   => [
					'id'      => 'watermark_opacity',
					'name'    => esc_html__( 'Opacity', 'gravity-pdf-watermark' ),
					'desc2'   => '%',
					'type'    => 'number',
					'size'    => 'small',
					'std'     => 20,
					'min'     => 1,
					'max'     => 100,
					'class'   => 'gfpdf-watermark',
					'tooltip' => '<h6>' . esc_html__( 'Opacity', 'gravity-pdf-watermark' ) . '</h6>' . esc_html__( 'Select a value between 0-100 to control the text and image watermark opacity. 0 = completely transparent; 100 = not transparent.', 'gravity-pdf-watermark' ),
				],
			];

			$settings += $watermark_settings;

			$this->logger->notice( 'Add watermark fields to PDF settings' );
		}

		return $settings;
	}

	/**
	 * @since 1.0
	 */
	public function load_admin_assets() {
		if ( $this->misc->is_gfpdf_page() ) {
			$form_id = ( isset( $_GET['id'] ) ) ? (int) $_GET['id'] : false;
			$pdf_id  = ( isset( $_GET['pid'] ) ) ? $_GET['pid'] : false;

			if ( $form_id !== false && $pdf_id !== false ) {
				$version = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? time() : GFPDF_WATERMARK_VERSION;

				wp_enqueue_script( 'gfpdf_js_watermark', plugins_url( 'assets/js/watermark-toggle.js', GFPDF_WATERMARK_FILE ), [ 'jquery' ], $version );

				wp_enqueue_style( 'gfpdf_css_watermark', plugins_url( 'assets/css/watermark.css', GFPDF_WATERMARK_FILE ), [], $version );
			}
		}
	}
}
