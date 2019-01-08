<?php

namespace App\Repository;

use function GuzzleHttp\Psr7\modify_request;

class CustomersRepository extends \Doctrine\ORM\EntityRepository
{
    public function getLongLatCustomers()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select("c")
            ->from('App:Customers', 'c')
            ->where('c.longitude IS NOT NULL')
            ->andWhere('c.latitude IS NOT NULL')
            ->andWhere("c.address LIKE :loc OR c.address = :loc1 OR c.address IS NULL");
        $qb->setParameter('loc', '%Service Not Available%');
        $qb->setParameter('loc1', 'Unnamed Road');
        $customersData = $qb->getQuery()->getResult();
        return $customersData;
    }

    public function getFilterActiveCustomers($dateFrom = false, $dateTo = false, $offsetCustomer = false)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select("c")
            ->from('App:Customers', 'c')
            ->where('c.longitude IS NOT NULL')
            ->andWhere('c.latitude IS NOT NULL');

        if ($dateFrom and $dateTo) {
            $qb->andWhere('DATE(c.createdOn) >= DATE(:dateFrom) AND DATE(c.createdOn) <= DATE(:dateTo)');
            $qb->setParameter('dateFrom', $dateFrom);
            $qb->setParameter('dateTo', $dateTo);
        }

        if ($offsetCustomer) {
            $qb->andWhere('c.id>=:offset');
            $qb->andWhere('c.isApproved=1');
            $qb->setParameter('offset', $offsetCustomer);
        }

        $customersData = $qb->getQuery()->getResult();
        return $customersData;
    }

    public function getFilterCustomersDebitLogs($dateFrom = false, $dateTo = false)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select("cd")
            ->from('App:CustomerDebitLogs', 'cd');

        if ($dateFrom and $dateTo) {
            $qb->andWhere('DATE(cd.createdOn) >= DATE(:dateFrom) AND DATE(cd.createdOn) <= DATE(:dateTo)');
            $qb->setParameter('dateFrom', $dateFrom);
            $qb->setParameter('dateTo', $dateTo);
        }

        $customersData = $qb->getQuery()->getResult();
        return $customersData;
    }

    public function getFilterAssetsIssued($dateFrom = false, $dateTo = false)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select("a")
            ->from('App:AssetsIssuedToCustomer', 'a')
            ->where('a.isApproved=1');

        if ($dateFrom and $dateTo) {
            $qb->andWhere('DATE(a.createdOn) >= DATE(:dateFrom) AND DATE(a.createdOn) <= DATE(:dateTo)');
            $qb->setParameter('dateFrom', $dateFrom);
            $qb->setParameter('dateTo', $dateTo);
        }

        $customersData = $qb->getQuery()->getResult();
        return $customersData;
    }

    public function getLongLatCouponCodes()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select("c")
            ->from('App:CouponCodes', 'c')
            ->where('c.longitude IS NOT NULL')
            ->andWhere('c.latitude IS NOT NULL')
            ->andWhere("c.address LIKE :loc OR c.address = :loc1 OR c.address IS NULL");
        $qb->setParameter('loc', '%Service Not Available%');
        $qb->setParameter('loc1', 'Unnamed Road');
        $couponCodesData = $qb->getQuery()->getResult();
        return $couponCodesData;
    }

    public function getLongLatAssetsIssued()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select("c")
            ->from('App:AssetsIssuedToCustomer', 'c')
            ->where('c.longitude IS NOT NULL')
            ->andWhere('c.latitude IS NOT NULL')
            ->andWhere("c.address LIKE :loc OR c.address = :loc1 OR c.address IS NULL");
        $qb->setParameter('loc', '%Service Not Available%');
        $qb->setParameter('loc1', 'Unnamed Road');
        $assetsIssuedData = $qb->getQuery()->getResult();
        return $assetsIssuedData;
    }

    public function getLongLatDriverLogs()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select("c")
            ->from('App:DriverLogs', 'c')
            ->where('c.longitude IS NOT NULL')
            ->andWhere('c.latitude IS NOT NULL')
            ->andWhere("c.address LIKE :loc OR c.address = :loc1 OR c.address IS NULL");
        $qb->setParameter('loc', '%Service Not Available%');
        $qb->setParameter('loc1', 'Unnamed Road');
        $driverLogsData = $qb->getQuery()->getResult();
        return $driverLogsData;
    }

    public function getLongLatCreatedOrders()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select("c")
            ->from('App:Orders', 'c')
            ->where('c.createLongitude IS NOT NULL')
            ->andWhere('c.createLatitude IS NOT NULL')
            ->andWhere("c.createAddress LIKE :loc OR c.createAddress = :loc1 OR c.createAddress IS NULL");
        $qb->setParameter('loc', '%Service Not Available%');
        $qb->setParameter('loc1', 'Unnamed Road');
        $createOrdersData = $qb->getQuery()->getResult();
        return $createOrdersData;
    }

    public function getLongLatFinalOrders()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select("c")
            ->from('App:Orders', 'c')
            ->where('c.finalLongitude IS NOT NULL')
            ->andWhere('c.finalLatitude IS NOT NULL')
            ->andWhere("c.finalAddress LIKE :loc OR c.finalAddress = :loc1 OR c.finalAddress IS NULL");
        $qb->setParameter('loc', '%Service Not Available%');
        $qb->setParameter('loc1', 'Unnamed Road');
        $createOrdersData = $qb->getQuery()->getResult();
        return $createOrdersData;
    }

    public function getFilteredOrderRecords($data)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select("c.balance,c.name as customerName,t.type as typeId,cc.name as transactionType,t.taxAmount as tax,t.amount as transactionAmount,t.amountWithoutTax as transactionAmountWithoutTax,o.id as orderId,i.id as invoiceId,t.id as transactionId,i.amount as invoiceAmount")
            ->from('App:Transactions', 't')
            ->innerJoin('t.order', 'o')
            ->innerJoin('t.invoice', 'i')
            ->innerJoin('t.customer', 'c')
            ->leftJoin('App:CustomerCategories', 'cc', 'WITH', 'cc.id=t.type')
            ->where('o.deliverBy = :driver')
            ->setParameter('driver', $data['driverId']);

        if (isset($data['dateFrom']) and isset($data['dateTo'])) {
            $qb->andWhere('DATE(o.createdOn) >= DATE(:start)');
            $qb->setParameter('start', $data['dateFrom']->format('Y-m-d'));

            $qb->andWhere('DATE(o.createdOn) <= DATE(:end)');
            $qb->setParameter('end', $data['dateTo']->format('Y-m-d'));
        }

        if (isset($data['customerId'])) {
            $qb->andWhere('c.id=:customer')
                ->setParameter('customer', $data['customerId']);
        }
        if (isset($data['paymentTerm'])) {
            $qb->andWhere('t.type=:paymentType')
                ->setParameter('paymentType', $data['paymentTerm']);
        }

        $ordersData = $qb->getQuery()->getResult();
        return $ordersData;
    }

    public function getFilteredOrderRecordsForNewTransactions($data)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select("c.balance,c.name as customerName,t.type as typeId,cc.name as transactionType,SUM(t.taxAmount) as tax,SUM(t.amount) as transactionAmount,SUM(t.amountWithoutTax) as transactionAmountWithoutTax,o.id as orderId,i.id as invoiceId,t.id as transactionId,i.amount as invoiceAmount,i.amountAfterTax as invoiceWithTax")
            ->from('App:Invoices', 'i')
            ->innerJoin('i.order', 'o')
