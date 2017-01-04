<?php
namespace Concrete\Package\Ht7Default\Controller\SinglePage\Dashboard\Ht7Settings;

use \Concrete\Core\Page\Controller\DashboardPageController;
use Page;

class Tools extends DashboardPageController
{
    public function getCollectionDescription()
    {
        return t("Default Settings Page for Ht7 Tools");
    }
    
    public function view()
    {
        $c = Page::getCurrentPage();
        $pages = $c->getCollectionChildren();
        $this->set('pages', $pages);
    }
}
