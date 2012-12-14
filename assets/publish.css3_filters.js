/*
	Copyight: Deux Huit Huit 2013
	License: MIT, see the LICENCE file
*/

/**
 * Client code for css3_filters
 *
 * @author deuxhuithuit
 */
(function ($, undefined) {

	var 
	
	FIELD = 'field-css3_filters',
	FIELD_CLASS = '.' + FIELD,
	
	_updateParentValue = function (e) {
		var 
		$el = $(this),
		value = $el.val(),
		parentd = $el.data().parent,
		parent = parentd || $el.closest('label').find('i>.filter-value');
		
		parent.text(value);
		
		if (!parentd) {
			$el.data('parent', parent);
		}
	},
	
	_hookOne = function (index, el) {
		var
		$el = $(el),
		input = $('.frame>label>input', $el);
		
		input.change(_updateParentValue);
	},
	
	init = function () {
		$(FIELD_CLASS).each(_hookOne);
	};

	$(init);

})(jQuery);