<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('text');

/**
 * Form Field class for the Joomla Platform.
 * Supports a one line text field.
 *
 * @link   http://www.w3.org/TR/html-markup/input.text.html#input.text
 * @since  11.1
 */
class JFormFieldJopensimnames extends JFormFieldText {

	protected $type = 'Jopensimnames';

	public function __construct($form = null) {
		parent::__construct();
		$this->required = true;
	}

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput() {
		$plgRegisterjOpenSim	= JPluginHelper::getPlugin('user', 'jopensimregister');
		$this->params			= new JRegistry($plgRegisterjOpenSim->params);
		$requiredField			= $this->params->get('plgJopensimRegisterUser');
		if($requiredField == "required") {
			$this->class = "required";
			if(is_array($this->labelclass)) $this->labelclass[] = "required";
			elseif($this->labelclass) $this->labelclass .= " required";
			else $this->labelclass = "required";
			$this->__set('required',1);
		}
		return parent::getInput();
	}

	protected function getLabel() {
		$plgRegisterjOpenSim	= JPluginHelper::getPlugin('user', 'jopensimregister');
		$this->params			= new JRegistry($plgRegisterjOpenSim->params);
		$requiredField			= $this->params->get('plgJopensimRegisterUser');
		if($requiredField == "required") {
			$this->required = 'true';
		}

		if ($this->hidden) {
			return '';
		}

		$data = $this->getLayoutData();

		// Forcing the Alias field to display the tip below
		$position = $this->element['name'] == 'alias' ? ' data-placement="bottom" ' : '';

		// Here mainly for B/C with old layouts. This can be done in the layouts directly
		$extraData = array(
			'text'        => $data['label'],
			'for'         => $this->id,
			'classes'     => explode(' ', 'hasPopover required'),
			'position'    => $position,
		);

		return $this->getRenderer($this->renderLabelLayout)->render(array_merge($data, $extraData));
	}
}
