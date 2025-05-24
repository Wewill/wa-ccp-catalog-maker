var ccpcm_ajax;

(function($){
  ccpcm_ajax = function(method, data = {}, callback = function(data) {}, async = true) {
    $.ajax({
       url : '/wp-content/plugins/wa-ccp-catalog-maker/wa-ccp-catalog-maker-ajax.php', // La ressource ciblée
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
