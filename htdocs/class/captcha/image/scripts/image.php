<?php
/**
 * CAPTCHA class For XOOPS
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @package             class
 * @subpackage          CAPTCHA
 */

include __DIR__  . '/../../../../mainfile.php';

error_reporting(0);
$xoopsLogger->activated = false;

/**
 * Class XoopsCaptchaImageHandler
 */
class XoopsCaptchaImageHandler
{
    public $config  = [];
    public $code;
    public $mode    = 'gd';
    public $invalid = false;

    public $oImage;
    public $font;
    public $spacing;
    public $width;
    public $height;

    public $captchaHandler;

    /**
     *
     */
    public function __construct()
    {
        xoops_load('XoopsCaptcha');
        $this->captchaHandler = XoopsCaptcha::getInstance();
        $this->config          = $this->captchaHandler->loadConfig('image');
    }

    public function loadImage()
    {
        $this->generateCode();
        $this->createImage();
    }

    /**
     * Create Code
     */
    public function generateCode()
    {
        if ($this->invalid) {
            return false;
        }

        if ($this->mode === 'bmp') {
            $this->config['num_chars'] = 4;
            $this->code                = mt_rand(10 ** ($this->config['num_chars'] - 1), (int)str_pad('9', $this->config['num_chars'], '9'));
        } else {
            $raw_code = md5(uniqid(mt_rand(), true));
            if (!empty($this->config['skip_characters'])) {
                $valid_code = str_replace($this->config['skip_characters'], '', $raw_code);
                $this->code = substr($valid_code, 0, $this->config['num_chars']);
            } else {
                $this->code = substr($raw_code, 0, $this->config['num_chars']);
            }
            if (!$this->config['casesensitive']) {
                $this->code = strtoupper($this->code);
            }
        }
        $this->captchaHandler->setCode($this->code);

        return true;
    }

    /**
     * @return string|bool
     */
    public function createImage()
    {
        if ($this->invalid) {
            header('Content-type: image/gif');
            readfile(XOOPS_ROOT_PATH . '/images/subject/icon2.gif');

            return null;
        }

        if ($this->mode === 'bmp') {
            return $this->createImageBmp();
        } else {
            return $this->createImageGd();
        }
    }

    /**
     * @param string $name
     * @param string $extension
     *
     * @return array|mixed
     */
    public function getList($name, $extension = '')
    {
        xoops_load('XoopsCache');
        if ($items = XoopsCache::read("captcha_captcha_{$name}")) {
            return $items;
        }

        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
        $file_path = XOOPS_ROOT_PATH . "/class/captcha/image/{$name}";
        $files     = XoopsLists::getFileListAsArray($file_path);
        $items = [];
        foreach ($files as $item) {
            if (empty($extension) || preg_match("/(\.{$extension})$/i", $item)) {
                $items[] = $item;
            }
        }
        XoopsCache::write("captcha_captcha_{$name}", $items);

        return $items;
    }

    /**
     *  Create CAPTCHA image with GD
     *  Originated by DuGris' SecurityImage
     *  --------------------------------------------------------------------------- //
     *  Class : SecurityImage 1.5                                                    //
     *  Author: DuGris aka L. Jen <http://www.dugris.info>                            //
     *  Email : DuGris@wanadoo.fr                                                    //
     *  Licence: GNU                                                                    //
     *  Project: The XOOPS Project                                                    //
     *  --------------------------------------------------------------------------- //
     */
    public function createImageGd()
    {
        $this->loadFont();
        $this->setImageSize();

        $this->oImage = imagecreatetruecolor((int)$this->width, (int)$this->height);
        $background   = imagecolorallocate($this->oImage, 255, 255, 255);
        imagefilledrectangle($this->oImage, 0, 0, (int)$this->width, (int)$this->height, $background);

        switch ($this->config['background_type']) {
            default:
            case 0:
                $this->drawBars();
                break;

            case 1:
                $this->drawCircles();
                break;

            case 2:
                $this->drawLines();
                break;

            case 3:
                $this->drawRectangles();
                break;

            case 4:
                $this->drawEllipses();
                break;

            case 5:
                $this->drawPolygons();
                break;

            case 100:
                $this->createFromFile();
                break;
        }
        $this->drawBorder();
        $this->drawCode();

        header('Content-type: image/jpeg');
        if (!imagejpeg($this->oImage)) {
            // Log or handle the error as you see fit
            return false;
        }

        if (!imagedestroy($this->oImage)) {
            // Log or handle the error as you see fit
            return false;
        }

        return true;
    }

