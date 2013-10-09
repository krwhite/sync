  jQuery.noConflict();

  jQuery(document).ready(function($) {







$('.tips').tooltip();





// Groups Stuff



/*

if($('#groups-list').length > 0) {

	var $list = $('#viewList'),

	    $grid = $('#viewGrid'),

	    $filter = $('#filterGroups'),

	    $search = $('#searchGroups'),

	    $container = $('#groups-list');



	    function GridOn() {

	    	$container.isotope({

			    // options

			    itemSelector : '.item',

			    layoutMode: 'fitRows'

		  	});

	    	$container.removeClass('list_view').addClass('grid_view');

	    }

	    function GridOff() {

	    	$container.isotope( 'destroy' );

	    	$container.removeClass('grid_view').addClass('list_view');

	    }

	    GridOn();

	$list.click(function() {

		$(this).addClass('hide');

		$grid.removeClass('hide');

		GridOff();

	});	

	$grid.click(function() {

		$(this).addClass('hide');

		$list.removeClass('hide');

		GridOn();

	});



	$filter.click(function() {

		$(this).addClass('active');

	});

}

*/





});