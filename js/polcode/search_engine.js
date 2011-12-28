function searchInProducts(url) {
    new Ajax.Request(url, {
        onSuccess: function(response) {
            var html = new Element('div');
            html.update(response.responseText);
            
            var result = html.select('.col-main').first().innerHTML;
            
            $$('.multisearch-col-shop').invoke('update', result);
        }
    });
}

function searchInBlog(url) {
    new Ajax.Request(url, {
        onSuccess: function(response) {
            var html = new Element('div');
            html.update(response.responseText);
            
            var result = html.select('.col-main').first().innerHTML;
            
            $$('.multisearch-col-blog').invoke('update', result);
        }
    });
}
