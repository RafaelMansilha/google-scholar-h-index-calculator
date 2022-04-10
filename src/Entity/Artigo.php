<?php

namespace App\Entity;

use App\Repository\ArtigoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArtigoRepository::class)
 */
class Artigo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", length=2500)
     */
    private $titulo;

    /**
     * @ORM\Column(type="integer")
     */
    private $numeroCitacoes;

    /**
     * @ORM\Column(type="integer")
     */
    private $ano;

    /**
     * @ORM\Column(type="array")
     */
    private $autores = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $periodicoNome;

    /**
     * @ORM\OneToMany(targetEntity=Citacao::class, mappedBy="artigo", cascade={"persist"})
     */
    private $citacoes;

    /**
     * @ORM\ManyToOne(targetEntity=Periodico::class, inversedBy="artigos", cascade={"persist"})
     */
    private $periodico;

    /**
     * @ORM\ManyToOne(targetEntity=Periodico::class, inversedBy="artigosAutores", cascade={"persist"})
     */
    private $periodicoAutores;

    /**
     * @ORM\ManyToOne(targetEntity=Periodico::class, inversedBy="artigosPeriodicos", cascade={"persist"})
     */
    private $periodicoPeriodicos;

    /**
     * @ORM\ManyToOne(targetEntity=Periodico::class, inversedBy="artigosAutoresEPeriodicos", cascade={"persist"})
     */
    private $periodicosAutoresEPeriodicos;

    /**
     * @ORM\ManyToOne(targetEntity=Periodico::class, inversedBy="artigosAutoresDoPeriodico", cascade={"persist"})
     */
    private $periodicoAutoresDoPeriodico;

    public function __construct()
    {
        $this->citacoes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getNumeroCitacoes(): ?int
    {
        return $this->numeroCitacoes;
    }

    public function setNumeroCitacoes(int $numeroCitacoes): self
    {
        $this->numeroCitacoes = $numeroCitacoes;

        return $this;
    }

    public function getAno(): ?int
    {
        return $this->ano;
    }

    public function setAno(int $ano): self
    {
        $this->ano = $ano;

        return $this;
    }

    public function getAutores(): ?array
    {
        return $this->autores;
    }

    public function setAutores(array $autores): self
    {
        $this->autores = $autores;

        return $this;
    }

    public function getPeriodicoNome(): ?string
    {
        return $this->periodicoNome;
    }

    public function setPeriodicoNome(string $periodicoNome): self
    {
        $this->periodicoNome = $periodicoNome;

        return $this;
    }

    /**
     * @return Collection|Citacao[]
     */
    public function getCitacoes(): Collection
    {
        return $this->citacoes;
    }

    public function addCitaco(Citacao $citaco): self
    {
        if (!$this->citacoes->contains($citaco)) {
            $this->citacoes[] = $citaco;
            $citaco->setArtigo($this);
        }

        return $this;
    }

    public function removeCitaco(Citacao $citaco): self
    {
        if ($this->citacoes->removeElement($citaco)) {
            // set the owning side to null (unless already changed)
            if ($citaco->getArtigo() === $this) {
                $citaco->setArtigo(null);
            }
        }

        return $this;
    }

    public function getPeriodico(): ?Periodico
    {
        return $this->periodico;
    }

    public function setPeriodico(?Periodico $periodico): self
    {
        $this->periodico = $periodico;

        return $this;
    }

    public function getPeriodicoAutores(): ?Periodico
    {
        return $this->periodicoAutores;
    }

    public function setPeriodicoAutores(?Periodico $periodicoAutores): self
    {
        $this->periodicoAutores = $periodicoAutores;

        return $this;
    }

    public function getPeriodicoPeriodicos(): ?Periodico
    {
        return $this->periodicoPeriodicos;
    }

    public function setPeriodicoPeriodicos(?Periodico $periodicoPeriodicos): self
    {
        $this->periodicoPeriodicos = $periodicoPeriodicos;

        return $this;
    }

    public function getPeriodicosAutoresEPeriodicos(): ?Periodico
    {
        return $this->periodicosAutoresEPeriodicos;
    }

    public function setPeriodicosAutoresEPeriodicos(?Periodico $periodicosAutoresEPeriodicos): self
    {
        $this->periodicosAutoresEPeriodicos = $periodicosAutoresEPeriodicos;

        return $this;
    }

    public function resetCitacoes(){
        $this->citacoes = new ArrayCollection();
    }

    public function getPeriodicoAutoresDoPeriodico(): ?Periodico
    {
        return $this->periodicoAutoresDoPeriodico;
    }

    public function setPeriodicoAutoresDoPeriodico(?Periodico $periodicoAutoresDoPeriodico): self
    {
        $this->periodicoAutoresDoPeriodico = $periodicoAutoresDoPeriodico;

        return $this;
    }
}
