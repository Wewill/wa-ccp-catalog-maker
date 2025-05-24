(function($) {
  var ccppm_templates_select = $('#ccppm_templates_select');
  var ccppm_templates_type = $('#ccppm_templates_type');

  var ccppm_template_btn_new = $('#ccppm_template_btn_new');
  var ccppm_template_new = $('#ccppm_template_new');
  var ccppm_template_new_name = $('#ccppm_template_new_name');
  var ccppm_template_btn_new_append = $('#ccppm_template_btn_new_append');
  var ccppm_template_btn_new_cancel = $('#ccppm_template_btn_new_cancel');
  var ccppm_template_btn_save = $('#ccppm_template_btn_save');
  var ccppm_template_btn_delete = $('#ccppm_template_btn_delete');

  // récupérer la liste des templates
  var ccppm_templates_select_update = function(id) {
    var name = id;
    var callback_get_templates = function(data) {
      ccppm_templates_select.empty();
      ccppm_templates_select.append('<option> *** Select a template ***</option>');
      $(data).each(function(idx, value) {
        ccppm_templates_select.append('<option value="'+value+'"'+((value == name)?'SELECTED="SELECTED"':"")+'>'+value+'</option>');
      });
    }
    ccppm_ajax('get_templates', {}, callback_get_templates);
  }

  ccppm_templates_select_update();

  ccppm_templates_select.on('change', function(event) {
    var id = ccppm_templates_select.val();
    var ccppm_templates_select_change_callback = function(data) {
      editor.setValue(data['template']['content']);
      ccppm_templates_type.val(data['template']['type']);
    }
    ccppm_ajax('get_template', {'id': id}, ccppm_templates_select_change_callback);
  });


  ccppm_template_btn_save.on('click', function(event) {
    var id = ccppm_templates_select.val();
    var type = ccppm_templates_type.val();
    var ccppm_template_save_click_callback = function(data) {
      editor.setValue(data['content']);
    }
    ccppm_ajax('set_template', {'id': id, 'data': { 'content': editor.getValue(), 'type': type }}, ccppm_template_save_click_callback);
  });

  ccppm_template_btn_delete.on('click', function(event) {
    if(confirm('Are you sure, you went to remove this template ?')) {
      var id = ccppm_templates_select.val();
      var ccppm_template_delete_click_callback = function(data) {
        ccppm_templates_select_update();
        editor.setValue('');
      }
      ccppm_ajax('delete_template', {'id': id}, ccppm_template_delete_click_callback);
    }
  });

  function ccppm_generate() {
    lastGen = new Date();
    var dd = false;
    var data = {};
    data['data'] = '';
    var type = ccppm_templates_type.val();
    try {
        eval(editor.getValue());
    } catch (e) {
        if (e instanceof SyntaxError) {
            console.log(e.message);
        }
    }
    if ( dd ) {
      if (type != 'master') {
        dd = {'content': dd};
      }
      pdfMake.createPdf(dd).getDataUrl(function(outDoc) {
        console.log('generated in ' + (new Date().getTime() - lastGen.getTime()) + ' ms');
        $('#ccppm_template_container_right').attr('src', outDoc);
      });
    }
  }

  var editor = ace.edit('ccppm_template_content');
//  editor.setTheme('ace/theme/monokai');
//
  editor.getSession().setMode('ace/mode/javascript');
  editor.setOptions({
    enableBasicAutocompletion: false,
    enableSnippets: false,
    enableLiveAutocompletion: false,
    wrap: true,
  });
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
    var ccppm_template_btn_new_append_callback = function(data) {
      ccppm_templates_select_update(data['id']);
      editor.setValue(data['content']);
      ccppm_template_new.css('display', 'none');
      ccppm_template_new_name.val('noname');
    }
    ccppm_ajax('set_template', {'new': true, 'data': { 'content': editor.getValue(), 'type': type  }, 'id': ccppm_template_new_name.val()}, ccppm_template_btn_new_append_callback);
  });
})(jQuery);
