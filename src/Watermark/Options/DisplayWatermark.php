<?php

namespace GFPDF\Plugins\Watermark\Watermark\Options;

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
			$text    = ! empty( $settings['watermark_text'] ) ? $settings['watermark_text'] : false;
			$font    = ! empty( $settings['watermark_text_font'] ) ? $settings['watermark_text_font'] : 'DejavuSansCondensed';
			$opacity = isset( $settings['watermark_opacity'] ) ? ( (float) $settings['watermark_opacity'] + 0.01 ) / 100 : 0.2;

			/* Add the image watermark */
			$image_path = $this->misc->convert_url_to_path( $image );
			if ( $image_path !== false && is_file( $image_path ) ) {
				$mpdf->SetWatermarkImage( $image_path, $opacity );
			} else {
				$mpdf->SetWatermarkImage( $image, $opacity );
			}

			/* Add the text watermark */
			$mpdf->SetWatermarkText( $text, $opacity );
			$mpdf->watermark_font = $font;
		}

		return $mpdf;
	}
}
