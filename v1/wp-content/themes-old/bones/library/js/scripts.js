/*

Bones Scripts File

Author: Eddie Machado



This file should contain any js scripts you want to add to the site.

Instead of calling it in the header or throwing it inside wp_head()

this file will be called automatically in the footer so as not to

slow the page load.



*/



// IE8 ployfill for GetComputed Style (for Responsive Script below)

if (!window.getComputedStyle) {

    window.getComputedStyle = function(el, pseudo) {

        this.el = el;

        this.getPropertyValue = function(prop) {

            var re = /(\-([a-z]){1})/g;

            if (prop == 'float') prop = 'styleFloat';

            if (re.test(prop)) {

                prop = prop.replace(re, function () {

                    return arguments[2].toUpperCase();

                });

            }

            return el.currentStyle[prop] ? el.currentStyle[prop] : null;

        }

        return this;

    }

}



// as the page loads, call these scripts

jQuery(document).ready(function($) {



    /*

    Responsive jQuery is a tricky thing.

    There's a bunch of different ways to handle

    it, so be sure to research and find the one

    that works for you best.

    */



    /* getting viewport width */

    var responsive_viewport = $(window).width();



    /* if is below 481px */

    if (responsive_viewport < 481) {



    } /* end smallest screen */



    /* if is larger than 481px */

    if (responsive_viewport > 481) {



    } /* end larger than 481px */



    /* if is above or equal to 768px */

    if (responsive_viewport >= 768) {



        /* load gravatars */

        $('.comment img[data-gravatar]').each(function(){

            $(this).attr('src',$(this).attr('data-gravatar'));

        });



    }



    /* off the bat large screen actions */

    if (responsive_viewport > 1030) {



    }





	// add all your scripts here



    //global variables

    var global = {

            site_url : $('body').attr('data-url')

    };



// add tooltips to anything with the class tips

$('.tips').tooltip();



// add tooltips to anything with the class tips

$('.pops').popover({html:true});



//show more functionality for showing and hiding data

$('.showMore').on('click', function(){

    $(this).next('.showMoreData').toggleClass('hidden');

});





// add timeago to time

if($('.timeago').length > 0 ){

    $('.timeago').each(function(){

        var time = moment();

        var prettyTime = moment(time).fromNow();

        $(this).text(prettyTime);



    });



}







//js for conference rooms

if($('.conf-rooms').length > 0 ){

    var imagemap = '';

    var image = $('.conf-rooms').prev('img');

    $('.conf-rooms area').each(function(){

        var cords = $(this).attr('coords').split(','),

            title = $(this).attr('title'),

            content = $(this).attr('data-content');

        imagemap += '<a class="coords" href="#" style="left:'+cords[0]+'px; top:'+cords[1]+'px; width:'+ (cords[2]-cords[0]) +'px; height:'+ (cords[3]-cords[1]) +'px" title="'+ title+'" data-content="'+ content +'"></a>';



    });



    image.before(imagemap);

    $('.coords').popover({'placement' : 'left', 'html': true, 'trigger' : 'hover'});

    $('body').click (function(e){

        if(e.target.className == 'popover-content' || e.target.className == "popover-title" || e.target.className == "coords"){

            $('.coords').popover('hide');

            $(e.target).popover('show');

        }

        else{

            $('.coords').popover('hide');

        }

    });

}







//is js then use the custom search field for searching buddypress content

$('#searchform').addClass('hide');

$('#custom-search').removeClass('hide');



function submitSearch(){

    var searchTerm = $('#search-input').val();

    window.location = global.site_url+"/search/?search-terms="+ searchTerm;

}



$('#search-input').focus(function(){



    $(this).keypress(function(e){

        e.keyCode == '13' ? submitSearch() : null;

    });

    $('#search-submit').click(function(){

        submitSearch();

    });

});



//function for showing the search term

function getSearchTerm(){

    $('#results-header').html(getParam('search-terms'));



    $('.tab-pane').each(function(){

        var amount = $(this).find('.is-searchable').length;

        var badge = amount > 0 ? 'label-success' : null;

        $('#nav-tabs').find('a[href="#' + $(this).attr('id')+'"]').append('<span class="label '+ badge +'">'+ amount +'</span>');



    });

}



// function for parsing url for search query

function getParam(name) {

    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");

    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),

        results = regex.exec(location.search);

    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));

}

//is the results page visible?

$('#results-header').length > 0 ? getSearchTerm() : null;









// Groups Stuff

if($('.groups #groups-list').length > 0){

    var $list = $('#viewList'),

        $grid = $('#viewGrid'),

        $filter = $('#filterGroups'),

        $search = $('#searchGroups'),

        $container = $('#groups-list');



        function GridOn(){

            $container.isotope({

                // options

                itemSelector : '.item',

                layoutMode: 'fitRows'

            });

            $container.removeClass('list_view').addClass('grid_view');

        }

        function GridOff(){

            $container.isotope( 'destroy' );

            $container.removeClass('grid_view').addClass('list_view');

        }

        GridOn();

    $list.click(function(){

        $(this).addClass('hide');

        $grid.removeClass('hide');

        GridOff();

    });

    $grid.click(function(){

        $(this).addClass('hide');

        $list.removeClass('hide');

        GridOn();

    });



    $filter.click(function(){

        $(this).addClass('active');

    });





}















