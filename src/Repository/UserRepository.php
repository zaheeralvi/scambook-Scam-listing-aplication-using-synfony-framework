<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getAllDrivers()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('d')
            ->from('App:User', 'd')
            ->innerJoin('d.userRole', 'ur')
            ->where("ur.roleType = 'ROLE_DRIVER'")
            ->orderBy("d.id", 'DESC');
        $allDrivers = $qb->getQuery()->getResult();

        return $allDrivers;
    }

    public function getAllUsersExceptDrivers()
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('d')
            ->from('App:User', 'd')
            ->innerJoin('d.userRole', 'ur')
            ->where("ur.roleType != 'ROLE_DRIVER'")
            ->orderBy("d.id", 'DESC');
        $allDrivers = $qb->getQuery()->getResult();

        return $allDrivers;
    }

    public function getAllFilteredCustomers($date_from, $date_to, $driver_id, $price)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select("p.name as productName,CONCAT(d.username,' | ',d.salesmanName,' | ',d.firstName) as driverName,cp.price,c.name,cp.createdOn as datecreated")
            ->from('App:CustomerProductPrices', 'cp')
            ->innerJoin('cp.customer', 'c')
            ->innerJoin('cp.product', 'p')
            ->innerJoin('App:User', 'd', 'WITH', 'd.userRoute = c.route')
