<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use App\Utils\Interfaces\CacheInterface;

class AdminChangesDataSubscriber implements EventSubscriberInterface
{
    protected $routeNamesThatMustClearCache = [
        'categories.POST',
        'editCategory.POST',
        'deleteCategory.GET',
        'deleteVideo.GET',
        'set-video-duration.GET',
        'updateVideoCat.POST',
        'like_video.POST',
        'dislike_video.POST',
        'undo_like_video.POST',
        'undo_dislike_video.POST',   
    ];

    public function __construct( CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest()->attributes->get('_route') . '.'. $event->getRequest()->getMethod();

        if( !in_array($request,$this->routeNamesThatMustClearCache) )
        {
            return;
        }

        $cache = $this->cache->cache;
        $cache->clear();
    }

    public static function getSubscribedEvents()
    {
        return [
           'kernel.response' => 'onKernelResponse',
        ];
    }
}
