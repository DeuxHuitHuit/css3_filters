<?php
	/*
	Copyight: Deux Huit Huit 2012
	License: MIT, see the LICENCE file
	*/

	if(!defined("__IN_SYMPHONY__")) die("<h2>Error</h2><p>You cannot directly access this file</p>");

	require_once(EXTENSIONS . '/css3_filters/fields/field.css3_filters.php');

	/**
	 *
	 * @author Deux Huit Huit
	 * http://www.deuxhuithuit.com
	 *
	 */
	class extension_css3_filters extends Extension {

		/**
		 * Name of the extension
		 * @var string
		 */
		const EXT_NAME = 'CSS3 Filters';

		/**
		 *
		 * Symphony utility function that permits to
		 * implement the Observer/Observable pattern.
		 * We register here delegate that will be fired by Symphony
		 */
		public function getSubscribedDelegates(){
			return array(
				array(
					'page' => '/backend/',
					'delegate' => 'InitaliseAdminPageHead',
					'callback' => 'appendResources'
				),
			);
		}


		/*********** DELEGATES ***********************/

		/**
		 *
		 * Appends resource (js/css) files referneces into the head, if needed
		 * @param array $context
		 */
		public function appendResources(Array $context) {
			// store de callback array localy
			$c = Administration::instance()->getPageCallback();

			// publish page, new or edit
			if(isset($c['context']['section_handle']) && in_array($c['context']['page'], array('new', 'edit'))){

				Administration::instance()->Page->addStylesheetToHead(
					URL . '/extensions/css3_filters/assets/publish.css3_filters.css',
					'screen',
					time(),
					false
				);

				Administration::instance()->Page->addScriptToHead(
					URL . '/extensions/css3_filters/assets/publish.css3_filters.js',
					time() + 1,
					false
				);

				return;
			}
		}


		/* ********* INSTALL/UPDATE/UNISTALL ******* */

		/**
		 * Creates the table needed for the settings of the field
		 */
		public function install() {
			return FieldCss3_Filters::createFieldTable();

		}

		/**
		 * Creates the table needed for the settings of the field
		 */
		public function update($previousVersion) {
			return true;
		}

		/**
		 *
		 * Drops the table needed for the settings of the field
		 */
		public function uninstall() {
			return FieldCss3_Filters::deleteFieldTable();
		}

	}