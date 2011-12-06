
/***************Help*****************************/
	showMe	= function(item,list){
		if($(item)){
			$$('.main div.section').each(function(item, ind){
				item.hide();
			});
			$(item).show();
			$$('.left-menu ul li').each(function(item, ind){
				item.removeClassName('selected');
			});
			$(list).addClassName('selected');
		}
	}
/***************Help*****************************/

/***************Campaign templates*****************************/

	saveAndContinueEdit	= function(){
            editForm.submit($('edit_form').action+'back/edit/');
	}

	getCmsContent = function(postUrl, item){
		var alldata = new Hash();
		var aux = item.options[item.selectedIndex].value.split('_');

		alldata.set('store_id',aux[0]);
		alldata.set('cms_id',aux[1]);
		if($('cms_inlined').checked){
			alldata.set('cms_inlined',$('cms_inlined').checked);
		}

		new Ajax.Request(postUrl, {
			method: "post",
		  	parameters: alldata,
		  	onSuccess: function(response) {
				$$('textarea#cms_content').first().update(response.responseText);
			}
		});
	}

   	previewMe = function(source){
		if($(source.toString())){
			var w = window.open('', '', 'width=800,height=600,resizeable,scrollbars,left=100,top=50,location:yes');
			w.document.write($(source).value);
			w.document.close();
		}
	}

	getContentBySection = function(item){
		var content = (item.options[item.selectedIndex].value)? $(item.options[item.selectedIndex].value).value : '' ;
		$('dcs_content').value = content;
	}

	updateSource = function(){

		var sectionCode = $('dcs').options[$('dcs').selectedIndex].value;
		var source = $$('textarea#source').first().value;

		if(source.search(sectionCode) == -1){
		    alert('Content section not found on source field.');
		}else if(source.search(sectionCode) != 0){
			var aux = $(sectionCode).value;
			var first = source.substr(0,source.indexOf(aux.substr(0,200)));
			var last = source.substr(first.length+aux.length,source.length);
			var newSource = first +$('dcs_content').value+ last;
			$(sectionCode).value = $('dcs_content').value;
			$$('textarea#source').first().value = newSource;
		}
		return true;
	}

/***************Campaign templates*****************************/

/***************BulkSynchro*****************************/
	formSubmit = function(item,errorText,postUrl){

		var waySelect = $(item+'-select');
		var storeSelect = $(item+'-form-additional').down('#store');
		var listSelect = $(item+'-form-additional').down('#list');

		if(waySelect.selectedIndex == 0 || storeSelect.selectedIndex == 0 || listSelect.selectedIndex == 0){
			alert(errorText);
			return false;
		}

		var alldata = new Hash();
		var way = waySelect.options[waySelect.selectedIndex].value;
		var list = listSelect.options[listSelect.selectedIndex].value;;
		var store = storeSelect.options[storeSelect.selectedIndex].value;;
		alldata.set('way',way);
		alldata.set('list',list);
		alldata.set('start',$('start').value);
		alldata.set('limit',$('limit').value);
		alldata.set('store',store);

		runWebHooksAjaxFunction(postUrl, alldata);
	}
/***************BulkSynchro*****************************/
	runWebHooksAjaxFunction = function(postUrl,alldata){

		new Ajax.Request(postUrl, {
			method: "post",
		  	parameters: alldata,
		  	onSuccess: function(response) {
				window.location.href = window.location.href;
			}
		});
	}
/***************WebHooks*****************************/
	submitMyHooks = function(postUrl){

		var alldata = new Hash();
		$$("table#webHooksGrid_table tr").each(function(row, ind){
			if(ind == 0){
				return;
			}
			var data = new Hash();
			var i = 0;
			var list_id = '';
			if(row.select("td").length){
		    	row.select("td").each(function(td, index){
					if(i == 0){
						list_id = td.innerHTML.replace(/^\s+|\s+$/g, '');
						data.set('list_id',list_id);
					}if(i > 1){
			        	td.select("input").each(function(check, index){
							var value = 0;
		                    if(check.checked){
		                    	value = 1;
		                    }
				        	data.set(check.name,value);
			        	});
					}
					i++;
		   		});

		    }
	   		alldata.set(list_id,data.toQueryString());
		});
		runWebHooksAjaxFunction(postUrl, alldata);
	}
/***************WebHooks*****************************/