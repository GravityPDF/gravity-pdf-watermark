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
class TestAddWatermarkFields extends WP_UnitTestCase {

	/**
	 * @var AddWatermarkFields
	 * @since 1.0
	 */
	private $class;

	/**
	 * @since 1.0
	 */
	public function setUp() {

		$this->class = new AddWatermarkFields( \GPDFAPI::get_misc_class() );
		$this->class->set_logger( $GLOBALS['GFPDF_Test']->log );
		$this->class->init();
	}

	/**
	 * @since 1.0
	 */
	public function test_add_filter() {
		$settings = apply_filters( 'gfpdf_form_settings_appearance', [] );
		$this->assertCount( 5, $settings );

		$settings = apply_filters(
			'gfpdf_form_settings_appearance',
			[
				'item1' => true,
				'item2' => true,
			]
		);

		$this->assertCount( 7, $settings );

		add_filter( 'gfpdf_display_text_watermark', '__return_false' );
		$settings = apply_filters( 'gfpdf_form_settings_appearance', [] );
		$this->assertCount( 0, $settings );
	}

	/**
	 * @since 1.0
	 */
	public function test_add_options() {

		/* Fail the test */
		$wp_scripts = wp_scripts();
		$wp_styles  = wp_styles();

		$this->assertArrayNotHasKey( 'gfpdf_js_watermark', $wp_scripts->registered );
		$this->assertArrayNotHasKey( 'gfpdf_css_watermark', $wp_styles->registered );

		/* Replicate the Gravity PDF settings admin page */
		set_current_screen( 'dashboard' );
		$_GET['page'] = 'gfpdf-';
		$_GET['id']   = 1;
		$_GET['pid']  = 1;

		$this->class->load_admin_assets();

		$wp_scripts = wp_scripts();
		$wp_styles  = wp_styles();

		$this->assertArrayHasKey( 'gfpdf_js_watermark', $wp_scripts->registered );
		$this->assertArrayHasKey( 'gfpdf_css_watermark', $wp_styles->registered );
	}
}
