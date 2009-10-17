<?php
/**
 * Necessary global variables 
 */
global $db;
global $ADODB_FETCH_MODE;
global $w2p_performance_dbtime;
global $w2p_performance_old_dbqueries;
global $AppUI;

require_once '../base.php';
require_once W2P_BASE_DIR . '/includes/config.php';
require_once W2P_BASE_DIR . '/includes/main_functions.php';
require_once W2P_BASE_DIR . '/includes/db_adodb.php';

/*
 * Need this to test actions that require permissions.
 */
$AppUI  = new CAppUI;
$_POST['login'] = 'login';
$_REQUEST['login'] = 'sql';
$AppUI->login('admin', 'passwd');

require_once W2P_BASE_DIR . '/includes/session.php';
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Extensions/Database/TestCase.php';

/**
 * LinksTest Class.
 * 
 * Class to test the companies class
 * @author Trevor Morse<trevor.morse@gmail.com>
 * @package web2project
 * @subpackage unit_tests
 */
class Links_Test extends PHPUnit_Extensions_Database_TestCase 
{
    
    protected $backupGlobals = FALSE;
        
    /**
     * Return database connection for tests
     */
    protected function getConnection()
    {
        $pdo = new PDO(w2PgetConfig('dbtype') . ':host=' . 
                       w2PgetConfig('dbhost') . ';dbname=' . 
                       w2PgetConfig('dbname'), 
                       w2PgetConfig('dbuser'), w2PgetConfig('dbpass'));
        return $this->createDefaultDBConnection($pdo, w2PgetConfig('dbname'));
    }
    
    /**
     * Set up default dataset for testing
     */
    protected function getDataSet()
    {
        return $this->createXMLDataSet($this->getDataSetPath().'linksSeed.xml');
    }
    protected function getDataSetPath()
    {
      return dirname(dirname(__FILE__)).'/db_files/';
    }

    /**
     * Tests the Attributes of a new Links object.
     */
    public function testNewLinkAttributes() 
    {        
        $link = new CLink();
        
        $this->assertType('CLink', $link);
        $this->assertObjectHasAttribute('link_id',          $link);
        $this->assertObjectHasAttribute('link_project',     $link);
        $this->assertObjectHasAttribute('link_url',         $link);
        $this->assertObjectHasAttribute('link_task',        $link);
        $this->assertObjectHasAttribute('link_name',        $link);
        $this->assertObjectHasAttribute('link_parent',      $link);
        $this->assertObjectHasAttribute('link_description', $link);
        $this->assertObjectHasAttribute('link_owner',       $link);
        $this->assertObjectHasAttribute('link_date',        $link);
        $this->assertObjectHasAttribute('link_icon',        $link);
        $this->assertObjectHasAttribute('link_category',    $link);
        $this->assertObjectHasAttribute('_tbl_prefix',      $link);
        $this->assertObjectHasAttribute('_tbl',             $link);
        $this->assertObjectHasAttribute('_tbl_key',         $link);
        $this->assertObjectHasAttribute('_error',           $link);
        $this->assertObjectHasAttribute('_query',           $link);
    }
    
    /**
     * Tests the Attribute Values of a new Link object.
     */
    public function testNewLinkAttributeValues() 
    {        
        $link = new CLink();
        $this->assertType('CLink', $link);
        $this->assertNull($link->link_id);
        $this->assertNull($link->link_project);
        $this->assertNull($link->link_url);
        $this->assertNull($link->link_task);
        $this->assertNull($link->link_name);
        $this->assertNull($link->link_parent);
        $this->assertNull($link->link_description);
        $this->assertNull($link->link_owner);
        $this->assertNull($link->link_date);
        $this->assertNull($link->link_icon);
        $this->assertNull($link->link_category);
        $this->assertEquals('',       $link->_tbl_prefix); 
        $this->assertEquals('links',  $link->_tbl);
        $this->assertEquals('link_id',$link->_tbl_key);
        $this->assertEquals('',       $link->_error);
        $this->assertType('DBQuery',  $link->_query);
    }

