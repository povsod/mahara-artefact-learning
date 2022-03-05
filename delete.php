<?php
/**
 *
 * @package    mahara
 * @subpackage artefact-learning
 * @author     Gregor Anzelj
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 2013-2022 Gregor Anzelj <gregor.anzelj@gmail.com>
 *
 */

define('INTERNAL', true);
define('MENUITEM', 'create/learning');

require_once(dirname(dirname(dirname(__FILE__))) . '/init.php');
require_once('pieforms/pieform.php');
safe_require('artefact','learning');

define('TITLE', get_string('deletelearning','artefact.learning'));

$id = param_integer('id');
$todelete = new ArtefactTypeLearning($id);
if (!$USER->can_edit_artefact($todelete)) {
    throw new AccessDeniedException(get_string('accessdenied', 'error'));
}

$deleteform = array(
    'name' => 'deletelearningform',
    'plugintype' => 'artefact',
    'pluginname' => 'learning',
    'renderer' => 'div',
    'elements' => array(
        'submit' => array(
            'type' => 'submitcancel',
            'value' => array(get_string('deletelearning','artefact.learning'), get_string('cancel')),
            'goto' => get_config('wwwroot') . '/artefact/learning/index.php',
        ),
    )
);
$form = pieform($deleteform);

$smarty = smarty();
setpageicon($smarty, 'icon-graduation-cap');
$smarty->assign('form', $form);
$smarty->assign('PAGEHEADING', get_string('deletelearning','artefact.learning'));
$smarty->assign('subheading', get_string('deletethislearning','artefact.learning',$todelete->get('title')));
$smarty->assign('message', get_string('deletelearningconfirm','artefact.learning'));
$smarty->display('artefact:learning:delete.tpl');

// calls this function first so that we can get the artefact and call delete on it
function deletelearningform_submit(Pieform $form, $values) {
    global $SESSION, $todelete;

    $todelete->delete();
    $SESSION->add_ok_msg(get_string('learningdeletedsuccessfully', 'artefact.learning'));

    redirect('/artefact/learning/index.php');
}
