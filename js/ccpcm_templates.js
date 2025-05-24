var ccpcm_templates_select = false;
var ccpcm_templates_master_select_render = false;
var ccpcm_global = {};

(function($) {
  ccpcm_templates_select = $('#ccpcm_templates_select');
  var ccpcm_templates_type = $('#ccpcm_templates_type');
  var ccpcm_templates_elements_count = $('#ccpcm_templates_elements_count');
  var ccpcm_templates_elements_ids = $('#ccpcm_templates_elements_ids');
  var ccpcm_template_btn_new = $('#ccpcm_template_btn_new');
  var ccpcm_template_new = $('#ccpcm_template_new');
  var ccpcm_template_new_name = $('#ccpcm_template_new_name');
  var ccpcm_template_btn_new_append = $('#ccpcm_template_btn_new_append');
  var ccpcm_template_btn_new_cancel = $('#ccpcm_template_btn_new_cancel');
  var ccpcm_template_btn_save = $('#ccpcm_template_btn_save');
  var ccpcm_template_btn_delete = $('#ccpcm_template_btn_delete');
  var ccpcm_template_btn_download = $('#ccpcm_template_btn_download');
  var ccpcm_template_btn_auto_render = $('#ccpcm_template_btn_auto_render');
  var ccpcm_template_btn_render = $('#ccpcm_template_btn_render');
  ccpcm_templates_master_select_render = $('#ccpcm_templates_master_select_render');
  var ccpcm_template_add_blank_page = $('#ccpcm_template_add_blank_page');

  var temp_data = {};

  // récupérer la liste des templates
  var ccpcm_templates_select_update = function(id) {
    var name = id;
    var callback_get_templates = function(data) {
      ccpcm_templates_select.empty();
      ccpcm_templates_select.append('<option value=""> *** Select a template ***</option>');
      $(data).each(function(idx, value) {
        ccpcm_templates_select.append('<option value="'+value['id']+'"'+((value == name)?'SELECTED="SELECTED"':"")+' class="ccpcm_templates_type_'+value['type']+'" title="'+value['type']+((value['elements_count'])?' : '+value['elements_count']+' elements':'')+'">'+value['id']+'</option>');
      });
    }
    ccpcm_ajax('get_templates_with_infos', {}, callback_get_templates);
  }

  // récupérer la liste des masters
  var ccpcm_templates_master_select_render_update = function(id) {
    var name = id;
    var callback_get_ccpcm_templates_master_select_render = function(data) {
      ccpcm_templates_master_select_render.empty();
      ccpcm_templates_master_select_render.append('<option value=""> *** Select a master ***</option>');
      $(data).each(function(idx, value) {
        ccpcm_templates_master_select_render.append('<option value="'+value+'"'+((value == name)?'SELECTED="SELECTED"':"")+'>'+value+'</option>');
      });
    }
    ccpcm_ajax('get_catalogue_masters', {}, callback_get_ccpcm_templates_master_select_render);
  }

  ccpcm_templates_master_select_render_update();
  ccpcm_templates_select_update();

  ccpcm_templates_select.on('change', function(event) {
    var id = ccpcm_templates_select.val();
    var ccpcm_templates_select_change_callback = function(data) {
      console.log('data', data);
      temp_data = data['data'];
      ccpcm_templates_type.val(data['template']['type']);
      ccpcm_templates_elements_count.val(data['template']['elements_count']);
      ccpcm_templates_elements_ids.val(data['template']['elements_ids']);
      ccpcm_templates_master_select_render_update(data['template']['master_select_render']);
      editor.setValue(data['template']['content']);
    }
    var data = {
      'id': id,
    };
    if ($('#dpi_selector').val()) data['dpi'] = $('#dpi_selector').val();
    
    ccpcm_ajax('get_template', data, ccpcm_templates_select_change_callback);
  });

  ccpcm_template_btn_save.on('click', function(event) {
    var id = ccpcm_templates_select.val();
    var type = ccpcm_templates_type.val();
    var master_select_render = ccpcm_templates_master_select_render.val();
    var elements_count = ccpcm_templates_elements_count.val();
    var elements_ids = ccpcm_templates_elements_ids.val();
    var ccpcm_template_save_click_callback = function(data) {
      temp_data = data['data'];
      editor.setValue(data['content']);
    }
    ccpcm_ajax('set_template', {'id': id, 'data': { 'content': editor.getValue(), 'type': type, 'elements_count': elements_count, 'master_select_render': master_select_render, 'elements_ids': elements_ids }}, ccpcm_template_save_click_callback);
  });

  ccpcm_template_btn_delete.on('click', function(event) {
    if(confirm('Are you sure, you want to remove this template ?')) {
      var id = ccpcm_templates_select.val();
      var ccpcm_template_delete_click_callback = function(data) {
        ccpcm_templates_select_update();
        editor.setValue('');
      }
      ccpcm_ajax('delete_template', {'id': id}, ccpcm_template_delete_click_callback);
    }
  });

  ccpcm_template_btn_download.on('click', function(event) {
    ccpcm_generate(true, true);
  });

  ccpcm_template_btn_render.on('click', function(event) {
    ccpcm_generate(true, false);
  });

  function ccpcm_generate(force = false, download = false) {
//    console.clear();
    if (!(force || ccpcm_template_btn_auto_render.prop( "checked")))
      return false;
    lastGen = new Date();
    var dd = false;
    var data = temp_data;
    var type = ccpcm_templates_type.val();
    var add_blank_page = ccpcm_template_add_blank_page.prop('checked');
    display_javascript('_Javascript');
    if ($('#dpi_selector').val()) ccpcm_global.renderDpi = $('#dpi_selector').val();
    if (type != 'master') {
	var master = ccpcm_templates_master_select_render.val();
	if (master) {
		console.log('DEBUG > Render with master '+master); 
		display_javascript(master);
	} else
		display_javascript('_Master_A4');
		
//	display_javascript(ccpcm_templates_master_select_render.val());
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

        if (ccpcm_global.master) {
	  if (add_blank_page == 1) {
		console.log("Add blank page before");
		dd.unshift({text: '', pageBreak: 'after'});
	  }
          ccpcm_global.master['content'] = ccpcm_global.documentContentParser(dd);
          dd = ccpcm_global.master;
          ccpcm_global.master = false;
        } else {
          dd['content'] = ccpcm_global.documentContentParser(dd);
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
        var filename = ccpcm_templates_select.val()+"_"+(new Date()).toISOString()+".pdf";
        console.log(filename);
        pdfMake.createPdf(dd).download(filename);
      } else {
        pdfMake.createPdf(dd).getDataUrl(function(outDoc) {
          console.log('generated in ' + (new Date().getTime() - lastGen.getTime()) + ' ms');
          $('#ccpcm_template_container_right').attr('src', outDoc);
        });
      }
    }
  }

  var editor = CodeMirror.fromTextArea(document.getElementById("ccpcm_template_content"), {
    lineNumbers: true,
    lineWrapping: true,
  });
  editor.setSize(null, 1000);

  editor.on('change', function(event) {
     ccpcm_generate();
  });

  ccpcm_template_btn_new.on('click', function(event) {
    ccpcm_template_new.css('display', 'inline-block');
  });
  ccpcm_template_btn_new_cancel.on('click', function(event) {
    ccpcm_template_new.css('display', 'none');
    ccpcm_template_new_name.val('noname');
  });

  ccpcm_template_btn_new_append.on('click', function(event) {
    var type = ccpcm_templates_type.val();
    var elements_count = ccpcm_templates_elements_count.val();
    var elements_ids = ccpcm_templates_elements_ids.val();
    var ccpcm_template_btn_new_append_callback = function(data) {
      ccpcm_templates_select_update(data['id']);
      ccpcm_template_new.css('display', 'none');
      ccpcm_template_new_name.val('noname');
      editor.setValue(data['content']);
    }
    ccpcm_ajax('set_template', {'new': true, 'data': { 'content': editor.getValue(), 'type': type, 'elements_count': elements_count, 'elements_ids': elements_ids }, 'id': ccpcm_template_new_name.val()}, ccpcm_template_btn_new_append_callback);
  });
})(jQuery);
