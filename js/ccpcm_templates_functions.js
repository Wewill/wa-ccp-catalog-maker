var templates_caches = {};
var catalogue_data_caches = {};

function display_template(template_name, data) {
  var $ = jQuery;
  if (! template_name) {
    console.log('WARNING !! display_template : no template_name');
    return ['display_template : no template_name'];
  }
  if (! templates_caches[template_name]) {
    var template = false;
    var ccpcm_display_template_callback = function(data) {
      template = data['template']['content'];
    }
    ccpcm_ajax('get_template', {'id': template_name, 'options': {'no_data': true}}, ccpcm_display_template_callback, false);
    templates_caches[template_name] = template;
  } else {
    template = templates_caches[template_name];
  }
  var dd = ['display_template : dd not set'];
  var done = false;
//  console.log("DEBUG : display_template : ", template_name);
  template += "\n//# sourceURL="+template_name+".js";
  try {
      console.log('display_template', template_name, 'data', data);
      eval(template);
      done = true;
  } catch (e) {
//    if (e instanceof SyntaxError) {
      if (e) {
          console.log(e.message);
      }
  }
  if (!done) {
	console.log('ERREUR/ERROR : DEBUG / display_template : nothing in dd : ', template_name);
	console.log('ERREUR/ERROR : DEBUG template :', template);
	console.log('ERREUR/ERROR : DEBUG data :', data);
	console.log('ERREUR/ERROR : END DEBUG / display_template : nothing in dd : ', template_name);
  }
  return dd;
}

function display_image(image, meta = {}, name = false, url = false) {
  var $ = jQuery;
  if (!image || image == undefined)
    return {'text': 'NO IMAGE AVAILABLE'};
  var dpi = ccpcm_global.renderDpi;
  var file = false;
  if (name != false) {
    file = image['base64'][name];
  } else {
    file = image['base64'];
  }
  if (! templates_caches[file]) {
    var base64 = '';
    var ccpcm_display_image_callback = function(data) {
      base64 = data['base64'];
    }
    ccpcm_ajax('get_file', {'file': file, 'meta': meta, 'url': url, 'dpi': dpi}, ccpcm_display_image_callback, false);
    if (!base64) {
      return {'text': 'NO IMAGE AVAILABLE'};
    }
    templates_caches[file] = base64;
    meta['image'] = base64;
    return meta;
  } else {
    meta['image'] = templates_caches[file];
    return meta;
  }
}

function display_style(template_name) {
  var $ = jQuery;
  if (! templates_caches[template_name]) {
    var template = false;
    var ccpcm_display_style_callback = function(data) {
      template = data['template']['content'];
    }
    ccpcm_ajax('get_template', {'id': template_name, 'options': {'no_data': true}}, ccpcm_display_style_callback, false);
    templates_caches[template_name] = template;
  } else {
    template = templates_caches[template_name];
  }
  var styles;
  try {
      eval(template);
  } catch (e) {
    if (e) {
          console.log(e.message);
      }
  }
  return styles;
}

function display_javascript(template_name) {
  var $ = jQuery;
  if (! templates_caches[template_name]) {
    var template = false;
    var ccpcm_display_style_callback = function(data) {
      template = data['template']['content'];
    }
    ccpcm_ajax('get_template', {'id': template_name, 'options': {'no_data': true}}, ccpcm_display_style_callback, false);
    templates_caches[template_name] = template;
  } else {
    template = templates_caches[template_name];
  }
  template += "\n//# sourceURL="+template_name+".js";
  try {
      eval(template);
  } catch (e) {
    if (e) {
      console.log(e.message);
    }
  }
}