    /**
     * Tests that the proper error message is returned when a link is attempted
     * to be created without a name.
     */
    public function testCreateLinkNoName() 
    {        
        global $AppUI;

        $link = new CLink();

        $post_array = array(
            'dosql'             => 'do_link_aed',
            'link_id'           => 0,
            'link_name'         => '',
            'link_project'      => 0,
            'link_task'         => 0,
            'link_url'          => 'http://web2project.net',
            'link_parent'       => '0',
            'link_description'  => 'This is web2project',
            'link_owner'        => 1,
            'link_date'         => '2009-01-01',
            'link_icon'         => '',
            'link_category'     => 0
        );
        $link->bind($post_array);
        $errorArray = $link->store($AppUI);

        /**
         * Verify we got the proper error message
         */
        $this->assertArrayHasKey('link_name', $errorArray);

        /**
         * Verify that link id was not set
         */
        $this->assertEquals(0, $link->link_id);
    }

    /**
     * Tests that the proper error message is returned when a link is attempted
     * to be created without a url.
     */
    public function testCreateLinkNoUrl() 
    {        
        global $AppUI;

        $link = new CLink();

        $post_array = array(
            'dosql'             => 'do_link_aed',
            'link_id'           => 0,
            'link_name'         => 'web2project homepage',
            'link_project'      => 0,
            'link_task'         => 0,
            'link_url'          => '',
            'link_parent'       => '0',
            'link_description'  => 'This is web2project',
            'link_owner'        => 1,
            'link_date'         => '2009-01-01',
            'link_icon'         => '',
            'link_category'     => 0
        );
        $link->bind($post_array);
        $errorArray = $link->store($AppUI);

        /**
         * Verify we got the proper error message
         */
        $this->assertArrayHasKey('link_url', $errorArray);

        /**
         * Verify that link id was not set
         */
        $this->assertEquals(0, $link->link_id);
    }

    /**
     * Tests that the proper error message is returned when a link is attempted
     * to be created without an owner.
     */
    public function testCreateLinkNoOwner() 
    {        
        global $AppUI;

        $link = new CLink();

        $post_array = array(
            'dosql'             => 'do_link_aed',
            'link_id'           => 0,
            'link_name'         => 'web2project homepage',
            'link_project'      => 0,
            'link_task'         => 0,
            'link_url'          => 'http://web2project.net',
            'link_parent'       => '0',
            'link_description'  => 'This is web2project',
            'link_owner'        => null,
            'link_date'         => '2009-01-01',
            'link_icon'         => '',
            'link_category'     => 0
        );
        $link->bind($post_array);
        $errorArray = $link->store($AppUI);

        /**
         * Verify we got the proper error message
         */
        $this->assertArrayHasKey('link_owner', $errorArray);

        /**
         * Verify that link id was not set
         */
        $this->assertEquals(0, $link->link_id);
    }

    /**
     * Tests the proper creation of a link
     */
    public function testCreateLink() 
    {        
        global $AppUI;
        $link = new CLink();

        $post_array = array(
            'dosql'             => 'do_link_aed',
            'link_id'           => 0,
            'link_name'         => 'web2project homepage',
            'link_project'      => 0,
            'link_task'         => 0,
            'link_url'          => 'http://web2project.net',
            'link_parent'       => '0',
            'link_description'  => 'This is web2project',
            'link_owner'        => 1,
            'link_date'         => '2009-01-01',
            'link_icon'         => '',
            'link_category'     => 0
        );
        $link->bind($post_array);
        $result = $link->store($AppUI);

        $this->assertTrue($result);
        $this->assertEquals('web2project homepage',   $link->link_name);
        $this->assertEquals(0,                        $link->link_project);
        $this->assertEquals(0,                        $link->link_task);
        $this->assertEquals('http://web2project.net', $link->link_url);
        $this->assertEquals(0,                        $link->link_parent);
        $this->assertEquals('This is web2project',    $link->link_description);
        $this->assertEquals(1,                        $link->link_owner);
        $this->assertEquals('2009-01-01',             $link->link_date);
        //$this->assertEquals('obj/',                   $link->link_icon);
        $this->assertEquals(0,                        $link->link_category);

//        $xml_dataset = $this->createXMLDataSet(dirname(__FILE__).'/../db_files/linksTestCreate.xml');        
//        $this->assertTablesEqual($xml_dataset->getTable('links'), $this->getConnection()->createDataSet()->getTable('links'));
    }

