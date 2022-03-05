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

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
require_once('pieforms/pieform.php');
safe_require('artefact', 'learning');
safe_require('artefact', 'file');

if (!PluginArtefactLearning::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('Learning','artefact.learning')));
}

$stage = param_integer('stage', 0);
$id = param_integer('id', 0);
if ((!$id || !$stage) && ($stage >= 1 && $stage <= 5)) {
    throw new InvalidArgumentException("Couldn't find stage $stage of learning with id $id");
}

define('LEARNING_SUBPAGE', 'learningstage'.$stage);
define('TITLE', get_string('editlearning','artefact.learning'));
define('SUBSECTIONHEADING', get_string('stage'.$stage.'title', 'artefact.learning'));
// To include help (help icon)
define('SECTION_PAGE', 'stage'.$stage);

$learning = new ArtefactTypeLearning($id);
if (!$USER->can_edit_artefact($learning)) {
    throw new AccessDeniedException();
}

$content = '';
switch ($stage) {
    case 1:
        $content = $learning->get('priorknowledge');
        break;
    case 2:
        $content = $learning->get('goals');
        break;
    case 3:
        $content = $learning->get('strategies');
        break;
    case 4:
        $content = $learning->get('evidence');
        break;
    case 5:
        $content = $learning->get('evaluation');
        break;
}
$attachments = $learning->attachment_id_list_with_item($stage);

$folder = param_integer('folder', 0);
$browse = (int) param_variable('browse', 0);
$highlight = null;
$page = get_config('wwwroot') . 'artefact/learning/stage.php?stage=' . $stage . '&id=' . $id;
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
        'content' => array(
            'type' => 'wysiwyg',
            'title' => get_string('content'),
            'rows' => 20,
            'cols' => 50,
            'defaultvalue' => (!is_null($content) ? $content : get_string('hint', 'artefact.learning')),
            'rules' => array('maxlength' => 65536),
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
        'submitpost' => array(
            'type' => 'submitcancel',
            'class' => 'btn-primary',
            'value' => array(get_string('save'), get_string('cancel')),
            'goto' => get_config('wwwroot') . 'artefact/learning/index.php',
        )
    ),
));

/*
 * Javascript specific to this page.  Creates the list of files
 * attached to the learning.
 */
$wwwroot = get_config('wwwroot');
$noimagesmessage = json_encode(get_string('noimageshavebeenattachedtothispost', 'artefact.blog'));
$javascript = <<<EOF

// Override the image button on the tinyMCE editor.  Rather than the
// normal image popup, open up a modified popup which allows the user
// to select an image from the list of image files attached to the
// resume goals or skills.

// Get all the files in the attached files list that have been
// recognised as images.  This function is called by the the popup
// window, but needs access to the attachment list on this page
function attachedImageList() {
    var images = [];
    var attachments = learningform_filebrowser.selecteddata;
    for (var a in attachments) {
        if (attachments[a].artefacttype == 'image' || attachments[a].artefacttype == 'profileicon') {
            images.push({
                'id': attachments[a].id,
                'name': attachments[a].title,
                'description': attachments[a].description ? attachments[a].description : ''
            });
        }
    }
    return images;
}

function imageSrcFromId(imageid) {
    return config.wwwroot + 'artefact/file/download.php?file=' + imageid;
}

function imageIdFromSrc(src) {
    var artefactstring = 'download.php?file=';
    var ind = src.indexOf(artefactstring);
    if (ind != -1) {
        return src.substring(ind+artefactstring.length, src.length);
    }
    return '';
}

var imageList = {};

function learningImageWindow(ui, v) {
    var t = tinyMCE.activeEditor;

    imageList = attachedImageList();

    var template = new Array();

    template['file'] = '{$wwwroot}artefact/blog/image_popup.php';
    template['width'] = 355;
    template['height'] = 275 + (tinyMCE.isMSIE ? 25 : 0);

    // Language specific width and height addons
    template['width'] += t.getLang('lang_insert_image_delta_width', 0);
    template['height'] += t.getLang('lang_insert_image_delta_height', 0);
    template['inline'] = true;

    t.windowManager.open(template);
}

function learningform_callback(form, data) {
    learningform_filebrowser.callback(form, data);
};

EOF;


$smarty = smarty(array(), array(), array(), array(
    'sideblocks' => array(learningstage_sideblock($stage)),
    'tinymcesetup' => "ed.addCommand('mceImage', learningImageWindow);",
));
setpageicon($smarty, 'icon-graduation-cap');
$smarty->assign('INLINEJAVASCRIPT', $javascript);
$smarty->assign_by_ref('form', $form);
$smarty->assign('PAGEHEADING', hsc(TITLE));
$smarty->assign('SUBPAGENAV', PluginArtefactLearning::submenu_items($id));
$smarty->display('form.tpl');


function learningform_submit(Pieform $form, $values) {
    global $USER, $SESSION, $id, $stage;

    db_begin();
    $time = time();
    $artefact = new ArtefactTypeLearning($id);
    $artefact->set('mtime', $time);
    $artefact->set('atime', $time);
    $artefact->commit();

    $data = array(
        'artefact' => $id,
    );
    // If the content contains only the hint replace it with NULL
    $hint = '<p>'.get_string('hint', 'artefact.learning').'</p>';
    if ($values['content'] == $hint) {
        $values['content'] = null;
    }
    switch ($stage) {
        case 1:
            $data['priorknowledge'] = $values['content'];
            break;
        case 2:
            $data['goals'] = $values['content'];
            break;
        case 3:
            $data['strategies'] = $values['content'];
            break;
        case 4:
            $data['evidence'] = $values['content'];
            break;
        case 5:
            $data['evaluation'] = $values['content'];
            break;
    }
    update_record('artefact_learning_stages', (object)$data, 'artefact');

    // Attachments
    $old = $artefact->attachment_id_list_with_item($stage);
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
                    $artefact->attach($n, $stage);
                }
                catch (ArtefactNotFoundException $e) {}
            }
        }
    }

    db_commit();

    $result = array(
        'error'   => false,
        'message' => get_string('learningsavedsuccessfully', 'artefact.learning'),
        'goto'    => get_config('wwwroot') . 'artefact/learning/stage.php?stage=' . $stage . '&id=' . $id,
    );
    $form->reply(PIEFORM_OK, $result);

}


function add_attachment($attachmentid) {
    global $artefact, $stage;
    if ($artefact) {
        $artefact->attach($attachmentid, $stage);
    }
}

function delete_attachment($attachmentid) {
    global $artefact;
    if ($artefact) {
        $artefact->detach($attachmentid);
    }
}


/*
 * Learning stage sideblock that displays stage specific guiding questions
 * in a sidebar. Those guiding questions are the same ones that are also
 * displayed on contextual help.
 */
function learningstage_sideblock($stage=null) {
	$title = get_string('stage'.$stage.'title', 'artefact.learning');
	$content = get_string('stage'.$stage.'questions', 'artefact.learning');
    $sideblock = array(
        'name'   => 'learningstage',
        'id'     => 'sb-learningstage',
        'weight' => -20,
        'data' => array(
            'title' => $title,
		    'content' => $content,
		),
        'template' => 'artefact:learning:learningstage.tpl',
        'visible' => true,
    );
    return $sideblock;
}
