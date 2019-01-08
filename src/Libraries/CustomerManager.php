<?php

namespace App\Libraries;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class CustomerManager
{
    public $container, $em;

    public function __construct(Container $container, EntityManager $em)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function getCustomerBalance($customer){
        $em = $this->em;
        $debitLogs = $em->getRepository('App:CustomerDebitLogs')->findBy(['customer' => $customer]);
        $totalOutstanding = 0;
        if ($debitLogs) {
            foreach ($debitLogs as $debitLog) {
                if ($debitLog->getType() == 1) {
                    $totalOutstanding = round($totalOutstanding,2) + round($debitLog->getAmountAfterTax(),2);
                } else if ($debitLog->getType() == 2) {
                    $totalOutstanding = round($totalOutstanding,2) - round(($debitLog->getAmount() * -1),2);
                }
            }
        }

        $qb = $em->createQueryBuilder('t');
        $qb->select('Sum(cc.worthOffAfterTax) as totalWorthOff')
            ->from('App:CouponCodes', 'cc')
            ->where('cc.customer=:customer')
            ->andWhere('cc.isRedeamed=1');
        $qb->setParameter('customer', $customer->getId());
        $redeemedTotal = $qb->getQuery()->getResult()[0]['totalWorthOff'];
        $balance = $totalOutstanding;
        if ($redeemedTotal) {
            $balance = $totalOutstanding + $redeemedTotal;
        }
        return $balance;
    }

    public function updateCustomersBalance()
    {
        $em = $this->em;
        $customers = $this->em->getRepository('App:Customers')->getFilterActiveCustomers(false,false,54080);
        foreach ($customers as $key=>$customer){
            $balance = $this->getCustomerBalance($customer);
            $customer->setBalance($balance);
            $em->flush();

            var_dump($customer->getId());
        }

    }

