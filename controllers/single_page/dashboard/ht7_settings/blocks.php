<?php
namespace Concrete\Package\Ht7Default\Controller\SinglePage\Dashboard\Ht7Settings;

use \Concrete\Core\Page\Controller\DashboardPageController;
use Page;

class Blocks extends DashboardPageController
{
    public function getCollectionDescription()
    {
        return t("Default Settings Page for Blocks");
    }
    
    public function view()
    {
        $c = Page::getCurrentPage();
        $pages = $c->getCollectionChildren();
        $this->set('pages', $pages);
    }
}
