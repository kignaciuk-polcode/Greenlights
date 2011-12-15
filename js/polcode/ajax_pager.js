function ajax_pager_request(url) {
    new Ajax.Request(url, {
        onSuccess: function(response) {
            var html = new Element('div');
            html.update(response.responseText);
            // There may be many .SOME-CLASS so use `invoke` to iterate through them
            $$('.category-products').invoke('update', html.select('.category-products').first().innerHTML);
        }
    });
}
