<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2019 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('radio');

/**
 * Provides selection for starting avatar in OpenSim
 *
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 * @since       2.5.5
 */
class JFormFieldAvatar extends JFormFieldRadio {
	/**
	* The form field type.
	*
	* @var    string
	* @since  2.5.5
	*/
	protected $type = 'Avatar';

	/**
	* Method to get the field label markup.
	*
	* @return  string  The field label markup.
	*
	* @since   2.5.5
	*/
	protected function getLabel() {
		// Initialise variables.
		$label = '';

		if ($this->hidden) {
			return $label;
		}

		$plgRegisterjOpenSim	= JPluginHelper::getPlugin('user', 'jopensimregister');
		$this->params			= new JRegistry($plgRegisterjOpenSim->params);
		$avatarlist				= $this->params->get('plgJopensimRegisterAvatar');
		$requiredField			= $this->params->get('plgJopensimRegisterUser');

		// Get the label text from the XML element, defaulting to the element name.
		$text = $this->element['label'] ? (string) $this->element['label'] : (string) $this->element['name'];
		$text = $this->translateLabel ? JText::_($text) : $text;

		JHtml::_('behavior.modal');

		// Build the class for the label.
		$class = !empty($this->description) ? 'hasPopover' : '';
		if($requiredField == "required") {
			$this->required = true;
			$class =$class . ' required';
		}

		$class = !empty($this->labelClass) ? $class . ' jopensimavatar ' . $this->labelClass : $class.' jopensimavatar';

		// Add the opening label tag and main attributes attributes.
		$label .= '<label id="' . $this->id . '-lbl" for="' . $this->id . '" class="' . $class . '"';

		// If a description is specified, use it to build a tooltip.
		if (!empty($this->description)) {
			$label .= ' data-title="'
				. htmlspecialchars(
                    trim($text),
				ENT_COMPAT, 'UTF-8'
			).'"
            data-content="'.JText::_($this->description).'"';
		}

		$avatararticle = $this->params->get('plgJopensimRegisterAvatarArticle');
		if($avatararticle > 0) {
			$link = '<a class="modal" title="" href="index.php?option=com_content&amp;view=article&amp;layout=modal&amp;id=' . $avatararticle . '&amp;tmpl=component" rel="{handler: \'iframe\', size: {x:800, y:500}}">' . $text . '</a>';
		} else {
			$link = $text;
		}

		// Add the label text and closing tag.
		$label .= '>' . $link . '</label>';
		return $label;
	}


	protected function getOptions() {
		// Initialize variables.
		$options = array();
		$avatars = array();
		$plgRegisterjOpenSim	= JPluginHelper::getPlugin('user', 'jopensimregister');
		$this->params			= new JRegistry($plgRegisterjOpenSim->params);
		$avatarlist				= $this->params->get('plgJopensimRegisterAvatar');
		$requiredField			= $this->params->get('plgJopensimRegisterUser');
		if($requiredField == "required") {
			$this->required = "required ";
		} else {
			$this->required = "";
        }

		if(is_array($avatarlist) && count($avatarlist) > 0) {
			foreach($avatarlist AS $avatar) {
				$zaehler = count($avatars);
				$temp = explode(":",$avatar);
				$avatars[$zaehler]['value'] = $temp[0];
				if(is_file(JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'jopensim'.DIRECTORY_SEPARATOR.'avatars'.DIRECTORY_SEPARATOR.$temp[0].'.jpg')) {
					$attr['title'] = $temp[1];
					$attr['align'] = "absmiddle";
					$img = JHtml::image("images/jopensim/avatars/".$temp[0].".jpg",$temp[1],$attr);
					$avatars[$zaehler]['text'] = $img;
					$avatars[$zaehler]['class'] = $this->required."jopensimavatar";
					$avatars[$zaehler]['required'] = TRUE;
				} elseif(is_file(JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'jopensim'.DIRECTORY_SEPARATOR.'avatars'.DIRECTORY_SEPARATOR.$temp[0].'.png')) {
					$attr['title'] = $temp[1];
					$attr['align'] = "absmiddle";
					$img = JHtml::image("images/jopensim/avatars/".$temp[0].".png",$temp[1],$attr);
					$avatars[$zaehler]['text'] = $img;
					$avatars[$zaehler]['class'] = $this->required."jopensimavatar";
					$avatars[$zaehler]['required'] = TRUE;
				} elseif(is_file(JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'jopensim'.DIRECTORY_SEPARATOR.'avatars'.DIRECTORY_SEPARATOR.$temp[0].'.jpeg')) {
					$attr['title'] = $temp[1];
					$attr['align'] = "absmiddle";
					$img = JHtml::image("images/jopensim/avatars/".$temp[0].".jpeg",$temp[1],$attr);
					$avatars[$zaehler]['text'] = $img;
					$avatars[$zaehler]['class'] = $this->required."jopensimavatar";
					$avatars[$zaehler]['required'] = TRUE;
				} else {
					$avatars[$zaehler]['text'] = $temp[1];
				}
			}
		} else {
			$avatars[0]['value'] = "-1";
			$avatars[0]['text'] = "<div class='label label-danger spacer'>".JText::_('PLG_JOPENSIMREGISTER_ERROR_NOAVATARS')."</div>";
		}

		foreach ($avatars as $option) {
			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_('select.option', (string) $option['value'], trim((string) $option['text']), 'value', 'text');

			// Set some option attributes.
			if(array_key_exists("class",$option)) $tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.
			if(array_key_exists("onclick",$option)) $tmp->onclick = (string) $option['onclick'];

			// Add the option object to the result set.
			$options[] = $tmp;
		}
		reset($options);
		return $options;
	}

