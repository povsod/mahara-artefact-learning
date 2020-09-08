<?php
/**
 *
 * @package    mahara
 * @subpackage artefact-learning
 * @author     Gregor Anzelj
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2013-2020 Gregor Anzelj <gregor.anzelj@gmail.com>
 *
 */


define('INTERNAL', 1);
define('MENUITEM', 'create/learning');
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'learning');
define('SECTION_PAGE', 'details');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
safe_require('artefact', 'learning');
define('TITLE', get_string('learningdetails','artefact.learning'));

$id = param_integer('id');

$learning = new ArtefactTypeLearning($id);
if (!$USER->can_edit_artefact($learning)) {
    throw new AccessDeniedException(get_string('accessdenied', 'error'));
}
$learning->files = ArtefactTypeLearning::get_stage_attachments($id);


$smarty = smarty(array('paginator', 'expandable'));
setpageicon($smarty, 'icon-graduation-cap');
$smarty->assign('learning', $learning);
$smarty->assign('id', $learning->get('id'));
$smarty->assign('description', $learning->get('description'));
$smarty->assign('tags', $learning->get('tags'));
$smarty->assign('owner', $learning->get('owner'));
$smarty->assign('PAGEHEADING', $learning->get('title'));
$smarty->display('artefact:learning:details.tpl');
