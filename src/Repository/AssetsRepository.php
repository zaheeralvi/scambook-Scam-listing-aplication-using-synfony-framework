<?php

namespace App\Repository;

use function GuzzleHttp\Psr7\modify_request;

class AssetsRepository extends \Doctrine\ORM\EntityRepository
{
    public function getAllAssets()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select("asset.name,asset.id")
            ->from('App:Assets', 'asset');

        $allAssetsData = $qb->getQuery()->getResult();
        return $allAssetsData;
    }

    public function getTotalNumCustomersUpdated($date_from, $date_to, $driver_id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select("COUNT(c.id) as customersUpdated")
            ->from('App:Customers', 'c')
//            ->innerJoin('o.customer','c')
            ->where('c.latitude is not NULL')
            ->andWhere('c.longitude is not NULL')//            ->groupBy('o.id');
        ;
        if ($date_from || $date_to || $driver_id) {
            $qb->innerJoin('App:AssetsIssuedToCustomer', 'aic', 'WITH', 'aic.customer=c');
        }

        if ($date_from) {
            $qb->andWhere('DATE(aic.deliveryDate) >= DATE(:start)');
            $qb->setParameter('start', $date_from->format('Y-m-d'));
        }

        if ($date_to) {
            $qb->andWhere('DATE(aic.deliveryDate) <= DATE(:end)');
            $qb->setParameter('end', $date_to->format('Y-m-d'));
        }

        if ($driver_id) {
            $qb->andWhere('aic.driver = ' . $driver_id);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getTotalNumCustomersGetAssets($date_from, $date_to, $driver_id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select("COUNT(c.id) as customersGetAssets")
            ->from('App:Customers', 'c')
//            ->innerJoin('o.customer','c')
            ->where('c.latitude is not NULL')
            ->andWhere('c.longitude is not NULL')
            ->andWhere('c.deliverQuantity > 0');

        if ($date_from || $date_to || $driver_id) {
            $qb->innerJoin('App:AssetsIssuedToCustomer', 'aic', 'WITH', 'aic.customer=c');
        }

        if ($date_from) {
            $qb->andWhere('DATE(aic.deliveryDate) >= DATE(:start)');
            $qb->setParameter('start', $date_from->format('Y-m-d'));
        }

        if ($date_to) {
            $qb->andWhere('DATE(aic.deliveryDate) <= DATE(:end)');
            $qb->setParameter('end', $date_to->format('Y-m-d'));
        }

        if ($driver_id) {
            $qb->andWhere('aic.driver = ' . $driver_id);
        }
        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getLastMonthOrdersByCustomer($customerId)
    {
//        $customerId=5196;
        $currentdate = new \DateTime('now');
        $lastMonthdate = (new \DateTime('now'))->modify('-30 days');

        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

//        $qb->select("SUM(orderItems.quantity) as total")
//            ->from('App:Orders', 'aOrders')
//            ->innerJoin('aOrders.orderItems', 'orderItems')
//            ->where('orderItems.product=1')
//            ->andWhere('aOrders.customer=' . $customerId)
//            ->groupBy('aOrders.customer');
//            ->orderBy('assetIssuedC.deliveryDate', 'DESC');


        $qb->select("SUM(orderItems.quantity) as total")
            ->from('App:AssetsIssuedToCustomer', 'aic')
            ->innerJoin('aic.customer', 'c')
            ->innerJoin('App:Orders', 'o','WITH','o.customer=c')
            ->innerJoin('o.orderItems', 'orderItems')
            ->where('orderItems.product=1')
            ->andWhere('aic.asset=1')
            ->andWhere('c.id=' . $customerId)
            ->groupBy('c.id');

        $qb->andWhere('DATE(o.diliverOn) >= DATE(:start)');
        $qb->setParameter('start', $lastMonthdate->format('Y-m-d'));

        $qb->andWhere('DATE(o.diliverOn) <= DATE(:end)');
        $qb->setParameter('end', $currentdate->format('Y-m-d'));

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getAssetsIssuedToCustomersRecords($date_from, $date_to, $driver_id,$customer_name)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $currentdate = new \DateTime('now');
        $lastMonthdate = (new \DateTime('now'))->modify('-30 days');

        $qb->select("c.id as customerId,SUM(orderItems.quantity) as total")
//            ->from('App:AssetsIssuedToCustomer', 'aic')
            ->from('App:Customers', 'c')
//            ->innerJoin('aic.customer', 'c')
            ->innerJoin('App:Orders', 'o','WITH','o.customer=c')
            ->innerJoin('o.orderItems', 'orderItems')
            ->where('orderItems.product=1')
//            ->andWhere('aic.asset=1')
//            ->andWhere('c.id=' . $customerId)
            ->groupBy('c.id');

        $qb->andWhere('DATE(o.diliverOn) >= DATE(:start)');
        $qb->setParameter('start', $lastMonthdate->format('Y-m-d'));

        $qb->andWhere('DATE(o.diliverOn) <= DATE(:end)');
        $qb->setParameter('end', $currentdate->format('Y-m-d'));

        $lastMonthCustomers = $qb->getQuery()->getResult();
        $lastMonthCustomersQty = [];
        foreach ($lastMonthCustomers as $cust){
            $lastMonthCustomersQty[$cust['customerId']] = $cust['total'];
        }

        $qb = $em->createQueryBuilder();
        $qb->select("c.id as customerId,SUM(orderItems.quantity) as total")
//            ->from('App:AssetsIssuedToCustomer', 'aic')
            ->from('App:Customers', 'c')
//            ->innerJoin('aic.customer', 'c')
            ->innerJoin('App:Orders', 'o','WITH','o.customer=c')
            ->innerJoin('o.orderItems', 'orderItems')
            ->where('orderItems.product=1')
//            ->andWhere('aic.asset=1')
//            ->andWhere('c.id=' . $customerId)
            ->groupBy('c.id');
//            ->groupBy('aic.customer');

        if($date_from) {
            $qb->andWhere('DATE(o.diliverOn) >= DATE(:start)');
            $qb->setParameter('start', $date_from->format('Y-m-d'));
        }
        if($date_to) {
            $qb->andWhere('DATE(o.diliverOn) <= DATE(:end)');
            $qb->setParameter('end', $date_to->format('Y-m-d'));
        }

        $dateRangeCustomers = $qb->getQuery()->getResult();
        $dateRangeCustomersQty = [];
        foreach ($dateRangeCustomers as $cust){
            $dateRangeCustomersQty[$cust['customerId']] = $cust['total'];
        }


        $qb = $em->createQueryBuilder();
        $currentdate = new \DateTime('now');
        $lastMonthdate = (new \DateTime('now'))->modify('-90 days');

        $qb->select("c.id as customerId,SUM(orderItems.quantity) as total")
//            ->from('App:AssetsIssuedToCustomer', 'aic')
            ->from('App:Customers', 'c')
//            ->innerJoin('aic.customer', 'c')
            ->innerJoin('App:Orders', 'o','WITH','o.customer=c')
            ->innerJoin('o.orderItems', 'orderItems')
            ->where('orderItems.product=1')
//            ->andWhere('aic.asset=1')
//            ->andWhere('c.id=' . $customerId)
            ->groupBy('c.id');

        $qb->andWhere('DATE(o.diliverOn) >= DATE(:start)');
        $qb->setParameter('start', $lastMonthdate->format('Y-m-d'));

        $qb->andWhere('DATE(o.diliverOn) <= DATE(:end)');
        $qb->setParameter('end', $currentdate->format('Y-m-d'));

        $last3MonthsCustomers = $qb->getQuery()->getResult();
        $last3MonthCustomersQty = [];
        foreach ($last3MonthsCustomers as $cust){
            $last3MonthCustomersQty[$cust['customerId']] = $cust['total'];
        }

        $qb = $em->createQueryBuilder();
        $qb->select("c.id as customerId,c.deliverQuantity as deliverQuantity,c.quantity as quantity")
            ->from('App:AssetsIssuedToCustomer', 'aic')
            ->innerJoin('aic.customer', 'c')
//            ->leftJoin('App:Orders', 'dorder','WITH','dorder.customer=c')
            ->leftJoin('aic.driver', 'u')
            ->andWhere('aic.asset=1')
            ->groupBy('c.id')
            ->orderBy('aic.deliveryDate', 'DESC');

//        if ($date_from) {
//            $qb->andWhere('DATE(aic.deliveryDate) >= DATE(:start)');
//            $qb->setParameter('start', $date_from->format('Y-m-d'));
//        }
//
//        if ($date_to) {
//            $qb->andWhere('DATE(aic.deliveryDate) <= DATE(:end)');
//            $qb->setParameter('end', $date_to->format('Y-m-d'));
//        }

        if ($driver_id) {
            $qb->andWhere('u.id = ' . $driver_id);
        }
        $deliveryQtyCustomersResults = $qb->getQuery()->getResult();
        $deliveryQtyCustomers = [];
        foreach ($deliveryQtyCustomersResults as $cust){
            $deliveryQtyCustomers[$cust['customerId']] = array('deliverQuantity'=>$cust['deliverQuantity'],'quantity'=>$cust['quantity']);
        }


        $qb = $em->createQueryBuilder();
        $qb->select("dorder.diliverOn as deliveryDate,c.id as customerId,CONCAT(u.username,' | ',u.salesmanName,' | ',u.firstName) as driverName,SUM(c.deliverQuantity) as deliverQuantity,SUM(c.quantity) as quantity,c.name,c.sunday,c.monday,c.tuesday,c.wednesday,c.thursday,c.friday,c.saturday,c.morning,c.afternoon,c.evening")
            ->from('App:AssetsIssuedToCustomer', 'aic')
            ->innerJoin('aic.customer', 'c')
            ->leftJoin('App:Orders', 'dorder','WITH','dorder.customer=c')
            ->leftJoin('aic.driver', 'u')
            ->andWhere('aic.asset=1')
            ->groupBy('c.id')
            ->orderBy('dorder.diliverOn', 'DESC');
        $qb->leftJoin('App:OrderItems','oi','WITH','dorder.id=oi.order');


        if ($date_from) {
            $qb->andWhere('oi.product=1');
            $qb->andWhere('DATE(dorder.diliverOn) >= DATE(:start)');
            $qb->setParameter('start', $date_from->format('Y-m-d'));
        }

        if ($date_to) {
            $qb->andWhere('oi.product=1');
            $qb->andWhere('DATE(dorder.diliverOn) <= DATE(:end)');
            $qb->setParameter('end', $date_to->format('Y-m-d'));
        }

        if ($driver_id) {
            $qb->andWhere('u.id = ' . $driver_id);
        }

        if($customer_name){
            $qb->andWhere("c.name LIKE '%" . $customer_name."%'");
        }

        $customerRecords = [];
        $results = $qb->getQuery()->getResult();
        foreach ($results as $result) {
            if (isset($last3MonthCustomersQty[$result['customerId']])) {
                $result['last3MonthsQty'] = $last3MonthCustomersQty[$result['customerId']];
            } else {
                $result['last3MonthsQty'] = '';
            }
            if (isset($lastMonthCustomersQty[$result['customerId']])) {
                $result['lastMonthQty'] = $lastMonthCustomersQty[$result['customerId']];
            } else {
                $result['lastMonthQty'] = '';
            }
            if (isset($dateRangeCustomersQty[$result['customerId']])) {
                $result['dateRangeQty'] = $dateRangeCustomersQty[$result['customerId']];
            } else {
                $result['dateRangeQty'] = '';
            }
            if (isset($deliveryQtyCustomers[$result['customerId']])) {
                $result['deliverQuantity'] = $deliveryQtyCustomers[$result['customerId']]['deliverQuantity'];
                $result['quantity'] = $deliveryQtyCustomers[$result['customerId']]['quantity'];
            } else {
                $result['deliverQuantity'] = 0;
                $result['quantity'] = 0;
            }
            $customerRecords[] = $result;
        }
        return $customerRecords;
//        return $qb->getQuery()->getResult();
    }

    public
    function getAssetsSerialRecords($date_from, $date_to, $asset_id, $serial_num, $driver_id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select("CONCAT(u.username,' | ',u.salesmanName,' | ',u.firstName) as driverName,assetSr.saleDate,c.name as customerName,assetSr.createdOn as loadDate,assetSr.id,assetSr.serialNo,asset.name,asset.unitPrice,IDENTITY(loadAssetSr.loadOut) as loadOutId")
            ->from('App:AssetSerials', 'assetSr')
            ->innerJoin('assetSr.asset', 'asset')
            ->innerJoin('assetSr.deliverBy', 'u')
            ->leftJoin('assetSr.customer', 'c')
            ->innerJoin('App:LoadOutAssetSerials', 'loadAssetSr', 'loadAssetSr.assetSerial=assetSr')
//            ->where('asset.id = :coolerDispenserId')
            ->where('1=1')
            ->groupBy('assetSr.id');
//            ->setParameter('coolerDispenserId', 2);
//;
        if ($date_from) {
            $qb->andWhere('DATE(assetSr.createdOn) >= DATE(:start)');
            $qb->setParameter('start', $date_from->format('Y-m-d'));
        }

        if ($date_to) {
            $qb->andWhere('DATE(assetSr.createdOn) <= DATE(:end)');
            $qb->setParameter('end', $date_to->format('Y-m-d'));
        }


        if ($driver_id) {
            $qb->andWhere('u.id = ' . $driver_id);
        }

        if ($asset_id) {
            $qb->andWhere('asset.id = :assetId')
                ->setParameter('assetId', $asset_id);
        }

        if ($serial_num) {
            $qb->andWhere('assetSr.serialNo = ' . $serial_num);
        }

        $allAssetsSerials = $qb->getQuery()->getResult();

        $assetSerialData = array();
        foreach ($allAssetsSerials as $assetSerial) {
            $loadOutObj = $em->getRepository('App:LoadOutAssetSerials')->findBy(array('assetSerial' => $assetSerial['id']));
            $loadOutIds = '';
            foreach ($loadOutObj as $loadOut) {
                $loadOutIds = $loadOutIds . $loadOut->getLoadOut()->getId() . ',';
            }
            $assetSerial['loadOutIds'] = rtrim($loadOutIds, ',');
            $assetSerialData[] = $assetSerial;
        }

        return $assetSerialData;
    }
}
