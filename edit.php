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

define('INTERNAL', 1);
define('MENUITEM', 'create/learning');
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'learning');
define('LEARNING_SUBPAGE', 'learninginfo');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
require_once('pieforms/pieform.php');
safe_require('artefact', 'learning');
safe_require('artefact', 'file');

if (!PluginArtefactLearning::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('Learning','artefact.learning')));
}

$id = param_integer('id', 0);
if (!$id) {
    define('TITLE', get_string('newlearning','artefact.learning'));
    define('SUBSECTIONHEADING', get_string('learninginfo', 'artefact.learning'));
	$title = '';
    $description = '';
    $tags = array();
    $attachments = array();
    $allowcomments = true;
}
else {
    define('TITLE', get_string('editlearning','artefact.learning'));
    define('SUBSECTIONHEADING', get_string('learninginfo', 'artefact.learning'));    
	$learning = new ArtefactTypeLearning($id);
    if (!$USER->can_edit_artefact($learning)) {
        throw new AccessDeniedException(get_string('canteditdontownlearning', 'artefact.learning'));
    }
    $title = $learning->get('title');
    $description = $learning->get('description');
    $tags = $learning->get('tags');
    $attachments = get_column_sql("SELECT attachment FROM {artefact_attachment} WHERE artefact = ? AND item IS NULL", array($id));
    $allowcomments = $learning->get('allowcomments');
}

$folder = param_integer('folder', 0);
$browse = (int) param_variable('browse', 0);
$highlight = null;
if ($id) {
    $page = get_config('wwwroot') . 'artefact/learning/edit.php?id=' . $id;
}
else {
    $page = get_config('wwwroot') . 'artefact/learning/edit.php';
}
if ($file = param_integer('file', 0)) {
    $highlight = array($file);
}

$form = pieform(array(
    'name'               => 'learningform',
    'method'             => 'post',
    'jssuccesscallback'  => 'learningform_callback',
    'jserrorcallback'    => 'learningform_callback',
    'plugintype'         => 'artefact',
    'pluginname'         => 'learning',
    'elements' => array(
        'title' => array(
            'type' => 'text',
            'defaultvalue' => $title,
            'title' => get_string('name'),
            'size' => 30,
            'rules' => array('required' => true),
        ),
        'description' => array(
            'type' => 'textarea',
            'rows' => 10,
            'cols' => 50,
            'resizable' => false,
            'defaultvalue' => $description,
            'title' => get_string('description'),
        ),
        'tags' => array(
            'type' => 'tags',
            'title' => get_string('tags'),
            'description' => get_string('tagsdescprofile'),
            'defaultvalue' => $tags,
        ),
        'filebrowser' => array(
            'type'         => 'filebrowser',
            'title'        => get_string('attachments', 'artefact.blog'),
            'folder'       => $folder,
            'highlight'    => $highlight,
            'browse'       => $browse,
            'page'         => $page,
            'config'       => array(
                'upload'          => true,
                'uploadagreement' => get_config_plugin('artefact', 'file', 'uploadagreement'),
                'resizeonuploaduseroption' => get_config_plugin('artefact', 'file', 'resizeonuploaduseroption'),
                'resizeonuploaduserdefault' => $USER->get_account_preference('resizeonuploaduserdefault'),
                'createfolder'    => false,
                'edit'            => false,
                'select'          => true,
            ),
            'defaultvalue'       => $attachments,
            'selectlistcallback' => 'artefact_get_records_by_id',
            'selectcallback'     => 'add_attachment',
            'unselectcallback'   => 'delete_attachment',
        ),
        'allowcomments' => array(
            'type'         => 'switchbox',
            'title'        => get_string('allowcomments','artefact.comment'),
            'description'  => get_string('allowcommentsonlearning','artefact.learning'),
            'defaultvalue' => $allowcomments,
        ),
        'submitpost' => array(
            'type' => 'submitcancel',
            'class' => 'btn-primary',
            'value' => array(get_string('save'), get_string('cancel')),
            'goto' => get_config('wwwroot') . 'artefact/learning/index.php',
        )
    ),
));


$smarty = smarty();
setpageicon($smarty, 'icon-graduation-cap');
$smarty->assign_by_ref('form', $form);
$smarty->assign('PAGEHEADING', hsc(TITLE));
$smarty->assign('SUBPAGENAV', PluginArtefactLearning::submenu_items($id));
$smarty->display('form.tpl');


function learningform_submit(Pieform $form, $values) {
    global $USER, $SESSION, $id;

    $newlearning = true;
    if ($id > 0) {
        $newlearning = false;
    }

    db_begin();
    $artefact = new ArtefactTypeLearning($id);
    $artefact->set('owner', $USER->get('id'));
    $artefact->set('title', $values['title']);
    $artefact->set('description', $values['description']);
    $artefact->set('tags', $values['tags']);
    $artefact->set('allowcomments', $values['allowcomments']);
    $artefact->commit();
    $id = $artefact->get('id');

    $data = (object)array(
        'artefact'       => $id,
        'goals'          => null,
        'priorknowledge' => null,
        'strategies'     => null,
        'evidence'       => null,
        'evaluation'     => null,
    );

    if ($newlearning) {
        insert_record('artefact_learning_stages', $data);
    }

    // Attachments
    $old = get_column_sql("SELECT attachment FROM {artefact_attachment} WHERE artefact = ? AND item IS NULL", array($id));
    $new = is_array($values['filebrowser']) ? $values['filebrowser'] : array();
    if (!empty($new) || !empty($old)) {
        foreach ($old as $o) {
            if (!in_array($o, $new)) {
                try {
                    $artefact->detach($o);
                }
                catch (ArtefactNotFoundException $e) {}
            }
        }
        foreach ($new as $n) {
            if (!in_array($n, $old)) {
                try {
                    $artefact->attach($n);
                }
                catch (ArtefactNotFoundException $e) {}
            }
        }
    }

    db_commit();

    $result = array(
        'error'   => false,
        'message' => get_string('learningsavedsuccessfully', 'artefact.learning'),
        'goto'    => get_config('wwwroot') . 'artefact/learning/edit.php?id=' . $id,
    );
    $form->reply(PIEFORM_OK, $result);

}


function add_attachment($attachmentid) {
    global $artefact, $fieldset;
    if ($artefact) {
        $artefact->attach($attachmentid);
    }
}

function delete_attachment($attachmentid) {
    global $artefact;
    if ($artefact) {
        $artefact->detach($attachmentid);
    }
}

