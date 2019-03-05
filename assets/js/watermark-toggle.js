/**
 * PDF Settings
 * Dependancies: jQuery
 */

(function ($) {

  /**
   * Show the Watermark Text fields
   *
   * @since 1.0
   */
  function show () {
    $('.gfpdf-watermark-text').show()
    $('.gfpdf-watermark-text-font').show()
  }

  /**
   * Hide the Watermark Text fields
   *
   * @since 1.0
   */
  function hide () {
    $('.gfpdf-watermark-text').hide().find('input').val('')
    $('.gfpdf-watermark-text-font').hide().find('select').val('')
  }

  $(function () {
    var checkbox = $('#gfpdf_settings\\[watermark_toggle\\]')
    checkbox.click(function () {
      $(this).is(':checked') ? show() : hide()
    })

    if (checkbox.is(':checked')) {
      show()
    }
  })
})(jQuery)