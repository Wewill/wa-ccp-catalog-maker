var ccppm_ajax;

(function($){
  ccppm_ajax = function(method, data = {}, callback = function(data) {}, async = true) {
    $.ajax({
       url : '/wp-content/plugins/ccp-pdf-maker/ccp-pdf-maker-ajax.php', // La ressource ciblée
       type : 'POST', // Le type de la requête HTTP
       dataType : 'json',
       async: async,
       data: {
         method: method,
         data: data,
         edition_slug: edition_slug,
         edition_id: edition_id
       },
       success : callback,
       error: function(jqXHR, textStatus, errorThrown) {
         console.log(textStatus, errorThrown);
       }
    });
  }
}(jQuery));
