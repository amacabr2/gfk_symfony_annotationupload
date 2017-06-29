<?php

namespace Amacabr2\UploadBundle\Annotation;


use Doctrine\Common\Annotations\AnnotationReader;

class UploadAnnotationReader {

    /**
     * @var AnnotationReader
     */
    private $reader;

    /**
     * UploadAnnotationReader constructor.
     * @param AnnotationReader $reader
     */
    public function __construct(AnnotationReader $reader) {
        $this->reader = $reader;
    }

    public  function isUploadable($entity): bool {
        $reflection = new \ReflectionClass(get_class($entity));
        return $this->reader->getClassAnnotation($reflection, Uploadable::class) !== null;
    }

    public function getUploadbleFields($entity): array {
        $reflection = new \ReflectionClass(get_class($entity));
        if (!$this->isUploadable($entity)) {
            return [];
        }
        $properties = [];
        foreach ($reflection->getProperties() as $property) {
            $annotation = $this->reader->getPropertyAnnotation($property, UploadableField::class);
            if ($annotation !== null) {
                $properties[$property->getName()] = $annotation;
            }
        }
        return $properties;
    }

}