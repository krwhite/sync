var hasChanged = false;
var formSubmitted = false;

function substr_count(mainString, subString){
	var re = new RegExp(subString, 'g');
	if(!mainString.match(re) || !mainString || !subString)
	return 0;
	var count = mainString.match(re);
	return count.length;
}

function str_word_count(s){
	if(!s.length)
	return 0;
	s = s.replace(/(^\s*)|(\s*$)/gi,"");
	s = s.replace(/[ ]{2,}/gi," ");
	s = s.replace(/\n /,"\n");
	return s.split(' ').length;
}

function countTags(s){
	if(!s.length)
	return 0;
	return s.split(',').length;
}

function post_has_errors(title,content,bio,category,tags){
	var error_string = '';
	if((fep_rules.min_words_title !=0 && title==='') || (fep_rules.min_words_content !=0 && content==='') || (fep_rules.min_words_bio !=0 && bio==='') || category==-1 || (fep_rules.min_tags !=0 &&  tags==='') )
	error_string = 'You missed one or more required fields</br>';
	
	var stripped_content = content.replace(/(<([^>]+)>)/ig,"");
	var stripped_bio = bio.replace(/(<([^>]+)>)/ig,"");

	if ( title != '' && str_word_count(title) < fep_rules.min_words_title )
        error_string += 'The title is too short<br/>';
	if ( content != '' && str_word_count( title ) > fep_rules.max_words_title )
        error_string += 'The title is too long<br/>';
    if ( content != '' && str_word_count( stripped_content ) < fep_rules.min_words_content )
        error_string += 'The article is too short<br/>';
    if ( str_word_count( stripped_content ) > fep_rules.max_words_content )
        error_string += 'The article is too long<br/>';
	if ( bio != '' && str_word_count( stripped_bio ) < fep_rules.min_words_bio )
        error_string += 'The bio is too short<br/>';
    if ( str_word_count( stripped_bio ) > fep_rules.max_words_bio )
        error_string += 'The bio is too long<br/>';
	if ( substr_count( content, '</a>' ) > fep_rules.max_links )
        error_string += 'There are too many links in the article body<br/>';
	if ( substr_count( bio, '</a>' ) > fep_rules.max_links_bio )
        error_string += 'There are too many links in the bio<br/>';
	if ( tags != '' && countTags(tags) < fep_rules.min_tags )
		error_string += 'You haven\'t added the required number of tags<br/>';
	if ( countTags(tags) > fep_rules.max_tags )
		error_string += 'There are too many tags<br/>';
		
	if ( error_string == '' )
	return false;
	else
	return '<h2>Your submission has errors. Please try again!</h2>'+error_string;
}

jQuery(document).ready(function(){

jQuery("td.post-delete a").click(function(event) {
	fep_delete_post(event.target.id);
});


function fep_delete_post(id){
var confirmation = confirm('Are you sure?');
if(confirmation){
var loadimg = '#loading-'+id;
var row = '#row-'+id;
jQuery(row +' td.post-delete a').hide();
jQuery(loadimg).show().css({'float':'none','height':'18px'});
jQuery.ajax({
type: 'POST',
url: fepajaxhandler.ajaxurl,
data: {
action: 'fep_delete_posts',
post_id:id
},

success:function(data, textStatus, XMLHttpRequest){
jQuery(row).hide();
var message = '#message';
jQuery(message).html('');
var arr = jQuery.parseJSON(data);
jQuery(message).show().addClass('success').append(arr.message);
if( jQuery(message).offset().top < jQuery(window).scrollTop() ){
jQuery('html, body').animate({ scrollTop: jQuery(message).offset().top }, 'slow'); }
},
 
error: function(MLHttpRequest, textStatus, errorThrown){
alert(errorThrown);
}
 
}); }
}


jQuery("#submit").click(function() {
tinyMCE.triggerSave();
var post_title = jQuery("#post_title").val();
var post_content = jQuery("#post_content").val();
var about_the_author = jQuery("#about_the_author").val();
var post_category = jQuery("#post_category").val();
var post_tags = jQuery("#post_tags").val();
var post_id = jQuery("#post_id").val();
var post_nonce = jQuery("#fepnonce").val();
fep_add_post(post_title,post_content,about_the_author,post_category,post_tags,post_id,post_nonce);
});

function fep_add_post(title,content,bio,category,tags,id,nonce)
{
var message = '#message';
var form = '#new-post-form';

if( post_has_errors(title,content,bio,category,tags) ){
	if( jQuery('#new-post-form').offset().top < jQuery(window).scrollTop() ){
	jQuery('html, body').animate({ scrollTop: jQuery('#new-post-form').offset().top-10 }, 'slow'); }
	jQuery(message).html('');
	var errors = post_has_errors(title,content,bio,category,tags);
	jQuery(message).show().append(errors);
	return;
}
jQuery("button#submit").removeClass('active-btn').addClass('passive-btn');
jQuery.ajaxSetup({cache:false});
jQuery("img#loading").show();
jQuery.ajax({
type: 'POST',
url: fepajaxhandler.ajaxurl,
data: {
action: 'fep_process_form_input',
post_title: title,
post_content: content,
about_the_author:bio,
post_category:category,
post_tags:tags,
post_id:id,
post_nonce:nonce
},
 
success:function(data, textStatus, XMLHttpRequest){
	hasChanged = false;
	formSubmitted = true;
	jQuery(message).html('');
	var arr = jQuery.parseJSON(data);
	if(arr.error){
	jQuery('#post-form').html('');
	jQuery(message).removeClass('warning').addClass('success');
	}
	if( jQuery('#new-post-form').offset().top < jQuery(window).scrollTop() ){
	jQuery('html, body').animate({ scrollTop: jQuery('#new-post-form').offset().top-10 }, 'slow');}
	jQuery("img#loading").hide();
	jQuery("button#submit").removeClass('passive-btn').addClass('active-btn');
	jQuery(message).show().append(arr.message);
},

error: function(MLHttpRequest, textStatus, errorThrown){
alert(errorThrown);
}
 
});
}

});