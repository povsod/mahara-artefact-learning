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

class PluginArtefactLearning extends PluginArtefact {

    public static function get_artefact_types() {
        return array(
            'learning',
        );
    }

    public static function get_block_types() {
        return array();
    }

    public static function get_plugin_name() {
        return 'learning';
    }

    public static function is_active() {
        return get_field('artefact_installed', 'active', 'name', 'learning');
    }

    public static function menu_items() {
        return array(
            'create/learning' => array(
                'path' => 'create/learning',
                'url'  => 'artefact/learning/index.php',
                'title' => get_string('Learning', 'artefact.learning'),
                'weight' => 100,
            ),
        );
    }

    /*
     *  Show different subpage navigation tabs if user is
     *  adding or editing his/her learning.
     *  @param integer $id   learning id (for new learning this defaults to 0)
     *  @return array $tabs  array containing all the tabs is returned
     */
    public static function submenu_items($id = 0) {
        if ($id) {
            $tabs = array(
                'learninginfo' => array(
                    'page'  => 'edit',
                    'url'   => 'artefact/learning/edit.php?id=' . $id,
                    'title' => get_string('learninginfo', 'artefact.learning'),
                ),
                'learningstage1' => array(
                    'page'  => 'stage',
                    'url'   => 'artefact/learning/stage.php?stage=1&id=' . $id,
                    'title' => get_string('learningstage1', 'artefact.learning'),
                ),
                'learningstage2' => array(
                    'page'  => 'stage',
                    'url'   => 'artefact/learning/stage.php?stage=2&id=' . $id,
                    'title' => get_string('learningstage2', 'artefact.learning'),
                ),
                'learningstage3' => array(
                    'page'  => 'stage',
                    'url'   => 'artefact/learning/stage.php?stage=3&id=' . $id,
                    'title' => get_string('learningstage3', 'artefact.learning'),
                ),
                'learningstage4' => array(
                    'page'  => 'stage',
                    'url'   => 'artefact/learning/stage.php?stage=4&id=' . $id,
                    'title' => get_string('learningstage4', 'artefact.learning'),
                ),
                'learningstage5' => array(
                    'page'  => 'stage',
                    'url'   => 'artefact/learning/stage.php?stage=5&id=' . $id,
                    'title' => get_string('learningstage5', 'artefact.learning'),
                ),
            );
        }
        else {
            $tabs = array(
                'learninginfo' => array(
                    'page'  => 'edit',
                    'url'   => 'artefact/learning/edit.php',
                    'title' => get_string('learninginfo', 'artefact.learning'),
                )
            );
        }
        if (defined('LEARNING_SUBPAGE') && isset($tabs[LEARNING_SUBPAGE])) {
            $tabs[LEARNING_SUBPAGE]['selected'] = true;
        }
        return $tabs;
    }

    public static function get_artefact_type_content_types() {
        return array(
            'learning' => array('text'),
        );
    }
}

class ArtefactTypeLearning extends ArtefactType {

    public function __construct($id = 0, $data = null) {
        parent::__construct($id, $data);
        // Get learning stages data
        if ($this->id) {
            $ldata = get_record('artefact_learning_stages', 'artefact', $this->id);
            if ($ldata = get_record('artefact_learning_stages', 'artefact', $this->id)) {
                foreach($ldata as $name => $value) {
                    $this->$name = $value;
                }
            }
            else {
                // This should never happen unless the user is playing around with artefact IDs in the location bar or similar
                throw new ArtefactNotFoundException(get_string('stagesdonotexist', 'artefact.learning'));
            }
        }
    }

    public function can_have_attachments() {
        return true;
    }

    public static function get_links($id) {
        return array(
            '_default' => get_config('wwwroot') . 'artefact/learning/learning.php?id=' . $id,
        );
    }

    public function delete() {
        if (empty($this->id)) {
            return;
        }

        db_begin();
        delete_records('artefact_learning_stages', 'artefact', $this->id);
        parent::delete();
        db_commit();
    }

    public static function bulk_delete($artefactids, $log=false) {
        if (empty($artefactids)) {
            return;
        }

        $idstr = join(',', array_map('intval', $artefactids));

        db_begin();
        delete_records_select('artefact_learning_stages', 'artefact IN (' . $idstr . ')');
        parent::bulk_delete($artefactids);
        db_commit();
    }

