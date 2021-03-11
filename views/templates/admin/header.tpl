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
{if !empty($wi_importer.errors)}
    {foreach $wi_importer.errors as $error}
    <div class="alert alert-danger wi_error">
        {$error|escape:'html':'UTF-8'}
    </div>
    {/foreach}
{/if}
<div class="wi_module_wrapper">
  <ul class="wi_module_menu">
    {foreach $wi_importer.menu as $tab}    
    <li>
      <a href="{$tab.link|escape:'html':'UTF-8'}" {if $tab.active}class="active"{/if}>{$tab.label|escape:'html':'UTF-8'}</a>
    </li>
    {/foreach}
  </ul>
