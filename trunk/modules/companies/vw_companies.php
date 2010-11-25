<?php /* $Id$ $URL$ */
if (!defined('W2P_BASE_DIR')) {
	die('You should not access this file directly.');
}

global $search_string;
global $owner_filter_id;
global $currentTabId;
global $currentTabName;
global $tabbed;
global $type_filter;
global $orderby;
global $orderdir;

// load the company types

$types = w2PgetSysVal('CompanyType');
// get any records denied from viewing

$obj = new CCompany();
$allowedCompanies = $obj->getAllowedRecords($AppUI->user_id, 'company_id, company_name');

$company_type_filter = $currentTabId;
//Not Defined
$companiesType = true;
if ($currentTabName == 'All Companies') {
	$companiesType = false;
}
if ($currentTabName == 'Not Applicable') {
	$company_type_filter = 0;
}

// retrieve list of records
$q = new DBQuery;
$q->addTable('companies', 'c');
$q->addQuery('c.company_id, c.company_name, c.company_type, c.company_description, count(distinct p.project_id) as countp, count(distinct p2.project_id) as inactive, con.contact_first_name, con.contact_last_name');
$q->addJoin('projects', 'p', 'c.company_id = p.project_company AND p.project_active = 1');
$q->addJoin('users', 'u', 'c.company_owner = u.user_id');
$q->addJoin('contacts', 'con', 'u.user_contact = con.contact_id');
$q->addJoin('projects', 'p2', 'c.company_id = p2.project_company AND p2.project_active = 0');
if (count($allowedCompanies) > 0) {
	$q->addWhere('c.company_id IN (' . implode(',', array_keys($allowedCompanies)) . ')');
} else {
	$q->addWhere('0=1');
}
if ($companiesType) {
	$q->addWhere('c.company_type = ' . (int)$company_type_filter);
}
if ($search_string != '') {
	$q->addWhere('c.company_name LIKE "%'.$search_string.'%"');
}
if ($owner_filter_id > 0) {
	$q->addWhere('c.company_owner = '.$owner_filter_id);
}
$q->addGroup('c.company_id');
$q->addOrder($orderby . ' ' . $orderdir);
$rows = $q->loadList();
?>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<tr>
	<th nowrap="nowrap">
		<a href="?m=companies&orderby=company_name" class="hdr"><?php echo $AppUI->_('Company Name'); ?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=companies&orderby=countp" class="hdr"><?php echo $AppUI->_('Active Projects'); ?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=companies&orderby=inactive" class="hdr"><?php echo $AppUI->_('Archived Projects'); ?></a>
	</th>
	<th nowrap="nowrap">
		<a href="?m=companies&orderby=company_type" class="hdr"><?php echo $AppUI->_('Type'); ?></a>
	</th>
</tr>
<?php
$s = '';
$none = true;
foreach ($rows as $row) {
	$none = false;
	$s .= '<tr>';
	$s .= '<td>' . (trim($row['company_description']) ? w2PtoolTip($row['company_name'], $row['company_description']) : '') . '<a href="./index.php?m=companies&a=view&company_id=' . $row['company_id'] . '" >' . $row['company_name'] . '</a>' . (trim($row['company_description']) ? w2PendTip() : '') . '</td>';
	$s .= '<td width="125" align="right" nowrap="nowrap">' . $row['countp'] . '</td>';
	$s .= '<td width="125" align="right" nowrap="nowrap">' . $row['inactive'] . '</td>';
	$s .= '<td align="left" nowrap="nowrap">' . $AppUI->_($types[$row['company_type']]) . '</td>';
	$s .= '</tr>';
}
echo $s;
if ($none) {
	echo '<tr><td colspan="5">' . $AppUI->_('No companies available') . '</td></tr>';
}
?>
</table>