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

// Need this to test actions that require permissions.
$AppUI  = new CAppUI;
$_POST['login'] = 'login';
$_REQUEST['login'] = 'sql';
$AppUI->login('admin', 'passwd');

require_once W2P_BASE_DIR . '/includes/session.php';
require_once 'PHPUnit/Framework.php';
/**
 * DateTest Class.
 * 
 * Class to test the date include
 * @author D. Keith Casey, Jr.
 * @package web2project
 * @subpackage unit_tests
 */
class CAppUI_Test extends PHPUnit_Framework_TestCase 
{
	public function test__()
	{
		global $AppUI, $w2Pconfig;

		$w2Pconfig['locale_warn'] = false;
		$this->assertEquals('Company', $AppUI->__('Company'));
		$this->assertEquals('NoGonnaBeThere', $AppUI->__('NoGonnaBeThere'));

		/* Turn on 'untranslatable' warning */
		$w2Pconfig['locale_warn'] = true;
		$this->assertEquals('Projects^', $AppUI->__('Projects'));
		$this->assertEquals('Add File^', $AppUI->__('Add File'));

		/* Change to another language and reload tranlations */
		$AppUI->user_locale = 'es';
		require W2P_BASE_DIR . '/locales/core.php';
		$this->assertEquals('Proyectos', $AppUI->__('Projects'));
		$this->assertEquals('Ciudad', $AppUI->__('City'));
		$this->assertEquals('StillNotThere^', $AppUI->__('StillNotThere'));

		/* Change back to English and reload tranlations */
		$AppUI->user_locale = 'en';
		require W2P_BASE_DIR . '/locales/core.php';
		$this->assertEquals('Projects', $AppUI->__('Projects'));
		$this->assertEquals('NoGonnaBeThere^', $AppUI->__('NoGonnaBeThere'));
	}

	public function testGetPref()
	{
		global $AppUI;

		$this->assertEquals('en', $AppUI->getPref('LOCALE'));
		$this->assertEquals('', $AppUI->getPref('NotGonnaBeThere'));
	}

	public function testSetPref()
	{
		global $AppUI;

		$this->assertEquals('en', $AppUI->getPref('LOCALE'));
		$AppUI->setPref('AddingThis', 'Monkey');
		$this->assertEquals('Monkey', $AppUI->getPref('AddingThis'));
	}

  public function testSetState()
  {
  	global $AppUI;
    
    $AppUI->setState('testSetState', 'someValue');
    $this->assertEquals('someValue', $AppUI->getState('testSetState'));
    $AppUI->setState('testSetState', 'anotherValue');
    $this->assertEquals('anotherValue', $AppUI->getState('testSetState'));
  }

  public function testProcessTabState()
  {
    global $AppUI;   
    $myArray = array('existingKey' => 13, 'existingKey2' => 42);

    $AppUI->processIntState('testProcessState', null,     'existingKey', 9);
    $this->assertEquals(9, $AppUI->getState('testProcessState'));

    $AppUI->processIntState('testProcessState', $myArray, 'existingKey', 9);
    $this->assertEquals(13, $AppUI->getState('testProcessState'));

    $AppUI->processIntState('testProcessNull', $myArray,  'missingKey',  14);
    $this->assertEquals(14, $AppUI->getState('testProcessNull'));

    $AppUI->processIntState('testProcessState', $myArray, 'missingKey',  79);
    $this->assertEquals(13, $AppUI->getState('testProcessState'));
  }

	public function testSavePlace()
	{
		global $AppUI;

		$_SERVER['QUERY_STRING'] = 'testUrl';
		$AppUI->savePlace();
		$this->assertEquals('testUrl', $AppUI->getPlace());

		$AppUI->savePlace('?m=projects&amp;a=view&amp;project_id=1');
		$this->assertEquals('?m=projects&amp;a=view&amp;project_id=1', $AppUI->getPlace());
	}

	public function testResetPlace()
	{
		global $AppUI;

		$_SERVER['QUERY_STRING'] = 'testUrl';
		$AppUI->savePlace();
		$this->assertEquals('testUrl', $AppUI->getPlace());
		$AppUI->resetPlace();
		$this->assertEquals('', $AppUI->getPlace());
	}

	public function testHoldRestoreObject()
	{
	  global $AppUI;
	  
	  $this->assertNull($AppUI->restoreObject());
	  $myArray = array('one' => 'something', 2 => 'another');
	  $AppUI->holdObject($myArray);

	  $result = $AppUI->restoreObject();
	  $this->AssertEquals(2, count($result));
	  $this->assertArrayHasKey('one', $result);
	  $this->assertArrayHasKey(2, $result);
	  $this->assertNull($AppUI->restoreObject());
	}

	public function testSetMsg()
	{
	  global $AppUI;

	  $msg = 'This is a test';
	  $AppUI->setMsg($msg, 0, false);
	  $this->AssertEquals($msg, $AppUI->msg);
	  $AppUI->setMsg($msg, 0, true);
	  $this->AssertEquals($msg.' '.$msg, $AppUI->msg);	  
	  $AppUI->setMsg($msg, 0, false);
	  $this->AssertEquals($msg, $AppUI->msg);

	  $myArray = array('one' => 'First Message', 'two' => 'Second Message');
	  $AppUI->setMsg($myArray, 0, false);
	  $this->AssertEquals('First Message<br />Second Message', $AppUI->msg);

	  $AppUI->setMsg($msg, 0, false);
	  $this->AssertEquals($msg, $AppUI->msg);
	}
}