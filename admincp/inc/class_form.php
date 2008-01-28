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

/**
 * Generate a form on the page.
 */
class DefaultForm
{
	/**
	 * @var boolean Should this form be returned instead of output to the browser?
	 */
	var $_return = false;

	/**
	 * Constructor. Outputs the form tag with the specified options.
	 *
	 * @param string The action for the form.
	 * @param string The method (get or post) for this form.
	 * @param string The ID of the form.
	 * @param boolean Should file uploads be allowed for this form?
	 * @param boolean Should this form be returned instead of output to the browser?
	 * @param boolean Should this form be returned instead of output to the browser?
	 */
	function __construct($script, $method, $allow_uploads=0, $name="", $id="", $return=false)
	{
		global $mybb;
		$form = "<form action=\"{$script}\" method=\"{$method}\"";
		if($allow_uploads != 0)
		{
			$form .= " type=\"multipart/form-data\"";
		}
		if($id != "")
		{
			$form .= " id=\"{$id}\"";
		}
		$form .= ">\n";
		$form .= $this->generate_hidden_field("my_post_key", $mybb->post_code);
		if($return == false)
		{
			echo $form;
		}
		else
		{
			$this->_return = true;
			return $form;
		}
	}

	function DefaultForm($script, $method, $id="", $allow_uploads=0)
	{
		$this->__construct($script, $method, $id, $allow_uploads);
	}


	/**
	 * Generate and return a hidden field.
	 *
	 * @param string The name of the hidden field.
	 * @param string The value of the hidden field.
	 * @param array Optional array of options (id)
	 * @return string The generated hidden
	 */
	function generate_hidden_field($name, $value, $options=array())
	{
		$input = "<input type=\"hidden\" name=\"{$name}\" value=\"".htmlspecialchars($value)."\"";
		if(isset($options['id']))
		{
			$input .= " id=\"".$options['id']."\"";
		}
		$input .= " />";
		return $input;
	}
	
	/**
	 * Generate a text box field.
	 *
	 * @param string The name of the text box.
	 * @param string The value of the text box.
	 * @param array Array of options for the text box (class, style, id)
	 * @return string The generated text box.
	 */
	function generate_text_box($name, $value="", $options=array())
	{
		$input = "<input type=\"text\" name=\"".$name."\" value=\"".htmlspecialchars($value)."\"";
		if(isset($options['class']))
		{
			$input .= " class=\"text_input ".$options['class']."\"";
		}
		else
		{
			$input .= " class=\"text_input\"";
		}
		if(isset($options['style']))
		{
			$input .= " style=\"".$options['style']."\"";
		}
		if(isset($options['id']))
		{
			$input .= " id=\"".$options['id']."\"";
		}
		$input .= " />";
		return $input;
	}
	
	/**
	 * Generate a password input box.
	 *
	 * @param string The name of the password box.
	 * @param string The value of the password box.
	 * @param array Array of options for the password box (class, id)
	 * @return string The generated password input box.
	 */
	function generate_password_box($name, $value="", $options=array())
	{
		$input = "<input type=\"password\" name=\"".$name."\" value=\"".htmlspecialchars($value)."\"";
		if(isset($options['class']))
		{
			$input .= " class=\"text_input ".$options['class']."\"";
		}
		else
		{
			$input .= " class=\"text_input\"";
		}
		if(isset($options['id']))
		{
			$input .= " id=\"".$options['id']."\"";
		}
		$input .= " />";
		return $input;
	}

	/**
	 * Generate a file upload field.
	 *
	 * @param string The name of the file upload field.
	 * @param array Array of options for the file upload field (class, id)
	 * @return string The generated file upload field.
	 */
	function generate_file_upload_box($name, $options=array())
	{
		$input = "<input type=\"file\" name=\"".$name."\"";
		if(isset($options['class']))
		{
			$input .= " class=\"text_input ".$options['class']."\"";
		}
		else
		{
			$input .= " class=\"text_input\"";
		}
		if(isset($options['style']))
		{
			$input .= " style=\"".$options['style']."\"";
		}
		if(isset($options['id']))
		{
			$input .= " id=\"".$options['id']."\"";
		}
		$input .= " />";
		return $input;
		
	}

