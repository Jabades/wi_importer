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

if (!defined('_PS_VERSION_')) {
    exit;
}

class Wi_importer extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'wi_importer';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Jesús Abades';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Webimpacto Products CSV importer');
        $this->description = $this->l('Imports productos from a CSV source.');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('WI_IMPORTER_SOURCE', '');
        Configuration::updateValue('WI_IMPORTER_LIMIT', 5);
        Configuration::updateValue('WI_IMPORTER_POSITION', 2);
        $this->addTab($this->name, 'AdminWiImporterAjax', -1, 'Ajax');
        return parent::install() &&
        $this->registerHook('backOfficeHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('WI_IMPORTER_SOURCE');
        if ($id_tab = Tab::getIdFromClassName('AdminWiImporterAjax')) {
			$tab = new Tab($id_tab);
			$tab->delete();
		}
        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool) Tools::isSubmit('submitWi_importerModule')) == true) {
            $this->postProcess();
        }
        $html = $this->renderForm();
        $params = array(
            'wi_importer' => array(
                'module_dir' => $this->_path,
                'module_name' => $this->name,
                'base_url' => _MODULE_DIR_ . $this->name . '/',
                'iso_code' => $this->context->language->iso_code,
                'menu' => $this->getMenu(),
                'html' => $html,
                'errors' => empty($this->errors) ? array() : $this->errors,
            ),
        );

        $this->context->smarty->assign($params);

        $header = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/header.tpl');
        $body = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/body.tpl');
        $footer = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/footer.tpl');

        return $header . $body . $footer;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitWi_importerModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    protected function getMenu()
    {
        $tab = Tools::getValue('tab_sec');
        $tab_link = $this->context->link->getAdminLink('AdminModules', true)
        . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&tab_sec=';
        return array(
            array(
                'label' => $this->l('Set CSV source'),
                'link' => $tab_link . 'edit',
                'active' => ($tab == 'edit' || empty($tab) ? 1 : 0),
            ),
            array(
                'label' => $this->l('Help'),
                'link' => $tab_link . 'help',
                'active' => ($tab == 'help' ? 1 : 0),
            ),
        );
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 6,
                        'type' => 'text',
                        'desc' => $this->l('Enter the path of the CSV file.'),
                        'name' => 'WI_IMPORTER_SOURCE',
                        'label' => $this->l('CSV Path'),
                        'required' => true,
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'desc' => $this->l('This limit the number of products processed by AJAX to prevent a time out.'),
                        'name' => 'WI_IMPORTER_LIMIT',
                        'label' => $this->l('Products block limit'),
                        'required' => true,
                    ),
                    array(
                        'col' => 6,
                        'type' => 'importing',
                        'desc' => $this->l('Use these buttons, to import products from CSV or erase previously imported products by reference.'),
                        'name' => 'WI_IMPORTER_IMPORT',
                        'label' => $this->l('Import or delete products from CSV.'),
                        'required' => true,
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'WI_IMPORTER_SOURCE' => Configuration::get('WI_IMPORTER_SOURCE', ''),
            'WI_IMPORTER_LIMIT' => Configuration::get('WI_IMPORTER_LIMIT', null, null, null, 50),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        if (Tools::getValue('submitWi_importerModule')) {
            $form_values = $this->getConfigFormValues();
            foreach (array_keys($form_values) as $key) {
                Configuration::updateValue($key, Tools::getValue($key));
            }
            if (Configuration::get('WI_IMPORTER_SOURCE')) {
                $this->importFile(
                    Configuration::get('WI_IMPORTER_SOURCE')
                );
            }
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name || Tools::getValue('configure') == $this->name) {
            $token = Tools::getAdminTokenLite('AdminWiImporterAjax');
            if (version_compare(_PS_VERSION_, '1.6.1.0', '>=')) {
                Media::addJsDef(
                    array(
                        'wi_token' => $token,
                    )
                );                  
            } else {
                $this->context->smarty->assign(
                    array(
                        'wi_token' => $token,
                    )
                );
                return $this->context->smarty->fetch(
                    _PS_MODULE_DIR_ . $this->name
                    . DIRECTORY_SEPARATOR . 'views'
                    . DIRECTORY_SEPARATOR . 'templates'
                    . DIRECTORY_SEPARATOR . 'front'
                    . DIRECTORY_SEPARATOR . 'javascript.tpl'
                );
            }
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    public function getFilePath()
    {
        return _PS_MODULE_DIR_ . $this->name . DIRECTORY_SEPARATOR . 'csv' . DIRECTORY_SEPARATOR . 'products.csv';
    }

    public function importFile($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        $data = curl_exec($curl);
        curl_close($curl);
        $path = $this->getFilePath();
        if (file_exists($path)) {
            @unlink($path);
        }
        $file = fopen($path, 'w');
        fwrite($file, $data);
        fclose($file);
    }

    public function deleteProducts()
    {
        $path = $this->getFilePath();
        if (($file = fopen($path, 'r')) !== false) {
            while (($row = fgetcsv($file, 1000, ',')) !== false) {
                $reference = $row[1];
                if ($id_product = Product::getIdByReference($reference)) {
                    $product = new Product($id_product);
                    $product->delete();
                }
            }
            Configuration::updateValue('WI_IMPORTER_POSITION', 2);
        }
    }

    public function createProducts()
    {
        $path = $this->getFilePath();
        if (!file_exists($path)) {
            return false;
        }
        $fp = file($path);
        $total = count($fp);
        $line = 2;
        $position = Configuration::get(
            'WI_IMPORTER_POSITION',
            null,
            null,
            null,
            2
        );
        $limit = (int) Configuration::get('WI_IMPORTER_LIMIT');
        $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $id_shop = (int) Configuration::get('PS_SHOP_DEFAULT');
        $languages = $this->context->controller->getLanguages();
        $address = $this->context->shop->getAddress();
        if (($file = fopen($path, 'r')) !== false) {
            $count = 0;
            while (($row = fgetcsv($file, 1000, ',')) !== false) {
                $name = $row[0];
                $reference = $row[1];
                $ean13 = (int) $row[2];
                $price_cost = (float) $row[3];
                $price = (float) $row[4];
                $tax = (float) $row[5];
                $quantity = (int) $row[6];
                $categories = explode(';', $row[7]);
                $manufacturer_name = $row[8];
                if (!empty($reference) &&
                    $limit > $count &&
                    $line > $position &&
                    !($id_product = Product::getIdByReference($reference))
                ) {
                    $product = new Product();
                    foreach ($languages as $language) {
                        $id_lang = $language['id_lang'];
                        $product->name[$id_lang] = $name;
                        $product->link_rewrite[$id_lang] = Tools::link_rewrite($product->name[$id_lang]);
                    }
                    $product->id_category = array();
                    $product->reference = $row[1];
                    $product->ean13 = $row[2];
                    $product->id_shop_default = $id_shop;
                    $product->id_shop_list[] = $id_shop;
                    $product->id_tax_rules_group = 0;
                    $product->tax_rate = $tax;
                    $product->id_tax_rules_group = $this->getTaxRuleGroup($address->id_country, $tax);
                    if (!$id_manufacturer = Manufacturer::getIdByName($manufacturer_name)) {
                        $manufacturer = new Manufacturer();
                        $manufacturer->name = $manufacturer_name;
                        $manufacturer->active = true;
                        $manufacturer->save();
                        $manufacturer->associateTo($product->id_shop_list);
                        $manufacturer->save();
                        $id_manufacturer = (int) $manufacturer->id;
                    }
                    $id_parent = (int) Configuration::get('PS_HOME_CATEGORY');
                    foreach ($categories as $category_name) {
                        if (!$row = Category::searchByName($id_lang, $category_name)) {
                            $category = new Category();
                            $category->active = 1;
                            $category->id_parent = $id_parent;
                            foreach ($languages as $language) {
                                $id_lang = $language['id_lang'];
                                $category->name[$id_lang] = $category_name;
                                $category->meta_title[$id_lang] = $category->name[$id_lang];
                                $category->link_rewrite[$id_lang] = Tools::link_rewrite($category->name[$id_lang]);
                            }
                            $category->add();
                            $product->id_category[] = $category->id;
                        } else {
                            $data = current($row);
                            $product->id_category[] = $data['id_category'];
                        }
                    }
                    $product->id_category = array_unique($product->id_category);
                    $product->id_category_default = current($product->id_category);
                    $product->id_manufacturer = $id_manufacturer;
                    $product->price = (float) number_format($price / (1 + $tax / 100), 6, '.', '');
                    $product->wholesale_price = $price_cost; // No tax ?
                    $product->add();
                    $product->addToCategories($product->id_category);
                    StockAvailable::setQuantity($product->id, 0, $quantity, $id_shop);
                    $position++;
                    $count++;
                }
                $line++;
            }
            Configuration::updateValue('WI_IMPORTER_POSITION', empty($reference) ? 2 : $position);
            Category::regenerateEntireNtree();
        }
    }

    public function getTaxRuleGroup($id_country = null, $tax_rate = null)
    {
        $rates = TaxRulesGroup::getAssociatedTaxRatesByIdCountry($id_country);
        foreach ($rates as $id_tax_rules_group => $rate) {
            if (0 == ((float) $tax_rate - (float) $rate)) {
                return $id_tax_rules_group;
            }
        }
        return null;
    }

    /**
     * Function to add the controller for AJAX functions.
     */
    public function addTab($module, $tabClass, $id_parent = 0, $title)
    {			
        $tab = new Tab();
        $tab->class_name = $tabClass;
        $tab->id_parent  = $id_parent;
        $tab->module     = $module;
        $languages       = Language::getLanguages();
        foreach ($languages as $language) {
            $tab->name[$language['id_lang']] = $title;
        }
        $tab->add();
		return (int)$tab->id;
    }
}
