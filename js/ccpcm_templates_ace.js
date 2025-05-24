(function($) {
  var ccpcm_templates_select = $('#ccpcm_templates_select');
  var ccpcm_templates_type = $('#ccpcm_templates_type');

  var ccpcm_template_btn_new = $('#ccpcm_template_btn_new');
  var ccpcm_template_new = $('#ccpcm_template_new');
  var ccpcm_template_new_name = $('#ccpcm_template_new_name');
  var ccpcm_template_btn_new_append = $('#ccpcm_template_btn_new_append');
  var ccpcm_template_btn_new_cancel = $('#ccpcm_template_btn_new_cancel');
  var ccpcm_template_btn_save = $('#ccpcm_template_btn_save');
  var ccpcm_template_btn_delete = $('#ccpcm_template_btn_delete');

  // récupérer la liste des templates
  var ccpcm_templates_select_update = function(id) {
    var name = id;
    var callback_get_templates = function(data) {
      ccpcm_templates_select.empty();
      ccpcm_templates_select.append('<option> *** Select a template ***</option>');
      $(data).each(function(idx, value) {
        ccpcm_templates_select.append('<option value="'+value+'"'+((value == name)?'SELECTED="SELECTED"':"")+'>'+value+'</option>');
      });
    }
    ccpcm_ajax('get_templates', {}, callback_get_templates);
  }

  ccpcm_templates_select_update();

  ccpcm_templates_select.on('change', function(event) {
    var id = ccpcm_templates_select.val();
    var ccpcm_templates_select_change_callback = function(data) {
      editor.setValue(data['template']['content']);
      ccpcm_templates_type.val(data['template']['type']);
    }
    ccpcm_ajax('get_template', {'id': id}, ccpcm_templates_select_change_callback);
  });


  ccpcm_template_btn_save.on('click', function(event) {
    var id = ccpcm_templates_select.val();
    var type = ccpcm_templates_type.val();
    var ccpcm_template_save_click_callback = function(data) {
      editor.setValue(data['content']);
    }
    ccpcm_ajax('set_template', {'id': id, 'data': { 'content': editor.getValue(), 'type': type }}, ccpcm_template_save_click_callback);
  });

  ccpcm_template_btn_delete.on('click', function(event) {
    if(confirm('Are you sure, you went to remove this template ?')) {
      var id = ccpcm_templates_select.val();
      var ccpcm_template_delete_click_callback = function(data) {
        ccpcm_templates_select_update();
        editor.setValue('');
      }
      ccpcm_ajax('delete_template', {'id': id}, ccpcm_template_delete_click_callback);
    }
  });

  function ccpcm_generate() {
    lastGen = new Date();
    var dd = false;
    var data = {};
    data['data'] = '';
    var type = ccpcm_templates_type.val();
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
        $('#ccpcm_template_container_right').attr('src', outDoc);
      });
    }
  }

  var editor = ace.edit('ccpcm_template_content');
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
    var ccpcm_template_btn_new_append_callback = function(data) {
      ccpcm_templates_select_update(data['id']);
      editor.setValue(data['content']);
      ccpcm_template_new.css('display', 'none');
      ccpcm_template_new_name.val('noname');
    }
    ccpcm_ajax('set_template', {'new': true, 'data': { 'content': editor.getValue(), 'type': type  }, 'id': ccpcm_template_new_name.val()}, ccpcm_template_btn_new_append_callback);
  });
})(jQuery);
