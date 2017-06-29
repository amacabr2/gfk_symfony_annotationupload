<?php

namespace Amacabr2\UploadBundle\Listener;


use Amacabr2\UploadBundle\Annotation\UploadAnnotationReader;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyAccess;

class UploadSubscriber implements EventSubscriber {

    /**
     * @var UploadAnnotationReader
     */
    private $reader;

    public function __construct(UploadAnnotationReader $reader) {
        $this->reader = $reader;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents() {
        return [
            'prePersist'
        ];
    }

    /**
     * @param EventArgs $event
     */
    public function prePersist(EventArgs $event) {
        $entity = $event->getEntity();
        foreach ($this->reader->getUploadbleFields($entity) as $property => $annotation) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $file = $accessor->getValue($entity, $property);
            if ($file instanceof UploadedFile) {
                $filename = $file->getClientOriginalName();
                $file->move($annotation->getPath(), $filename);
                $accessor->setValue($entity, $annotation->getFilename(), $filename);
            }
        }
    }

}