<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EtatRepository::class)
 */
class Etat
{
    const ETAT_NON_COMMENCE = 'Non commencé';
    const ETAT_COMMENCE = 'Commencé';
    const ETAT_TERMINE = 'Terminé';
    const ETAT_ANNULE = 'Annulé';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $etat;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="etat")
     */
    private $sorties;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorties(Sortie $sorties): self
    {
        if (!$this->sorties->contains($sorties)) {
            $this->sorties[] = $sorties;
            $sorties->setEtat($this);
        }

        return $this;
    }

    public function removeSorties(Sortie $sorties): self
    {
        if ($this->sorties->removeElement($sorties)) {
            // set the owning side to null (unless already changed)
            if ($sorties->getEtat() === $this) {
                $sorties->setEtat(null);
            }
        }

        return $this;
    }
}
