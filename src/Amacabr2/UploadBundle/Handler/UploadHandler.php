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

    /**
     * @param $entity
     * @param $property
     * @param $annotation
     */
    public function uploadFile($entity, $property, $annotation) {
        $file = $this->accessor->getValue($entity, $property);
        if ($file instanceof UploadedFile) {
            $this->removeOldFile($entity, $annotation);
            $filename = $file->getClientOriginalName();
            $file->move($annotation->getPath(), $filename);
            $this->accessor->setValue($entity, $annotation->getFilename(), $filename);
        }
    }

    /**
     * @param $entity
     * @param $property
     * @param $annotation
     */
    public function setFileFromFilename($entity, $property, $annotation) {
        $file = $this->getFileFromFilename($entity, $annotation);
        $this->accessor->setValue($entity, $property, $file);
    }

    /**
     * @param $entity
     * @param $annotation
     */
    public function removeOldFile($entity, $annotation) {
        $file = $this->getFileFromFilename($entity, $annotation);
        if ($file !== null) {
            @unlink($file->getRealPath());
        }
    }

    /**
     * @param $entity
     * @param $property
     */
    public function removeFile($entity, $property) {
        $file = $this->accessor->getValue($entity, $property);
        if ($file instanceof File) {
            @unlink($file->getRealPath());
        }
    }

    /**
     * @param $entity
     * @param $annotation
     * @return File | null
     */
    private function getFileFromFilename($entity, $annotation) {
        $filename = $this->accessor->getValue($entity, $annotation->getFilename());
        if (empty($filename)) {
            return null;
        }
        return new File($annotation->getPath() . DIRECTORY_SEPARATOR . $filename, false);
    }

}