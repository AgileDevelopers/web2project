<?php /* $Id$ $URL$ */
if (!defined('W2P_BASE_DIR')) {
	die('You should not access this file directly.');
}

$del = isset($_POST['del']) ? $_POST['del'] : 0;

$isNotNew = $_POST['forum_id'];
$perms = &$AppUI->acl();
if ($del) {
	if (!canDelete('forums')) {
		$AppUI->redirect('m=public&a=access_denied');
	}
} elseif ($isNotNew) {
	if (!canEdit('forums')) {
		$AppUI->redirect('m=public&a=access_denied');
	}
} else {
	if (!canAdd('forums')) {
		$AppUI->redirect('m=public&a=access_denied');
	}
}

$obj = new CForum();

if (($msg = $obj->bind($_POST))) {
	$AppUI->setMsg($msg, UI_MSG_ERROR);
	$AppUI->redirect();
}

// prepare (and translate) the module name ready for the suffix
$AppUI->setMsg('Forum');
if ($del) {
	if (($msg = $obj->delete())) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
		$AppUI->redirect();
	} else {
		$AppUI->setMsg('deleted', UI_MSG_ALERT, true);
		$AppUI->redirect('m=forums');
	}
} else {
	if (($msg = $obj->store())) {
		$AppUI->setMsg($msg, UI_MSG_ERROR);
	} else {
		$AppUI->setMsg($isNotNew ? 'updated' : 'added', UI_MSG_OK, true);
	}
	$AppUI->redirect();
}