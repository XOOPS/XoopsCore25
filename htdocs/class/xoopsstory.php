<?php
/**
 * XOOPS news story
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

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

$GLOBALS['xoopsLogger']->addDeprecated("'/class/xoopstory.php' is deprecated since XOOPS 2.5.4, please create your own class instead.");
include_once XOOPS_ROOT_PATH . '/class/xoopstopic.php';
include_once XOOPS_ROOT_PATH . '/kernel/user.php';

/**
 * Class XoopsStory
 */
class XoopsStory
{
    /**
     * @var string
     */
    public $table;
    /**
     * @var int
     */
    public $storyid;
    /**
     * @var int
     */
    public $topicid;
    /**
     * @var int
     */
    public $uid;
    /**
     * @var string
     */
    public $title;
    /**
     * @var string
     */
    public $hometext;
    /**
     * @var string
     */
    public $bodytext  = '';
    /**
     * @var int
     */
    public $counter;
    /**
     * @var int
     */
    public $created;
    /**
     * @var int
     */
    public $published;
    /**
     * @var int
     */
    public $expired;
    /**
     * @var string
     */
    public $hostname;
    /**
     * @var int
     */
    public $nohtml    = 0;
    /**
     * @var int
     */
    public $nosmiley  = 0;
    /**
     * @var int
     */
    public $ihome     = 0;
    /**
     * @var int
     */
    public $notifypub = 0;
    /**
     * @var string
     */
    public $type;
    /**
     * @var int
     */
    public $approved;
    /**
     * @var int
     */
    public $topicdisplay;
    /**
     * @var string
     */
    public $topicalign;
    /**
     * @var XoopsMySQLDatabase
     */
    public $db;
    /**
     * @var string
     */
    public $topicstable;
    /**
     * @var int
     */
    public $comments;

    /**
     * @param int|array $storyid
     */
    public function Story($storyid = -1)
    {
        /** @var XoopsMySQLDatabase $db */
        $db           = XoopsDatabaseFactory::getDatabaseConnection();
        $this->db     = $db;

        $this->table       = '';
        $this->topicstable = '';
        if (is_array($storyid)) {
            $this->makeStory($storyid);
        } elseif ($storyid !== -1) {
            $this->getStory((int)$storyid);
        }
    }

    /**
     * @param int $value
     */
    public function setStoryId($value)
    {
        $this->storyid = (int)$value;
    }

    /**
     * @param int $value
     */
    public function setTopicId($value)
    {
        $this->topicid = (int)$value;
    }

    /**
     * @param int $value
     */
    public function setUid($value)
    {
        $this->uid = (int)$value;
    }

    /**
     * @param string $value
     */
    public function setTitle($value)
    {
        $this->title = $value;
    }

    /**
     * @param string $value
     */
    public function setHometext($value)
    {
        $this->hometext = $value;
    }

    /**
     * @param string $value
     */
    public function setBodytext($value)
    {
        $this->bodytext = $value;
    }

    /**
     * @param int $value
     */
    public function setPublished($value)
    {
        $this->published = (int)$value;
    }

    /**
     * @param int $value
     */
    public function setExpired($value)
    {
        $this->expired = (int)$value;
    }

    /**
     * @param string $value
     */
    public function setHostname($value)
    {
        $this->hostname = $value;
    }

    /**
     * @param int $value
     */
    public function setNohtml($value = 0)
    {
        $this->nohtml = $value;
    }

    /**
     * @param int $value
     */
    public function setNosmiley($value = 0)
    {
        $this->nosmiley = $value;
    }

    /**
     * @param int $value
     */
    public function setIhome($value)
    {
        $this->ihome = $value;
    }

    /**
     * @param int $value
     */
    public function setNotifyPub($value)
    {
        $this->notifypub = $value;
    }

    /**
     * @param string $value
     */
    public function setType($value)
    {
        $this->type = $value;
    }

    /**
     * @param int $value
     */
    public function setApproved($value)
    {
        $this->approved = (int)$value;
    }

    /**
     * @param int $value
     */
    public function setTopicdisplay($value)
    {
        $this->topicdisplay = $value;
    }

    /**
     * @param string $value
     */
    public function setTopicalign($value)
    {
        $this->topicalign = $value;
    }

    /**
     * @param int $value
     */
    public function setComments($value)
    {
        $this->comments = (int)$value;
    }