    public function updateCustomerLocations()
    {
        $customers = $this->em->getRepository('App:Customers')->getLongLatCustomers();
        $baseURL = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyByM31PrX8XBoqb6LT5yWouwZmkxi7uXxc&';

        foreach ($customers as $key => $row) {

            $urlTohit = $baseURL . "latlng=" . $row->getLatitude() . "," . $row->getLongitude() . "&sensor=true";
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $urlTohit);
            curl_setopt($ch, CURLOPT_HEADER, 0);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $address = curl_exec($ch);
            if(json_decode($address, true)['results']) {
                echo "**** updating customer#" . $row->getId() . "=>" . $row->getName() . "**** \r\n";
                echo "**** New Address => " . json_decode($address, true)['results'][0]['formatted_address'] . "**** \r\n";
                $row->setAddress(json_decode($address, true)['results'][0]['formatted_address']);
                $this->em->flush();
            }
//            if ($key > 3) {
//                die;
//            }
        }
    }

    public function updateCouponCodeLocations()
    {
        $customers = $this->em->getRepository('App:Customers')->getLongLatCouponCodes();
        $baseURL = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyByM31PrX8XBoqb6LT5yWouwZmkxi7uXxc&';

        foreach ($customers as $key => $row) {
            $urlTohit = $baseURL . "latlng=" . $row->getLatitude() . "," . $row->getLongitude() . "&sensor=true";
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $urlTohit);
            curl_setopt($ch, CURLOPT_HEADER, 0);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $address = curl_exec($ch);
            if(json_decode($address, true)['results']) {
                echo "**** updating Address#" . $row->getId() . "=>" . $row->getCode() . "**** \r\n";
                echo "**** New Address => " . json_decode($address, true)['results'][0]['formatted_address'] . "**** \r\n";
                $row->setAddress(json_decode($address, true)['results'][0]['formatted_address']);
                $this->em->flush();
            }
//            if ($key > 3) {
//                die;
//            }
        }
    }

    public function updateAssetsIssuedLocations()
    {
        $customers = $this->em->getRepository('App:Customers')->getLongLatAssetsIssued();
        $baseURL = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyByM31PrX8XBoqb6LT5yWouwZmkxi7uXxc&';

        foreach ($customers as $key => $row) {
//            echo $row->getAddress();die;
            $urlTohit = $baseURL . "latlng=" . $row->getLatitude() . "," . $row->getLongitude() . "&sensor=true";
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $urlTohit);
            curl_setopt($ch, CURLOPT_HEADER, 0);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $address = curl_exec($ch);
//            var_dump($address);die;
            if(json_decode($address, true)['results']) {
                echo "**** updating Assets Issued Address# " . $row->getId(). " **** \r\n";
                echo "**** New Address => " . json_decode($address, true)['results'][0]['formatted_address'] . "**** \r\n";

                $row->setAddress(json_decode($address, true)['results'][0]['formatted_address']);
                $this->em->flush();
            }
//            if ($key > 2) {
//                die;
//            }
        }
    }

    public function updateDriverLogsLocations()
    {
        $customers = $this->em->getRepository('App:Customers')->getLongLatDriverLogs();
        $baseURL = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyByM31PrX8XBoqb6LT5yWouwZmkxi7uXxc&';

        foreach ($customers as $key => $row) {
//            echo $row->getAddress();die;
            $urlTohit = $baseURL . "latlng=" . $row->getLatitude() . "," . $row->getLongitude() . "&sensor=true";
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $urlTohit);
            curl_setopt($ch, CURLOPT_HEADER, 0);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $address = curl_exec($ch);
//            var_dump($address);die;
            if(json_decode($address, true)['results']) {
                echo "**** updating Driver Logs Address# " . $row->getId(). " **** \r\n";
                echo "**** New Address => " . json_decode($address, true)['results'][0]['formatted_address'] . "**** \r\n";

                $row->setAddress(json_decode($address, true)['results'][0]['formatted_address']);
                $this->em->flush();
            }
//            if ($key > 2) {
//                die;
//            }
        }
    }

    public function updateOrdersCreatedLocations()
    {
        $customers = $this->em->getRepository('App:Customers')->getLongLatCreatedOrders();
        $baseURL = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyByM31PrX8XBoqb6LT5yWouwZmkxi7uXxc&';

        foreach ($customers as $key => $row) {
//            echo $row->getAddress();die;
            $urlTohit = $baseURL . "latlng=" . $row->getCreateLatitude() . "," . $row->getCreateLongitude() . "&sensor=true";
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $urlTohit);
            curl_setopt($ch, CURLOPT_HEADER, 0);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $address = curl_exec($ch);
            if(json_decode($address, true)['results']) {
                echo "**** updating Order Create Address# " . $row->getId(). " **** \r\n";
                echo "**** New Address => " . json_decode($address, true)['results'][0]['formatted_address'] . "**** \r\n";

                $row->setCreateAddress(json_decode($address, true)['results'][0]['formatted_address']);
                $this->em->flush();
            }
//            if ($key > 2) {
//                die;
//            }
        }
    }

    public function updateOrdersFinalLocations()
    {
        $customers = $this->em->getRepository('App:Customers')->getLongLatFinalOrders();
        $baseURL = 'https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyByM31PrX8XBoqb6LT5yWouwZmkxi7uXxc&';

        foreach ($customers as $key => $row) {
//            echo $row->getAddress();die;
            $urlTohit = $baseURL . "latlng=" . $row->getFinalLatitude() . "," . $row->getFinalLongitude() . "&sensor=true";
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $urlTohit);
            curl_setopt($ch, CURLOPT_HEADER, 0);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $address = curl_exec($ch);
            if(json_decode($address, true)['results']) {
                echo "**** updating Order Final Address# " . $row->getId(). " **** \r\n";
                echo "**** New Address => " . json_decode($address, true)['results'][0]['formatted_address'] . "**** \r\n";

                $row->setFinalAddress(json_decode($address, true)['results'][0]['formatted_address']);
                $this->em->flush();
            }
//            if ($key > 2) {
//                die;
//            }
        }
    }

    public function calculatePreviousNonPaidTotalVendorLogs($createdOn, $currentDate, $prevDate, $row, $DaysLogs, $postType, $type, &$logCheckBoxesData)
    {
        if (($prevDate == null or $createdOn >= $prevDate) and ($currentDate == null or $createdOn <= $currentDate)) {
            if ($row->getType() == 1) {
                if (isset($DaysLogs[$row->getPurchaseOrderId()->getId()]['invoices_sum'])) {
                    $DaysLogs[$row->getPurchaseOrderId()->getId()]['invoices_sum'] += $row->getAmount();
                    $DaysLogs[$row->getPurchaseOrderId()->getId()]['invoices_tax_sum'] += $row->getTaxAmount();
                    $DaysLogs[$row->getPurchaseOrderId()->getId()]['invoices_after_tax_sum'] += $row->getAmountWithTax();
                } else {
                    $DaysLogs[$row->getPurchaseOrderId()->getId()]['invoices_sum'] = $row->getAmount();
                    $DaysLogs[$row->getPurchaseOrderId()->getId()]['invoices_tax_sum'] = $row->getTaxAmount();
                    $DaysLogs[$row->getPurchaseOrderId()->getId()]['invoices_after_tax_sum'] = $row->getAmountWithTax();
                }
            } else {
                if ($row->getPurchaseOrderId() and isset($DaysLogs[$row->getPurchaseOrderId()->getId()]['transactions_sum'])) {
                    $DaysLogs[$row->getPurchaseOrderId()->getId()]['transactions_sum'] += $row->getAmount();
                    $DaysLogs[$row->getPurchaseOrderId()->getId()]['transactions_tax_sum'] += $row->getTaxAmount();
                    $DaysLogs[$row->getPurchaseOrderId()->getId()]['transactions_after_tax_sum'] += $row->getAmountWithTax();
                } elseif($row->getPurchaseOrderId()) {
                    $DaysLogs[$row->getPurchaseOrderId()->getId()]['transactions_sum'] = $row->getAmount();
                    $DaysLogs[$row->getPurchaseOrderId()->getId()]['transactions_tax_sum'] = $row->getTaxAmount();
                    $DaysLogs[$row->getPurchaseOrderId()->getId()]['transactions_after_tax_sum'] = $row->getAmountWithTax();
                }
            }
            if ($type == $postType) {
                $logCheckBoxesData[] = $row->getId();
            }
        }

        return $DaysLogs;
    }

    public function calculatePreviousNonPaidTotalLogs($createdOn, $currentDate, $prevDate, $row, $DaysLogs, $postType, $type, &$logCheckBoxesData)
    {
        if (($prevDate == null or $createdOn >= $prevDate) and ($currentDate == null or $createdOn <= $currentDate)) {
            if ($row->getType() == 1) {
                if (isset($DaysLogs[$row->getOrder()->getId()]['invoices_sum'])) {
                    $DaysLogs[$row->getOrder()->getOrderInvoice()->getId()]['invoices_sum'] += $row->getAmount();
                    $DaysLogs[$row->getOrder()->getOrderInvoice()->getId()]['invoices_tax_sum'] += $row->getTaxAmount();
                    $DaysLogs[$row->getOrder()->getOrderInvoice()->getId()]['invoices_after_tax_sum'] += $row->getAmountAfterTax();
                } else {
                    $DaysLogs[$row->getOrder()->getOrderInvoice()->getId()]['invoices_sum'] = $row->getAmount();
                    $DaysLogs[$row->getOrder()->getOrderInvoice()->getId()]['invoices_tax_sum'] = $row->getTaxAmount();
                    $DaysLogs[$row->getOrder()->getOrderInvoice()->getId()]['invoices_after_tax_sum'] = $row->getAmountAfterTax();
                }
            } else {
                if (isset($DaysLogs[$row->getOrder()->getOrderInvoice()->getId()]['transactions_sum'])) {
                    $DaysLogs[$row->getOrder()->getOrderInvoice()->getId()]['transactions_sum'] += $row->getAmountWithoutTax();
                    $DaysLogs[$row->getOrder()->getOrderInvoice()->getId()]['transactions_tax_sum'] += $row->getTaxAmount();
                    $DaysLogs[$row->getOrder()->getOrderInvoice()->getId()]['transactions_after_tax_sum'] += $row->getTaxAmount();
                } else {
                    $DaysLogs[$row->getOrder()->getOrderInvoice()->getId()]['transactions_sum'] = $row->getAmountWithoutTax();
                    $DaysLogs[$row->getOrder()->getOrderInvoice()->getId()]['transactions_tax_sum'] = $row->getTaxAmount();
                    $DaysLogs[$row->getOrder()->getOrderInvoice()->getId()]['transactions_after_tax_sum'] = $row->getAmount();
                }
            }
            if ($type == $postType) {
                $logCheckBoxesData[] = $row->getId();
            }
        }

        return $DaysLogs;
    }

}