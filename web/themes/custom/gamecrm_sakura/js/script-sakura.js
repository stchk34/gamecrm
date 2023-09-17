(function ($, Drupal) {
  Drupal.behaviors.conditions = {
    attach: function(context, settings) {
      // Attach change event to the first checkbox
      $('#edit-field-reward-1', context).change(function() {
        if ($(this).is(':checked')) {
          $('#edit-field-number-of-points-0-value').val(" ");
        }
      });

      // Attach change event to the second checkbox
      $('#edit-field-reward-2', context).change(function() {
        if ($(this).is(':checked')) {
          $('#edit-field-badge-3, #edit-field-badge-1, #edit-field-badge-2').prop('checked', false);
        }
      });
    }
  };
})(jQuery, Drupal);

jQuery(document).ready(function($) {
  $('#edit-field-number-of-points-0-value').on('mousewheel DOMMouseScroll', function(e) {
    e.preventDefault();
  });
});
