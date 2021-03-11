<?php
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
 */

class AdminWiImporterAjaxController extends ModuleAdminController
{
    public function ajaxProcessImport()
    {
        $module = Module::getInstanceByName('wi_importer');        
        $path = $module->getFilePath();
        if (!file_exists($path)) {
            $percent = 100;
        } else {
            $fp = file($path);
            $lines = count($fp) - 1;
            $position = (int) Configuration::get('WI_IMPORTER_POSITION');
            $module->createProducts();
            $percent = (int) (($position * 100) / $lines);
            if ($percent >= 100) {
                $percent = 100;
                Configuration::updateValue('WI_IMPORTER_POSITION', 2);
            }
        }                
		die(
            Tools::jsonEncode(array('percent' => $percent))
        );
    }

    public function ajaxProcessDelete()
    {
        $module = Module::getInstanceByName('wi_importer');
        $module->deleteProducts();
        die(
            Tools::jsonEncode(array('deleted' => 1))
        );
    }
}