	/**
	 * Generate a text area.
	 *
	 * @param string The name of of the text area.
	 * @param string The value of the text area field.
	 * @param array Array of options for text area (class, id, rows, cols, style, disabled)
	 * @return string The generated text area field.
	 */
	function generate_text_area($name, $value="", $options=array())
	{
		$textarea = "<textarea";
		if(!empty($name))
		{
			$textarea .= " name=\"{$name}\"";
		}
		if(isset($options['class']))
		{
			$textarea .= " class=\"{$options['class']}\"";
		}
		if(isset($options['id']))
		{
			$textarea .= " id=\"{$options['id']}\"";
		}
		if($options['style'])
		{
			$textarea .= " style=\"{$options['style']}\"";
		}
		if($options['disabled'])
		{
			$textarea .= " disabled=\"disabled\"";
		}
		if(!$options['rows'])
		{
			$options['rows'] = 5;
		}
		if(!$options['cols'])
		{
			$options['cols'] = 45;
		}
		$textarea .= " rows=\"{$options['rows']}\" cols=\"{$options['cols']}\">";
		$textarea .= htmlspecialchars_uni($value);
		$textarea .= "</textarea>";
		return $textarea;
	}

	/**
	 * Generate a radio button.
	 *
	 * @param string The name of the radio button.
	 * @param string The value of the radio button
	 * @param string The label of the radio button if there is one.
	 * @param array Array of options for the radio button (id, class, checked)
	 * @return string The generated radio button.
	 */
	function generate_radio_button($name, $value="", $label="", $options=array())
	{
		$input = "<label";
		if(isset($options['id']))
		{
			$input .= " for=\"{$options['id']}\"";
		}
		if(isset($options['class']))
		{
			$input .= " class=\"label_{$options['class']}\"";
		}
		$input .= "><input type=\"radio\" name=\"{$name}\" value=\"".htmlspecialchars($value)."\"";
		if(isset($options['class']))
		{
			$input .= " class=\"radio_input ".$options['class']."\"";
		}
		else
		{
			$input .= " class=\"radio_input\"";
		}
		if(isset($options['id']))
		{
			$input .= " id=\"".$options['id']."\"";
		}
		if(isset($options['checked']) && $options['checked'] != 0)
		{
			$input .= " checked=\"checked\"";
		}
		$input .= " />";
		if($label != "")
		{
			$input .= $label;
		}
		$input .= "</label>";
		return $input;
	}

	/**
	 * Generate a checkbox.
	 *
	 * @param string The name of the check box.
	 * @param string The value of the check box.
	 * @param string The label of the check box if there is one.
	 * @param array Array of options for the check box (id, class, checked)
	 * @return string The generated check box.
	 */
	function generate_check_box($name, $value="", $label="", $options=array())
	{
		$input = "<label";
		if(isset($options['id']))
		{
			$input .= " for=\"{$options['id']}\"";
		}
		if(isset($options['class']))
		{
			$input .= " class=\"label_{$options['class']}\"";
		}
		$input .= "><input type=\"checkbox\" name=\"{$name}\" value=\"".htmlspecialchars($value)."\"";
		if(isset($options['class']))
		{
			$input .= " class=\"checkbox_input ".$options['class']."\"";
		}
		else
		{
			$input .= " class=\"checkbox_input\"";
		}
		if(isset($options['id']))
		{
			$input .= " id=\"".$options['id']."\"";
		}
		if($options['checked'] === true || $options['checked'] == 1)
		{
			$input .= " checked=\"checked\"";
		}
		if(isset($options['onclick']))
		{
			$input .= " onclick=\"{$options['onclick']}\"";
		}
		$input .= " /> ";
		if($label != "")
		{
			$input .= $label;
		}
		$input .= "</label>";
		return $input;
	}
	
