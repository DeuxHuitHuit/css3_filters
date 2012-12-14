<?php

	if (!defined('__IN_SYMPHONY__')) die('<h2>Symphony Error</h2><p>You cannot directly access this file</p>');

	require_once(TOOLKIT . '/class.field.php');

	/**
	 *
	 * Field class that will represent some CSS3 Filters
	 * @author Deux Huit Huit
	 *
	 */
	class FieldCss3_Filters extends Field {

		/**
		 *
		 * Name of the field table
		 * @var string
		 */
		const FIELD_TBL_NAME = 'tbl_fields_css3_filters';


		/**
		 *
		 * Constructor for the Field object
		 * @param mixed $parent
		 */
		public function __construct(){
			// call the parent constructor
			parent::__construct();
			// set the name of the field
			$this->_name = __(extension_css3_filters::EXT_NAME);
			// permits to make it required
			$this->_required = false;
			// permits the make it show in the table columns
			$this->_showcolumn = true;
			// set as not required by default
			$this->set('required', 'no');
			// set not unique by default
			$this->set('unique', 'no');

		}

		public function isSortable(){
			return false;
		}

		public function canFilter(){
			return false;
		}

		public function canImport(){
			return false;
		}

		public function canPrePopulate(){
			return false;
		}

		public function mustBeUnique(){
			return ($this->get('unique') == 'yes');
		}

		public function allowDatasourceOutputGrouping(){
			return false;
		}

		public function requiresSQLGrouping(){
			return false;
		}

		public function allowDatasourceParamOutput(){
			return false;
		}

		/* ********** INPUT AND FIELD *********** */

		/**
		 * This function permits parsing different field settings values
		 *
		 * @param array $settings
		 *	the data array to initialize if necessary.
		 */
		public function setFromPOST(Array $settings = array()) {

			// call the default behavior
			parent::setFromPOST($settings);

			// declare a new setting array
			$new_settings = array();

			// always display in table mode
			$new_settings['show_column'] = 'yes';

			// set new settings
			$new_settings['field-handles'] = $settings['field-handles'];

			// save it into the array
			$this->setArray($new_settings);
		}

		/**
		 *
		 * Save field settings into the field's table
		 */
		public function commit() {

			// if the default implementation works...
			if(!parent::commit()) return FALSE;

			$id = $this->get('id');

			// exit if there is no id
			if($id == false) return FALSE;

			// declare an array contains the field's settings
			$settings = array();

			// the field id
			$settings['field_id'] = $id;

			// the related fields handles
			$settings['field-handles'] = $this->get('field-handles');

			// DB
			$tbl = self::FIELD_TBL_NAME;

			Symphony::Database()->query("DELETE FROM `$tbl` WHERE `field_id` = '$id' LIMIT 1");

			// return if the SQL command was successful
			return Symphony::Database()->insert($settings, $tbl);

		}




		/* ******* DATA SOURCE ******* */

		/**
		 *
		 * This array will populate the Datasource included elements.
		 * @return array - the included elements
		 * @see http://symphony-cms.com/learn/api/2.2.3/toolkit/field/#fetchIncludableElements
		 */
		public function fetchIncludableElements() {
			return FALSE;
		}

		/**
		 * Appends data into the XML tree of a Data Source
		 * @param $wrapper
		 * @param $data
		 */
		public function appendFormattedElement(&$wrapper, $data) {
			return FALSE;
		}




		/* ********* UI *********** */

		private function convertHandlesIntoIds($handles) {
			$ids = '';

			if (!empty($handles) && $handles != '*' ) {
				$aHandles = explode(',', $handles);
				$parent_section = $this->get('parent_section');

				foreach ($aHandles as $handle) {
					$where = "AND t1.`element_name` = '$handle'";
					$field = FieldManager::fetch(NULL, $parent_section, 'ASC', 'sortorder', NULL, NULL, $where);
					$fieldId = array_keys($field);
					$fieldId = $fieldId[0];

					if (!empty($fieldId)) {
						$ids .= 'field-' . $field[$fieldId]->get('id') . ',';
					}
				}
			} else {
				$ids = '*'; // valid for all fields
			}

			return $ids;
		}

		/**
		 *
		 * Builds the UI for the publish page
		 * @param XMLElement $wrapper
		 * @param mixed $data
		 * @param mixed $flagWithError
		 * @param string $fieldnamePrefix
		 * @param string $fieldnamePostfix
		 */
		public function displayPublishPanel(&$wrapper, $data=NULL, $flagWithError=NULL, $fieldnamePrefix=NULL, $fieldnamePostfix=NULL) {
			if (!$data) {
				$data = array(
					'hue' => 0,
					'saturation' => 0,
					'brightness' => 0
				);
			}
			$field = new XMLElement('div');
			$field->setValue($this->get('label'));
			$field->setAttribute('data-field-handles', $this->convertHandlesIntoIds($this->get('field-handles')));

			$frame = new XMLElement('span', NULL, array('class' => 'frame'));

			$iHue = $this->createRange('Hue',        'hue',        $data, $flagWithError, $fieldnamePrefix, $fieldnamePostfix);
			$iSat = $this->createRange('Saturation', 'saturation', $data, $flagWithError, $fieldnamePrefix, $fieldnamePostfix);
			$iBri = $this->createRange('Brightness', 'brightness', $data, $flagWithError, $fieldnamePrefix, $fieldnamePostfix);

			$frame->appendChild($iHue);
			$frame->appendChild($iSat);
			$frame->appendChild($iBri);

			$field->appendChild($frame);

			$wrapper->appendChild($field);
		}

		private function createRange($text, $key, $data, $flagWithError=NULL, $fieldnamePrefix=NULL, $fieldnamePostfix=NULL, $min=-255, $max=255) {
			$lbl = new XMLElement('label', __($text), array('class' => ''));
			$input = new XMLElement('input', NULL, array(
				'type' => 'range',
				'value' => $data[$key],
				'name' => 'fields'.$fieldnamePrefix.'['.$this->get('element_name').']'.$fieldnamePostfix . "[$key]",
				'min' => $min,
				'max' => $max
			));
			$input->setSelfClosingTag(true);

			$value = new XMLElement('i');
			$value->appendChild(new XMLElement('span', '('));
			$value->appendChild(new XMLElement('span', $data[$key], array('class' => 'filter-value')));
			$value->appendChild(new XMLElement('span', ')'));

			$lbl->appendChild($value);
			$lbl->appendChild($input);

			if ($flagWithError) {
				$lbl = Widget::wrapFormElementWithError($lbl, $flagWithError);
			}

			return $lbl;
		}

		/**
		 *
		 * Builds the UI for the field's settings when creating/editing a section
		 * @param XMLElement $wrapper
		 * @param array $errors
		 */
		public function displaySettingsPanel(&$wrapper, $errors=NULL){

			/* first line, label and such */
			parent::displaySettingsPanel($wrapper, $errors);

			$handles_wrap = new XMLElement('div', NULL, array('class' => 'css3_filters'));
			$handles_wrap->appendChild( $this->createInput('Fields handles <i>Type * for all fields; Comma separated list for multiple fields; Optional</i>', 'field-handles', $errors) );
			$wrapper->appendChild($handles_wrap);
		}


		private function createInput($text, $key, $errors=NULL) {
			$order = $this->get('sortorder');
			$lbl = new XMLElement('label', __($text), array('class' => 'column'));
			$input = new XMLElement('input', NULL, array(
				'type' => 'text',
				'value' => $this->get($key),
				'name' => "fields[$order][$key]"
			));
			$input->setSelfClosingTag(true);

			$lbl->prependChild($input);

			if (isset($errors[$key])) {
				$lbl = Widget::wrapFormElementWithError($lbl, $errors[$key]);
			}

			return $lbl;
		}

		/**
		 *
		 * Build the UI for the table view
		 * @param Array $data
		 * @param XMLElement $link
		 * @return string - the html of the link
		 */
		public function prepareTableValue($data, XMLElement $link=NULL){
			// does this cell serve as a link ?
			if (!$link){
				// if not, wrap our html with a external link to the resource url
				$link = new XMLElement('div');
			}

			$link->setAttribute('data-field-handles', $this->convertHandlesIntoIds($this->get('field-handles')));
			$link->setAttribute('data-hue', $data['hue']);
			$link->setAttribute('data-sat', $data['saturation']);
			$link->setAttribute('data-bri', $data['brightness']);

			$link->setValue('filter');

			// returns the link's html code
			return $link->generate();
		}

		/**
		 *
		 * Return a plain text representation of the field's data
		 * @param array $data
		 * @param int $entry_id
		 */
		public function preparePlainTextValue($data, $entry_id = null) {
			return NULL;
		}


		/**
		 *
		 * This function allows Fields to cleanup any additional things before it is removed
		 * from the section.
		 * @return boolean
		 */
		public function tearDown() {
			// do nothing
			// this field has no data
			return false;
		}


		/* ********* SQL Data Definition ************* */

		/**
		 *
		 * Creates table needed for entries of invidual fields
		 */
		public function createTable(){
			return Symphony::Database()->query("
				CREATE TABLE IF NOT EXISTS `tbl_entries_data_" . $this->get('id') . "` (
					`id` int(11) unsigned NOT NULL auto_increment,
					`entry_id` int(11) unsigned NOT NULL,
					`saturation` int(3) signed NOT NULL DEFAULT 0,
					`brightness` int(3) signed NOT NULL DEFAULT 0,
					`hue` int(3) signed NOT NULL DEFAULT 0,
					PRIMARY KEY  (`id`),
					KEY `entry_id` (`entry_id`)
				) TYPE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			");
		}

		/**
		 * Creates the table needed for the settings of the field
		 */
		public static function createFieldTable() {

			$tbl = self::FIELD_TBL_NAME;

			return Symphony::Database()->query("
				CREATE TABLE IF NOT EXISTS `$tbl` (
					`id` 				int(11) unsigned NOT NULL auto_increment,
					`field_id` 			int(11) unsigned NOT NULL,
					`field-handles`		varchar(255) NULL,
					PRIMARY KEY (`id`),
					KEY `field_id` (`field_id`)
				)  ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			");
		}


		/**
		 *
		 * Drops the table needed for the settings of the field
		 */
		public static function deleteFieldTable() {
			$tbl = self::FIELD_TBL_NAME;

			return Symphony::Database()->query("
				DROP TABLE IF EXISTS `$tbl`
			");
		}

	}