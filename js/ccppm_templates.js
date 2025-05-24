var ccppm_templates_select = false;
var ccppm_templates_master_select_render = false;
var ccppm_global = {};

(function($) {
  ccppm_templates_select = $('#ccppm_templates_select');
  var ccppm_templates_type = $('#ccppm_templates_type');
  var ccppm_templates_elements_count = $('#ccppm_templates_elements_count');
  var ccppm_templates_elements_ids = $('#ccppm_templates_elements_ids');
  var ccppm_template_btn_new = $('#ccppm_template_btn_new');
  var ccppm_template_new = $('#ccppm_template_new');
  var ccppm_template_new_name = $('#ccppm_template_new_name');
  var ccppm_template_btn_new_append = $('#ccppm_template_btn_new_append');
  var ccppm_template_btn_new_cancel = $('#ccppm_template_btn_new_cancel');
  var ccppm_template_btn_save = $('#ccppm_template_btn_save');
  var ccppm_template_btn_delete = $('#ccppm_template_btn_delete');
  var ccppm_template_btn_download = $('#ccppm_template_btn_download');
  var ccppm_template_btn_auto_render = $('#ccppm_template_btn_auto_render');
  var ccppm_template_btn_render = $('#ccppm_template_btn_render');
  ccppm_templates_master_select_render = $('#ccppm_templates_master_select_render');
  var ccppm_template_add_blank_page = $('#ccppm_template_add_blank_page');

  var temp_data = {};

  // récupérer la liste des templates
  var ccppm_templates_select_update = function(id) {
    var name = id;
    var callback_get_templates = function(data) {
      ccppm_templates_select.empty();
      ccppm_templates_select.append('<option value=""> *** Select a template ***</option>');
      $(data).each(function(idx, value) {
        ccppm_templates_select.append('<option value="'+value['id']+'"'+((value == name)?'SELECTED="SELECTED"':"")+' class="ccppm_templates_type_'+value['type']+'" title="'+value['type']+((value['elements_count'])?' : '+value['elements_count']+' elements':'')+'">'+value['id']+'</option>');
      });
    }
    ccppm_ajax('get_templates_with_infos', {}, callback_get_templates);
  }

  // récupérer la liste des masters
  var ccppm_templates_master_select_render_update = function(id) {
    var name = id;
    var callback_get_ccppm_templates_master_select_render = function(data) {
      ccppm_templates_master_select_render.empty();
      ccppm_templates_master_select_render.append('<option value=""> *** Select a master ***</option>');
      $(data).each(function(idx, value) {
        ccppm_templates_master_select_render.append('<option value="'+value+'"'+((value == name)?'SELECTED="SELECTED"':"")+'>'+value+'</option>');
      });
    }
    ccppm_ajax('get_catalogue_masters', {}, callback_get_ccppm_templates_master_select_render);
  }

  ccppm_templates_master_select_render_update();
  ccppm_templates_select_update();

  ccppm_templates_select.on('change', function(event) {
    var id = ccppm_templates_select.val();
    var ccppm_templates_select_change_callback = function(data) {
      console.log('data', data);
      temp_data = data['data'];
      ccppm_templates_type.val(data['template']['type']);
      ccppm_templates_elements_count.val(data['template']['elements_count']);
      ccppm_templates_elements_ids.val(data['template']['elements_ids']);
      ccppm_templates_master_select_render_update(data['template']['master_select_render']);
      editor.setValue(data['template']['content']);
    }
    var data = {
      'id': id,
    };
    if ($('#dpi_selector').val()) data['dpi'] = $('#dpi_selector').val();
    
    ccppm_ajax('get_template', data, ccppm_templates_select_change_callback);
  });

  ccppm_template_btn_save.on('click', function(event) {
    var id = ccppm_templates_select.val();
    var type = ccppm_templates_type.val();
    var master_select_render = ccppm_templates_master_select_render.val();
    var elements_count = ccppm_templates_elements_count.val();
    var elements_ids = ccppm_templates_elements_ids.val();
    var ccppm_template_save_click_callback = function(data) {
      temp_data = data['data'];
      editor.setValue(data['content']);
    }
    ccppm_ajax('set_template', {'id': id, 'data': { 'content': editor.getValue(), 'type': type, 'elements_count': elements_count, 'master_select_render': master_select_render, 'elements_ids': elements_ids }}, ccppm_template_save_click_callback);
  });

  ccppm_template_btn_delete.on('click', function(event) {
    if(confirm('Are you sure, you want to remove this template ?')) {
      var id = ccppm_templates_select.val();
      var ccppm_template_delete_click_callback = function(data) {
        ccppm_templates_select_update();
        editor.setValue('');
      }
      ccppm_ajax('delete_template', {'id': id}, ccppm_template_delete_click_callback);
    }
  });

  ccppm_template_btn_download.on('click', function(event) {
    ccppm_generate(true, true);
  });

  ccppm_template_btn_render.on('click', function(event) {
    ccppm_generate(true, false);
  });

  function ccppm_generate(force = false, download = false) {
//    console.clear();
    if (!(force || ccppm_template_btn_auto_render.prop( "checked")))
      return false;
    lastGen = new Date();
    var dd = false;
    var data = temp_data;
    var type = ccppm_templates_type.val();
    var add_blank_page = ccppm_template_add_blank_page.prop('checked');
    display_javascript('_Javascript');
    if ($('#dpi_selector').val()) ccppm_global.renderDpi = $('#dpi_selector').val();
    if (type != 'master') {
	var master = ccppm_templates_master_select_render.val();
	if (master) {
		console.log('DEBUG > Render with master '+master); 
		display_javascript(master);
	} else
		display_javascript('_Master_A4');
		
//	display_javascript(ccppm_templates_master_select_render.val());
//      display_javascript('_Master_Card');
//      display_javascript('_Master_Card_A5');
//    display_javascript('_Master_PRINT');
//      display_javascript('_Master');
//    display_javascript('_Master_PRINT_A5');
//      display_javascript('_Master_A5');
//    display_javascript('_Master_PRINT_Landscape_A4');
    }
    try {
        eval(editor.getValue());
    } catch (e) {
        if (e) {
            console.log(e.message);
        }
    }
    if ( dd ) {
      var styles;
      if (type != 'master') {

        if (ccppm_global.master) {
	  if (add_blank_page == 1) {
		console.log("Add blank page before");
		dd.unshift({text: '', pageBreak: 'after'});
	  }
          ccppm_global.master['content'] = ccppm_global.documentContentParser(dd);
          dd = ccppm_global.master;
          ccppm_global.master = false;
        } else {
          dd['content'] = ccppm_global.documentContentParser(dd);
          dd['styles'] = display_style('_Styles');
        }
      }

      if (styles != undefined) {
        if (dd['styles'] == undefined)
          dd['styles'] = {};
        $.each(styles, function(key, value){
          dd['styles'][key] = value;
        });
      }
      if (download) {
        var filename = ccppm_templates_select.val()+"_"+(new Date()).toISOString()+".pdf";
        console.log(filename);
        pdfMake.createPdf(dd).download(filename);
      } else {
        pdfMake.createPdf(dd).getDataUrl(function(outDoc) {
          console.log('generated in ' + (new Date().getTime() - lastGen.getTime()) + ' ms');
          $('#ccppm_template_container_right').attr('src', outDoc);
        });
      }
    }
  }

  var editor = CodeMirror.fromTextArea(document.getElementById("ccppm_template_content"), {
    lineNumbers: true,
    lineWrapping: true,
  });
  editor.setSize(null, 1000);

  editor.on('change', function(event) {
     ccppm_generate();
  });

  ccppm_template_btn_new.on('click', function(event) {
    ccppm_template_new.css('display', 'inline-block');
  });
  ccppm_template_btn_new_cancel.on('click', function(event) {
    ccppm_template_new.css('display', 'none');
    ccppm_template_new_name.val('noname');
  });

  ccppm_template_btn_new_append.on('click', function(event) {
    var type = ccppm_templates_type.val();
    var elements_count = ccppm_templates_elements_count.val();
    var elements_ids = ccppm_templates_elements_ids.val();
    var ccppm_template_btn_new_append_callback = function(data) {
      ccppm_templates_select_update(data['id']);
      ccppm_template_new.css('display', 'none');
      ccppm_template_new_name.val('noname');
      editor.setValue(data['content']);
    }
    ccppm_ajax('set_template', {'new': true, 'data': { 'content': editor.getValue(), 'type': type, 'elements_count': elements_count, 'elements_ids': elements_ids }, 'id': ccppm_template_new_name.val()}, ccppm_template_btn_new_append_callback);
  });
})(jQuery);