	/**
	 * Generate a select box.
	 *
	 * @param string The name of the select box.
	 * @param array Array of options in key => val format.
	 * @param mixed Either a string containing the selected item or an array containing multiple selected items (options['multiple'] must be true)
	 * @param array Array of options for the select box (multiple, class, id, size)
	 * @return string The select box.
	 */
	function generate_select_box($name, $option_list, $selected=array(), $options=array())
	{
		if(!isset($options['multiple']))
		{
			$select = "<select name=\"{$name}\"";
		}
		else
		{
			$select = "<select name=\"{$name}\" multiple=\"multiple\"";
		}
		if(isset($options['class']))
		{
			$select .= " class=\"{$options['class']}\"";
		}
		if(isset($options['id']))
		{
			$select .= " id=\"{$options['id']}\"";
		}
		if(isset($options['size']))
		{
			$select .= " size=\"{$options['size']}\"";
		}
		$select .= ">\n";
		foreach($option_list as $value => $option)
		{
			$select_add = '';
			if(!empty($selected) && ((string)$value == (string)$selected || (is_array($selected) && in_array($value, $selected))))
			{
				$select_add = " selected=\"selected\"";
			}
			$select .= "<option value=\"{$value}\"{$select_add}>{$option}</option>\n";
		}
		$select .= "</select>\n";
		return $select;
	}
	
	/**
	 * Generate a forum selection box.
	 *
	 * @param string The name of the selection box.
	 * @param mixed Array/string of the selected items.
	 * @param array Array of options (pid, main_option, multiple)
	 * @param boolean Is this our first interation of this funciton?
	 * @return string The built select box.
	 */
	function generate_forum_select($name, $selected, $options=array(), $is_first=1)
	{
		global $fselectcache, $forum_cache, $selectoptions;
		
		if(!$selectoptions)
		{
			$selectoptions = '';
		}
		
		if(!$options['depth'])
		{
			$options['depth'] = 0;
		}
		
		$options['depth'] = intval($options['depth']);
		
		if(!$options['pid'])
		{
			$pid = 0;
		}
		
		$pid = intval($options['pid']);
		
		if(!is_array($fselectcache))
		{
			if(!is_array($forum_cache))
			{
				$forum_cache = cache_forums();
			}
	
			foreach($forum_cache as $fid => $forum)
			{
				if($forum['active'] != 0)
				{
					$fselectcache[$forum['pid']][$forum['disporder']][$forum['fid']] = $forum;
				}
			}
		}
		
		if($options['main_option'] && $is_first)
		{
			$selectoptions .= "<option value=\"-1\">{$options['main_option']}</option>\n";
		}
		
		if(is_array($fselectcache[$pid]))
		{
			foreach($fselectcache[$pid] as $main)
			{
				foreach($main as $forum)
				{
					if($forum['fid'] != "0" && $forum['linkto'] == '')
					{
						$select_add = '';
	
						if(!empty($selected) && ($forum['fid'] == $selected || (is_array($selected) && in_array($forum['fid'], $selected))))
						{
							$select_add = " selected=\"selected\"";
						}
						
						$sep = '';
						if(isset($options['depth']))
							$sep = str_repeat("&nbsp;", $options['depth']);

						$selectoptions .= "<option value=\"{$forum['fid']}\"{$select_add}>".$sep.htmlspecialchars_uni($forum['name'])."</option>\n";
	
						if($forum_cache[$forum['fid']])
						{
							$options['depth'] += 5;
							$options['pid'] = $forum['fid'];
							$this->generate_forum_select($forum['fid'], $selected, $options, 0);
							$options['depth'] -= 5;
						}
					}
				}
			}
		}
		
		if($is_first == 1)
		{
			if(!isset($options['multiple']))
			{
				$select = "<select name=\"{$name}\"";
			}
			else
			{
				$select = "<select name=\"{$name}\" multiple=\"multiple\"";
			}
			if(isset($options['class']))
			{
				$select .= " class=\"{$options['class']}\"";
			}
			if(isset($options['id']))
			{
				$select .= " id=\"{$options['id']}\"";
			}
			if(isset($options['size']))
			{
				$select .= " size=\"{$options['size']}\"";
			}
			$select .= ">\n".$selectoptions."</select>\n";
			$selectoptions = '';
			return $select;
		}
	}
	
