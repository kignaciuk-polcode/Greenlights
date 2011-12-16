<?php

class Polcode_Social_Model_Observer
{
    
    public function _construct() {}
    
    public function add_order($observer)
    {
        $order = $observer->getEvent()->getOrder();
        
        $order_id = $order->getId();
        $order_date = $order->getCreatedAt();
        
        $launch_date = new DateTime($order_date);
        $launch_date->modify('+ 2 weeks');
        
        
        $model = Mage::getModel('social/mails');
        
        $collection = $model->getCollection();
        $collection->addFieldToFilter('order_id', array('eq' => $order_id));
        
        if ($collection->getSize() == 0) {
            $model->setOrderId($order_id);
            $model->setLaunchDate($launch_date->format('Y-m-d H:i:s'));
            $model->save();
        }
    }
    
    public function sendMails($observer) {
        $date = new DateTime();
        Mage::log("Cron test - task executed at " . $date->format('Y-m-d H:i:s'));
    }
    
}
