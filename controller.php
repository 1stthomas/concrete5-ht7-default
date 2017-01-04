<?php
namespace Concrete\Package\Ht7Default;

use \Concrete\Core\Backup\ContentImporter;
use \Concrete\Core\Package\Package;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends Package
{
    protected $pkgHandle = 'ht7_default';
    protected $appVersionRequired = '5.7.5.8';
    protected $pkgVersion = '0.0.1';
    
    public function getPackageDescription()
    {
        return t('Installs the ht7 default package.');
    }
    
    public function getPackageName()
    {
        return t('Ht7 Default');
    }
    
    public function install()
    {
        $pkg = parent::install();
        // Process install.xml file
        $pkg = Package::getByHandle($this->pkgHandle);
        $ci = new ContentImporter();
        $ci->importContentFile($pkg->getPackagePath() . '/install.xml');
    }
}