//            ->groupBy('c.id')
            ->orderBy('cp.createdOn', 'DESC');

        if ($date_from) {
            $qb->andWhere('DATE(cp.createdOn) >= DATE(:start)');
            $qb->setParameter('start', $date_from->format('Y-m-d'));
        }

        if ($date_to) {
            $qb->andWhere('DATE(cp.createdOn) <= DATE(:end)');
            $qb->setParameter('end', $date_to->format('Y-m-d'));
        }

        if ($driver_id) {
            $qb->andWhere('d.id = ' . $driver_id);
        }

        if (isset($price['type']) and isset($price['value'])) {
            if ($price['type'] == 1 and $price['value']) {
                $qb->andWhere('cp.price = ' . $price['value']);
            } elseif ($price['type'] == 2 and $price['value']) {
                $qb->andWhere('cp.price > ' . $price['value']);
            } elseif ($price['type'] == 3 and $price['value']) {
                $qb->andWhere('cp.price < ' . $price['value']);
            }
        }

        return $qb->getQuery()->getResult();
    }

    public function getCustomerDiscountReportRecords($date_from, $date_to, $driver_id, $productId, $emirate, $supervisor)
    {
        $em = $this->getEntityManager();
        $supervisorDriversIds = null;
        if($supervisor) {
            $qb = $em->createQueryBuilder();
            $qb->select("u.id")
                ->from('App:User', 'u')
                ->innerJoin('u.reportUser', 'r')
                ->where('r.id = ' . $supervisor)
                ->andWhere('u.userRole = 3');
            $supervisorDrivers = $qb->getQuery()->getResult();
            $supervisorDriversIds = array();
            foreach ($supervisorDrivers as $d) {
                $supervisorDriversIds[] = $d['id'];
            }
            $supervisorDriversIds = implode(',', $supervisorDriversIds);
        }

        if ($driver_id) {
            $driver_id = implode(',',$driver_id);
        }

        $qb = $em->createQueryBuilder();
        $qb->select("c.id,SUM(oi.totalAmount) as totalInvoices")
            ->from('App:OrderItems', 'oi')
            ->innerJoin('oi.order', 'o')
            ->innerJoin('o.customer', 'c')
            ->innerJoin('oi.product', 'p')
            ->groupBy('c.id');

        if ($productId) {
            $qb->where('p.id=' . $productId);
        }

        if ($date_from) {
            $qb->andWhere('DATE(o.createdOn) >= DATE(:start)');
            $qb->setParameter('start', $date_from->format('Y-m-d'));
        }

        if ($date_to) {
            $qb->andWhere('DATE(o.createdOn) <= DATE(:end)');
            $qb->setParameter('end', $date_to->format('Y-m-d'));
        }

        if ($driver_id) {
//            $qb->andWhere('o.deliverBy = ' . $driver_id);
            $qb->andWhere('o.deliverBy IN ('.$driver_id.')');
        }
        if ($emirate) {
            $qb->andWhere('c.emiratesProvinceState = ' . $emirate);
        }

        if ($supervisor and $supervisorDriversIds) {
            $qb->innerJoin('c.route', 'r');
            $qb->innerJoin('App:User', 'd', 'WITH', 'd.userRoute=r.id');
            /*$qb->innerJoin('d.reportUser', 's');*/
            $qb->andWhere('d.id IN (' . $supervisorDriversIds . ')');
        } elseif($supervisorDriversIds=="" and $supervisorDriversIds!==null) {
            $qb->andWhere('c.id=0');
        }

        $orderItems = $qb->getQuery()->getResult();
        $customerInvoices = array();
        foreach ($orderItems as $orderItem) {
            $customerInvoices[$orderItem['id']] = $orderItem['totalInvoices'];
        }

        $qb = $em->createQueryBuilder();
        $qb->select("c.id,SUM(oi.quantity) as productQuantity")
            ->from('App:OrderItems', 'oi')
            ->innerJoin('oi.order', 'o')
            ->innerJoin('o.customer', 'c')
            ->innerJoin('oi.product', 'p')
            ->groupBy('c.id');

        if ($productId) {
            $qb->where('p.id=' . $productId);
        }

        if ($date_from) {
            $qb->andWhere('DATE(o.createdOn) >= DATE(:start)');
            $qb->setParameter('start', $date_from->format('Y-m-d'));
        }

        if ($date_to) {
            $qb->andWhere('DATE(o.createdOn) <= DATE(:end)');
            $qb->setParameter('end', $date_to->format('Y-m-d'));
        }

        if ($emirate) {
            $qb->andWhere('c.emiratesProvinceState = ' . $emirate);
        }
        if ($supervisor and $supervisorDriversIds) {
            /*$qb->innerJoin('c.route', 'r');
            $qb->innerJoin('App:User', 'd', 'WITH', 'd.userRoute=r.id');
            $qb->innerJoin('d.reportUser', 's');
            $qb->andWhere('s.id = ' . $supervisor);*/
//            $qb->andWhere('c.route IN ('.$supervisorRoutesIds.')');

            $qb->innerJoin('c.route', 'r');
            $qb->innerJoin('App:User', 'd', 'WITH', 'd.userRoute=r.id');
            /*$qb->innerJoin('d.reportUser', 's');*/
            $qb->andWhere('d.id IN (' . $supervisorDriversIds . ')');
        } elseif($supervisorDriversIds=="" and $supervisorDriversIds!==null) {
            $qb->andWhere('c.id=0');
        }
        if ($driver_id) {
            $qb->andWhere('o.deliverBy IN (' . $driver_id.')');
        }

        $orderItems = $qb->getQuery()->getResult();
        $customerProductQuantity = array();
        foreach ($orderItems as $orderItem) {
            $customerProductQuantity[$orderItem['id']] = $orderItem['productQuantity'];
        }

//        $qb = $em->createQueryBuilder();
//        $qb->select("oi.unitPrice,c.id as customerId,Identity(oi.product) as productId")
//            ->from('App:OrderItems', 'oi')
//            ->from('App:Orders', 'o')
//            ->innerJoin('o.customer', 'c')
//            ->andWhere('c.longitude IS NOT NULL')
//            ->andWhere('c.latitude IS NOT NULL')
//            ->andWhere('o.orderStatus =3')
//            ->groupBy('c.id')
//            ->addGroupBy('oi.product');
//        $customerProductPrices = $qb->getQuery()->getResult();
//        var_dump(count($customerProductPrices));die;
        $qb = $em->createQueryBuilder();
//        CONCAT(sup.username,' | ',sup.salesmanName,' | ',sup.firstName)
        $qb->select("sup.firstName as supName,eps.name as emiratesProvinceState,CONCAT(d.username,' | ',d.salesmanName,' | ',d.firstName) as driverName,c.id as customerId,o.createdOn as date,p.name as productName,cp.price as customerBasePrice,p.basePrice as productBasePrice,c.name as customerName")
            ->from('App:CustomerProductPrices', 'cp')
            ->innerJoin('cp.customer', 'c')
            ->innerJoin('App:Orders', 'o', 'WITH', 'o.customer=c.id')
            ->innerJoin('cp.product', 'p')
            ->innerJoin('App:User', 'd', 'WITH', 'd.userRoute = c.route')
            ->leftJoin('d.reportUser','sup')
            ->leftJoin('c.emiratesProvinceState','eps')
            ->andWhere('c.longitude IS NOT NULL')
            ->andWhere('c.latitude IS NOT NULL')
            ->groupBy('c.id');

        if ($date_from) {
            $qb->andWhere('DATE(o.createdOn) >= DATE(:start)');
            $qb->setParameter('start', $date_from->format('Y-m-d'));
        }

        if ($date_to) {
            $qb->andWhere('DATE(o.createdOn) <= DATE(:end)');
            $qb->setParameter('end', $date_to->format('Y-m-d'));
        }

        if ($driver_id) {
//            $qb->andWhere('d.id = ' . $driver_id);

            $qb->andWhere('d.id IN( ' . $driver_id.')');
        }

        if ($emirate) {
            $qb->andWhere('c.emiratesProvinceState = ' . $emirate);
        }

        if ($supervisor and $supervisorDriversIds) {
//            $qb->innerJoin('c.route', 'r');
//            $qb->innerJoin('App:User','d','WITH','d.userRoute=r.id');
//            $qb->innerJoin('d.reportUser', 's');
//            $qb->andWhere('s.id = ' . $supervisor);
//            $qb->andWhere('c.route IN ('.$supervisorRoutesIds.')');
//            $qb->innerJoin('c.route', 'r');
//            $qb->innerJoin('App:User', 'd', 'WITH', 'd.userRoute=r.id');
            /*$qb->innerJoin('d.reportUser', 's');*/
            $qb->andWhere('d.id IN (' . $supervisorDriversIds . ')');
        } elseif($supervisorDriversIds=="" and $supervisorDriversIds!==null) {
            $qb->andWhere('c.id=0');
        }
        if ($productId) {
            $qb->andWhere('p.id=' . $productId);
        }

        $customers = $qb->getQuery()->getResult();
        foreach ($customers as $key => $customer) {
            if (isset($customerInvoices[$customer['customerId']])) {
                $customers[$key]['totalInvoices'] = $customerInvoices[$customer['customerId']];
            } else {
                $customers[$key]['totalInvoices'] = 0;
            }
            if (isset($customerProductQuantity[$customer['customerId']])) {
                $customers[$key]['productQuantity'] = $customerProductQuantity[$customer['customerId']];
            } else {
                $customers[$key]['productQuantity'] = 0;
            }
        }


        return $customers;
    }
    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('u')
            ->where('u.something = :value')->setParameter('value', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
