<?php

namespace GFPDF\Plugins\Watermark\Watermark\Options;

use GPDFAPI;

use WP_UnitTestCase;

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
 * Class TestAddWatermarkFields
 *
 * @package GFPDF\Tests\EnhancedLabels
 */
class TestDisplayWatermark extends WP_UnitTestCase {

	/**
	 * @var DisplayWatermark
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {

		$this->class = new DisplayWatermark( \GPDFAPI::get_misc_class() );
		$this->class->set_logger( $GLOBALS['GFPDF_Test']->log );
		$this->class->init();
	}

	/**
	 * @since 1.0
	 */
	public function test_add_filter() {

		/* Check nothing happens */
		$mpdf       = new \Mpdf\Mpdf( [ 'mode' => 'c' ] );
		$mpdf->PDFA = true;

		$mpdf = apply_filters( 'gfpdf_mpdf_post_init_class', $mpdf, [], [], [] );

		$this->assertTrue( $mpdf->PDFA );

		/* Check everything passes */
		$settings = [
			'watermark_toggle'    => 1,
			'watermark_image'     => __DIR__ . '/TestDisplayWatermark.php',
			'watermark_text'      => 'SAMPLE',
			'watermark_text_font' => 'DejaVuSans',
			'watermark_opacity'   => 50,
		];

		$mpdf       = new \Mpdf\Mpdf( [ 'mode' => 'c' ] );
		$mpdf->PDFA = true;
		$mpdf->PDFX = true;

		$mpdf = apply_filters( 'gfpdf_mpdf_post_init_class', $mpdf, [], [], $settings );

		$this->assertFalse( $mpdf->PDFA );
		$this->assertFalse( $mpdf->PDFX );
		$this->assertSame( $settings['watermark_image'], $mpdf->watermarkImage );
		$this->assertSame( $settings['watermark_text'], $mpdf->watermarkText );
		$this->assertSame( $settings['watermark_text_font'], $mpdf->watermark_font );
	}
}
