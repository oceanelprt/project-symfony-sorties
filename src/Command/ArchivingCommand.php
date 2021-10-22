<?php

namespace App\Command;

use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ArchivingCommand extends Command
{
    private $sortieRepository;
    private $etatRepository;

    protected static $defaultName = 'app:archiving:command';

    public function __construct(SortieRepository $sortieRepository, EtatRepository $etatRepository)
    {
        $this->sortieRepository = $sortieRepository;
        $this->etatRepository = $etatRepository;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('MAJ de la BDD')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->note('gestion des sorties');

        // recuperation de l'id ETAT_OUVERT
        $etatOuvertId = $this->etatRepository->findOneBy(['etat' => 'ETAT_OUVERT'])->getId();

        // recuperation de l'id ETAT_FERME
        $etatFermeId = $this->etatRepository->findOneBy(['etat' => 'ETAT_FERME'])->getId();

        // recuperation de l'id ETAT_EN_COURS
        $etatEnCoursId = $this->etatRepository->findOneBy(['etat' => 'ETAT_EN_COURS'])->getId();

        $this->sortieRepository->changeEtatForInProgress($etatEnCoursId, $etatOuvertId);
        $this->sortieRepository->changeEtatForClosing($etatFermeId, $etatEnCoursId);

        $this->sortieRepository->archivingOldSortie();

        $io->success(sprintf('j\'ai archivé et modifié pleins de trucs'));

       return 0;
    }
}