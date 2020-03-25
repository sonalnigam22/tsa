/**
 * @file
 * Global utilities.
 *
 */
(function ($, Drupal) {

  'use strict';

  Drupal.behaviors.theme_tsa_gov = {
    attach: function (context, settings) {

    }
  };
  
    $("#menu-icon1").on("click", function(){ 
      $(".mega-dropdown").slideToggle();
      $(this).toggleClass("active");
	});

})(jQuery, Drupal);
