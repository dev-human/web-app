<?php
/**
 * Handles multi line text and generates an image with a fixed width
 */

namespace Imanee\ImageToolsBundle;

use Imanee\Drawer;
use Imanee\ImageResource\ImagickResource;
use Imanee\Imanee;

class TextImageGenerator
{
    /** @var  array */
    protected $defaults;

    /**
     * @param array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->setDefaults(
            array_merge(
                [
                    'font_file'      => __DIR__ . '/Resources/fonts/quote_default.otf',
                    'font_size'      => 30,
                    'line_spacing'   => 5,
                    'padding'        => 20,
                    'color'          => '#333333',
                    'background'     => '#F5F5F5',
                    'highlight_text' => true,
                    'highlight'      => '#61e83f'
                ],
                $settings
            )
        );
    }

    /**
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * @param array $defaults
     */
    public function setDefaults($defaults)
    {
        $this->defaults = $defaults;
    }

    /**
     * @param $setting
     * @return null
     */
    public function get($setting)
    {
        return isset($this->defaults[$setting]) ? $this->defaults[$setting] : null;
    }

    /**
     * @param $option
     * @param $value
     */
    public function set($option, $value)
    {
        $this->defaults[$option] = $value;
    }

    /**
     * Returns a highlighted line of text
     * @returns Imanee the highlighted line of text
     */
    public function getTextLine($line)
    {
        $drawer = $this->getDrawer();
        $width = $this->getLineWidth($line, $drawer->getFont(), $drawer->getFontSize());
        $height = $this->get('font_size') + ($this->get('line_spacing')*2);

        $text = new Imanee();

        return $text
            ->newImage($width, $height, $this->get('highlight'))
            ->setDrawer($drawer)
            ->annotate($line, 0, $height - ($this->get('line_spacing')*2));
    }

    /**
     * Creates a Drawer object based on current settings
     * @return Drawer $drawer
     */
    public function getDrawer()
    {
        $drawer = new Drawer();
        $drawer
            ->setFont($this->get('font_file'))
            ->setFontColor($this->get('color'))
            ->setFontSize($this->get('font_size'));

        return $drawer;
    }

    /**
     * Returns how many characters fit in one line
     * based on a font and size
     * @param string $font Font file
     * @param int $size Font size
     * @param int $width Width to fit
     * @return int The max amount of chars to fit a line of $width width
     */
    public function getCharCount($font, $size, $width, $sample = null)
    {
        if (!$sample) {
            $sample = "This is a sample text, to mix different characters. Should be long enough to fill a line.";
        }

        $words = explode(' ', $sample);
        $text = $words[0];
        $length = 1;

        for ($i = 1; $i < count($words); $i++) {
            $text .= ' ' . $words[$i];

            if ($this->getLineWidth($text, $font, $size) >= $width) {
                break;
            }

            $length = strlen($text);
        }

        return $length;
    }

    /**
     * @param $text
     * @param $font
     * @param $size
     * @return mixed
     */
    public function getLineWidth($text, $font, $size)
    {
        $drawer = new Drawer();
        $drawer->setFont($font)
            ->setFontSize($size);

        $im = new ImagickResource();
        $size = $im->getTextGeometry($text, $drawer);

        return $size['width'];
    }

    /**
     * @param $text
     * @param int $width
     * @return Imanee
     * @throws \Imanee\Exception\UnsupportedMethodException
     */
    public function generateImage($text, $width = 500)
    {
        $font = $this->get('font_file');
        $fontSize = $this->get('font_size');
        $lineSpacing = $this->get('line_spacing');
        $padding = $this->get('padding');
        $background = $this->get('background');

        $charCount = $this->getCharCount($font, $fontSize, $width - (2*$padding), $text);

        $wrap = wordwrap($text, $charCount, '------');
        $lines = explode('------', $wrap);

        $drawer = $this->getDrawer();

        // calculates image height
        $height = (($fontSize + ($lineSpacing*3)) * count($lines)) + (2 * $padding);

        $imanee = new Imanee();
        $imanee->newImage($width, $height, $background);
        $imanee->setDrawer($drawer);

        $cx = $padding;
        $cy = $padding;

        foreach ($lines as $line) {
            $lineImg = $this->getTextLine($line);
            $cx = ($width / 2) - ($lineImg->getWidth() / 2);
            $imanee->compositeImage($lineImg, $cx, $cy);
            $cy += $lineImg->getHeight() + $lineSpacing;
        }

        return $imanee;
    }
}