    public function loadFont()
    {
        $fonts      = $this->getList('fonts', 'ttf');
        $this->font = XOOPS_ROOT_PATH . '/class/captcha/image/fonts/' . $fonts[array_rand($fonts)];
    }

    public function setImageSize()
    {
        $MaxCharWidth  = 0;
        $MaxCharHeight = 0;
        $oImage        = imagecreatetruecolor(100, 100);
        $text_color    = imagecolorallocate($oImage, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));
        $FontSize      = $this->config['fontsize_max'];
        for ($Angle = -30; $Angle <= 30; ++$Angle) {
            for ($i = 65; $i <= 90; ++$i) {
                $CharDetails   = imageftbbox($FontSize, $Angle, $this->font, chr($i), []);
                $_MaxCharWidth = abs($CharDetails[0] + $CharDetails[2]);
                if ($_MaxCharWidth > $MaxCharWidth) {
                    $MaxCharWidth = $_MaxCharWidth;
                }
                $_MaxCharHeight = abs($CharDetails[1] + $CharDetails[5]);
                if ($_MaxCharHeight > $MaxCharHeight) {
                    $MaxCharHeight = $_MaxCharHeight;
                }
            }
        }
        imagedestroy($oImage);

        $this->height  = $MaxCharHeight + 2;
        $this->spacing = (int)(($this->config['num_chars'] * $MaxCharWidth) / $this->config['num_chars']);
        $this->width   = ($this->config['num_chars'] * $MaxCharWidth) + ($this->spacing / 2);
    }

    /**
     * Return random background
     *
     * @return string|null
     */
    public function loadBackground()
    {
        $RandBackground = null;
        if ($backgrounds = $this->getList('backgrounds', '(gif|jpg|png)')) {
            $RandBackground = XOOPS_ROOT_PATH . '/class/captcha/image/backgrounds/' . $backgrounds[array_rand($backgrounds)];
        }

        return $RandBackground;
    }

    /**
     * Draw Image background
     */
    public function createFromFile()
    {
        if ($RandImage = $this->loadBackground()) {
            $ImageType = @getimagesize($RandImage);
            if (isset($ImageType[2])) {
                switch ($ImageType[2]) {
                    case 1:
                        $BackgroundImage = imagecreatefromgif($RandImage);
                        break;

                    case 2:
                        $BackgroundImage = imagecreatefromjpeg($RandImage);
                        break;

                    case 3:
                        $BackgroundImage = imagecreatefrompng($RandImage);
                        break;
                }
            }
        }
        if (!empty($BackgroundImage)) {
            imagecopyresized($this->oImage, $BackgroundImage, 0, 0, 0, 0, imagesx($this->oImage), imagesy($this->oImage), imagesx($BackgroundImage), imagesy($BackgroundImage));
            imagedestroy($BackgroundImage);
        } else {
            $this->drawBars();
        }
    }

    /**
     * Draw Code
     */
    public function drawCode()
    {
        for ($i = 0; $i < $this->config['num_chars']; ++$i) {
            // select random greyscale colour
            $text_color = imagecolorallocate($this->oImage, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));

            // write text to image
            $Angle = mt_rand(10, 30);
            if ($i % 2) {
                $Angle = mt_rand(-30, -10);
            }

            // select random font size
            $FontSize = mt_rand($this->config['fontsize_min'], $this->config['fontsize_max']);

            $CharDetails = imageftbbox($FontSize, $Angle, $this->font, $this->code[$i], []);
            $CharHeight  = abs($CharDetails[1] + $CharDetails[5]);

            // calculate character starting coordinates
            $posX = ($this->spacing / 2) + ($i * $this->spacing);
            $posY = 2 + ($this->height / 2) + ($CharHeight / 4);

            imagefttext($this->oImage, $FontSize, $Angle, (int)$posX, (int)$posY, $text_color, $this->font, $this->code[$i], []);
        }
    }

    /**
     * Draw Border
     */
    public function drawBorder()
    {
        $rgb          = mt_rand(50, 150);
        $border_color = imagecolorallocate($this->oImage, $rgb, $rgb, $rgb);
        imagerectangle($this->oImage, 0, 0, $this->width - 1, $this->height - 1, $border_color);
    }

    /**
     * Draw Circles background
     */
    public function drawCircles()
    {
        for ($i = 1; $i <= $this->config['background_num']; ++$i) {
            $randomcolor = imagecolorallocate($this->oImage, mt_rand(190, 255), mt_rand(190, 255), mt_rand(190, 255));
            imagefilledellipse($this->oImage, mt_rand(0, $this->width - 10), mt_rand(0, $this->height - 3), mt_rand(10, 20), mt_rand(20, 30), $randomcolor);
        }
    }

    /**
     * Draw Lines background
     */
    public function drawLines()
    {
        for ($i = 0; $i < $this->config['background_num']; ++$i) {
            $randomcolor = imagecolorallocate($this->oImage, mt_rand(190, 255), mt_rand(190, 255), mt_rand(190, 255));
            imageline($this->oImage, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $randomcolor);
        }
    }

    /**
     * Draw Rectangles background
     */
    public function drawRectangles()
    {
        for ($i = 1; $i <= $this->config['background_num']; ++$i) {
            $randomcolor = imagecolorallocate($this->oImage, mt_rand(190, 255), mt_rand(190, 255), mt_rand(190, 255));
            imagefilledrectangle($this->oImage, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $randomcolor);
        }
    }

    /**
     * Draw Bars background
     */
    public function drawBars()
    {
        for ($i = 0; $i <= $this->height;) {
            $randomcolor = imagecolorallocate($this->oImage, mt_rand(190, 255), mt_rand(190, 255), mt_rand(190, 255));
            imageline($this->oImage, 0, (int)$i, (int)$this->width, (int)$i, (int)$randomcolor);
            $i += 2.5;
        }
        for ($i = 0; $i <= $this->width;) {
            $randomcolor = imagecolorallocate($this->oImage, mt_rand(190, 255), mt_rand(190, 255), mt_rand(190, 255));
            imageline($this->oImage, (int)$i, 0, (int)$i, (int)$this->height, (int)$randomcolor);
            $i += 2.5;
        }
    }

    /**
     * Draw Ellipses background
     */
    public function drawEllipses()
    {
        for ($i = 1; $i <= $this->config['background_num']; ++$i) {
            $randomcolor = imagecolorallocate($this->oImage, mt_rand(190, 255), mt_rand(190, 255), mt_rand(190, 255));
            imageellipse($this->oImage, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $randomcolor);
        }
    }

    /**
     * Draw polygons background
     */
    public function drawPolygons()
    {
        for ($i = 1; $i <= $this->config['background_num']; ++$i) {
            $randomcolor = imagecolorallocate($this->oImage, mt_rand(190, 255), mt_rand(190, 255), mt_rand(190, 255));
            $coords      = [];
            for ($j = 1; $j <= $this->config['polygon_point']; ++$j) {
                $coords[] = mt_rand(0, $this->width);
                $coords[] = mt_rand(0, $this->height);
            }
            imagefilledpolygon($this->oImage, $coords, $this->config['polygon_point'], $randomcolor);
        }
    }
    /**#@-*/

    /**
     *  Create CAPTCHA image with BMP
     *
     *  TODO
     * @param  string $file
     * @return string
     */
    public function createImageBmp($file = '')
    {
        $image = '';

        if (empty($file)) {
            header('Content-type: image/bmp');
            echo $image;
        } else {
            return $image;
        }
        return null;
    }
}

$imageHandler = new XoopsCaptchaImageHandler();
$imageHandler->loadImage();
