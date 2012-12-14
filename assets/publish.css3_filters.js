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
	
	INPUTS_CLASS = '.frame>label>input',
	
	ALL_IMG_CLASS = '#contents .field img',
	
	_createFilter = function (container) {
		var 
		inputs = container.find(INPUTS_CLASS),
		hue = inputs.eq(0).val(),
		sat = inputs.eq(1).val(),
		bri = inputs.eq(2).val();
		
		return 'hue-rotate(' + hue + 'deg) saturate(' + sat + '%) brightness(' + bri + '%)';
	},
	
	_applyPreview = function (previewsd, container) {
		var 
		previews = previewsd || $(), // empty
		handles = container.data('field-handles');
		
		if (!previewsd) {
			 if (handles === '*') {
				// all images ????
				previews = $(ALL_IMG_CLASS);
			} else if (!!handles) {
				previews = $();
			} 
		}
		
		if (!!previews.length) {
			var filter = _createFilter(container);
			previews.css(
				//filter: filter ,
				'-webkit-filter', filter
			);
		}
		return previews;
	}
	
	_updatePreview = function (e) {
		var
		$el = $(this),
		container = $el.closest(FIELD_CLASS),
		previewsd = $el.data().previews,
		previews = _applyPreview(previewsd, container);
		
		if (!previewsd) {
			$el.data('previews', previews);
		}
	},
	
	_updateParentValue = function (e) {
		var 
		$el = $(this),
		value = $el.val(),
		parentd = $el.data().parent,
		parent = parentd || $el.closest('label').find('i>.filter-value');
		
		// update value
		parent.text(value);
		
		if (!parentd) {
			$el.data('parent', parent);
		}
	},
	
	_applyOne = function (index, el) {
		_applyPreview(null, $(el));
	},
	
	_hookOne = function (index, el) {
		var
		$el = $(el),
		input = $(INPUTS_CLASS, $el);
		
		input
			.change(_updateParentValue)
			.change(_updatePreview);
	},
	
	load = function () {
		$(FIELD_CLASS).each(_applyOne);
	},
	
	init = function () {
		$(FIELD_CLASS).each(_hookOne);
		$(window).on('load', ALL_IMG_CLASS, load); // not working !!!
		
		// lame
		var 
		count = 0,
		tick = function () {
			if (count < 5) {
				console.log('loading css filters');
				load();
				count++;
				timer();
			}
		},
		timer = function () {
			setTimeout(tick, 500);
		};
		timer();
	};

	$(init);

})(jQuery);