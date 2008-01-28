<?php
/**
 * MyBB 1.2
 * Copyright � 2007 MyBB Group, All Rights Reserved
 *
 * Website: http://www.mybboard.net
 * License: http://www.mybboard.net/about/license
 *
 * $Id$
 */

define("IN_MYBB", 1);

$templatelist = "memberlist,memberlist_member,memberlist_search,memberlist_user,memberlist_user_groupimage,memberlist_user_avatar";
$templatelist .= ",postbit_www,postbit_email,multipage_nextpage,multipage_page_current,multipage_page,multipage_start,multipage_end,multipage";
require_once "./global.php";

// Load global language phrases
$lang->load("memberlist");

if($mybb->settings['enablememberlist'] == 0)
{
	error($lang->memberlist_disabled);
}

$plugins->run_hooks("memberlist_start");

add_breadcrumb($lang->nav_memberlist);

if($mybb->usergroup['canviewmemberlist'] == 0)
{
	error_no_permission();
}

// Showing advanced search page?
if($mybb->input['action'] == "search")
{
	eval("\$search_page = \"".$templates->get("memberlist_search")."\";");
	$plugins->run_hooks("memberlist_search");
	output_page($search_page);	
}
else
{
	$search_url = '';
	
	// Incoming sort field?
	if($mybb->input['sort'])
	{
		$mybb->input['sort'] = strtolower($mybb->input['sort']);
	}
	else
	{
		$mybb->input['sort'] = $mybb->settings['default_memberlist_sortby'];
	}
	
	switch($mybb->input['sort'])
	{
		case "regdate":
			$sort_field = "u.regdate";
			break;
		case "lastvisit":
			$sort_field = "u.lastactive";
			break;
		case "reputation":
			$sort_field = "u.reputation";
			break;
		case "postnum":
			$sort_field = "u.postnum";
			break;
		default:
			$sort_field = "u.username";
			$mybb->input['sort'] = 'username';
			break;
	}
	$sort_selected[$mybb->input['sort']] = " selected=\"selected\"";
	
	// Incoming sort order?
	if($mybb->input['order'])
	{
		$mybb->input['order'] = strtolower($mybb->input['order']);
	}
	else
	{
		$mybb->input['order'] = strtolower($mybb->settings['default_memberlist_order']);
	}
	
	if($mybb->input['order'] == "asc" || (!$mybb->input['order'] && $mybb->input['sort'] == 'username'))
	{
		$sort_order = "ASC";
		$mybb->input['order'] = "asc";
	}
	else
	{
		$sort_order = "DESC";
		$mybb->input['order'] = "desc";
	}
	$order_check[$mybb->input['order']] = " checked=\"checked\"";
	
	// Incoming results per page?
	$mybb->input['perpage'] = intval($mybb->input['perpage']);
	if($mybb->input['perpage'] > 0)
	{
		$per_page = $mybb->input['perpage'];
	}
	else
	{
		$per_page = $mybb->input['perpage'] = intval($mybb->settings['membersperpage']);	
	}
	
	$search_query = '1=1';
	$search_url = "memberlist.php?sort={$mybb->input['sort']}&order={$mybb->input['order']}&per_page={$mybb->input['perpage']}";
	
	// Limiting results to a certain letter
	if($mybb->input['letter'])
	{
		$letter = chr(ord($mybb->input['letter']));
		if($mybb->input['letter'] == -1)
		{
			$search_query .= " AND u.username NOT REGEXP('[a-zA-Z]')";
		}
		else if(strlen($letter) == 1)
		{
			$search_query .= " AND u.username LIKE '".$db->escape_string($letter)."%'";
		}
		$search_url .= "&letter={$letter}";
	}

	// Searching for a matching username
	$search_username = htmlspecialchars_uni($mybb->input['username']);
	if(trim($mybb->input['username']))
	{
		$mybb->input['username'] = $db->escape_string_like($mybb->input['username']);
		// Name begins with
		if($mybb->input['username_match'] == "begins")
		{
			$search_query .= " AND u.username LIKE '".$db->escape_string($mybb->input['username'])."%'";
			$search_url .= "&username_match=begins";
		}
		// Just contains
		else
		{
			$search_query .= " AND u.username LIKE '%".$db->escape_string($mybb->input['username'])."%'";
		}
		$search_url .= "&username={$mybb->input['username']}";
	}

	// Website contains
	$search_website = htmlspecialchars_uni($mybb->input['website']);
	if(trim($mybb->input['website']))
	{
		$mybb->input['website'] = $db->escape_string_like($mybb->input['website']);
		$search_query .= " AND u.website LIKE '%".$db->escape_string($mybb->input['website'])."%'";
		$search_url .= "&website={$mybb->input['website']}";
	}

	// AIM Identity
	if(trim($mybb->input['aim']))
	{
		$mybb->input['aim'] = $db->escape_string_like($mybb->input['aim']);
		$search_query .= " AND u.aim LIKE '%".$db->escape_string($mybb->input['aim'])."%'";
		$search_url .= "&aim={$mybb->input['aim']}";
	}

	// ICQ Number
	if(trim($mybb->input['icq']))
	{
		$mybb->input['icq'] = $db->escape_string_like($mybb->input['icq']);
		$search_query .= " AND u.icq LIKE '%".$db->escape_string($mybb->input['icq'])."%'";
		$search_url .= "&icq={$mybb->input['icq']}";
	}

	// MSN/Windows Live Messenger address
	if(trim($mybb->input['msn']))
	{
		$mybb->input['msn'] = $db->escape_string_like($mybb->input['msn']);
		$search_query .= " AND u.msn LIKE '%".$db->escape_string($mybb->input['msn'])."%'";
		$search_url .= "&msn={$mybb->input['msn']}";
	}

	// Yahoo! Messenger address
	if(trim($mybb->input['yahoo']))
	{
		$mybb->input['yahoo'] = $db->escape_string_like($mybb->input['yahoo']);
		$search_query .= " AND u.yahoo LIKE '%".$db->escape_string($mybb->input['yahoo'])."%'";
		$search_url .= "&yahoo={$mybb->input['yahoo']}";	
	}

	$query = $db->simple_select("users u", "COUNT(*) AS users", "{$search_query}");
	$num_users = $db->fetch_field($query, "users");

	$page = intval($mybb->input['page']);
	if($page && $page > 0)
	{
		$start = ($page - 1) * $per_page;
	}
	else
	{
		$start = 0;
		$page = 1;
	}
	$search_url = htmlspecialchars_uni($search_url);
	$multipage = multipage($num_users, $per_page, $page, $search_url);
	
	// Cache a few things
	$usergroups_cache = $cache->read('usergroups');
	$query = $db->simple_select("usertitles", "*", "", array('order_by' => 'posts', 'order_dir' => 'DESC'));
	while($usertitle = $db->fetch_array($query))
	{
		$usertitles_cache[$usertitle['posts']] = $usertitle;
	}
	$query = $db->query("
		SELECT u.*, f.*
		FROM ".TABLE_PREFIX."users u
		LEFT JOIN ".TABLE_PREFIX."userfields f ON (f.ufid=u.uid)
		WHERE {$search_query}
		ORDER BY {$sort_field} {$sort_order}
		LIMIT {$start}, {$per_page}
	");
	while($user = $db->fetch_array($query))
	{
		$plugins->run_hooks("memberlist_user");

		$user['username'] = format_name($user['username'], $user['usergroup'], $user['displaygroup']);

		$user['profilelink'] = build_profile_link($user['username'], $user['uid']);
		
		// Get the display usergroup
		if(!$user['displaygroup'])
		{
			$user['displaygroup'] = $user['usergroup'];
		}
		$usergroup = $usergroups_cache[$user['displaygroup']];
		
		// Work out the usergroup/title stuff
		if(!empty($usergroup['image']))
		{
			if(!empty($mybb->user['language']))
			{
				$language = $mybb->user['language'];
			}
			else
			{
				$language = $mybb->settings['bblanguage'];
			}
			$usergroup['image'] = str_replace("{lang}", $language, $usergroup['image']);
			$usergroup['image'] = str_replace("{theme}", $theme['imgdir'], $usergroup['image']);
			eval("\$usergroup['groupimage'] = \"".$templates->get("memberlist_user_groupimage")."\";");
		}

		$has_custom_title = 0;
		if(trim($user['usertitle']) != "")
		{
			$has_custom_title = 1;
		}

		if($usergroup['usertitle'] != "" && !$has_custom_title)
		{
			$user['usertitle'] = $usergroup['usertitle'];
		}
		elseif(is_array($usertitles_cache) && !$usergroup['usertitle'])
		{
			foreach($usertitles_cache as $posts => $titleinfo)
			{
				if($user['postnum'] >= $posts)
				{
					if(!$has_custom_title)
					{
						$user['usertitle'] = $titleinfo['title'];
					}
					$user['stars'] = $titleinfo['stars'];
					$user['starimage'] = $titleinfo['starimage'];
					break;
				}
			}
		}

		if($usergroup['stars'])
		{
			$user['stars'] = $usergroup['stars'];
		}

		if(!$user['starimage'])
		{
			$user['starimage'] = $usergroup['starimage'];
		}
		$user['starimage'] = str_replace("{theme}", $theme['imgdir'], $user['starimage']);

		for($i = 0; $i < $user['stars']; ++$i)
		{
			$user['userstars'] .= "<img src=\"".$user['starimage']."\" border=\"0\" alt=\"*\" />";
		}

		if($user['userstars'] && $usergroup['groupimage'])
		{
			$user['userstars'] = "<br />".$user['userstars'];
		}
	
		// Show avatar
		if($user['avatar'] != '')
		{
			$user['avatar'] = htmlspecialchars_uni($user['avatar']);
			$avatar_dimensions = explode("|", $user['avatardimensions']);
			
			if($avatar_dimensions[0] && $avatar_dimensions[1])
			{
				list($max_width, $max_height) = explode("x", $mybb->settings['memberlistmaxavatarsize']);
			 	if($avatar_dimensions[0] > $max_width || $avatar_dimensions[1] > $max_height)
				{
					require_once MYBB_ROOT."inc/functions_image.php";
					$scaled_dimensions = scale_image($avatar_dimensions[0], $avatar_dimensions[1], $max_width, $max_height);
					$avatar_width_height = "width=\"{$scaled_dimensions['width']}\" height=\"{$scaled_dimensions['height']}\"";
				}
				else
				{
					$avatar_width_height = "width=\"{$avatar_dimensions[0]}\" height=\"{$avatar_dimensions[1]}\"";	
				}
			}
			
			eval("\$user['avatar'] = \"".$templates->get("memberlist_user_avatar")."\";");
		}
		else
		{
			$user['avatar'] = "";
		}		
		
		$user['regdate'] = my_date($mybb->settings['dateformat'], $user['regdate']).", ".my_date($mybb->settings['timeformat'], $user['regdate']);
		$user['lastvisit'] = my_date($mybb->settings['dateformat'], $user['lastactive']).", ".my_date($mybb->settings['timeformat'], $user['lastactive']);;
		$user['postnum'] = my_number_format($user['postnum']);
		$alt_bg = alt_trow();
		eval("\$users .= \"".$templates->get("memberlist_user")."\";");
	}

	// Do we have no results?
	if(!$users)
	{
		$users = "<tr>\n<td colspan=\"6\" align=\"center\" class=\"trow1\">$lang->error_no_members</td>\n</tr>";
	}

	$plugins->run_hooks("memberlist_end");

	eval("\$memberlist = \"".$templates->get("memberlist")."\";");
	output_page($memberlist);
}
?>