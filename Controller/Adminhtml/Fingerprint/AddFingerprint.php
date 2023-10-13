<?php

namespace Smartwave\NewFingerprints\Controller\Adminhtml\Fingerprint;
// namespace Vendor\Module\Controller\Adminhtml\Fingerprint;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;


class AddFingerprint extends Action
{
    
    public function execute()
    {
        
        /* 
        $this->messageManager->addSuccessMessage(__('File uploaded successfully.')); */
        // return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/');
        if (!empty($_POST)) {
    
            $model = $this->_objectManager->create('Smartwave\NewFingerprints\Model\NewFingerprints');
            $imageModel = $this->_objectManager->create('Smartwave\NewFingerprints\Model\NewFingerprintsCustomerImages');
            try {
                $columnName = 'cust_id';
                $valueToMatch = $_POST['id'];
                /** get existing record from customer images table */
                $imageModel->getResource()->load($imageModel, $_POST['id'], $columnName);         
                
                if ($imageModel->getId()) {
                    if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
                        $path = $_FILES['image']['name'];
                        $ext = pathinfo($path, PATHINFO_EXTENSION);
                        $file_name = time() . '-' . mt_rand() . "." . $ext;
                        /** save new image in folder */
                        copy($_FILES['image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/api/customer_images/' . $file_name);
                        if (isset($_FILES['image'])) {
                            copy($_FILES['image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/api/thumbs/' . $file_name);
                        }

                        /** delete existing image from folder */
                        $existingData = $imageModel->getData();
                        if($existingData['image_name']){
                            $fileToDelete = $_SERVER['DOCUMENT_ROOT'] . '/api/customer_images/' . $existingData['image_name'];
                            if (file_exists($fileToDelete)) {
                                unlink($fileToDelete);
                            } 
                        }
                        
                        /** update new image in customer images table */
                        $imageModel->setImageName($file_name);
                        $imageModel->setImageDate(date('m/d/Y'));
                        $imageModel->setIsFinal(1);
                        $imageModel->save();                
                    }
                    $this->messageManager->addSuccessMessage(__('You saved the data.'));

                } else if(!$imageModel->getId()){
                    /** if data not exist in customer table */
                    if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
                        $path = $_FILES['image']['name'];
                        $ext = pathinfo($path, PATHINFO_EXTENSION);
                        $file_name = time() . '-' . mt_rand() . "." . $ext;
                       
                        copy($_FILES['image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/api/customer_images/' . $file_name);
                        if (isset($_FILES['image'])) {
                            copy($_FILES['image']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/api/thumbs/' . $file_name);
                        }
                        /** save data in customer images table */
                        $data11 = [
                            'cust_id' => $_POST['id'],
                            'image_name' => $file_name,
                            'image_date' => date('m/d/Y'),
                            'created_at' => date("Y-m-d H:i:s"),
                            'is_final' => "1"
                        ];
                        $imageModel->setData($data11);
                        $imageModel->save();                   
                    }
                    $this->messageManager->addSuccessMessage(__('You saved the data.'));
                }else{
                    $this->messageManager->addErrorMessage(__('You don\'t import correct csv file'));
                }
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }
            // $this->_redirect('/adminfingerprint/fingerprint/');

        }

        // $this->_view->loadLayout();
        // $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        // $resultPage->getConfig()->getTitle()->set(__('UPD Print Capture'));
        // $this->_view->renderLayout();
    }
}
