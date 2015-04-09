<?php

namespace Console\Command;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Silex\Application;

/**
 * Description of SetNextRebillDate
 *
 * @author lbreleur
 */
class InitNextRecurringDate extends BaseCommand {

    protected function configure() {
        $this
                ->setName("ddc:init-next-recurring-date")
                ->setDescription("Init the next recurring date for all memebrship in reel_gateway_billing_schedule")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $db = $this->getDatabaseLayer();

        $entities = $db->fetchAll("SELECT id FROM reel_gateway_billing_schedules");

        $progress = new ProgressBar($output, count($entities));
        $progress->start();

        $output->write("\nInit start");

        $currentHour = $this->generateCurrentHour();
        foreach ($entities as $entity) {
            $db->executeQuery("UPDATE reel_gateway_billing_schedules SET next_recurring_date =" . $currentHour->getTimestamp() . " WHERE id =" . $entity['id'] . ";");
            $progress->advance();
        }

        $progress->finish();
        $output->write("\nDone\n");
    }

    private function generateCurrentHour() {
        $now = new \DateTime('NOW');
        return new \DateTime($now->format("Y-m-d H:00:00"));
    }

    private function getDatabaseLayer() {

        $config = \App\ConfigContainer::getInstance()->retrieve("database");

        $silex = new Application();
        $silex->register(new \Silex\Provider\DoctrineServiceProvider(), $config);

        return $silex['db'];
    }

}
