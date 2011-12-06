Event.observe(window, 'load', function() {
    payment.save = payment.save.wrap(function(originalSaveMethod) {
        payment.originalSaveMethod = originalSaveMethod;

        var opsValidator = new Validation($('co-payment-form'));
        if (!opsValidator.validate()) {
            return;
        }
        if ('ops_directDebit' == payment.currentMethod) {
            payment.saveOpsDirectDebit();
        }
        if ('ops_cc' == payment.currentMethod) {
            payment.saveOpsCcBrand();
        }

        originalSaveMethod();
    });

    payment.saveOpsDirectDebit = function() {
        checkout.setLoadWaiting('payment');
        var countryId = $('ops_directdebit_country_id').value;
        var accountNo = $('ops_directdebit_account_no').value;
        var bankCode  = $('ops_directdebit_bank_code').value;
        var CN        = $('ops_directdebit_CN').value;
        new Ajax.Request(opsDirectDebitUrl, {
            method: 'post',
            parameters: { country : countryId, account : accountNo, bankcode : bankCode, CN : CN },
            onSuccess: function(transport) {
                checkout.setLoadWaiting(false);
                payment.originalSaveMethod();
            },
            onFailure: function(transport) {
                checkout.setLoadWaiting(false);
                if (transport.responseText && 0 < transport.responseText.length) {
                    message = transport.responseText;
                } else {
                    message = 'Payment failed. Please select another payment method.';
                }
                alert(Translator.translate(message));
            }
        });
    };

    payment.saveOpsCcBrand = function() {
        checkout.setLoadWaiting('payment');
        var owner = $('OPS_CC_CN').value;
        new Ajax.Request(opsSaveCcBrandUrl, {
            method: 'post',
            parameters: { brand : $('OPS_CC_BRAND').value, cn: owner },
            onSuccess: function(transport) {
                if (-1 < opsCcBrandsForAliasInterface.indexOf($('OPS_CC_BRAND').value)) {
                    payment.requestOpsCcAlias();
                } else {
                    checkout.setLoadWaiting(false);
                }
                payment.originalSaveMethod();
            },
            onFailure: function(transport) {
                alert(Translator.translate('Payment failed. Please select another payment method.'));
            }
        });
    };

    payment.requestOpsCcAlias = function() {
        checkout.setLoadWaiting('payment');

        var iframe = $('ops_iframe_' + payment.currentMethod);
        var doc = null;

        if(iframe.contentDocument) {
            doc = iframe.contentDocument;
        } else if(iframe.contentWindow) {
            doc = iframe.contentWindow.document;
        } else if(iframe.document) {
            doc = iframe.document;
        }

        doc.body.innerHTML="";
        iframe.alreadySet = false;

        if ('true' != iframe.alreadySet) {
            form = doc.createElement('form');
            form.id = 'ops_request_form';
            form.method = 'post';
            form.action = url; 
            submit = doc.createElement('submit');
            form.appendChild(submit);
                                                    
            var cardholder = doc.createElement('input');
            cardholder.id = 'CN';
            cardholder.name = 'CN';
            cardholder.value = $('OPS_CC_CN').value;

            var cardnumber = doc.createElement('input');
            cardnumber.id = 'CARDNO';
            cardnumber.name = 'CARDNO';
            cardnumber.value = $('OPS_CC_CARDNO').value;

            var verificationCode = doc.createElement('input');
            verificationCode.id = 'CVC';
            verificationCode.name = 'CVC';
            verificationCode.value = $('OPS_CC_CVC').value;

            var brandElement = doc.createElement('input');
            brandElement.id = 'BRAND';
            brandElement.name = 'BRAND';
            brandElement.value = $('OPS_CC_BRAND').value;

            var edElement = doc.createElement('input');
            edElement.id = 'ED';
            edElement.name = 'ED';
            edElement.value = $('OPS_CC_ECOM_CARDINFO_EXPDATE_MONTH').value + $('OPS_CC_ECOM_CARDINFO_EXPDATE_YEAR').value;

            var pspidElement = doc.createElement('input');
            pspidElement.id = 'PSPID';
            pspidElement.name = 'PSPID';
            pspidElement.value = pspid;

            var orderIdElement = doc.createElement('input');
            orderIdElement.name = 'ORDERID';
            orderIdElement.id = 'ORDERID';
            orderIdElement.value = orderId;

            var acceptUrlElement = doc.createElement('input');
            acceptUrlElement.name = 'ACCEPTURL';
            acceptUrlElement.id = 'ACCEPTURL';
            acceptUrlElement.value = acceptUrl;

            var exceptionUrlElement = doc.createElement('input');
            exceptionUrlElement.name = 'EXCEPTIONURL';
            exceptionUrlElement.id = 'EXCEPTIONURL';
            exceptionUrlElement.value = exceptionUrl;

            var paramplusElement = doc.createElement('input');
            paramplusElement.name = 'PARAMPLUS';
            paramplusElement.id = 'PARAMPLUS';
            paramplusElement.value = '{ "format":"json" }';

            form.appendChild(pspidElement);
            form.appendChild(brandElement);
            form.appendChild(cardholder);
            form.appendChild(cardnumber);
            form.appendChild(verificationCode);
            form.appendChild(edElement);
            form.appendChild(acceptUrlElement);
            form.appendChild(exceptionUrlElement);
            form.appendChild(orderIdElement);
            form.appendChild(paramplusElement);

            var hash = doc.createElement('input');
            hash.id = 'SHASIGN';
            hash.name = 'SHASIGN';

            new Ajax.Request(hashUrl, {
                method: 'get',
                parameters: { brand: brandElement.value, orderid: orderId, paramplus: paramplusElement.value },
                onSuccess: function(transport) {
                    var data = transport.responseText.evalJSON();
                    hash.value = data.hash;

                    form.appendChild(hash);
                    doc.body.appendChild(form);
                    iframe.alreadySet = 'true';

                    form.submit();

                    doc.body.innerHTML = '{ "result" : "waiting" }';
                    
                    setTimeout("payment.processOpsResponse()", 3000);
                }
            });
        }
    };

    payment.processOpsResponse = function() {
        var responseIframe = $('ops_iframe_' + payment.currentMethod);
        var responseResult;
        if(responseIframe.contentDocument) {
            responseResult = responseIframe.contentDocument;
        } else if(responseIframe.contentWindow) {
            responseResult = responseIframe.contentWindow.document;
        } else if(responseIframe.document) {
            responseResult = responseIframe.document;
        }

        if ("undefined" == typeof(responseResult)) {
            currentStatus = '{ "result" : "failure" }'.evalJSON();
        } else {
            var currentStatus = responseResult.body.innerHTML.evalJSON();
            if ("undefined" == typeof(currentStatus.result)) {
                currentStatus = '{ "result" : "failure" }'.evalJSON();
            }
        }

        if ('success' == currentStatus.result) {

            new Ajax.Request(opsCcSaveAliasUrl, {
                method: 'post',
                parameters: { alias : currentStatus.alias },
                onSuccess: function(transport) {
                    var data = transport.responseText;
                    checkout.setLoadWaiting(false);
                    payment.stashCcData();
                    payment.originalSaveMethod();
                },
                onFailure: function(transport) {
                    payment.applyStashedCcData();
                }
            });

            return true;
        }

        alert(Translator.translate('Payment failed. Please review your input or select another payment method.'));
        checkout.setLoadWaiting(false);
        return false;
    };

    payment.criticalOpsCcData = ['CN', 'CARDNO', 'CVC'];
    payment.stashedOpsCcData = new Array();

    payment.stashCcData = function() {
        payment.criticalOpsCcData.each(function(item) {
            if (!payment.stashedOpsCcData[item] || $('OPS_CC_' + item).value.length) {
                payment.stashedOpsCcData[item] = $('OPS_CC_' + item).value;
                $('OPS_CC_' + item).removeClassName('required-entry');
                $('OPS_CC_' + item).value = '';
                $('OPS_CC_' + item).disable();
            }
        });
    };

    payment.applyStashedCcData = function() {
        payment.criticalOpsCcData.each(function(item) {
            if ($('OPS_CC_' + item)) {
                if (payment.stashedOpsCcData[item] && 0 < payment.stashedOpsCcData[item].length) {
                    $('OPS_CC_' + item).value = payment.stashedOpsCcData[item];
                }
                $('OPS_CC_' + item).addClassName('required-entry');
                $('OPS_CC_' + item).enable();
            }
        });
    };

    payment.toggleOpsDirectDebitInputs = function(country) {
        var cn = 'ops_directdebit_CN';
        var bankcode = 'ops_directdebit_bank_code';
        var showInput = function(id) {
            $$('#' + id)[0].up().show();
            $(id).addClassName('required-entry');
        };
        var hideInput = function(id) {
            $$('#' + id)[0].up().hide();
            $(id).removeClassName('required-entry');
        };
        if ('NL' == country) {
            showInput(cn);
            hideInput(bankcode);
        }
        if ('DE' == country || 'AT' == country) {
            hideInput(cn);
            showInput(bankcode);
        }
    };

    payment.toggleOpsCcInputs = function() {
        if (-1 < opsCcBrandsForAliasInterface.indexOf($('OPS_CC_BRAND').value)) {
            $('ops_cc_data').show();
        } else {
            $('ops_cc_data').hide();
        }
    };

    accordion.openSection = accordion.openSection.wrap(function(originalOpenSectionMethod, section) {
        if (section.id == 'opc-payment') {
            payment.applyStashedCcData();
        }
        originalOpenSectionMethod(section);
    });
});