    /** 
     * Tests loading the Link Object
     */
    public function testLoad() 
    {        
        $link = new CLink();
        $link->load(1);

        $this->assertEquals('web2project homepage',   $link->link_name);
        $this->assertEquals(0,                        $link->link_project);
        $this->assertEquals(0,                        $link->link_task);
        $this->assertEquals('http://web2project.net', $link->link_url);
        $this->assertEquals(0,                        $link->link_parent);
        $this->assertEquals('This is web2project',    $link->link_description);
        $this->assertEquals(1,                        $link->link_owner);
        $this->assertEquals('2009-01-01 00:00:00',    $link->link_date);
        $this->assertEquals('obj/',                   $link->link_icon);
        $this->assertEquals(0,                        $link->link_category);
    }
    
//    /**
//     * Tests the update of a company
//     */
//    public function testUpdateLink() 
//    {       
//        global $AppUI;
//        $link = new CLink();
//        $link->load(1);
//        
//        $post_array = array(
//            'dosql'                 => 'do_company_aed',
//            'company_id'            => $link_id,
//            'company_name'          => 'UpdatedCompany',
//            'company_email'         => 'updated@example.org',
//            'company_phone1'        => '1.777.999.9999',
//            'company_phone2'        => '1.777.999.9998',
//            'company_fax'           => '1.777.999.9997',
//            'company_address1'      => 'Updated Address 1',
//            'company_address2'      => 'Updated Address 2',
//            'company_city'          => 'Updated City',
//            'company_state'         => 'NS',
//            'company_zip'           => 'A2A 2B2',
//            'company_country'       => 'CA',
//            'company_primary_url'   => 'ut.web2project.net',
//            'company_owner'         => 1,
//            'company_type'          => 2,
//            'company_description'   => 'This is an updated company.'
//        );
//        
//        $link->bind($post_array);
//        $link->store($AppUI);
//        
//        $this->assertEquals('UpdatedCompany',               $link->company_name);
//        $this->assertEquals('updated@example.org',          $link->company_email);
//        $this->assertEquals('1.777.999.9999',               $link->company_phone1);
//        $this->assertEquals('1.777.999.9998',               $link->company_phone2);
//        $this->assertEquals('1.777.999.9997',               $link->company_fax);
//        $this->assertEquals('Updated Address 1',            $link->company_address1);
//        $this->assertEquals('Updated Address 2',            $link->company_address2);
//        $this->assertEquals('Updated City',                 $link->company_city);
//        $this->assertEquals('NS',                           $link->company_state);
//        $this->assertEquals('A2A 2B2',                      $link->company_zip);
//        $this->assertEquals('CA',                           $link->company_country);
//        $this->assertEquals('ut.web2project.net',           $link->company_primary_url);
//        $this->assertEquals(1,                              $link->company_owner);
//        $this->assertEquals(2,                              $link->company_type);
//        $this->assertEquals('This is an updated company.',  $link->company_description);
//        
//        $xml_dataset = $this->createXMLDataSet($this->getDataSetPath().'testUpdateCompany.xml');        
//        $this->assertTablesEqual($xml_dataset->getTable('companies'), $this->getConnection()->createDataSet()->getTable('companies'));
//    }

//    /**
//     * Tests the delete of a company
//     */
//    public function testDeleteLink() 
//    {              
//        global $AppUI;
//
//        $link = new CLink();
//        $link->link_id = 1;
//        $result = $link->delete($AppUI);
//        $this->assertTrue($result);
//    }
}