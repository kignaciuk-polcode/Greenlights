var AdminOffer = new Class.create();
AdminOffer.prototype = {

    initialize : function(data){
        if(!data) data = {};
        this.loadBaseUrl    = false;
        this.productConfigureAddFields = {};
    },

    toggleCustomPrice: function(checkbox, elemId, tierBlock) {
               
        if (checkbox.checked) {
            $(elemId).disabled = false;
            $(elemId).show();
            if($(tierBlock)) $(tierBlock).hide();
        }
        else {
            $(elemId).disabled = true;
            $(elemId).hide();
            if($(tierBlock)) $(tierBlock).show();
        }
    },


    productConfigureSubmit : function(listType, area, fieldsPrepare, itemsFilter) {
        // prepare loading areas and build url
        //area = this.prepareArea(area);
        this.loadingAreas = area;
        var url = this.loadBaseUrl + 'block/' + area + '?isAjax=true';

        console.log(url);


        // prepare additional fields
        fieldsPrepare = this.prepareParams(fieldsPrepare);
//        fieldsPrepare.reset_shipping = 1;
        fieldsPrepare.json = 1;
//
        // create fields
        var fields = [];
        for (var name in fieldsPrepare) {
            fields.push(new Element('input', {type: 'hidden', name: name, value: fieldsPrepare[name]}));
        }
        
        console.log(fields);
        
        
//        productConfigure.addFields(fields);
//
//        // filter items
//        if (itemsFilter) {
//            productConfigure.addItemsFilter(listType, itemsFilter);
//        }
//
//        // prepare and do submit
//        productConfigure.addListType(listType, {urlSubmit: url});
//        productConfigure.setOnLoadIFrameCallback(listType, function(response){
//            this.loadAreaResponseHandler(response);
//        }.bind(this));
//        productConfigure.submit(listType);
//        // clean
//        this.productConfigureAddFields = {};
    },
    
    prepareParams : function(params){
        if (!params) {
            params = {};
        }

        return params;
    },    

    itemsUpdate : function(){
        
        
        var area = ['sidebar', 'items', 'shipping_method', 'billing_method','totals', 'giftmessage'];
        // prepare additional fields
        var fieldsPrepare = {update_items: 1};
        var info = $('inquiry-items_grid').select('input', 'select', 'textarea');
        for(var i=0; i<info.length; i++){
            if(!info[i].disabled && (info[i].type != 'checkbox' || info[i].checked)) {
                fieldsPrepare[info[i].name] = info[i].getValue();
            }
        }
        fieldsPrepare = Object.extend(fieldsPrepare, this.productConfigureAddFields);
        console.log(fieldsPrepare);

        this.productConfigureSubmit('quote_items', area, fieldsPrepare);
        this.orderItemChanged = false;
    }




}

