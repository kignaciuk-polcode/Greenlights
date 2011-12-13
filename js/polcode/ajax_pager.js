function ajax_pager_request(url) {
    $j.ajax({
        url: url,
        success: function (result) {
            var resultDiv = $j(result).find('.category-products');
            $j('.category-products').html(resultDiv.html());
        }
    });
}
