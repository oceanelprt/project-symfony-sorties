<?php

namespace App\Command;

use App\Repository\SortieRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ArchivingCommand extends Command
{
    private $sortieRepository;
    private $archiveRepository;

    protected static $defaultName = 'app:archiving:command';

    public function __construct(SortieRepository $sortieRepository)
    {
        $this->sortieRepository = $sortieRepository;

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

        $io->note('Archivage des anciennes sorties');


        $this->sortieRepository->deleteOldSortie();

        $io->success(sprintf('j\'ai delete plein de trucs'));

       return 0;
    }
}