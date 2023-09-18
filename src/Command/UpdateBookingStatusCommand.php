<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Booking;

#[AsCommand(
    name: 'UpdateBookingStatus',
    description: 'Mettez à jour le statut des réservations en fonction de leurs dates de début et de fin par rapport à la date actuelle.',
)]
class UpdateBookingStatusCommand extends Command
{
    private $bookingRepository;
    private $entityManager;

    public function __construct(BookingRepository $bookingRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->bookingRepository = $bookingRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this->setDescription('Update the status of the bookings based on current date');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $bookings = $this->bookingRepository->findAll();

        $today = new \DateTime();

        foreach ($bookings as $booking) {
            if ($booking->getStartDate() <= $today && $booking->getEndDate() >= $today && $booking->getStatusBooking() !== Booking::STATUS_ONGOING) {
                $booking->setStatusBooking(Booking::STATUS_ONGOING);
                $this->entityManager->persist($booking);
            } elseif ($booking->getEndDate() < $today && $booking->getStatusBooking() !== Booking::STATUS_COMPLETED) {
                $booking->setStatusBooking(Booking::STATUS_COMPLETED);
                $this->entityManager->persist($booking);
            }
        }

        $this->entityManager->flush();

        $io->success('Vous avez une nouvelle commande ! Maintenant, personnalisez-la ! Passez --help pour voir vos options.');

        return Command::SUCCESS;
    }
}