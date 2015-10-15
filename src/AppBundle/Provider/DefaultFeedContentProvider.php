<?php
namespace AppBundle\Provider;

use \Symfony\Component\OptionsResolver\Options;
use \Doctrine\Bundle\DoctrineBundle\Registry;
use Debril\RssAtomBundle\Exception\FeedException\FeedNotFoundException;
use Debril\RssAtomBundle\Provider\FeedContentProviderInterface;
use Debril\RssAtomBundle\Protocol\Parser\FeedContent;
use Debril\RssAtomBundle\Protocol\Parser\Item;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use AppBundle\Entity\Collection;

class DefaultFeedContentProvider implements FeedContentProviderInterface
{

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;
    
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router 
     */
    protected $router;
    
    /**
     * @var int
     */
    protected $maxItemsCount = 10;
    
    /**
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     */
    public function __construct(Registry $doctrine, Router $router)
    {
        $this->doctrine = $doctrine;
        $this->router = $router;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\Options $options
     * @return \Debril\FeedAggregatorBundle\Provider\Feed
     * @throws FeedNotFoundException
     */
    public function getFeedContent(array $options)
    {
        $feedTitle = 'dev-human - non technical content for humans who code';
        
        // fetch feed from data repository
        // $options['id'] has a value - string "null", if not set...
        if(!empty($options['id']) && $options['id']!=='null'){
            
            $collection = $this->getDoctrine()
                ->getManager()
                ->getRepository('AppBundle:Collection')
                ->findOneBy(array(
                    'slug'  =>  $options['id']
                ));
            
            if(!($collection instanceof Collection)){
                throw new FeedNotFoundException();
            }
            
            $stories = $this->getDoctrine()
                ->getManager()
                ->getRepository('AppBundle:Story')
                ->findFromCollectionWithLimit(
                        $collection->getId(),
                        $this->maxItemsCount
                );
            
            
            if(empty($collection->getDescription())){
                $feedTitle = 'dev-human - '.$collection->getName();
            }else{
                $feedTitle = 'dev-human - '.$collection->getDescription();
            }
            
        }else{
            $stories = $this->getDoctrine()
                    ->getManager()
                    ->getRepository('AppBundle:Story')
                    ->findRecentPosts($this->maxItemsCount);
        }

        $feed = $this->getFeed($stories, $feedTitle);
        
        // if the feed is an actual FeedOutInterface instance, then return it
        if ($feed instanceof \Debril\RssAtomBundle\Protocol\FeedOutInterface){
            return $feed;
        }

        // $feed is null, which means no Feed was found with this id.
        throw new FeedNotFoundException();
    }
    
    protected function getFeed($stories, $title )
    {
        $feed = new FeedContent();
        $feed->setLastModified(new \DateTime());

        $feed->setTitle($title);
        $feed->setDescription(
                'dev-human is a collaborative, non-technical blog written by '
                . 'developers. We talk about life and stuff robots can\'t '
                . 'understand.');
        
        foreach($stories as $story){
            $shortDescription = substr($story->getTextOnlyContent(),0,240).'...';
            $link = $this->router->generate(
                    'devhuman_show_article', 
                    array(
                        'author'=>$story->getAuthor()->getUsername(), 
                        'slug'=>$story->getSlug()
                    ),
                    true
            );
            
            $item = new Item();
            $item->setTitle($story->getTitle());
            $item->setDescription($shortDescription);
            $item->setAuthor($story->getAuthor()->getName());
            $item->setLink($link);
            $item->setComment($link."#disqus_thread");
            $item->setUpdated($story->getUpdated());
            
            $feed->addItem($item);
        }
        
        return $feed;
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    public function getDoctrine()
    {
        return $this->doctrine;
    }
}
