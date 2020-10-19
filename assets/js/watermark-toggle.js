/**
 * PDF Settings
 * Dependancies: jQuery
 */

(function ($) {
  $(function () {
    var $checkbox = $('#gfpdf_settings\\[watermark_toggle\\]')
    var $fields = $('.gfpdf-watermark')

    $checkbox.click(function () {
      $fields.toggle()
    })

    if ($checkbox.is(':checked')) {
      $fields.show()
    }
  })
})(jQuery)