    /**
     * @param bool $approved
     *
     * @return bool|int
     */
    public function store($approved = false)
    {
        //$newpost = 0;
        $myts     = MyTextSanitizer::getInstance();
        $title    = $myts->censorString($this->title);
        $hometext = $myts->censorString($this->hometext);
        $bodytext = $myts->censorString($this->bodytext);
        $title    = $myts->addSlashes($title);
        $hometext = $myts->addSlashes($hometext);
        $bodytext = $myts->addSlashes($bodytext);
//        if (!isset($this->nohtml) || $this->nohtml != 1) {
         if (!($this->nohtml) || $this->nohtml !== 1) {
            $this->nohtml = 0;
        }
        if (!($this->nosmiley) || $this->nosmiley !== 1) {
            $this->nosmiley = 0;
        }
        if (!($this->notifypub) || $this->notifypub !== 1) {
            $this->notifypub = 0;
        }
        if (!($this->topicdisplay) || $this->topicdisplay !== 0) {
            $this->topicdisplay = 1;
        }
        $expired = !empty($this->expired) ? $this->expired : 0;
        if (!($this->storyid)) {
            //$newpost = 1;

            /** @property XoopsMySQLDatabase $db */
            $newstoryid = $this->db->genId($this->table . '_storyid_seq');
            $created    = time();
            $published  = $this->approved ? $this->published : 0;

            $sql = sprintf("INSERT INTO %s (storyid, uid, title, created, published, expired, hostname, nohtml, nosmiley, hometext, bodytext, counter, topicid, ihome, notifypub, story_type, topicdisplay, topicalign, comments) VALUES (%u, %u, '%s', %u, %u, %u, '%s', %u, %u, '%s', '%s', %u, %u, %u, %u, '%s', %u, '%s', %u)", $this->table, $newstoryid, $this->uid, $title, $created, $published, $expired, $this->hostname, $this->nohtml, $this->nosmiley, $hometext, $bodytext, 0, $this->topicid, $this->ihome, $this->notifypub, $this->type, $this->topicdisplay, $this->topicalign, $this->comments);
        } else {
            if ($this->approved) {
                $sql = sprintf("UPDATE %s SET title = '%s', published = %u, expired = %u, nohtml = %u, nosmiley = %u, hometext = '%s', bodytext = '%s', topicid = %u, ihome = %u, topicdisplay = %u, topicalign = '%s', comments = %u WHERE storyid = %u", $this->table, $title, $this->published, $expired, $this->nohtml, $this->nosmiley, $hometext, $bodytext, $this->topicid, $this->ihome, $this->topicdisplay, $this->topicalign, $this->comments, $this->storyid);
            } else {
                $sql = sprintf("UPDATE %s SET title = '%s', expired = %u, nohtml = %u, nosmiley = %u, hometext = '%s', bodytext = '%s', topicid = %u, ihome = %u, topicdisplay = %u, topicalign = '%s', comments = %u WHERE storyid = %u", $this->table, $title, $expired, $this->nohtml, $this->nosmiley, $hometext, $bodytext, $this->topicid, $this->ihome, $this->topicdisplay, $this->topicalign, $this->comments, $this->storyid);
            }
            $newstoryid = $this->storyid;
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        if (empty($newstoryid)) {
            $newstoryid    = $this->db->getInsertId();
            $this->storyid = (int)$newstoryid;
        }

        return $newstoryid;
    }

    /**
     * @param int $storyid
     */
    public function getStory($storyid)
    {
        $storyid = (int)$storyid;
        $sql     = 'SELECT * FROM ' . $this->table . ' WHERE storyid=' . $storyid . '';
        $array   = $this->db->fetchArray($this->db->query($sql));
        $this->makeStory($array);
    }

    /**
     * @param array $array
     * @return void
     */
    public function makeStory($array)
    {
        foreach ($array as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $sql = sprintf('DELETE FROM %s WHERE storyid = %u', $this->table, $this->storyid);
        if (!$result = $this->db->query($sql)) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function updateCounter()
    {
        $sql = sprintf('UPDATE %s SET counter = counter+1 WHERE storyid = %u', $this->table, $this->storyid);
        if (!$result = $this->db->queryF($sql)) {
            return false;
        }

        return true;
    }

    /**
     * @param int $total
     *
     * @return bool
     */
    public function updateComments($total)
    {
        $sql = sprintf('UPDATE %s SET comments = %u WHERE storyid = %u', $this->table, $total, $this->storyid);
        if (!$result = $this->db->queryF($sql)) {
            return false;
        }

        return true;
    }

    /**
     * @return int
     */
    public function topicid()
    {
        return $this->topicid;
    }

    /**
     * @return XoopsTopic
     */
    public function topic()
    {
        return new XoopsTopic($this->topicstable, $this->topicid);
    }

    /**
     * @return int
     */
    public function uid()
    {
        return $this->uid;
    }

    /**
     * @return string
     */
    public function uname()
    {
        return XoopsUser::getUnameFromId($this->uid);
    }

    /**
     * @param string $format
     *
     * @return string
     */
    public function title($format = 'Show')
    {
        $title = '';
        $myts   = MyTextSanitizer::getInstance();
        $smiley = 1;
        if ($this->nosmiley()) {
            $smiley = 0;
        }
        switch ($format) {
            case 'Show':
            case 'Edit':
                $title = $myts->htmlSpecialChars($this->title);
                break;
            case 'Preview':
            case 'InForm':
                $title = $myts->htmlSpecialChars($myts->stripSlashesGPC($this->title));
                break;
        }

        return $title;
    }

    /**
     * @param string $format
     *
     * @return string
     */
    public function hometext($format = 'Show')
    {
        $hometext = '';
        $myts   = MyTextSanitizer::getInstance();
        $html   = 1;
        $smiley = 1;
        $xcodes = 1;
        if ($this->nohtml()) {
            $html = 0;
        }
        if ($this->nosmiley()) {
            $smiley = 0;
        }
        switch ($format) {
            case 'Show':
                $hometext = $myts->displayTarea($this->hometext, $html, $smiley, $xcodes);
                break;
            case 'Edit':
                $hometext = htmlspecialchars($this->hometext, ENT_QUOTES);
                break;
            case 'Preview':
                $hometext = $myts->previewTarea($this->hometext, $html, $smiley, $xcodes);
                break;
            case 'InForm':
                $hometext = htmlspecialchars($myts->stripSlashesGPC($this->hometext), ENT_QUOTES);
                break;
        }

        return $hometext;
    }

    /**
     * @param string $format
     *
     * @return string
     */
    public function bodytext($format = 'Show')
    {
        $bodytext = '';
        $myts   = MyTextSanitizer::getInstance();
        $html   = 1;
        $smiley = 1;
        $xcodes = 1;
        if ($this->nohtml()) {
            $html = 0;
        }
        if ($this->nosmiley()) {
            $smiley = 0;
        }
        switch ($format) {
            case 'Show':
                $bodytext = $myts->displayTarea($this->bodytext, $html, $smiley, $xcodes);
                break;
            case 'Edit':
                $bodytext = htmlspecialchars($this->bodytext, ENT_QUOTES);
                break;
            case 'Preview':
                $bodytext = $myts->previewTarea($this->bodytext, $html, $smiley, $xcodes);
                break;
            case 'InForm':
                $bodytext = htmlspecialchars($myts->stripSlashesGPC($this->bodytext), ENT_QUOTES);
                break;
        }

        return $bodytext;
    }

    /**
     * @return int
     */
    public function counter()
    {
        return $this->counter;
    }

    /**
     * @return int
     */
    public function created()
    {
        return $this->created;
    }

    /**
     * @return int
     */
    public function published()
    {
        return $this->published;
    }

    /**
     * @return int
     */
    public function expired()
    {
        return $this->expired;
    }

    /**
     * @return string
     */
    public function hostname()
    {
        return $this->hostname;
    }

    public function storyid()
    {
        return $this->storyid;
    }

    /**
     * @return int
     */
    public function nohtml()
    {
        return $this->nohtml;
    }

    /**
     * @return int
     */
    public function nosmiley()
    {
        return $this->nosmiley;
    }

    /**
     * @return int
     */
    public function notifypub()
    {
        return $this->notifypub;
    }

    public function type()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function ihome()
    {
        return $this->ihome;
    }

    /**
     * @return int
     */
    public function topicdisplay()
    {
        return $this->topicdisplay;
    }

    /**
     * @param bool $astext
     *
     * @return string
     */
    public function topicalign($astext = true)
    {
        $ret = 'left';
        if ($astext) {
            if ($this->topicalign === 'R') {
                $ret = 'right';
            }

            return $ret;
        }

        return $this->topicalign;
    }

    /**
     * @return int
     */
    public function comments()
    {
        return $this->comments;
    }
}
