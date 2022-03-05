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
define('SECTION_PAGE', 'index');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
safe_require('artefact', 'learning');

define('TITLE', get_string('mylearning','artefact.learning'));

if (!PluginArtefactLearning::is_active()) {
    throw new AccessDeniedException(get_string('plugindisableduser', 'mahara', get_string('plans','artefact.plans')));
}

// offset and limit for pagination
$offset = param_integer('offset', 0);
$limit  = param_integer('limit', 100);

$learnings = ArtefactTypeLearning::get_all_learnings($offset, $limit);
ArtefactTypeLearning::build_learning_list_html($learnings);

$js = <<< EOF
addLoadEvent(function () {
    {$learnings['pagination_js']}
});
EOF;

$smarty = smarty(array('paginator'));
setpageicon($smarty, 'icon-graduation-cap');
$smarty->assign('learning', $learnings);
$smarty->assign('strnolearningaddone',
    get_string('nolearningaddone', 'artefact.learning',
    '<a href="' . get_config('wwwroot') . 'artefact/learning/edit.php">', '</a>'));
$smarty->assign('PAGEHEADING', hsc(TITLE));
$smarty->assign('INLINEJAVASCRIPT', $js);
$smarty->display('artefact:learning:index.tpl');
