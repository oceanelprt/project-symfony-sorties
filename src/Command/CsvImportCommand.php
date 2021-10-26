<?php

namespace App\Command;

use App\Entity\Utilisateur;
use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class CsvImportCommand
 * @package AppBundle\ConsoleCommand
 */
class CsvImportCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    private $passwordEncoder;

    /**
     * CsvImportCommand constructor.
     *
     * @param EntityManagerInterface $em
     *
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder )
    {
        parent::__construct();

        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Configure
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function configure()
    {
        $this
            ->setName('app:csv:import')
            ->setDescription('Importation des utilisateurs')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Début de l\'importation...');

        $reader = Reader::createFromPath('%kernel.root_dir%/../assets/upload/csv/utilisateurs.csv')
            ->setHeaderOffset(0);
        $io->progressStart(iterator_count($reader->getRecords()));

        foreach ($reader as $row) {

            $pseudoUtilisateur = $this->em->getRepository(Utilisateur::class)
                ->findOneBy([
                    'pseudo' => $row['pseudo'],
                ]);
            $emailUtilisateur = $this->em->getRepository(Utilisateur::class)
                ->findOneBy([
                    'email' => $row['email'],
                ]);

                $utilisateur = new Utilisateur();
                $utilisateur
                    ->setPseudo($row['pseudo'])
                    ->setEmail($row['email']);

            if ($pseudoUtilisateur === null && $emailUtilisateur === null) {
                $utilisateur
                    ->setPrenom($row['prenom'])
                    ->setTelephone($row['telephone'])
                    ->setPassword(
                        $this->passwordEncoder->encodePassword(
                            $utilisateur,
                            random_bytes(15)))
                    ->setIsExpired(0)
                    ->setNom($row['nom']);

                $this->em->persist($utilisateur);

                $ville = $this->em->getRepository(Ville::class)
                    ->findOneBy([
                        'nom' => $row['ville'],
                        'codePostal' => $row['codePostal']
                    ]);
                if ($ville === null) {
                    $ville = (new Ville())
                        ->setNom($row['ville'])
                        ->setCodePostal($row['codePostal']);
                    $this->em->persist($ville);
                    $this->em->flush();
                }
                $utilisateur->setVille($ville);
            } else {
                // TODO créer un tableau d'utilisateur non persist
                // à retourner à la fin
                echo ($utilisateur);
            }
            $io->progressAdvance();
        }

        $this->em->flush();

        $io->progressFinish();
        $io->success('L\'importation est terminé');
    }
}