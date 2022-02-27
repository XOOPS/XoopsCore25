<?php
/**
 * XOOPS news topic
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 * @deprecated
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

$GLOBALS['xoopsLogger']->addDeprecated("'/class/xoopstopic.php' is deprecated since XOOPS 2.5.4, please create your own class instead.");

include_once XOOPS_ROOT_PATH . '/class/xoopstree.php';

/**
 * Class XoopsTopic
 */
class XoopsTopic
{
    /**
     * @var \XoopsMySQLDatabase
     */
    public $db;
    /**
     * @var string
     */
    public $table;
    /**
     * @var int|string|null
     */
    public $topic_id;
    /**
     * @var int|null
     */
    public $topic_pid;
    /**
     * @var string
     */
    public $topic_title;
    /**
     * @var string
     */
    public $topic_imgurl;
    /**
     * @var string
     */
    public $prefix; // only used in topic tree
    /**
     * @var bool
     */
    public $use_permission = false;
    /**
     * @var int
     */
    public $mid; // module id used for setting permission

    /**
     * @param string    $table
     * @param int|array $topicid
     */
    public function __construct($table, $topicid = 0)
    {
        /** @var XoopsMySQLDatabase $db */
        $db           = XoopsDatabaseFactory::getDatabaseConnection();
        $this->db     = $db;
        $this->table = $table;
        if (is_array($topicid)) {
            $this->makeTopic($topicid);
        } elseif ($topicid != 0) {
            $this->getTopic((int)$topicid);
        } else {
            $this->topic_id = $topicid;
        }
    }

    /**
     * @param string $value
     */
    public function setTopicTitle($value)
    {
        $this->topic_title = $value;
    }

    /**
     * @param string $value
     */
    public function setTopicImgurl($value)
    {
        $this->topic_imgurl = $value;
    }

    /**
     * @param int $value
     */
    public function setTopicPid($value)
    {
        $this->topic_pid = $value;
    }

    /**
     * @param int $topicid
     */
    public function getTopic($topicid)
    {
        $topicid = (int)$topicid;
        /** @property XoopsMySQLDatabase $db */
        $sql     = 'SELECT * FROM ' . $this->table . ' WHERE topic_id=' . $topicid . '';
        $array   = $this->db->fetchArray($this->db->query($sql));
        $this->makeTopic($array);
    }

