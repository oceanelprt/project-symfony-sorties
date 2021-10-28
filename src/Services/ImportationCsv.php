<?php

namespace App\Services;

use App\Controller\ResetPasswordController;
use App\Entity\Utilisateur;
use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ImportationCsv
{
    private string $targetDirectory;
    private string $fileName;
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordEncoder;
    private ResetPasswordController $resetPassword;
    private MailerInterface $mailer;

    public function __construct(
        string $targetDirectory,
        string $fileName,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $userPasswordHasherInterface,
        ResetPasswordController $resetPassword,
        MailerInterface $mailer)
    {
        $this->targetDirectory = $targetDirectory;
        $this->fileName = $fileName;
        $this->em = $em;
        $this->passwordEncoder = $userPasswordHasherInterface;
        $this->resetPassword = $resetPassword;
        $this->mailer = $mailer;
    }

    public function upload(UploadedFile $file)
    {
        try {
            $file->move($this->getTargetDirectory(), $this->getFileName());
        } catch (FileException $e) {
        }
    }

    public function createReader(): Reader
    {
       return Reader::createFromPath($this->getTargetDirectory().$this->getFileName())
            ->setHeaderOffset(0);
    }

    public function addUserAndCity($row): bool
    {
        $utilisateurValide = true;
        $pseudoUtilisateur = $this->em->getRepository(Utilisateur::class)
            ->findOneBy([
                'pseudo' => $row['pseudo'],
            ]);
        $emailUtilisateur = $this->em->getRepository(Utilisateur::class)
            ->findOneBy([
                'email' => $row['email'],
            ]);

        if ($pseudoUtilisateur === null && $emailUtilisateur === null) {
            $utilisateur = new Utilisateur();
            $utilisateur
                ->setPseudo($row['pseudo'])
                ->setPrenom($row['prenom'])
                ->setNom($row['nom'])
                ->setTelephone($row['telephone'])
                ->setPassword(
                    $this->passwordEncoder->hashPassword(
                        $utilisateur,
                        random_bytes(15)))
                ->setIsExpired(0)
                ->setEmail($row['email']);

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

            $this->resetPassword->processSendingPasswordResetEmail($row['email'], $this->mailer);
        } else {
            $utilisateurValide = false;
        }
        return $utilisateurValide;
    }

    public function invalidUser($row): Utilisateur
    {
        $utilisateur = new Utilisateur();
        $utilisateur
            ->setPseudo($row['pseudo'])
            ->setEmail($row['email']);
        return $utilisateur;
    }


    private function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    private function getFileName(): string
    {
        return $this->fileName;
    }
}