	/**
	 * Generate a submit button.
	 *
	 * @param string The value for the submit button.
	 * @param array Array of options for the submit button (class, id, name, dsiabled, onclick)
	 * @return string The generated submit button.
	 */
	function generate_submit_button($value, $options=array())
	{
		$input = "<input type=\"submit\" value=\"".htmlspecialchars($value)."\"";

		if(isset($options['class']))
		{
			$input .= " class=\"submit_button ".$options['class']."\"";
		}
		else
		{
			$input .= " class=\"submit_button\"";
		}
		if(isset($options['id']))
		{
			$input .= " id=\"".$options['id']."\"";
		}
		if(isset($options['name']))
		{
			$input .= " name=\"".$options['name']."\"";
		}
		if($options['disabled'])
		{
			$input .= " disabled=\"disabled\"";
		}
		if($options['onclick'])
		{
			$input .= " onclick=\"".str_replace('"', '\"', $options['onclick'])."\"";
		}
		$input .= " />";
		return $input;
	}
	
	/**
	 * Generate a reset button.
	 *
	 * @param string The value for the reset button.
	 * @param array Array of options for the reset button (class, id, name)
	 * @return string The generated reset button.
	 */
	function generate_reset_button($value, $options=array())
	{
		$input = "<input type=\"reset\" value=\"".htmlspecialchars($value)."\"";

		if(isset($options['class']))
		{
			$input .= " class=\"submit_button ".$options['class']."\"";
		}
		else
		{
			$input .= " class=\"submit_button\"";
		}
		if(isset($options['id']))
		{
			$input .= " id=\"".$options['id']."\"";
		}
		if(isset($options['name']))
		{
			$input .= " name=\"".$options['name']."\"";
		}
		$input .= " />";
		return $input;
	}

	/**
	 * Generate a yes/no radio button choice.
	 *
	 * @param string The name of the yes/no choice field.
	 * @param string The value that should be checked.
	 * @param boolean Using integers for the checkbox?
	 * @param array Array of options for the yes cehckbox (@see generate_radio_button)
	 * @param array Array of options for the no cehckbox (@see generate_radio_button)
	 * @return string The generated yes/no radio button.
	 */
	function generate_yes_no_radio($name, $value=1, $int=true, $yes_options=array(), $no_options = array())
	{
		global $lang;
		
		// Checked status
		if($value == "yes" || $value === '0')
		{
			$no_checked = 1;
			$yes_checked = 0;
		}
		else
		{
			$yes_checked = 1;
			$no_checked = 0;
		}
		// Element value
		if($int == true)
		{
			$yes_value = 1;
			$no_value = 0;
		}
		else
		{
			$yes_value = "yes";
			$no_value = "no";
		}
		// Set the options straight
		$yes_options['class'] = "radio_yes ".$yes_options['class'];
		$yes_options['checked'] = $yes_checked;
		$no_options['class'] = "radio_no ".$no_options['class'];
		$no_options['checked'] = $no_checked;
		
		$yes = $this->generate_radio_button($name, $yes_value, $lang->yes, $yes_options);
		$no = $this->generate_radio_button($name, $no_value, $lang->no, $no_options);
		return $yes." ".$no;
	}

