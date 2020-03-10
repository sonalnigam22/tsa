(function ($, Drupal, drupalSettings) {
	Drupal.behaviors.menu = {
		attach: function (context, settings) {
		  $(document).ready(function(){
             $("#block-exposedformjobspage-1 .js-form-item-keyword").addClass("col-sm-4 mb-2");
             $("#block-exposedformjobspage-1 .js-form-item-addresstext").addClass("col-sm-4 mb-2");
             $("#block-exposedformjobspage-1 .js-form-item-filter").addClass("col-sm-3 mb-2");
             $("#block-exposedformjobspage-1 .form-actions").addClass("col-sm-1 mb-2");
			 $(".block-views-exposed-filter-blockjobs-page-1").addClass("col-md-11 mx-auto");
			 $("#views-exposed-form-jobs-page-1 .js-form-item-keyword").addClass("col-sm-4 mb-2");
			 $("#views-exposed-form-jobs-page-1 .js-form-item-addresstext").addClass("col-sm-4 mb-2");
			 $("#views-exposed-form-jobs-page-1 .js-form-item-filter").addClass("col-sm-3 mb-2");
			 $("#views-exposed-form-jobs-page-1 .form-actions").addClass("col-sm-1 mb-2");
		  });
		}
	};
})(jQuery, Drupal, drupalSettings);