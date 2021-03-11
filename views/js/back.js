/**
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/

function wiProcessProducts(action)
{
    $.ajax({
        url: './index.php',
        method: 'post',
        dataType: 'json',
        cache: false,
        data: {
            'ajax': 1,
            'controller': 'AdminWiImporterAjax',
            'module_name': 'wi_importer',
            'configure': 'wi_importer',
            'action': action,
            'token': wi_token,
            'rand': new Date().getTime()
        },
        success: function (response) {
            if (action == 'import') {
                if (100 > response.percent) {
                    wiProcessProducts(action);
                }
                $('.wi-percent-complete').css('width', response.percent+'%');                
            } else {
                $('.lds-dual-ring').css('display', 'none');
            }
        },
        error: function (response) {
            console.log("error");
        }
    });
}

window.addEventListener('load',function() {
    $(document).on('click', '#wp-importer-import', function() {
        wiProcessProducts('import');
    });
    
    $(document).on('click', '#wp-importer-delete', function() {
        $('.lds-dual-ring').css('display', 'inline-block');
        wiProcessProducts('delete');
    });
});