//            ->innerJoin('t.invoice', 'i')
            ->innerJoin('i.customer', 'c')
            ->leftJoin('App:Transactions', 't', 'WITH', 'i.id=t.invoice')
            ->leftJoin('App:CustomerCategories', 'cc', 'WITH', 'cc.id=t.type')
            ->where('o.deliverBy = :driver')
            ->groupBy('i.id')
            ->setParameter('driver', $data['driverId']);

        if (isset($data['dateFrom']) and isset($data['dateTo'])) {
            $qb->andWhere('DATE(o.createdOn) >= DATE(:start)');
            $qb->setParameter('start', $data['dateFrom']->format('Y-m-d'));

            $qb->andWhere('DATE(o.createdOn) <= DATE(:end)');
            $qb->setParameter('end', $data['dateTo']->format('Y-m-d'));
        }

        if (isset($data['customerId'])) {
            $qb->andWhere('c.id=:customer')
                ->setParameter('customer', $data['customerId']);
        }
        if (isset($data['paymentTerm'])) {
            $qb->andWhere('t.type=:paymentType')
                ->setParameter('paymentType', $data['paymentTerm']);
        }

        $ordersData = $qb->getQuery()->getResult();
        return $ordersData;
    }

    public function getCustomerByPhoneEmptyUserNameAndPassword($phone)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select("c")
            ->from('App:Customers', 'c')
            ->where('c.contactPersonContact = :phone')
            ->setParameter('phone', $phone)
            ->andWhere('c.username IS NULL')
            ->andWhere('c.password IS NULL');
        $customer = $qb->getQuery()->getResult();
        return $customer;
    }

    public function getCustomerByPhoneNonEmptyUserNameAndPassword($phone)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select("c")
            ->from('App:Customers', 'c')
            ->where('c.contactPersonContact = :phone')
            ->setParameter('phone', $phone)
            ->andWhere('c.username IS NOT NULL')
            ->andWhere('c.password IS NOT NULL');
        $customer = $qb->getQuery()->getResult();
        return $customer;
    }

    public function getFilteredInvoices($filters = [])
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $customerIds = [];
        if (isset($filters['customer_id']) and $filters['customer_id']) {
            $customerIds = explode(',', $filters['customer_id']);
        }

        $qb->select("i")
            ->from('App:Invoices', 'i')
            ->where('DATE(i.createdOn) >= :startDate')
            ->andWhere('DATE(i.createdOn) <= :endDate')
            ->setParameter('startDate', (isset($filters['dateFrom']) ? $filters['dateFrom'] : null))
            ->setParameter('endDate', (isset($filters['dateTo']) ? $filters['dateTo'] : null));

        $qb->innerJoin('i.customer', 'c');
        $qb->andWhere("c.id IN (:customerName)")
            ->setParameter('customerName', $customerIds);

        if (isset($filters['driver']) and $filters['driver']) {
            $qb->innerJoin('i.order', 'o');
            $qb->andWhere("o.deliverBy = :driver")
                ->setParameter('driver', $filters['driver']);
        }

        if (isset($filters['summary_generated']) and $filters['summary_generated']) {
            $qb->andWhere("i.summaryInvoice is not NULL");
        } else {
            $qb->andWhere("i.summaryInvoice is NULL");
        }

//        var_dump($qb->getQuery()->getSQL());die;
        $invoices = $qb->getQuery()->getResult();
        return $invoices;
    }
}