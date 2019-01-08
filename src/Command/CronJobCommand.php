<?php

namespace App\Command;

use App\Libraries\CustomerManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class CronJobCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:updateCustomersLocation')

            // the short description shown while running "php bin/console list"
            ->setDescription('Update the customers location using api.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to Update the customers location using api.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Location Updating',
            '',
        ]);

        $customerManager = $this->getContainer()->get(CustomerManager::class);
//        $customerManager->updateCustomersBalance();
        $customerManager->updateCustomerLocations();
//        $output->writeln('Finished Customer Table....');
//        $customerManager->updateCouponCodeLocations();
//        $output->writeln('Finished Coupon Code Table....');
//        $customerManager->updateAssetsIssuedLocations();
//        $output->writeln('Finished Assets Issued Table....');
////        $customerManager->updateDriverLogsLocations();
////        $output->writeln('Finished Driver Logs Table....');
//        $customerManager->updateOrdersCreatedLocations();
//        $output->writeln('Finished Order Create Table....');
//        $customerManager->updateOrdersFinalLocations();
        $output->writeln('Finished Order Finalize....');
    }
}