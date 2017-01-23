<?php
namespace Concrete\Package\Ht7Default\Controller\SinglePage\Dashboard;

defined('C5_EXECUTE') or die('Access Denied.');

use \Concrete\Core\Page\Controller\DashboardPageController;
use Page;

class Ht7Settings extends DashboardPageController
{
    public function getCollectionDescription()
    {
        return t("Default Ht7 Settings Page");
    }
    
    public function view()
    {
        $c = Page::getCurrentPage();
        $pages = $c->getCollectionChildren();
        $this->set('pages', $pages);
    }
}
