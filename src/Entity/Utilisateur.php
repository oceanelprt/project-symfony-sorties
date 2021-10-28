<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 * @UniqueEntity(fields={"pseudo"}, message="Un compte avec ce pseudo existe déjà.")
 */
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true, nullable=false)
     * @Assert\Email(message = "L'email n'est pas valide.")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=180, unique=true, nullable=false)
     */
    private $pseudo;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=false)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=180, nullable=false)
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Le nom ne peut contenir que des lettres."
     * )
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=180, nullable=false)
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="Le prénom ne peut contenir que des lettres."
     * )
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\Length(max=10, maxMessage="Le numéro de téléphone ne peut pas contenir plus de 10 caractères.")
     */
    private $telephone;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="createur")
     */
    private $sortiesCreees;

    /**
     * @ORM\ManyToMany(targetEntity=Sortie::class, mappedBy="participants")
     */
    private $sorties;

    /**
     * @ORM\ManyToOne(targetEntity=Ville::class)
     */
    private $ville;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isExpired = true;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $photo;

    public function __construct()
    {
        $this->sortiesCreees = new ArrayCollection();
        $this->sorties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): string
    {
        $this->email = $email;

        return $this->email;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->pseudo;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->pseudo;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    
    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    
    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    
    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    
    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }


    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSortiesCreees(): Collection
    {
        return $this->sortiesCreees;
    }

    public function addSortiesCreees(Sortie $sortiesCreees): self
    {
        if (!$this->sortiesCreees->contains($sortiesCreees)) {
            $this->sortiesCreees[] = $sortiesCreees;
            $sortiesCreees->setCreateur($this);
        }

        return $this;
    }

    public function removeSortiesCreees(Sortie $sortiesCreees): self
    {
        if ($this->sortiesCreees->removeElement($sortiesCreees)) {
            // set the owning side to null (unless already changed)
            if ($sortiesCreees->getCreateur() === $this) {
                $sortiesCreees->setCreateur(null);
            }
        }

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
            $sorties->addParticipant($this);
        }

        return $this;
    }

    public function removeSorties(Sortie $sorties): self
    {
        if ($this->sorties->removeElement($sorties)) {
            $sorties->removeParticipant($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getPseudo();
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(Ville $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function isExpired(): bool
    {
        return $this->isExpired;
    }

    public function setIsExpired(bool $isExpired): self
    {
        $this->isExpired = $isExpired;

        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo): self
    {
        $this->photo = $photo;

        return $this;
    }
}
