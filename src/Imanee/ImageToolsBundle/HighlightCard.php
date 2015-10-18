<?php
/**
 * Class representing a "card" with a user highlight
 */

namespace Imanee\ImageToolsBundle;

use Imanee\Drawer;
use Imanee\ImageResource\ImagickResource;
use Imanee\Imanee;

class HighlightCard
{
    /** @var  string */
    protected $quoteAuthor;

    /** @var  string */
    protected $quoteAvatar;

    /** @var  string */
    protected $quoteSource;

    /** @var  string */
    protected $sourceLogo;

    /** @var  string */
    protected $sourceUrl;

    /** @var TextImageGenerator */
    protected $generator;

    public function __construct(array $settings = [])
    {
        $this->setQuoteAvatar(__DIR__ . '/Resources/img/avatar.png');
        $this->setSourceLogo(__DIR__ . '/Resources/img/imanee-icon.png');
        $this->setQuoteAuthor('Anonymous');
        $this->setQuoteSource('imanee');
        $this->setSourceUrl(" ");

        $this->settings = $settings;
        $this->generator = new TextImageGenerator($settings);
    }

    /**
     * @return string
     */
    public function getQuoteAuthor()
    {
        return $this->quoteAuthor;
    }

    /**
     * @param string $quoteAuthor
     */
    public function setQuoteAuthor($quoteAuthor)
    {
        $this->quoteAuthor = $quoteAuthor;
    }

    /**
     * @return string
     */
    public function getQuoteAvatar()
    {
        return $this->quoteAvatar;
    }

    /**
     * @param string $quoteAvatar
     */
    public function setQuoteAvatar($quoteAvatar)
    {
        $this->quoteAvatar = $quoteAvatar;
    }

    /**
     * @return string
     */
    public function getSourceLogo()
    {
        return $this->sourceLogo;
    }

    /**
     * @param string $sourceLogo
     */
    public function setSourceLogo($sourceLogo)
    {
        $this->sourceLogo = $sourceLogo;
    }

    /**
     * @return string
     */
    public function getQuoteSource()
    {
        return $this->quoteSource;
    }

    /**
     * @param string $quoteSource
     */
    public function setQuoteSource($quoteSource)
    {
        $this->quoteSource = $quoteSource;
    }

    /**
     * @return string
     */
    public function getSourceUrl()
    {
        return $this->sourceUrl;
    }

    /**
     * @param string $sourceUrl
     */
    public function setSourceUrl($sourceUrl)
    {
        $this->sourceUrl = $sourceUrl;
    }

    /**
     * @param $text
     * @param int $width
     * @return Imanee
     * @throws \Imanee\Exception\UnsupportedFormatException
     * @throws \Imanee\Exception\UnsupportedMethodException
     */
    public function generateQuoteCard($text, $width = 500)
    {
        $textImage = $this->generator->generateImage('"' . $text . '"', $width);
        $header = $this->getImageHeader($width, 40);
        $footer = $this->getImageFooter();
        $padding = $this->generator->get('padding');

        $height =
            $header->getHeight() + $textImage->getHeight() + ($footer->getHeight() + $padding);

        $imanee = new Imanee();
        $imanee
            ->newImage($width, $height, $this->generator->get('background'))
            ->placeImage($header, Imanee::IM_POS_TOP_LEFT)
            ->compositeImage($textImage, 0, $header->getHeight())
            ->compositeImage(
                $footer,
                $width - $footer->getWidth() - $padding,
                $height - $footer->getHeight() - $padding
            );

        return $imanee;
    }

    /**
     * @param $width
     * @param $height
     * @return Imanee
     * @throws \Imanee\Exception\UnsupportedMethodException
     */
    public function getImageHeader($width, $height)
    {
        $fontSize = 16;
        $imageSize = 40;
        $padding = $this->generator->get('padding');

        $header = new Imanee();
        $header->newImage($width, $height + ($padding), '#E5E5E5');

        $mask = new Imanee(__DIR__ . '/Resources/img/avatar-mask.png');

        $drawer = new Drawer();
        $drawer
            ->setFont($this->generator->get('font_file'))
            ->setFontColor($this->generator->get('color'))
            ->setFontSize($fontSize);

        $header->setDrawer($drawer);

        $header
            ->compositeImage($this->getQuoteAvatar(), $padding, $padding/2, $imageSize, $imageSize)
            ->compositeImage($mask, $padding, $padding/2, $imageSize, $imageSize)
            ->compositeImage(
                $this->getSourceLogo(),
                $header->getWidth() - $padding - ($imageSize),
                $padding/2,
                $imageSize,
                $imageSize
            )
            ->annotate($this->getQuoteAuthor(), $imageSize + (2*$padding), $padding + ($imageSize/4))
            ->annotate($this->getQuoteSource(), $imageSize + (2*$padding), ($imageSize), $fontSize*1.2);

        return $header;
    }

    /**
     * Appends Source URL
     * @return Imanee
     */
    public function getImageFooter()
    {
        $fontSize = 14;
        $fontColor = '#666666';

        $drawer = new Drawer();
        $drawer
            ->setFontColor($fontColor)
            ->setFontSize($fontSize);

        return Imanee::textGen($this->getSourceUrl(), $drawer);
    }
}
