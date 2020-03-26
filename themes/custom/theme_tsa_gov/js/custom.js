(function ($, Drupal, drupalSettings) {
	Drupal.behaviors.menu = {
		attach: function (context, settings) {
		  $(document).ready(function(){
             $("#block-exposedformjobspage-1 .js-form-item-keyword").addClass("col-sm-4 mb-2");
             $("#block-exposedformjobspage-1 .js-form-item-addresstext").addClass("col-sm-4 mb-2");
             $("#block-exposedformjobspage-1 .js-form-item-filter").addClass("col-sm-3 mb-2");
             $("#block-exposedformjobspage-1 .form-actions").addClass("col-sm-1 mb-2");
			 //$(".block-views-exposed-filter-blockjobs-page-1").addClass("col-md-11 mx-auto");
			 $("#views-exposed-form-jobs-page-1 .js-form-item-keyword").addClass("col-sm-4 mb-2");
			 $("#views-exposed-form-jobs-page-1 .js-form-item-addresstext").addClass("col-sm-4 mb-2");
			 $("#views-exposed-form-jobs-page-1 .js-form-item-filter").addClass("col-sm-3 mb-2");
			 $("#views-exposed-form-jobs-page-1 .form-actions").addClass("col-sm-1 mb-2");
			 
			 $(".toggle-accordion").on("click", function() {
				var accordionId = $(this).attr("accordion-id"),
				  numPanelOpen = $(accordionId + ' .collapse.show').length;
				  
				
				$(this).toggleClass("active");

				if (numPanelOpen == 0) {
				  openAllPanels(accordionId);
				} else {
				  closeAllPanels(accordionId);
				}
              })

			  openAllPanels = function(aId) {
				console.log("setAllPanelOpen");
				$(aId + ' .panel-collapse:not(".show")').collapse('show');
			  }
			  closeAllPanels = function(aId) {
				console.log("setAllPanelclose");
				$(aId + ' .panel-collapse.show').collapse('hide');
			  }
			  
			  $(document).on('click', '.socialShare', function() {
   
					  var self = $(this);
					  var id   = $(this).attr('id');
					  var element = $('#'+id+' a');
					  var c = 0;

					  if (self.hasClass('animate')) {
						return;
					  }

					  if (!self.hasClass('open')) {

						self.addClass('open');

						TweenMax.staggerTo(element, 0.3, {
							opacity: 1,
							visibility: 'visible'
						  },
						  0.075);
						TweenMax.staggerTo(element, 0.3, {
							top: -12,
							ease: Cubic.easeOut
						  },
						  0.075);

						TweenMax.staggerTo(element, 0.2, {
							top: 0,
							delay: 0.1,
							ease: Cubic.easeOut,
							onComplete: function() {
							  c++;
							  if (c >= element.length) {
								self.removeClass('animate');
							  }
							}
						  },
						  0.075);

						self.addClass('animate');

					  } else {

						TweenMax.staggerTo(element, 0.3, {
							opacity: 0,
							onComplete: function() {
							  c++;
							  if (c >= element.length) {
								self.removeClass('open animate');
								element.css('visibility', 'hidden');
							  };
							}
						  },
						  0.075);
					  }
				});
				
				var elementOffset = $('#tsa').offset().top;
				var scrollHeight = $('#tsa').height();
				$(window).on("scroll", function() {
					if ($(window).scrollTop() > (elementOffset - (scrollHeight + 160))) {
						$('#tsa').addClass('fixed-tsa');
					} else {
						$('#tsa').removeClass('fixed-tsa');
					}   
				});
				
				$(function() {
					  $('#tsa a[href*="#"]').smoothscroll({duration:  1000,
						  easing: 'easeOutCirc',
						  offset: 0,
						  hash: false,});
					});
		        });
		  
		}
	};
})(jQuery, Drupal, drupalSettings);