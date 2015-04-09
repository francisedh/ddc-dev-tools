<?php

namespace Console\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Silex\Application;

/**
 * Description of CleanDatabase
 *
 * @author lbreleur
 */
class CleanDatabase extends BaseCommand {

    protected function configure() {
        $this
                ->setName("ddc:clean-database")
                ->setDescription("Clean the ddc database")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $db = $this->getDatabaseLayer();

        $progress = new ProgressBar($output, count($this->getTables()));
        $progress->start();

        $output->write("\nClean up start");
        foreach ($this->getTables() as $table) {
            try {
                $db->executeQuery("DELETE FROM `$table`;");
            } catch (\Doctrine\DBAL\Exception\TableNotFoundException $exc) {
                //Log that the database doesn't exist
            }

            $progress->advance();
        }

        $progress->finish();
        $output->write("\nDone\n");
    }

    private function getDatabaseLayer() {
        $config = \App\ConfigContainer::getInstance()->retrieve("database");

        $silex = new Application();
        $silex->register(new \Silex\Provider\DoctrineServiceProvider(), $config);

        return $silex['db'];
    }

    private function getTables() {
        return array(
            'return_request'
            , 'order_exchange'
            , 'membership_to_ship'
            , 'ups_shipping_label'
            , 'ups_form'
            , 'package'
            , 'blacklist'
            , 'members'
            , 'members_detail'
            , 'membership_exchange'
            , 'reel_gateway_billing_schedules'
            , 'ddc_custom_stepdown'
            , 'reel_gateway_transactions'
            , 'reel_gateway_members'
            , 'reel_gateway_events'
            , 'reel_gateway_transaction_disputes'
            , 'rebilling_schedule_cron_entries_51'
            , 'pending_request'
            , 'order_exchange'
            , 'member_memberships'
            , 'transaction_input_output'
            , 'transactions'
            , 'refunded_transactions'
            , 'p1_upgrade_request'
            , 'p1_upgrade_request_status'
        );
    }

}
