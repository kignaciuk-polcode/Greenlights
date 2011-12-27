/**
 * ||GEISSWEB| EU-VAT-GROUPER
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GEISSWEB End User License Agreement
 * that is available through the world-wide-web at this URL:
 * http://www.geissweb.de/eula.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@geissweb.de so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.geissweb.de/ for more information
 * or send an email to support@geissweb.de or visit our customer forum at
 * http://forum.geissweb.de to make a feature request.
 *
 * @category   Mage
 * @package    Geissweb_Euvatgrouper
 * @copyright  Copyright (c) 2011 GEISS Weblösungen (http://www.geissweb.de)
 * @license    http://www.geissweb.de/eula.html GEISSWEB End User License Agreement
 */
function gw_init_vatvalidation(gw_vat_check_url, vatTranslator)
{
    var prefix;
    if (document.getElementById('taxvat') == null) { prefix = 'billing:'; } else { prefix=""; }

    try {

        if ($(prefix+'taxvat').getValue() != "")
        {
            new Ajax.Request(gw_vat_check_url, {
                method:'post',
                parameters: $(prefix+'taxvat').serialize(true),
                onLoading: function(){
                    if ($(prefix+'taxvat').getValue() != "") {
                        $('vatLoader').show();
                    }
                },
                onComplete: function(){
                    $('vatLoader').hide();
                },
                onSuccess: function(transport){

                    var response = transport.responseText.evalJSON();
                    var output = "<ul>";

                    if(response.valid == 1 && typeof(response.faultstring) == "undefined"){
                        output += '<li class="success-msg">';
                        output += (vatTranslator.translate('gw_valid_vat'));
                        output += '</li>';

                        if(response.is_vat_free == 1){
                            output += '<li class="success-msg">';
                            output += (vatTranslator.translate('gw_taxfree_success'));
                            output += '</li>';
                        } else if(response.is_vat_free == 0){
                            output += '<li class="notice-msg">';
                            output += (vatTranslator.translate('gw_customer_from_own_country'));
                            output += '</li>';
                        }
                        output += '</ul>';

                    } else if(response.valid == 0 && typeof(response.faultstring) == "undefined") {
                        output += '<li class="error-msg">';
                        output += (vatTranslator.translate('gw_invalid_vat'));
                        output += '</li></ul>';
                    } else {
                        output += '<li class="notice-msg">';
                        switch(response.faultstring)
                        {
                            case "{ 'INVALID_INPUT' }":
                                output += (vatTranslator.translate('gw_vatfield_wrong_syntax'));
                                break;
                            case "{ 'SERVICE_UNAVAILABLE' }":
                            case "{ 'SERVER_BUSY' }":
                                output += (vatTranslator.translate('gw_vies_unavailable'));
                                break;
                            case "{ 'MS_UNAVAILABLE' }":
                            case "{ 'TIMEOUT' }":
                                output += (vatTranslator.translate('gw_memberstate_unavailable'));
                                break;
                            default:
                                output += (vatTranslator.translate('gw_general_error'));
                                break;
                        }
                        output += '</li></ul>';
                    }
                    $('checkrsp').update(output);

                    //Onestepcheckout compatibility
                    if(typeof(document.getElementById('onestepcheckout-form')) != "undefined")
                    {
                        get_save_billing_function('/onestepcheckout/ajax/save_billing/', '/onestepcheckout/ajax/set_methods_separate/')();
                    }



                },
                onFailure: function(){
                    alert(vatTranslator.translate('gw_general_error'))
                }
            });//endajax

        //Field was cleared
        } else {
            new Ajax.Request(gw_vat_check_url, {
                method:'post',
                parameters: 'vatid=removed'
            });//endajax
            $('checkrsp').update();
        }

    } catch (error) {
        alert(vatTranslator.translate('gw_general_error') + " | " +error)
    }
}





