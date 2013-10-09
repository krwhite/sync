jQuery(document).ready(function(){
jQuery('#tabs div').hide();
jQuery('#tabs div:first').show();
jQuery('#tabs ul li:first').addClass('active');
 
jQuery('#tabs ul li a').click(function(){
jQuery('#tabs ul li').removeClass('active');
jQuery(this).parent().addClass('active');
var currentTab = jQuery(this).attr('href');
jQuery('#tabs div').hide();
jQuery(currentTab).show();
return false;
});
});

//Unsaved Changes

jQuery(document).ready(function() {
  hasChanged = false;
  jQuery("input, select, textarea, #post_content").change(function() {
	 hasChanged = true;
  });
	
});

window.onbeforeunload = confirmExit;
function confirmExit()
{ 	var mce = typeof(tinyMCE) != 'undefined' ? tinyMCE.activeEditor : false, post_title, post_content;

	if(!formSubmitted && (hasChanged || (mce && !mce.isHidden() && mce.isDirty() )) )
	return "You have unsaved changes. Proceed anyway?";
}