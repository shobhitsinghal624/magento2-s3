<?php
namespace Arkade\S3\Helper\MediaStorage\File\Storage\Database;

use Magento\MediaStorage\Helper\File\Storage\Database;

class Plugin
{
    private $helper;

    private $s3StorageFactory;

    private $dbStorageFactory;

    private $storageModel = null;

    public function __construct(
        \Arkade\S3\Helper\Data $helper,
        \Arkade\S3\Model\MediaStorage\File\Storage\S3Factory $s3StorageFactory,
        \Magento\MediaStorage\Model\File\Storage\DatabaseFactory $dbStorageFactory
    ) {
        $this->helper = $helper;
        $this->s3StorageFactory = $s3StorageFactory;
        $this->dbStorageFactory = $dbStorageFactory;
    }

    /**
     * Check whether we are using either the database or S3 as our file storage
     * backend.
     *
     * @param Database $subject
     * @param bool $result
     * @return bool
     */
    public function afterCheckDbUsage(Database $subject, $result)
    {
        if (!$result) {
            $result = $this->helper->checkS3Usage();
        }
        return $result;
    }

    public function aroundGetStorageDatabaseModel(Database $subject, $proceed)
    {
        if (is_null($this->storageModel)) {
            if ($subject->checkDbUsage() && $this->helper->checkS3Usage()) {
                $this->storageModel = $this->s3StorageFactory->create();
            } else {
                $this->storageModel = $this->dbStorageFactory->create();
            }
        }
        return $this->storageModel;
    }

    public function aroundSaveFileToFilesystem(Database $subject, $proceed, $filename)
    {
        if ($subject->checkDbUsage() && $this->helper->checkS3Usage()) {
            $file = $subject->getStorageDatabaseModel()->loadByFilename($subject->getMediaRelativePath($filename));
            if (!$file->getId()) {
                return false;
            }

            return $subject->getStorageFileModel()->saveFile($file->getData(), true);
        }
        return $proceed($filename);
    }
}