    public static function get_icon($options=null) {
        global $THEME;
        return $THEME->get_url('images/learning.gif', false, 'artefact/learning');
    }

    public static function is_singular() {
        return false;
    }


    /**
     * This function returns a list of the given user's learning.
     *
     * @param limit how many learnings to display per page
     * @param offset current page to display
     * @return array (count: integer, data: array)
     */
    public static function get_all_learnings($offset=0, $limit=100) {
        global $USER;

        ($data = get_records_sql_array("SELECT * FROM {artefact}
                                        WHERE owner = ? AND artefacttype = 'learning'
                                        ORDER BY title ASC", array($USER->get('id')), $offset, $limit))
                                        || ($data = array());
        foreach ($data as &$learning) {
            if (!isset($learning->tags)) {
                $learning->tags = ArtefactType::artefact_get_tags($learning->id);
            }
        }
        // Add Attachments count for general info and each stage
        foreach ($data as &$learning) {
            $count = get_field('artefact_attachment', 'COUNT(*)', 'artefact', $learning->id);
            $learning->count = $count;
        }

        $result = array(
            'count'  => count_records('artefact', 'owner', $USER->get('id'), 'artefacttype', 'learning'),
            'data'   => $data,
            'offset' => $offset,
            'limit'  => $limit,
        );

        return $result;
    }

    /**
     * Builds the learning list table
     *
     * @param learnings (reference)
     */
    public static function build_learning_list_html(&$learning) {
        $smarty = smarty_core();
        $smarty->assign_by_ref('learning', $learning);
        $learning['tablerows'] = $smarty->fetch('artefact:learning:learninglist.tpl');
        $pagination = build_pagination(array(
            'id' => 'learninglist_pagination',
            'class' => 'center',
            'url' => get_config('wwwroot') . 'artefact/learning/index.php',
            'jsonscript' => 'artefact/learning/learning.json.php',
            'datatable' => 'learninglist',
            'count' => $learning['count'],
            'limit' => $learning['limit'],
            'offset' => $learning['offset'],
            'firsttext' => '',
            'previoustext' => '',
            'nexttext' => '',
            'lasttext' => '',
            'numbersincludefirstlast' => false,
            'resultcounttextsingular' => get_string('learning', 'artefact.learning'),
            'resultcounttextplural' => get_string('learnings', 'artefact.learning'),
        ));
        $learning['pagination'] = $pagination['html'];
        $learning['pagination_js'] = $pagination['javascript'];
    }

    /**
     * This function returns all attachments of a single learning.
     *
     * @return array all attachments of a single learning
     */
    public static function get_stage_attachments($id, $viewid=0) {
        safe_require('artefact', 'file');
        $data = array();

        // General learning files
        $files = get_records_sql_assoc('SELECT a.id, a.artefacttype, aff.size, a.title, a.description 
            FROM {artefact_attachment} aa
            INNER JOIN {artefact} a ON a.id = aa.attachment
            INNER JOIN {artefact_file_files} aff ON a.id = aff.artefact
            WHERE aa.artefact = ? AND aa.item IS NULL
            ORDER BY a.title', array($id));
        if ($files) {
            foreach ($files as &$file) {
                $file->icon = call_static_method(generate_artefact_class_name($file->artefacttype), 'get_icon', array('id' => $file->id, 'viewid' => $viewid));
            }
        }
        $data['stage0'] = $files;

        // Individual learning stage files
        $stages = array(1, 2, 3, 4, 5);
        foreach ($stages as $stage) {
            $files = get_records_sql_assoc('SELECT a.id, a.artefacttype, aff.size, a.title, a.description 
                FROM {artefact_attachment} aa
                INNER JOIN {artefact} a ON a.id = aa.attachment
                INNER JOIN {artefact_file_files} aff ON a.id = aff.artefact
                WHERE aa.artefact = ? AND aa.item = ?
                ORDER BY a.title', array($id, $stage));
            if ($files) {
                foreach ($files as &$file) {
                    $file->icon = call_static_method(generate_artefact_class_name($file->artefacttype), 'get_icon', array('id' => $file->id, 'viewid' => $viewid));
                }
            }
            $data['stage'.$stage] = $files;
        }

        return $data;
    }

    public function render_self($options) {
    }
}
