<?php

if (!defined('_PS_VERSION_')) { // définition de la version de Prestashop 
    exit;
}

class SavePresta extends Module // Objet SavePresta qui est une instanciation de l'objet Module
{

    public function __construct() // Méthode qui définie le plan de construction du module
    {
        $this->name = 'SavePresta'; // Nom du module
        $this->tab = 'administration'; // Catégorie du module
        $this->version = '1.0.0'; // Version du module
        $this->author = 'Altayeb Rabeh and Chloé Pradeilles'; // Auteurs du module
        $this->need_instance = 0;

        $this->bootstrap = true; // Activation de Bootstrap pour la mise en page du module dans le BackOffice

        parent::__construct();

        $this->displayName = $this->l('SavePresta'); // Nom du module dans le BackOffice
        $this->description = $this->l('SavePresta est un module qui permet d\'effectuer une sauvegarde complète (fichiers + bdd) du site sur lequel le module est installé.'); // Description du module qui est affiché dans le BackOffice
        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?'); //Confirmation de la désinstallation du module
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_); // Défini pour quelle version, le module est accepté

        if (!Configuration::get('SAVEPRESTA')) { // permet de vérifier si la valeur NS_MONMODULE_PAGENAME est configurée ou non.
            $this->warning = $this->l('Aucun nom fourni');
        }
        
    }

 // Méthodes d'installation et de désinstallation
 // Elles font appel aux fonctions “install” et “uninstall” de la classe “Module” pour mettre toutes les configurations 
 // nécessaires à l’enregistrement du module.
    public function install() // Méthode d'installation du module 
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL); // Vérifie si le mode multi-boutique de Prestashop 1.7 est activé.
        }
     
        if (!parent::install() ||
            !Configuration::updateValue('SAVEPRESTA', 'name_Save') // Enregistrement du nom du module dans la base de donnée
        ) {
            return false;
        }
     
        return true;
    }

    public function uninstall() // Méthode de désinstallation du module 
    {
        if (!parent::uninstall() ||
        !Configuration::deleteByName('SAVEPRESTA') // Suppression de SAVEPRESTA dans la base de donnée
    ) {
        return false;
    }
 
    return true;
    }


    // Page de configuration 

    public function getContent()
    {
        $output = null;
 
        if (Tools::isSubmit('btnSubmit')) {
            $nameSave = strval(Tools::getValue('SAVEPRESTA'));
     
            if (
                !$nameSave||
                empty($nameSave)
            ) {
                $output .= $this->displayError($this->l('Veuillez renseigner le nom de la sauvegarde'));
            } else {
                Configuration::updateValue('SAVEPRESTA', $nameSave);
                $output .= $this->displayConfirmation($this->l('Sauvegarde effectuée'));

                /*

                Ici le code pour réaliser la sauvegarde fichiers + base de donnée

                */
            }
        }
     
        return $output.$this->displayForm();
    }


    public function displayForm()
    {
        // Get default language
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('SavePresta'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Nom de la sauvegarde'),
                    'name' => 'SAVEPRESTA',
                    'size' => 20,
                    'required' => true
                ]
            ],
            'submit' => [
                'title' => $this->l('Sauvegarder'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = [
            'Sauvegarder' => [
                'desc' => $this->l('Sauvegarder'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            ]
        ];

        // Load current value
        $helper->fields_value['SAVEPRESTA'] = Tools::getValue('SAVEPRESTA', Configuration::get('SAVEPRESTA'));

        return $helper->generateForm($fieldsForm);
    }


    /*

    Ici le code pour réaliser la sauvegarde fichiers + base de donnée

    */
    
}