	protected function getInput() {
		$html = array();

		$plgRegisterjOpenSim	= JPluginHelper::getPlugin('user', 'jopensimregister');
		$this->params			= new JRegistry($plgRegisterjOpenSim->params);
		$avatarlist				= $this->params->get('plgJopensimRegisterAvatar');
		$requiredField			= $this->params->get('plgJopensimRegisterUser');
		if($requiredField == "required") {
			$this->required = true;
		} else {
			$this->required = false;
		}

		$debug = var_export($this->class,TRUE);
		// Initialize some field attributes.
		$class     = !empty($this->class) ? ' class="radio ' . $this->class . '"' : ' class="radio"';
		$required  = $this->required ? ' required aria-required="true"' : '';
		$autofocus = $this->autofocus ? ' autofocus' : '';
		$disabled  = $this->disabled ? ' disabled' : '';
		$readonly  = $this->readonly;

		// Start the radio field output.
		$html[] = '<fieldset id="' . $this->id . '"' . $class . $required . $autofocus . $disabled . ' style="padding-left: 0px; margin: 0px;" >';

		// Get the field options.
		$options = $this->getOptions();
        $avatarcolumns = $this->params->get('plgJopensimRegisterAvatarColumns', 1);
        $columns = 0;

		// Build the radio field output.
		foreach ($options as $i => $option) {

			$html[] = '<fieldset id="'.$option->value.'" class="jform_jopensimregister_jopensimavatar ">';
			$html[] = '<div class="text-center btn btn-default ">';

			// Initialize some option attributes.
			$checked    = ((string) $option->value == (string) $this->value) ? ' checked="checked"' : '';
			$class      = !empty($option->class) ? ' class="' . $option->class . '"' : '';
			$disabled   = !empty($option->disable) || ($readonly && !$checked);
			$disabled   = $disabled ? ' disabled' : '';
			// Initialize some JavaScript option attributes.
			$onclick    = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
			$onchange   = !empty($option->onchange) ? ' onchange="' . $option->onchange . '"' : '';

			// Avatar picture
			$html[] = '<label for="' . $this->id . $i . '"' . $class . ' >'
				.JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)).'</label>';

			// Avatar radio select
			$html[] = '<input type="radio" style="float:none;margin-left:0px;" id="' . $this->id . $i . '" name="' . $this->name . '" value="'
				.htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8').'"'.$checked." ".$class." ".$required." ".$onclick." ".$onchange." ".$disabled. ' />';

			$required = '';

			$html[] = '</div>';
			$html[] = '</fieldset>';

			// Create Columns
			$columns++;
			if ($columns % $avatarcolumns != true) {
				$html[] = "<div class='clearfix'></div>";
			}
		}
		$html[] = '</fieldset>';

		return implode($html);
	}
}
