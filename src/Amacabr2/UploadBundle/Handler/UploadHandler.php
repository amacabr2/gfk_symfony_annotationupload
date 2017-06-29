<?php

namespace Amacabr2\UploadBundle\Handler;


use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyAccess;

class UploadHandler {

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    private $accessor;

    /**
     * UploadHandler constructor.
     */
    public function __construct() {
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function uploadFile($entity, $property, $annotation) {
        $file = $this->accessor->getValue($entity, $property);
        if ($file instanceof UploadedFile) {
            $filename = $file->getClientOriginalName();
            $file->move($annotation->getPath(), $filename);
            $this->accessor->setValue($entity, $annotation->getFilename(), $filename);
        }
    }

    public function setFileFromFilename($entity, $property, $annotation) {
        $filename = $this->accessor->getValue($entity, $annotation->getFilename());
        $file = new File($annotation->getPath() . DIRECTORY_SEPARATOR . $filename);
        $this->accessor->setValue($entity, $property, $file);
    }

}