//// autocomplete for tagging on new posts

if($('.autocomplete').length > 0){

    var $autoC = $('.autocomplete');

    var taglist = $autoC.attr('data-tag-list'),

        taglist = taglist.substring(0, taglist.length -1),

        taglist = taglist.split(',');

        tagss = [];

       $.each(taglist, function(i,v){

            tagss.push({id: v,text: v})

       });



    $autoC.select2({

        data: tagss,

        initSelection : function (element, callback) {

            var data = [];

            $(element.val().split(",")).each(function () {

                data.push({id: this, text: this});

            });

            callback(data);

        },

        multiple: true,

        allowClear: true,

        tokenSeparators: [",", " "],

        createSearchChoice: function(term, data) {

            if ($(data).filter(function() {

                return this.text.localeCompare(term) === 0;

            }).length === 0) {

                return {

                    id: term,

                    text: term

                };

            }

        }

    });

}





if(jQuery('.tag-list[data-tags]') > 0){

    var tags = $('.tag-list').attr('data-tags');

        //tags = tags.substring(0, tags.length -1),

        tags = tags.split(',');

        html = '';

        console.log(tags);

       $.each(tags, function(i,v){

            html+= '<a href="#">'+ v+'</a>';

       });

       $('.tag-list').addClass('tags-list').append(html);

}





$('#searchMembers').click(function(){

    $('#bps_action').slideToggle();



});







    //update the weather

/*    function grabWeather(){

        $.simpleWeather({

            //zipcode: '63105',

            woeid: '2486982',

            //location: '',

            unit: 'f',

            success: function(weather) {

              html =  '<div class="unit code"><img src="wp-content/themes/bones/library/images/weather/'+ weather.code +'.png"/></div>'

              html += '<div class="unit  temp"><h2>'+weather.temp+'&deg;<span>'+weather.units.temp+'</span></h2></div>';

              html += '<div class="lastUnit forecast">'+ weather.high +'&deg;<span>H</span><br>';

              html += weather.low +'&deg;<span>L</span></div>';

              $("#weather").html(html);

            },

            error: function(error) {

              $("#weather").html('<p>'+error+'</p>');

            }

        });

    }



    // poplulate date

    function grabDate(){

        var today = moment(),

            day = today.format('dddd'),

            date = today.format('MMMM D, YYYY');

        $('#day').html('<h2>'+ day +'</h2><h3>'+ date +'</h3>');

    }





    /// get time for the javascript clock

    function grabTime() {

        var t = moment(),

            o = t.hours() % 12 / 12 * 360 + 90,

            u = t.seconds() * 6,

            a = t.minutes() * 6;

        $("#timeNow h2").html(t.format('h:mm')+'<span> '+ t.format('A') +'</span>');

    }



    // update all the info on the screen

    function updateScreen() {

        setTimeout(function() {

            grabTime(); //time every minute

        }, 60000);

        setTimeout(function() {

            grabWeather(); //weather every 5 minutes

        }, 300000);

        setTimeout(function() {

            grabDate(); //weather every 1 hour

        }, 3600000);

    };



    //initiate the page

    if($('.home-page').length > 0){

        grabTime();

        grabDate();

        grabWeather();

                $('.info-header').removeClass('loading');

    //update the page

        updateScreen();

    }



*/







}); /* end of as page load scripts */





/*! A fix for the iOS orientationchange zoom bug.

 Script by @scottjehl, rebound by @wilto.

 MIT License.

*/

(function(w){

	// This fix addresses an iOS bug, so return early if the UA claims it's something else.

	if( !( /iPhone|iPad|iPod/.test( navigator.platform ) && navigator.userAgent.indexOf( "AppleWebKit" ) > -1 ) ){ return; }

    var doc = w.document;

    if( !doc.querySelector ){ return; }

    var meta = doc.querySelector( "meta[name=viewport]" ),

        initialContent = meta && meta.getAttribute( "content" ),

        disabledZoom = initialContent + ",maximum-scale=1",

        enabledZoom = initialContent + ",maximum-scale=10",

        enabled = true,

		x, y, z, aig;

    if( !meta ){ return; }

    function restoreZoom(){

        meta.setAttribute( "content", enabledZoom );

        enabled = true; }

    function disableZoom(){

        meta.setAttribute( "content", disabledZoom );

        enabled = false; }

    function checkTilt( e ){

		aig = e.accelerationIncludingGravity;

		x = Math.abs( aig.x );

		y = Math.abs( aig.y );

		z = Math.abs( aig.z );

		// If portrait orientation and in one of the danger zones

        if( !w.orientation && ( x > 7 || ( ( z > 6 && y < 8 || z < 8 && y > 6 ) && x > 5 ) ) ){

			if( enabled ){ disableZoom(); } }

		else if( !enabled ){ restoreZoom(); } }

	w.addEventListener( "orientationchange", restoreZoom, false );

	w.addEventListener( "devicemotion", checkTilt, false );

})( this );

