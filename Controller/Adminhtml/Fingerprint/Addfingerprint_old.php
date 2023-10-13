<?php

namespace Smartwave\NewFingerprints\Controller\Adminhtml\Fingerprint\;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;

class AddFingerprint extends Action
{
    public function execute()
    {
        // Handle the file upload here
        $uploader = $this->_objectManager->create(\Magento\MediaStorage\Model\File\Uploader::class, ['fileId' => 'fileToUpload']);
        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);

        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryWrite(DirectoryList::MEDIA);

        $result = $uploader->save($mediaDirectory->getAbsolutePath('your_module_folder')); // Change 'your_module_folder' to your desired upload directory

        // Process and save other form data if needed
        // ...

        $this->messageManager->addSuccessMessage(__('File uploaded successfully.'));
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
    }
}
