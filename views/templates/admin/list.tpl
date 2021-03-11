{*
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
*}
<div class="panel">
    <h3>
        <i class="icon-list-ul"></i> Block List
        <span class="panel-heading-action">
            <a id="desc-product-new" class="list-toolbar-btn" href="{$wi_welcome.link|escape:'html':'UTF-8'}">
                <span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Add new' mod='wi_welcome'}" data-html="true">
                    <i class="process-icon-new"></i>
                </span>
            </a>
        </span>
    </h3>
    {if !empty($wi_welcome.list)}
    <div id="slidesContent">
        <div id="slides">
            {foreach $wi_welcome.list as $item}
            <div id="slides_1" class="panel">
                <div class="row">
                    <div class="col-md-2">
                        <h4 class="pull-left">ID : #{$item.id|intval}</h4>
                    </div>
                    <div class="col-md-2">
                        <h4 class="pull-left">{$item.name|escape:'html':'UTF-8'}</h4>
                    </div>
                    <div class="col-md-2">
                        <h4 class="pull-left">{$item.hooks}</h4>
                    </div>
                    <div class="col-md-6">
                        <div class="btn-group-action pull-right">
                            <a class="btn btn-default" href="{$wi_welcome.link|escape:'html':'UTF-8'}&tab_sec=edit&WI_WELCOME_ID={$item.id|intval}">
                                <i class="icon-edit"></i>
                                {l s='Modify' mod='wi_welcome'}
                            </a>
                            <a class="btn btn-danger btn-delete-text-block" href="#" data-toggle="modal" data-id="{$item.id|intval}" data-target="#deleteModal">
                                <i class="icon-trash"></i>
                                {l s='Delete' mod='wi_welcome'}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            {/foreach}
        </div>
    </div>
    {else}
    <div class="alert alert-warning">
        <p>{l s='There is not text block created yet.' mod='wi_welcome'}</p>
    </diV
    {/if}
</div>
<div class="modal" id="deleteModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{l s='Deleting text block' mod='wi_welcome'}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning">
            <p class="text text-warnings">{l s='The block will be erased permanently. Are you sure ?' mod='wi_welcome'}</p>
        </div>      
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" id="wi_welcome_delete" data-id="0" data-url="{$wi_welcome.link|escape:'html':'UTF-8'}&tab_sec=list&WI_WELCOME_ID=">{l s='Delete' mod='wi_welcome'}</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Cancel' mod='wi_welcome'}</button>
      </div>
    </div>
  </div>
</div>
