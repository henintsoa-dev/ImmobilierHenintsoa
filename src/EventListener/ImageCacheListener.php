<?php // src/EventListener/ExceptionSubscriber.php
namespace App\EventListener;

use App\Entity\Image;
use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

#[AsEntityListener(event: Events::preFlush, method: 'imagePreFlush', entity: Image::class)]
#[AsEntityListener(event: Events::preRemove, method: 'propertyPreRemove', entity: Property::class)]
final class ImageCacheListener
{
    /**
     * @var CacheManager
     */
    private $cacheManager;

    private $targetDirectory;
    

    public function __construct(CacheManager $cacheManager, string $targetDirectory)
    {
        $this->cacheManager = $cacheManager;
        $this->targetDirectory = $targetDirectory;
    }

    public function propertyPreRemove(Property $property): void
    {
        if ($property->getImages()->count() > 0) {
            $this->cacheManager->remove($this->targetDirectory);
        }
    }

    public function imagePreFlush(Image $image)
    {
        $this->cacheManager->remove($this->targetDirectory);
    }
}