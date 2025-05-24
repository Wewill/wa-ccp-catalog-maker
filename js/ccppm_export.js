( function($) {
	$( "#selected_fields_all" ).click(function() {
		$('#selected_fields_fieldset input[type=checkbox]').prop('checked', true);
	});
	$( "#selected_fields_none" ).click(function() {
		$('#selected_fields_fieldset input[type=checkbox]').prop('checked', false);
	});
	$( "#sortable" ).sortable({
	});
	$( "#sortable" ).disableSelection();
	$( "#trash" ).droppable({
		drop: function( event, ui ) {
			$(ui.draggable).remove();
		}
	});

	$('#fields_sortable_button').click(function() {
		var fields_sortable = $('#fields_sortable');
		var elements = $('#sortable li.ui-state-default');
		var data = [];

		elements.each(function(idx, element) {
			data.push($(element).data('json'));
		});
		
		fields_sortable.val(btoa(JSON.stringify(data)));
		$('#formular_filter').submit();
	});

	$('#filter_field_append').click(function () {
		var option = jQuery('#filter_field_append option:selected');
		var selected_type = option.data('type');
		var options_comparator = jQuery('#filter_comparator_append option');
		options_comparator.each(function() {
			var element = jQuery(this);
			var data = element.data('selector');
			if (data) {
				var types = JSON.parse(atob(data));
				if (types.includes(selected_type)) {
					element.css('display', 'block');
				} else {
					element.css('display', 'none');
				}
			}
		});
	});

  } )(jQuery);
