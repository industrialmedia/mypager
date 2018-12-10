(function ($, Drupal) {
  "use strict";

  // Cached reference to $(window).
  var $window = $(window);

  // The selector for both manual load.
  var pagerSelector = '[data-drupal-views-mypager]';

  // The selector for the pager.
  var contentWrapperSelector = '[data-drupal-views-mypager-content-wrapper]';




  /**
   * Insert a views mypager view into the document.
   *
   * @param {jQuery} $newView
   *   New content detached from the DOM.
   */
  $.fn.mypagerInsertView = function ($newView) {
    // Extract the view DOM ID from the view classes.
    var matches = /(js-view-dom-id-\w+)/.exec(this.attr('class'));
    var currentViewId = matches[1].replace('js-view-dom-id-', 'views_dom_id:');

    // Get the existing ajaxViews object.
    var view = Drupal.views.instances[currentViewId];
    // Remove once so that the exposed form and pager are processed on
    // behavior attach.
    view.$view.removeOnce('ajax-pager');
    view.$exposed_form.removeOnce('exposed-form');
    // Make sure mypager can be reinitialized.
    var $existingPager = view.$view.find(pagerSelector);
    $existingPager.removeOnce('mypager');

    var $newRows = $newView.find(contentWrapperSelector).children();
    var $newPager = $newView.find('.js-pager__items');

    // Add the new rows to existing view.
    view.$view.find(contentWrapperSelector).append($newRows);
    // Replace the pager link with the new link and ajaxPageState values.
    $existingPager.replaceWith($newPager);

    // Run views and VIS behaviors.
    Drupal.attachBehaviors(view.$view[0]);
  };



})(jQuery, Drupal);
