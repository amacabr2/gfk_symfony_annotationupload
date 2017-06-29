<?php

namespace Amacabr2\UploadBundle\Listener;


use Amacabr2\UploadBundle\Annotation\UploadAnnotationReader;
use Amacabr2\UploadBundle\Handler\UploadHandler;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;

class UploadSubscriber implements EventSubscriber {

    /**
     * @var UploadAnnotationReader
     */
    private $reader;

    /**
     * @var UploadHandler
     */
    private $handler;

    /**
     * UploadSubscriber constructor.
     * @param UploadAnnotationReader $reader
     * @param UploadHandler $handler
     */
    public function __construct(UploadAnnotationReader $reader, UploadHandler $handler) {
        $this->reader = $reader;
        $this->handler = $handler;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents() {
        return [
            'prePersist',
            'preUpdate',
            'postLoad',
        ];
    }

    /**
     * @param EventArgs $event
     */
    public function prePersist(EventArgs $event) {
       $this->preEvent($event);
    }

    /**
     * @param EventArgs $event
     */
    public function preUpdate(EventArgs $event) {
        $this->preEvent($event);
    }

    /**
     * @param EventArgs $event
     */
    private function preEvent(EventArgs $event) {
        $entity = $event->getEntity();
        foreach ($this->reader->getUploadbleFields($entity) as $property => $annotation) {
            $this->handler->removeOldFile($entity, $annotation);
            $this->handler->uploadFile($entity, $property, $annotation);
        }
    }

    /**
     * @param EventArgs $event
     */
    public function postLoad(EventArgs $event) {
        $entity = $event->getEntity();
        foreach ($this->reader->getUploadbleFields($entity) as $property => $annotation) {
            $this->handler->setFileFromFilename($entity, $property, $annotation);
        }
    }

}