var ccppm_catalogues_select = false;
var ccppm_catalogue_masters_select = false;
var ccppm_global = {};
var ccppm_tags_to_keep = [
  'br',
  'i',
  'strong',
  'b',
  'img',
  'u',
  'ul',
  'li',
  'em',
  'h1',
  'h2',
  'h3',
  'h4',
  'h5',
  'h6',
  'p',
  'span',
  'aside',
];


(function($) {
  ccppm_catalogues_select = $('#ccppm_catalogues_select');
  ccppm_catalogue_masters_select = $('#ccppm_catalogue_masters_select');
  var ccppm_catalogue_btn_new = $('#ccppm_catalogue_btn_new');
  var ccppm_catalogue_new = $('#ccppm_catalogue_new');
  var ccppm_catalogue_new_name = $('#ccppm_catalogue_new_name');
  var ccppm_catalogue_btn_new_append = $('#ccppm_catalogue_btn_new_append');
  var ccppm_catalogue_btn_new_cancel = $('#ccppm_catalogue_btn_new_cancel');
  var ccppm_catalogue_btn_save = $('#ccppm_catalogue_btn_save');
  var ccppm_catalogue_btn_delete = $('#ccppm_catalogue_btn_delete');
  var ccppm_catalogue_popup_template_select = $('#ccppm_catalogue_popup_template_select');
  var ccppm_catalogue_popup_data_select = $('#ccppm_catalogue_popup_data_select');
  var ccppm_catalogue_popup_data2 = $('#ccppm_catalogue_popup_data2');
  var ccppm_catalogue_popup_data2_select = $('#ccppm_catalogue_popup_data2_select');
  var ccppm_catalogue_popup_section_display_by_select = $('#ccppm_catalogue_popup_section_display_by_select');
  var ccppm_catalogue_btn_render = $('#ccppm_catalogue_btn_render');
  var ccppm_catalogue_btn_download = $('#ccppm_catalogue_btn_download');
  var ccppm_catalogue_popup_html_title = $('#ccppm_catalogue_popup_html_title');
  var ccppm_catalogue_popup_html_color = $('#ccppm_catalogue_popup_html_color');
  var ccppm_catalogue_popup_htmlx2_title = $('#ccppm_catalogue_popup_htmlx2_title');
  var ccppm_catalogue_popup_htmlx2_color = $('#ccppm_catalogue_popup_htmlx2_color');

  var temp_data = {};

  // récupérer la liste des catalogues
  var ccppm_catalogues_select_update = function(id) {
    var name = id;
    var callback_get_catalogues = function(data) {
      ccppm_catalogues_select.empty();
      ccppm_catalogues_select.append('<option value=""> *** Select a catalogue ***</option>');
      $(data).each(function(idx, value) {
        ccppm_catalogues_select.append('<option value="'+value+'"'+((value == name)?'SELECTED="SELECTED"':"")+'>'+value+'</option>');
      });
    }
    ccppm_ajax('get_catalogues', {}, callback_get_catalogues);
  }

  // récupérer la liste des masters
  var ccppm_catalogue_masters_select_update = function(id) {
    var name = id;
    var callback_get_catalogue_masters = function(data) {
      ccppm_catalogue_masters_select.empty();
      ccppm_catalogue_masters_select.append('<option value=""> *** Select a master ***</option>');
      $(data).each(function(idx, value) {
        ccppm_catalogue_masters_select.append('<option value="'+value+'"'+((value == name)?'SELECTED="SELECTED"':"")+'>'+value+'</option>');
      });
    }
    ccppm_ajax('get_catalogue_masters', {}, callback_get_catalogue_masters);
  }

  ccppm_catalogues_select_update();
  ccppm_catalogue_masters_select_update();

/*
  Gestion des catalogues
*/
  ccppm_catalogues_select.on('change', function(event) {
    var id = ccppm_catalogues_select.val();
    catalogue_id = id;
    var ccppm_catalogues_select_change_callback = function(data) {
      catalogue_master = data['catalogue']['master'];
      ccppm_catalogue_masters_select_update(data['catalogue']['master']);
      set_catalogue_content(data['catalogue']['content']);
      if (data['data'])
        set_catalogue_values(data['data']);
    }
    ccppm_ajax('get_catalogue', {'id': id}, ccppm_catalogues_select_change_callback);
  });

  ccppm_catalogue_btn_save.on('click', function(event) {
    var id = ccppm_catalogues_select.val();
    var master = ccppm_catalogue_masters_select.val();
    var ccppm_catalogue_save_click_callback = function(data) {
      if (data['master'])
        ccppm_catalogue_masters_select_update(data['master']);
      if (data['content'])
        set_catalogue_content(data['content']);
      if (data['data'])
        set_catalogue_values(data['data']);
    }
    var content = get_catalogue_content();
    ccppm_ajax('set_catalogue', {'id': id, 'data': {'master': master, 'content': content}}, ccppm_catalogue_save_click_callback);
  });

  ccppm_catalogue_btn_delete.on('click', function(event) {
    if(confirm('Are you sure, you went to remove this catalogue ?')) {
      var id = ccppm_catalogues_select.val();
      var ccppm_catalogue_delete_click_callback = function(data) {
        ccppm_catalogues_select_update();
        ccppm_catalogue_masters_select_update('');
        set_catalogue_values({});
      }
      ccppm_ajax('delete_catalogue', {'id': id}, ccppm_catalogue_delete_click_callback);
    }
  });

  ccppm_catalogue_btn_new.on('click', function(event) {
    if (ccppm_catalogue_new.css('display') != 'inline-block')
      ccppm_catalogue_new.css('display', 'inline-block');
    else
      ccppm_catalogue_new.css('display', 'none');
  });

  ccppm_catalogue_btn_new_cancel.on('click', function(event) {
    ccppm_catalogue_new.css('display', 'none');
    ccppm_catalogue_new_name.val('noname');
  });

  ccppm_catalogue_btn_render.on('click', function(event) {
    ccppm_generate();
  });
  ccppm_catalogue_btn_download.on('click', function(event) {
    ccppm_generate(true);
  });

  /*
    Generation de la visualisation catalogue
  */
  function ccppm_generate(download = false) {
    console.clear();
    if (!catalogue_id)
      return false;
    lastGen = new Date();
    var dd = false;
    var data = temp_data;
    var master = ccppm_catalogue_masters_select.val();
    display_javascript('_Javascript');
    if ($('#dpi_selector').val()) ccppm_global.renderDpi = $('#dpi_selector').val();

    dd = display_template(master, data);

    if (download) {
      var filename = ccppm_catalogues_select.val()+"_"+ccppm_global.renderDpi+"dpi_"+(new Date()).toISOString()+".pdf";
      console.log(filename);
      // si toutes les autres methodes de debug on foirées
      //      console.log(dd);
      pdfMake.createPdf(dd).download(filename);
    } else {
      pdfMake.createPdf(dd).getDataUrl(function(outDoc) {
        console.log('generated in ' + (new Date().getTime() - lastGen.getTime()) + ' ms');
        $('#ccppm_catalogue_container_right').attr('src', outDoc);
      });
    }
  }

  /*
    Gestion des données
  */
  var get_catalogue_content = function () {
    var data = [];
    $('#ccppm_catalogue_config_sortable').children('li').each(function(idx, element) {
      var s_data = $(element).data();
      var d_data = {};
      $.each(s_data, function(key, value) {
        if ($.inArray(key, ['sortableItem', 'uiSortableItem']) == -1) {
          d_data[key] = value;
        }
      });
      data.push(d_data);
    });

    return data;
  }

  var set_catalogue_content = function (data) {
    var sortable = $('#ccppm_catalogue_config_sortable');
    sortable.empty();
    $.each(data, function(idx, row) {
      var element_infos = {};
      var title = row['type'].substr(0,1).toUpperCase() + row['type'].substr(1);
      var element = $('<li class="ccppm_catalogue_items_item" rel="'+row['type']+'">'+title+'</li>')
      var error = false;
      $.each(row, function(key, value) {
        if (key != '_display' && key != '_error') {
          element.data(key, value);
          var title_element = key.substr(0,1).toUpperCase() + key.substr(1);
          if (row['_display'] && row['_display'][key]) {
            element_infos[title_element] = row['_display'][key];
          } else {
            element_infos[title_element] = value;
          }
        }
        if (key == '_error') {
          element_infos['warning'] = value;
          error = true;
        }
      });
      if (error) element.addClass('ccppm_catalogue_content_error');
      ccppm_catalogue_element_init(element);
      set_element_info(element, element_infos);
      sortable.append(element);
    });
  }

  var set_catalogue_values = function (data) {
  }

  /*
    POPUPs
  */
  var current_element_popup = false;
  var ccppm_display_popup = function (type=false, element=false) {
    if (type) {
      current_element_popup = element;
      $('#ccppm_catalogue_popup_background').css('display', 'block');
      $('#ccppm_catalogue_popup').css('display', 'block');
      $('#ccppm_catalogue_popup').children('div.ccppm_catalogue_popup_type').each(function(idx, element) {
        $(element).children('div.ccppm_catalogue_popup_type_inside').each(function(idx2, element2) {
		$(element2).css('display', 'none');
	});
        $(element).css('display', 'none');
      });
      var template = element.data('template');
      //pre seletion
      if (type == 'page_break') {
        template = '_page_break';
      }
      ccppm_catalogue_popup_load_template( type, template );
      if (type == 'media') {
        $('#ccppm_catalogue_popup_media').css('display', 'block');
        var media_id = $(element).data('media_id');
        if (media_id != undefined) {
          var media_url = $(element).data('media_url');
          $('#ccppm_catalogue_popup_media_id').val(media_id);
          $('#ccppm_catalogue_popup_media_url').text(media_url);
          $('#ccppm_catalogue_popup_media_preview').attr('src', media_url);
        } else {
          $('#ccppm_catalogue_popup_media_id').val('');
          $('#ccppm_catalogue_popup_media_url').text('');
          $('#ccppm_catalogue_popup_media_preview').attr('src', '');
        }
      } else if (type == 'html') {
        $('#ccppm_catalogue_popup_html').css('display', 'block');
        var html = $(element).data('html');
        if (html == undefined)
          html = '';
        $("div#ccppm_catalogue_popup_html_rte").trumbowyg('html', html);
        ccppm_catalogue_popup_html_title.val($(element).data('title'));
        ccppm_catalogue_popup_html_color.val($(element).data('color'));
        $('#ccppm_catalogue_popup_html_render').attr('src', '');
      } else if (type == 'htmlx2') {
        $('#ccppm_catalogue_popup_htmlx2').css('display', 'block');
        var html = $(element).data('html');
        if (html == undefined)
          html = '';
        var html2 = $(element).data('html2');
        if (html2 == undefined)
          html2 = '';
        $("div#ccppm_catalogue_popup_htmlx2_1_rte").trumbowyg('html', html);
        $("div#ccppm_catalogue_popup_htmlx2_2_rte").trumbowyg('html', html2);
        ccppm_catalogue_popup_htmlx2_title.val($(element).data('title'));
        ccppm_catalogue_popup_htmlx2_color.val($(element).data('color'));
        $('#ccppm_catalogue_popup_htmlx2_render').attr('src', '');
      } else if (type == 'toc') {
        $('#ccppm_catalogue_popup_toc').css('display', 'block');
      } else if (type == 'index') {
        $('#ccppm_catalogue_popup_index').css('display', 'block');
      } else if (type == 'page_break') {
        $('#ccppm_catalogue_popup_other').css('display', 'block');
      } else {
	if (type == '2sections') {
		$('#ccppm_catalogue_popup_data2').css('display', 'block');
		var data2 = $(element).data('data2');
		ccppm_catalogue_popup_load_data2( type, data2 );
	} else {
		ccppm_catalogue_popup_load_data2( type, data2 );
	}
      	if (type == 'section') { 
        	$('#ccppm_catalogue_popup_data3').css('display', 'block');
		var displayBy = 'film';
		if ($(element).data('displayBy'))
			displayBy = $(element).data('displayBy');
		ccppm_catalogue_popup_section_display_by_select.val(displayBy);
	}
        //type d'objet
        $('#ccppm_catalogue_popup_data').css('display', 'block');
        var data = $(element).data('data');
        var order = $(element).data('order');
        ccppm_catalogue_popup_load_data( type, data );
        if (order != undefined)
          ccppm_catalogue_popup_set_order( order );
      }
    } else {
      current_element_popup = false;
      $('#ccppm_catalogue_popup_background').css('display', 'none');
      $('#ccppm_catalogue_popup').css('display', 'none');
    }
  }

  var ccppm_catalogue_popup_set_order = function(order) {
    $('#ccppm_catalogue_popup_data_order').val(order);
  }

  var ccppm_catalogue_popup_load_template = function( type, name = false ) {
    var callback_get_catalogue_templates = function( data ) {
      ccppm_catalogue_popup_template_select.empty();
      ccppm_catalogue_popup_template_select.append('<option value=""> *** Select a template ***</option>');
      $(data).each(function(idx, value) {
        ccppm_catalogue_popup_template_select.append('<option value="'+value+'"'+((value == name)?'SELECTED="SELECTED"':"")+'>'+value+'</option>');
      });
    }
    ccppm_ajax('get_catalogue_templates', {type: type}, callback_get_catalogue_templates);
  }

  var ccppm_catalogue_popup_load_data = function( type, id = false ) {
    var callback_ccppm_catalogue_popup_load_data = function( data ) {
      ccppm_catalogue_popup_data_select.empty();
      ccppm_catalogue_popup_data_select.append('<option value=""> *** Select a data ***</option>');
      $.each(data, function(t_id, t_value) {
//        console.log(t_value);
        ccppm_catalogue_popup_data_select.append('<option value="'+t_id+'"'+((t_id == id)?'SELECTED="SELECTED"':"")+'>'+t_value+'</option>');
      });
    }
    ccppm_ajax('get_catalogue_element_data', {type: type}, callback_ccppm_catalogue_popup_load_data);
  }

  var ccppm_catalogue_popup_load_data2 = function( type, id = false ) {
    var callback_ccppm_catalogue_popup_load_data2 = function( data ) {
      ccppm_catalogue_popup_data2_select.empty();
      ccppm_catalogue_popup_data2_select.append('<option value=""> *** Select a data ***</option>');
      $.each(data, function(t_id, t_value) {
//        console.log(t_value);
        ccppm_catalogue_popup_data2_select.append('<option value="'+t_id+'"'+((t_id == id)?'SELECTED="SELECTED"':"")+'>'+t_value+'</option>');
      });
    }
    ccppm_ajax('get_catalogue_element_data', {type: type}, callback_ccppm_catalogue_popup_load_data2);
  }

  $('#ccppm_catalogue_popup_background').on('click', function(event) {
    event.preventDefault();
    ccppm_display_popup(false);
    $("div#ccppm_catalogue_popup_html_rte").trumbowyg('empty');
    $("div#ccppm_catalogue_popup_htmlx2_1_rte").trumbowyg('empty');
    $("div#ccppm_catalogue_popup_htmlx2_2_rte").trumbowyg('empty');
  });

  /*
    Bouttons SAUVER sur popup
  */
  $('#ccppm_catalogue_popup_media_save').on('click', function(event) {
    event.preventDefault();
    var data = {
      'template': $('#ccppm_catalogue_popup_template_select').val(),
      'media_id': $('#ccppm_catalogue_popup_media_id').val(),
      'media_url': $('#ccppm_catalogue_popup_media_url').text(),
      'type': $(current_element_popup).attr('rel'),
    };
    set_element_info(current_element_popup, {
      'Template': $('#ccppm_catalogue_popup_template_select').children('option:selected').text(),
      'Url': $('#ccppm_catalogue_popup_media_url').text(),
    });
    current_element_popup.data(data);
    ccppm_display_popup(false);
  });

  $('#ccppm_catalogue_popup_toc_save').on('click', function(event) {
    event.preventDefault();
    data = {
      'template': $('#ccppm_catalogue_popup_template_select').val(),
      'type': $(current_element_popup).attr('rel'),
    };
    set_element_info(current_element_popup, {
      'Template': $('#ccppm_catalogue_popup_template_select').children('option:selected').text(),
    });
    current_element_popup.data(data);
    ccppm_display_popup(false);
  });

  $('#ccppm_catalogue_popup_index_save').on('click', function(event) {
    event.preventDefault();
    data = {
      'template': $('#ccppm_catalogue_popup_template_select').val(),
      'type': $(current_element_popup).attr('rel'),
    };
    set_element_info(current_element_popup, {
      'Template': $('#ccppm_catalogue_popup_template_select').children('option:selected').text(),
    });
    current_element_popup.data(data);
    ccppm_display_popup(false);
  });

  $('#ccppm_catalogue_popup_html_save').on('click', function(event) {
    event.preventDefault();
    var html = $("div#ccppm_catalogue_popup_html_rte").html();
    data = {
      'template': $('#ccppm_catalogue_popup_template_select').val(),
      'html': html,
      'type': $(current_element_popup).attr('rel'),
      'title': ccppm_catalogue_popup_html_title.val(),
      'color': ccppm_catalogue_popup_html_color.val(),
    };
    set_element_info(current_element_popup, {
      'Template': $('#ccppm_catalogue_popup_template_select').children('option:selected').text(),
      'Title': ccppm_catalogue_popup_html_title.val(),
      'Color': ccppm_catalogue_popup_html_color.val(),
      'HTML': html,
    });
    $("div#ccppm_catalogue_popup_html_rte").trumbowyg('empty');

    current_element_popup.data(data);
    ccppm_display_popup(false);
  });

  $('#ccppm_catalogue_popup_htmlx2_save').on('click', function(event) {
    event.preventDefault();
    var html = $("div#ccppm_catalogue_popup_htmlx2_1_rte").html();
    var html2 = $("div#ccppm_catalogue_popup_htmlx2_2_rte").html();
    data = {
      'template': $('#ccppm_catalogue_popup_template_select').val(),
      'html': html,
      'html2': html2,
      'type': $(current_element_popup).attr('rel'),
      'title': ccppm_catalogue_popup_htmlx2_title.val(),
      'color': ccppm_catalogue_popup_htmlx2_color.val(),
    };
    set_element_info(current_element_popup, {
      'Template': $('#ccppm_catalogue_popup_template_select').children('option:selected').text(),
      'Title': ccppm_catalogue_popup_htmlx2_title.val(),
      'Color': ccppm_catalogue_popup_htmlx2_color.val(),
      'HTML': html,
      'HTML2': html2,
    });
    $("div#ccppm_catalogue_popup_htmlx2_1_rte").trumbowyg('empty');
    $("div#ccppm_catalogue_popup_htmlx2_2_rte").trumbowyg('empty');

    current_element_popup.data(data);
    ccppm_display_popup(false);
  });

  $('#ccppm_catalogue_popup_data_save').on('click', function(event) {
    event.preventDefault();
    data = {
      'template': $('#ccppm_catalogue_popup_template_select').val(),
      'data': $('#ccppm_catalogue_popup_data_select').val(),
      'data2': $('#ccppm_catalogue_popup_data2_select').val(),
      'displayBy': $('#ccppm_catalogue_popup_section_display_by_select').val(),
      'order': $('#ccppm_catalogue_popup_data_order').val(),
      'type': $(current_element_popup).attr('rel'),
    };
    set_element_info(current_element_popup, {
      'Template': $('#ccppm_catalogue_popup_template_select').children('option:selected').text(),
      'Data': $('#ccppm_catalogue_popup_data_select').children('option:selected').text(),
      'Data2': ($('#ccppm_catalogue_popup_data2_select').val())?$('#ccppm_catalogue_popup_data2_select').children('option:selected').text():"",
      'DisplayBy': ($('#ccppm_catalogue_popup_section_display_by_select').val())?$('#ccppm_catalogue_popup_section_display_by_select').children('option:selected').text():"",
      'Order': $('#ccppm_catalogue_popup_data_order').children('option:selected').text(),
    });
    current_element_popup.data(data);
    ccppm_display_popup(false);
  });

  $('#ccppm_catalogue_popup_other_save').on('click', function(event) {
    event.preventDefault();
    data = {
      'template': $('#ccppm_catalogue_popup_template_select').val(),
      'type': $(current_element_popup).attr('rel'),
    };
    current_element_popup.data(data);
    ccppm_display_popup(false);
  });

  var set_element_info = function (element, data = false) {
    var div_infos = element.children('div.ccppm_catalogue_element_infos');
    div_infos.empty();
    if (data != false) {
      $.each(data, function(key, value){
        if (value)
          div_infos.append('<div><span>'+key+' : </span><span>'+value+'</span></div>');
      });
    } else {
      $.each(element.data(), function(key, value){
        if (value) {
          if ($.inArray(key, ['type']) == -1) {
            var title = key.substr(0,1).toUpperCase() + key.substr(1);
            div_infos.append('<div><span>'+title+' : </span><span>'+value+'</span></div>');
          }
        }
      });
    }
  }

  var file_frame;
//  var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
//  var set_to_post_id = 0; // Set this
	$('#ccppm_catalogue_popup_media_upload').on('click', function( event ){
		event.preventDefault();
		// If the media frame already exists, reopen it.
    var set_to_post_id = $('#ccppm_catalogue_popup_media_id').val();
		if ( file_frame ) {
			// Set the post ID to what we want
//			file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
			// Open frame
			file_frame.open();
			return;
		} else {
			// Set the wp.media post id so the uploader grabs the ID we want when initialised
//			wp.media.model.settings.post.id = set_to_post_id;
		}
		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: 'Select a media to upload',
			button: {
				text: 'Use this media',
			},
			multiple: false	// Set to true to allow multiple files to be selected
		});
		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			attachment = file_frame.state().get('selection').first().toJSON();
			// Do something with attachment.id and/or attachment.url here
      $( '#ccppm_catalogue_popup_media_url' ).text( attachment.url );
			$( '#ccppm_catalogue_popup_media_id' ).val( attachment.id );
      $( '#ccppm_catalogue_popup_media_preview' ).attr( 'src', attachment.url ).css( 'max-height', '300px' );
			// Restore the main post ID
//			wp.media.model.settings.post.id = wp_media_post_id;
		});
			// Finally, open the modal
			file_frame.open();
	});

