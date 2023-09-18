<?php

namespace App\Tests\Command;

use App\Command\UpdateBookingStatusCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class UpdateBookingStatusCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('UpdateBookingStatus');
        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        // Vérifiez que le texte de sortie contient une certaine chaîne.
        // Adaptez ceci en fonction de ce que votre commande est censée afficher.
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Vous avez une nouvelle commande', $output);

        // TODO: Ajouter d'autres assertions pour tester les modifications de données réelles, etc.
    }
}
