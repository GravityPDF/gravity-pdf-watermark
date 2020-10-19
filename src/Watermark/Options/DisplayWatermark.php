<?php

namespace GFPDF\Plugins\Watermark\Watermark\Options;

use GFPDF\Helper\Helper_Form;
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
 * Class DisplayWatermark
 *
 * @package GFPDF\Plugins\Watermark\Watermark\Options
 */
class DisplayWatermark {

	/**
	 * @since 1.0
	 */
	use Helper_Trait_Logger;

	/**
	 * @var Helper_Misc
	 *
	 * @since 1.0
	 */
	protected $misc;

	/**
	 * @var Helper_Form
	 *
	 * @since 1.1
	 */
	protected $gform;

	/**
	 * AddTextWatermarkFields constructor.
	 *
	 * @param Helper_Misc $misc
	 * @param Helper_Form $gform
	 *
	 * @since 1.0
	 */
	public function __construct( Helper_Misc $misc, Helper_Form $gform ) {
		$this->misc  = $misc;
		$this->gform = $gform;
	}

	/**
	 * Initialise our module
	 *
	 * @since 1.0
	 */
	public function init() {
		$this->add_filters();
	}

	/**
	 * @since 1.0
	 */
	public function add_filters() {
		add_filter( 'gfpdf_mpdf_post_init_class', [ $this, 'add_watermark_support' ], 10, 4 );
	}

	/**
	 * Add the watermark text / image to the PDF, if one is not already set
	 *
	 * @param  \Mpdf\Mpdf $mpdf
	 * @param array       $form     Current Gravity Form
	 * @param array       $entry    Current Gravity Forms Entry
	 * @param array       $settings Current PDF Settings
	 *
	 * @return \Mpdf\Mpdf
	 *
	 * @since  1.0
	 */
	public function add_watermark_support( $mpdf, $form, $entry, $settings ) {
		if ( ! empty( $settings['watermark_toggle'] ) && empty( $mpdf->watermarkText ) && empty( $mpdf->watermarkImage ) ) {
			/* Transparency not supported in the following formats */
			$mpdf->PDFA = false;
			$mpdf->PDFX = false;

			$image   = ! empty( $settings['watermark_image'] ) ? $settings['watermark_image'] : false;
			$text    = ! empty( $settings['watermark_text'] ) ? $this->gform->process_tags( $settings['watermark_text'], $form, $entry ) : false;
			$font    = ! empty( $settings['watermark_text_font'] ) ? $settings['watermark_text_font'] : 'DejavuSansCondensed';
			$opacity = isset( $settings['watermark_opacity'] ) ? ( (float) $settings['watermark_opacity'] + 0.01 ) / 100 : 0.2;

			/* Add the image watermark */
			$image_path = $this->misc->convert_url_to_path( $image );
			if ( $image_path !== false && is_file( $image_path ) ) {
				$mpdf->SetWatermarkImage( $image_path, $opacity );
			} elseif ( $image !== false ) {
				$mpdf->SetWatermarkImage( $image, $opacity );
			}

			/* Add the text watermark */
			$namespace = version_compare( PDF_EXTENDED_VERSION, '6.0.0-beta1', '<' ) ? '\\Mpdf\\Utils\\UtfString' : '\\GFPDF_Vendor\\Mpdf\\Utils\\UtfString';
			$text      = $namespace::strcode2utf( htmlspecialchars_decode( $text, ENT_QUOTES ) );
			$mpdf->SetWatermarkText( $text, $opacity );
			$mpdf->watermark_font = $font;
		}

		return $mpdf;
	}
}
