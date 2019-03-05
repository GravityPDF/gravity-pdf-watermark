<?php

namespace GFPDF\Plugins\Watermark\TextWatermark\Options;

use GFPDF\Helper\Helper_Trait_Logger;
use GFPDF\Helper\Helper_Misc;
use Monolog\Logger;

/**
 * @package     Gravity PDF Watermark
 * @copyright   Copyright (c) 2019, Blue Liquid Designs
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
	This file is part of Gravity PDF Watermark.

	Copyright (c) 2019, Blue Liquid Designs

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * Class AddTextWatermarkFields
 *
 * @package GFPDF\Plugins\Watermark\TextWatermark\Options
 */
class AddTextWatermarkFields {

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
				'watermark_toggle' => [
					'id'    => 'watermark_toggle',
					'name'  => esc_html__( 'Watermark', 'gravity-pdf-watermark' ),
					'desc'  => esc_html__( 'Enable Watermark in Preview', 'gravity-pdf-watermark' ),
					'type'  => 'checkbox',
					'class' => 'gfpdf-watermark-toggle',
				],

				'watermark_text' => [
					'id'    => 'watermark_text',
					'type'  => 'text',
					'name'  => esc_html__( 'Watermark Text', 'gravity-pdf-watermark' ),
					'class' => 'gfpdf-watermark-text gfpdf-hidden',
				],

				'watermark_text_font' => [
					'id'         => 'watermark_text_font',
					'name'       => esc_html__( 'Watermark Font', 'gravity-pdf-watermark' ),
					'type'       => 'select',
					'options'    => $options->get_installed_fonts(),
					'std'        => $options->get_option( 'default_font' ),
					'inputClass' => 'large',
					'chosen'     => true,
					'class'      => 'gfpdf-watermark-text-font gfpdf-hidden',
				],
			];

			$settings += $watermark_settings;

			$this->logger->notice( 'Add "text watermark" fields to settings' );
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
			}
		}
	}
}
