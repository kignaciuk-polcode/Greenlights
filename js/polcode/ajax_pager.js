function ajax_pager_request(url) {
    new Ajax.Request(url, {
        onSuccess: function(response) {
            var html = new Element('div');
            html.update(response.responseText);
            $$('.category-products').invoke('update', html.select('.category-products').first().innerHTML);
        }
    });
}
