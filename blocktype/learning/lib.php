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

defined('INTERNAL') || die();

class PluginBlocktypeLearning extends PluginBlocktype {

    public static function get_title() {
        return get_string('title', 'blocktype.learning/learning');
    }

    public static function get_description() {
        return get_string('description', 'blocktype.learning/learning');
    }

    public static function get_css_icon($blocktypename) {
        return 'graduation-cap';
    }

    public static function get_categories() {
        return array('general' => 99000);
    }

     /**
     * Optional method. If exists, allows this class to decide the title for
     * all blockinstances of this type
     */
    public static function get_instance_title(BlockInstance $bi) {
        $configdata = $bi->get('configdata');

        if (!empty($configdata['artefactid'])) {
            return $bi->get_artefact_instance($configdata['artefactid'])->get('title');
        }
        return '';
    }

    public static function render_instance(BlockInstance $instance, $editing=false, $versioning=false) {
        require_once(get_config('docroot') . 'artefact/lib.php');
        safe_require('artefact','learning');

        $configdata = $instance->get('configdata');

        if (!empty($configdata['artefactid'])) {
            $configdata['viewid'] = $instance->get('view');
            $learning = $instance->get_artefact_instance($configdata['artefactid']);
            $learning->files = ArtefactTypeLearning::get_stage_attachments($configdata['artefactid'], $configdata['viewid']);

            $smarty = smarty_core();
            $smarty->assign('options', $configdata);
            $smarty->assign('learning', $learning);
            $smarty->assign('id', $learning->get('id'));
            $smarty->assign('description', $learning->get('description'));
            $smarty->assign('tags', $learning->get('tags'));
            $smarty->assign('owner', $learning->get('owner'));
            if ($learning->get('allowcomments')) {
                safe_require('artefact', 'comment');
                $empty = array();
                $ids = array($learning->get('id'));
                $commentcount = ArtefactTypeComment::count_comments($empty, $ids);
                $smarty->assign('commentcount', $commentcount ? $commentcount[$learning->get('id')]->comments : 0);
            }
            $smarty->assign('PAGEHEADING', $learning->get('title'));
            return $smarty->fetch('blocktype:learning:content.tpl');
        }
    }

    // My Learning blocktype only has 'title' option so next two functions return as normal
    public static function has_instance_config(BlockInstance $instance) {
        return true;
    }

    public static function instance_config_form(BlockInstance $instance) {
        $instance->set('artefactplugin', 'learning');
        $configdata = $instance->get('configdata');

        $form = array();

        // Which resume field does the user want
        $form[] = self::artefactchooser_element((isset($configdata['artefactid'])) ? $configdata['artefactid'] : null);

        return $form;
    }

    public static function artefactchooser_element($default=null) {
        safe_require('artefact', 'learning');
        return array(
            'name'  => 'artefactid',
            'type'  => 'artefactchooser',
            'title' => get_string('learningtoshow', 'blocktype.learning/learning'),
            'defaultvalue' => $default,
            'blocktype' => 'learning',
            'selectone' => true,
            'search'    => false,
            'artefacttypes' => array('learning'),
            'template'  => 'artefact:learning:artefactchooser-element.tpl',
        );
    }

    public static function allowed_in_view(View $view) {
        return $view->get('owner') != null;
    }
}