    /**
     * @param array $array
     *
     * @return void
     */
    public function makeTopic($array)
    {
        foreach ($array as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @param int $mid
     * @return void
     */
    public function usePermission($mid)
    {
        $this->mid            = $mid;
        $this->use_permission = true;
    }

    /**
     * @return bool
     */
    public function store()
    {
        /** @property XoopsMySQLDatabase $db */
        $myts   = MyTextSanitizer::getInstance();
        $title  = '';
        $imgurl = '';
        if (($this->topic_title) && $this->topic_title != '') {
            $title = $myts->addSlashes($this->topic_title);
        }
        if (($this->topic_imgurl) && $this->topic_imgurl != '') {
            $imgurl = $myts->addSlashes($this->topic_imgurl);
        }
        if (!isset($this->topic_pid) || !is_numeric($this->topic_pid)) {
            $this->topic_pid = 0;
        }
        if (empty($this->topic_id)) {
            $this->topic_id = $this->db->genId($this->table . '_topic_id_seq');
            $sql            = sprintf("INSERT INTO %s (topic_id, topic_pid, topic_imgurl, topic_title) VALUES (%u, %u, '%s', '%s')", $this->table, $this->topic_id, $this->topic_pid, $imgurl, $title);
        } else {
            $sql = sprintf("UPDATE %s SET topic_pid = %u, topic_imgurl = '%s', topic_title = '%s' WHERE topic_id = %u", $this->table, $this->topic_pid, $imgurl, $title, $this->topic_id);
        }
        if (!$result = $this->db->query($sql)) {
            ErrorHandler::show('0022');
        }
        if ($this->use_permission == true) {
            if (empty($this->topic_id)) {
                $this->topic_id = $this->db->getInsertId();
            }
            $xt            = new XoopsTree($this->table, 'topic_id', 'topic_pid');
            $parent_topics = $xt->getAllParentId($this->topic_id);
            if (!empty($this->m_groups) && is_array($this->m_groups)) {
                foreach ($this->m_groups as $m_g) {
                    $moderate_topics = XoopsPerms::getPermitted($this->mid, 'ModInTopic', $m_g);
                    $add             = true;
                    // only grant this permission when the group has this permission in all parent topics of the created topic
                    foreach ($parent_topics as $p_topic) {
                        if (!in_array($p_topic, $moderate_topics)) {
                            $add = false;
                            continue;
                        }
                    }
                    if ($add == true) {
                        $xp = new XoopsPerms();
                        $xp->setModuleId($this->mid);
                        $xp->setName('ModInTopic');
                        $xp->setItemId($this->topic_id);
                        $xp->store();
                        $xp->addGroup($m_g);
                    }
                }
            }
            if (!empty($this->s_groups) && is_array($this->s_groups)) {
                foreach ($s_groups as $s_g) {
                    $submit_topics = XoopsPerms::getPermitted($this->mid, 'SubmitInTopic', $s_g);
                    $add           = true;
                    foreach ($parent_topics as $p_topic) {
                        if (!in_array($p_topic, $submit_topics)) {
                            $add = false;
                            continue;
                        }
                    }
                    if ($add == true) {
                        $xp = new XoopsPerms();
                        $xp->setModuleId($this->mid);
                        $xp->setName('SubmitInTopic');
                        $xp->setItemId($this->topic_id);
                        $xp->store();
                        $xp->addGroup($s_g);
                    }
                }
            }
            if (!empty($this->r_groups) && is_array($this->r_groups)) {
                foreach ($r_groups as $r_g) {
                    $read_topics = XoopsPerms::getPermitted($this->mid, 'ReadInTopic', $r_g);
                    $add         = true;
                    foreach ($parent_topics as $p_topic) {
                        if (!in_array($p_topic, $read_topics)) {
                            $add = false;
                            continue;
                        }
                    }
                    if ($add == true) {
                        $xp = new XoopsPerms();
                        $xp->setModuleId($this->mid);
                        $xp->setName('ReadInTopic');
                        $xp->setItemId($this->topic_id);
                        $xp->store();
                        $xp->addGroup($r_g);
                    }
                }
            }
        }

        return true;
    }

    public function delete()
    {
        $sql = sprintf('DELETE FROM %s WHERE topic_id = %u', $this->table, $this->topic_id);
        $this->db->query($sql);
    }

    /**
     * @return int
     */
    public function topic_id()
    {
        return $this->topic_id;
    }

    /**
     * @return int|null
     */
    public function topic_pid()
    {
        return $this->topic_pid;
    }

    /**
     * @param string $format
     *
     * @return string
     */
    public function topic_title($format = 'S')
    {
        $myts = MyTextSanitizer::getInstance();
        switch ($format) {
            case 'S':
            case 'E':
                $title = $myts->htmlSpecialChars($this->topic_title);
                break;
            case 'P':
            case 'F':
                $title = $myts->htmlSpecialChars($myts->stripSlashesGPC($this->topic_title));
                break;
        }

        return $title;
    }

    /**
     * @param string $format
     *
     * @return string
     */
    public function topic_imgurl($format = 'S')
    {
        $myts = MyTextSanitizer::getInstance();
        switch ($format) {
            case 'S':
            case 'E':
                $imgurl = $myts->htmlSpecialChars($this->topic_imgurl);
                break;
            case 'P':
            case 'F':
                $imgurl = $myts->htmlSpecialChars($myts->stripSlashesGPC($this->topic_imgurl));
                break;
        }

        return $imgurl;
    }

    /**
     * @return string|null
     */
    public function prefix()
    {
        if (isset($this->prefix)) {
            return $this->prefix;
        }
        return null;
    }

    /**
     * @return array
     */
    public function getFirstChildTopics()
    {
        $ret       = array();
        $xt        = new XoopsTree($this->table, 'topic_id', 'topic_pid');
        $topic_arr = $xt->getFirstChild($this->topic_id, 'topic_title');
        if (is_array($topic_arr) && count($topic_arr)) {
            foreach ($topic_arr as $topic) {
                $ret[] = new XoopsTopic($this->table, $topic);
            }
        }

        return $ret;
    }

    /**
     * @return array
     */
    public function getAllChildTopics()
    {
        $ret       = array();
        $xt        = new XoopsTree($this->table, 'topic_id', 'topic_pid');
        $topic_arr = $xt->getAllChild($this->topic_id, 'topic_title');
        if (is_array($topic_arr) && count($topic_arr)) {
            foreach ($topic_arr as $topic) {
                $ret[] = new XoopsTopic($this->table, $topic);
            }
        }

        return $ret;
    }

    /**
     * @return array
     */
    public function getChildTopicsTreeArray()
    {
        $ret       = array();
        $xt        = new XoopsTree($this->table, 'topic_id', 'topic_pid');
        $topic_arr = $xt->getChildTreeArray($this->topic_id, 'topic_title');
        if (is_array($topic_arr) && count($topic_arr)) {
            foreach ($topic_arr as $topic) {
                $ret[] = new XoopsTopic($this->table, $topic);
            }
        }

        return $ret;
    }

    /**
     * @param int    $none
     * @param int    $seltopic
     * @param string $selname
     * @param string $onchange
     */
    public function makeTopicSelBox($none = 0, $seltopic = -1, $selname = '', $onchange = '')
    {
        $xt = new XoopsTree($this->table, 'topic_id', 'topic_pid');
        if ($seltopic != -1) {
            $xt->makeMySelBox('topic_title', 'topic_title', $seltopic, $none, $selname, $onchange);
        } elseif (!empty($this->topic_id)) {
            $xt->makeMySelBox('topic_title', 'topic_title', $this->topic_id, $none, $selname, $onchange);
        } else {
            $xt->makeMySelBox('topic_title', 'topic_title', 0, $none, $selname, $onchange);
        }
    }

    //generates nicely formatted linked path from the root id to a given id
    /**
     * @param string $funcURL
     *
     * @return string
     */
    public function getNiceTopicPathFromId($funcURL)
    {
        $xt  = new XoopsTree($this->table, 'topic_id', 'topic_pid');
        $ret = $xt->getNicePathFromId($this->topic_id, 'topic_title', $funcURL);

        return $ret;
    }

    /**
     * @return array
     */
    public function getAllChildTopicsId()
    {
        $xt  = new XoopsTree($this->table, 'topic_id', 'topic_pid');
        $ret = $xt->getAllChildId($this->topic_id, 'topic_title');

        return $ret;
    }

    /**
     * @return array
     */
    public function getTopicsList()
    {
        $result = $this->db->query('SELECT topic_id, topic_pid, topic_title FROM ' . $this->table);
        $ret    = array();
        $myts   = MyTextSanitizer::getInstance();
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[$myrow['topic_id']] = array(
                'title' => $myts->htmlSpecialChars($myrow['topic_title']),
                'pid'   => $myrow['topic_pid'],
            );
        }

        return $ret;
    }

    /**
     * @param int $pid
     * @param string $title
     *
     * @return bool
     */
    public function topicExists($pid, $title)
    {
        $sql = 'SELECT COUNT(*) from ' . $this->table . ' WHERE topic_pid = ' . (int)$pid . " AND topic_title = '" . trim($title) . "'";
        $rs  = $this->db->query($sql);
        list($count) = $this->db->fetchRow($rs);
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }
}
