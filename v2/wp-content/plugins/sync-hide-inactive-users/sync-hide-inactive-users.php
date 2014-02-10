<?php
/*
Plugin Name: Sync Hide Inactive Users
Plugin URI: www.asynchrony.com
Description: Prevents inactive users from displaying in search results.
Author: B.J. Self
Version: 0.1
*/

class hideInactiveUsers extends WP_Widget
{
  function hideInactiveUsers()
  {
    $widget_ops = array('classname' => 'hideInactiveUsers', 'description' => 'Shows a list of inactive users.' );
    $this->WP_Widget('hideInactiveUsers', 'Sync Inactive Users List', $widget_ops);
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $title . $after_title;;
 
    ?>
	

	<h2>Inactive Users</h2>
	<ul>
	<?php 
		$user_ids = getInactiveUsers();
		
		if ($user_ids) {
			$user_logins = array_map(create_function('$o', 'return $o->user_login;'), get_users(array('include' => $user_ids)));
		}
		foreach ($user_logins as $login) {
			echo "<li>" . $login . "</li>";
		}
	?>
	</ul>


<?php
 
    echo $after_widget;
  }

}

add_action( 'widgets_init', create_function('', 'return register_widget("hideInactiveUsers");') );

function getInactiveUsers () {
	$emtpy = "No User IDs";

	$disabled_users = new WP_User_Query( array('search' => 'DISABLED-USER*', 'search_columns' => array('user_email')));
//	print_r($disabled_users->results);

	if ($disabled_users->results) {
		$disabled_user_ids = array_map(create_function('$o', 'return $o->ID;'), $disabled_users->results);
	}
		
//		$disabled_users[0] = phpversion();

	if ($disabled_user_ids) {
		return $disabled_user_ids;
	} else {
		return $empty;
	}
}

add_action('bp_ajax_querystring','sync_exclude_users',20,2);
function sync_exclude_users($qs=false,$object=false){
 //list of users to exclude
 
	$user_ids = getInactiveUsers();
	$prefix = '';
	foreach ($user_ids as $user_id)
	{
		$idList .= $prefix . '"' . $user_id . '"';
		$prefix = ', ';
	}
 
 $excluded_user=$idList;
 
 if($object!='members')//hide for members only
 return $qs;
 
 $args=wp_parse_args($qs);
 
 //check if we are listing friends?, do not exclude in this case
 /*
 if(!empty($args['user_id']))
 return $qs;
 */
 if(!empty($args['exclude']))
 $args['exclude']=$args['exclude'].','.$excluded_user;
 else
 $args['exclude']=$excluded_user;
 
 $qs=build_query($args);
 
 return $qs;
 
}

?>
