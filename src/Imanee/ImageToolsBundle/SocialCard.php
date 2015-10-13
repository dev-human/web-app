<?php
/**
 * Generates an image optimized for Social Networks, with a title and author
 */

namespace Imanee\ImageToolsBundle;

use Imanee\Imanee;

class SocialCard
{
    protected $title;
    protected $author;
    protected $background;
    protected $titleFontSize;
    protected $authorFontSize;

    public function __construct($title, $author, $background)
    {
        $this->title = $title;
        $this->author = $author;
        $this->background = $background;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getBackground()
    {
        return $this->background;
    }

    /**
     * @param mixed $background
     */
    public function setBackground($background)
    {
        $this->background = $background;
    }

    /**
     * @return mixed
     */
    public function getTitleFontSize()
    {
        return $this->titleFontSize;
    }

    /**
     * @param mixed $titleFontSize
     */
    public function setTitleFontSize($titleFontSize)
    {
        $this->titleFontSize = $titleFontSize;
    }

    /**
     * @return mixed
     */
    public function getAuthorFontSize()
    {
        return $this->authorFontSize;
    }

    /**
     * @param mixed $authorFontSize
     */
    public function setAuthorFontSize($authorFontSize)
    {
        $this->authorFontSize = $authorFontSize;
    }

    public function generateCard()
    {
        $imanee = new Imanee($this->background);

        $generator = new TextImageGenerator([
            'font_file' => __DIR__ . '/Resources/fonts/dreamorphans-b.ttf',
            'highlight_text' => false,
            'font_size' => $this->getTitleFontSize(),
            'color' => '#333333',
            'highlight' => 'transparent',
            'background' => 'transparent'
        ]);

        $text = $generator->generateImage($this->getTitle(), $imanee->getWidth());

        $generator->set('font_size', $this->getAuthorFontSize());
        $generator->set('color', '#666666');

        $author = $generator->generateImage('story  by ' . $this->getAuthor(), $imanee->getWidth());

        //$imanee->placeImage($text, Imanee::IM_POS_MID_CENTER);
        $imanee->compositeImage(
            $text,
            ($imanee->getWidth()/2) - ($text->getWidth()/2),
            ($imanee->getHeight()/2) - $text->getHeight()
        );

        $imanee->compositeImage(
            $author,
            ($imanee->getWidth()/2) - ($text->getWidth()/2),
            $imanee->getHeight()/2
        );

        return $imanee;
    }
}