	/**
	 * Generate an on/off radio button choice.
	 *
	 * @param string The name of the on/off choice field.
	 * @param string The value that should be checked.
	 * @param boolean Using integers for the checkbox?
	 * @param array Array of options for the on cehckbox (@see generate_radio_button)
	 * @param array Array of options for the off cehckbox (@see generate_radio_button)
	 * @return string The generated on/off radio button.
	 */
	function generate_on_off_radio($name, $value=1, $int=true, $on_options=array(), $off_options = array())
	{
		// Checked status
		if($value == "off" || (int) $value !== 1)
		{
			$off_checked = 1;
			$on_checked = 0;
		}
		else
		{
			$on_checked = 1;
			$off_checked = 0;
		}
		// Element value
		if($int == true)
		{
			$on_value = 1;
			$off_value = 0;
		}
		else
		{
			$on_value = "on";
			$off_value = "off";
		}
		
		// Set the options straight
		$on_options['class'] = "radio_on ".$on_options['class'];
		$on_options['checked'] = $on_checked;
		$off_options['class'] = "radio_off ".$off_options['class'];
		$off_options['checked'] = $off_checked;
		
		$on = $this->generate_radio_button($name, $on_value, "On", $on_options);
		$off = $this->generate_radio_button($name, $off_value, "Off", $off_options);
		return $on." ".$off;
	}
	
	function generate_date_select($name, $date, $options)
	{
		
	}
	
	/**
	 * Output a row of buttons in a wrapped container.
	 *
	 * @param array Array of the buttons (html) to output.
	 */
	function output_submit_wrapper($buttons)
	{
		$return = "<div class=\"form_button_wrapper\">\n";
		foreach($buttons as $button)
		{
			$return .= $button." \n";
		}
		$return .= "</div>\n";
		if($this->_return == false)
		{
			echo $return;
		}
		else
		{
			return $return;
		}
	}	

	/**
	 * Finish up a form.
	 */
	function end()
	{
		if($this->_return == false)
		{
			echo "</form>";
		}
		else
		{
			return "</form>";
		}
	}
}

/**
 * Generate a form container.
 */
class DefaultFormContainer
{
	var $_container;
	var $_title;

	/**
	 * Initialise the new form container.
	 *
	 * @param string The title of the forum container
	 * @param string An additional class to apply if we have one.
	 */
	function __construct($title='', $extra_class='')
	{
		$this->_container = new Table;
		$this->extra_class = $extra_class;
		$this->_title = $title;
	}

	function DefaultFormContainer($title='', $extra_class='')
	{
		$this->__construct($title, $extra_class);
	}

	/**
	 * Output a header row of the form container.
	 *
	 * @param string The header row label.
	 * @param array TODO
	 */
	function output_row_header($title, $extra=array())
	{
		$this->_container->construct_header($title, $extra);
	}

	/**
	 * Output a row of the form container.
	 *
	 * @param string The title of the row.
	 * @param string The description of the row/field.
	 * @param string The HTML content to show in the row.
	 * @param string The ID of the control this row should be a label for.
	 * @param array Array of options for the row cell.
	 * @param array Array of options for the row container.
	 */
	function output_row($title, $description="", $content="", $label_for="", $options=array(), $row_options=array())
	{
		if($label_for != '')
		{
			$for = " for=\"{$label_for}\"";
		}
		
		if($title)
		{
			$row = "<label{$for}>{$title}</label>";
		}
		
		if($description != '')
		{
			$row .= "\n<div class=\"description\">{$description}</div>\n";
		}
		$row .= "<div class=\"form_row\">{$content}</div>\n";
		
		$this->_container->construct_cell($row, $options);
		
		if(!isset($options['skip_construct']))
		{
			$this->_container->construct_row($row_options);
		}
	}
	
	/**
	 * Output a row cell for a table based form row.
	 *
	 * @param string The data to show in the cell.
	 * @param array Array of options for the cell.
	 */
	function output_cell($data, $options=array())
	{
		$this->_container->construct_cell($data, $options);
	}
	
	/**
	 * Build a row for the table based form row.
	 */
	function construct_row()
	{
		$this->_container->construct_row();
	}

	/**
	 * Count the number of rows in the form container. Useful for displaying a 'no rows' message.
	 *
	 * @return int The number of rows in the form container.
	 */
	function num_rows()
	{
		return $this->_container->num_rows();
	}

	/**
	 * Output the end of the form container row.
	 */
	function end()
	{
		$this->_container->output($this->_title, 1, "general form_container {$this->extra_class}");
	}
}

?>