/*
  catalogue append
*/

  ccppm_catalogue_btn_new_append.on('click', function(event) {
    var master = ccppm_catalogue_masters_select.val();
    var ccppm_catalogue_btn_new_append_callback = function(data) {
      ccppm_catalogues_select_update(data['id']);
      ccppm_catalogue_new.css('display', 'none');
      ccppm_catalogue_new_name.val('noname');
      if (data['content'])
        set_catalogue_content(data['content']);
      if (data['data'])
        set_catalogue_values(data['data']);
    }
    ccppm_ajax('set_catalogue', {'new': true, 'data': {'master': master, 'content': get_catalogue_content()}, 'id': ccppm_catalogue_new_name.val()}, ccppm_catalogue_btn_new_append_callback);
  });

  $("#ccppm_catalogue_config_sortable").sortable({
    revert: true,
    receive : function( event, ui ){
      var element = current_element_popup;
      type = element.attr('rel');
      data = element.data();

      ccppm_catalogue_element_init(element);
      ccppm_display_popup(type, element);
    }
  });

  var ccppm_catalogue_element_init = function(element) {
    var text = element.text();
    element.html('<strong>'+text+' </strong>');
    var modify = $('<span class="ccppm_catalogue_element_modify dashicons dashicons-admin-generic"></span>');
    var del = $('<span class="ccppm_catalogue_element_delete dashicons dashicons-trash"></span>');
    modify.on('click', function(event) {
      var type = element.attr('rel');
      ccppm_display_popup(type, element);
    });
    del.on('click', function(event) {
      if (confirm('Remove this element ?'))
        element.remove();
    });
    element.append(modify);
    element.append(del);
    element.append('<div class="ccppm_catalogue_element_infos"></div>');
  }

  $('.ccppm_catalogue_items_item').draggable({
    containment : '#ccppm_catalogue_config',
    connectToSortable: "#ccppm_catalogue_config_sortable",
    revert : 'invalid',
    helper: 'clone',
  });

  $('#ccppm_catalogue_config_sortable').droppable({
    accept: '.ccppm_catalogue_items_item',
    drop: function(event, ui) {
      current_element_popup = ui.draggable;
    }
  });
  $( "ul.ccppm_catalogue_config_position, ul.ccppm_catalogue_config_position li" ).disableSelection();

  $("div#ccppm_catalogue_popup_html_rte").trumbowyg({
    imageWidthModalEdit: true,
    urlProtocol: true,
    removeformatPasted: false,
    tagsToKeep: ccppm_tags_to_keep,
    plugins: {
      allowTagsFromPaste: {
        allowedTags: ccppm_tags_to_keep
      }
    }
  });
  $("div#ccppm_catalogue_popup_htmlx2_1_rte").trumbowyg({
    imageWidthModalEdit: true,
    urlProtocol: true,
    removeformatPasted: false,
    tagsToKeep: ccppm_tags_to_keep,
    plugins: {
      allowTagsFromPaste: {
        allowedTags: ccppm_tags_to_keep
      }
    }
  });
  $("div#ccppm_catalogue_popup_htmlx2_2_rte").trumbowyg({
    imageWidthModalEdit: true,
    urlProtocol: true,
    removeformatPasted: false,
    tagsToKeep: ccppm_tags_to_keep,
    plugins: {
      allowTagsFromPaste: {
        allowedTags: ccppm_tags_to_keep
      }
    }
  });

  /*
    Generation de la visualisation html / htmlx2 catalogue
  */

  const remplaceImageGrey = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIW2PIycn7DwAE1AJGuYpknAAAAABJRU5ErkJggg==';
  const dictRemplaceImages = {};
  const dictReplaceImagesRatio = {}; 
  function getMeta(url, callback) {
    const img = new Image();
    img.src = url;
    img.onload = function() { callback(this.width, this.height); }
  }
  function ccppm_catalogue_inner_get_ratio(content) {
    const re_img = /<img([^>]*)\ssrc=['"]([^'"]+)['"]([^>]*)>/gi;
    for (let match of content.matchAll(re_img)) {
	let [full, args1, src, args2] = match;
	if (!Object.keys(dictReplaceImagesRatio).includes(src)) {
	  getMeta(src, function(width, height)  { 
	    dictReplaceImagesRatio[src] = width / height;
        //    console.log('ratio', dictReplaceImagesRatio);
          });
	}
    }
  }

  $('#ccppm_catalogue_popup_html_btn_get_ratio').on('click', function(event) {
    event.preventDefault();
    var html = $("div#ccppm_catalogue_popup_html_rte").html();
    ccppm_catalogue_inner_get_ratio(html);
  });
  $('#ccppm_catalogue_popup_htmlx2_btn_get_ratio').on('click', function(event) {
    event.preventDefault();
    var html = $("div#ccppm_catalogue_popup_htmlx2_1_rte").html();
    var html2 = $("div#ccppm_catalogue_popup_htmlx2_2_rte").html();
    ccppm_catalogue_inner_get_ratio(html + html2);
  });
  function ccppm_catalogue_inner_generate_get_ratio(content) {
	const re_img = /<img([^>]*)\ssrc=['"]([^'"]+)['"]([^>]*)>/gi;
	const re_args = /([-._a-zA-Z0-9]+)=['"]([^'"]+)['"]/gi;
	for (let match of content.matchAll(re_img)) {
		let [full, args1, src, args2] = match;
		if (Object.keys(dictRemplaceImages).includes(full)) {
			content = content.replace(full, dictRemplaceImages[full]);
		} else if (Object.keys(dictReplaceImagesRatio).includes(src)) {
			args={}
			for(let match2 of (args1 + ' ' + args2).matchAll(re_args)) {
				let [full2, key, value] = match2;
				args[key] = value; 
			}
			if (Object.keys(args).includes('width')) {
				let width = parseInt(args['width']);
				let ratio = dictReplaceImagesRatio[src];
				args['height'] = width / ratio;
			}
			args_string = '';
			for(var key in args) {
				value = args[key];
				args_string += ' '+key+'="'+value+'"';
			}	
			let replace = "<img src=\""+remplaceImageGrey+"\""+args_string+"/>";
			content = content.replace(full, replace);
			dictRemplaceImages[full] = replace;
		} else {
			let replace = "<img  "+args1+" src=\""+remplaceImageGrey+"\" "+args2+"/>";
			content = content.replace(full, replace);
		}	
	}	
	return content;
  }

  function ccppm_catalogue_inner_generate(output_div_id, data, master, template) {
    console.clear();
    var dd = false;
    if (data['html']) {
        data['html'] = ccppm_catalogue_inner_generate_get_ratio(data['html']); 
    }
    if (data['html2']) {
        data['html2'] = ccppm_catalogue_inner_generate_get_ratio(data['html2']); 
    }
    display_javascript('_Javascript');
    if ($('#dpi_selector').val()) ccppm_global.renderDpi = $('#dpi_selector').val();
    tmp_display_content = window.display_content;
    window.display_content = function() { console.log('Dont display_content'); return {};}
    dd = display_template(master);
    window.display_content = tmp_display_content;
    tmp_display_content = null;
    dd.content = display_template(template, data);

    pdfMake.createPdf(dd).getDataUrl(function(outDoc) {
      $('#'+output_div_id).attr('src', outDoc);
    });
  }

  $('#ccppm_catalogue_popup_html_btn_render').on('click', function(event) {
    event.preventDefault();
    var master = ccppm_catalogue_masters_select.val();
    var template = $('#ccppm_catalogue_popup_template_select').val();
    var html = $("div#ccppm_catalogue_popup_html_rte").html();
    var data = {
      'template': template,
      'html': html,
      'type': $(current_element_popup).attr('rel'),
      'title': ccppm_catalogue_popup_htmlx2_title.val(),
      'color': ccppm_catalogue_popup_htmlx2_color.val(),
    };
    ccppm_catalogue_inner_generate('ccppm_catalogue_popup_html_render', data, master, template);
  });
  $('#ccppm_catalogue_popup_htmlx2_btn_render').on('click', function(event) {
    event.preventDefault();
    var master = ccppm_catalogue_masters_select.val();
    var template = $('#ccppm_catalogue_popup_template_select').val();
    var html = $("div#ccppm_catalogue_popup_htmlx2_1_rte").html();
    var html2 = $("div#ccppm_catalogue_popup_htmlx2_2_rte").html();
    var data = {
      'template': template,
      'html': html,
      'html2': html2,
      'type': $(current_element_popup).attr('rel'),
      'title': ccppm_catalogue_popup_htmlx2_title.val(),
      'color': ccppm_catalogue_popup_htmlx2_color.val(),
    };
    ccppm_catalogue_inner_generate('ccppm_catalogue_popup_htmlx2_render', data, master, template);
  });
 
})(jQuery);
