<?php

namespace App\Entity;

use App\Repository\PeriodicoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PeriodicoRepository::class)
 */
class Periodico
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=True)
     */
    private $indiceH5;

    /**
     * @ORM\Column(type="integer", nullable=True)
     */
    private $medianaH5;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $nome;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $issn;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $origem;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $qualis;

    /**
     * @ORM\Column(type="array", nullable=True)
     */
    private $possiveisNomes = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $areaDoConhecimento;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity=Artigo::class, mappedBy="periodico", cascade={"persist"})
     */
    private $artigos;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $grupo;

    /**
     * @ORM\OneToMany(targetEntity=Artigo::class, mappedBy="periodicoAutores", cascade={"persist"})
     */
    private $artigosAutores;

    /**
     * @ORM\OneToMany(targetEntity=Artigo::class, mappedBy="periodicoPeriodicos", cascade={"persist"})
     */
    private $artigosPeriodicos;

    /**
     * @ORM\OneToMany(targetEntity=Artigo::class, mappedBy="periodicosAutoresEPeriodicos", cascade={"persist"})
     */
    private $artigosAutoresEPeriodicos;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $indiceH5Autores;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $indiceH5Periodicos;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $indiceH5AutoresEPeriodicos;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $medianaH5Autores;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $medianaH5Periodicos;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $medianaH5AutoresEPeriodicos;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $IndiceH5AutoresDoPeriodico;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $medianaH5AutoresDoPeriodico;

    /**
     * @ORM\OneToMany(targetEntity=Artigo::class, mappedBy="periodicoAutoresDoPeriodico", cascade={"persist"})
     */
    private $artigosAutoresDoPeriodico;

    public function __construct()
    {
        $this->artigos = new ArrayCollection();
        $this->artigosAutores = new ArrayCollection();
        $this->artigosPeriodicos = new ArrayCollection();
        $this->artigosAutoresEPeriodicos = new ArrayCollection();
        $this->artigosAutoresDoPeriodico = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIndiceH5(): ?int
    {
        return $this->indiceH5;
    }

    public function setIndiceH5(?int $indiceH5): self
    {
        $this->indiceH5 = $indiceH5;

        return $this;
    }

    public function getMedianaH5(): ?int
    {
        return $this->medianaH5;
    }

    public function setMedianaH5(int $medianaH5): self
    {
        $this->medianaH5 = $medianaH5;

        return $this;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getIssn(): ?string
    {
        return $this->issn;
    }

    public function setIssn(string $issn): self
    {
        $this->issn = $issn;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getOrigem(): ?string
    {
        return $this->origem;
    }

    public function setOrigem(string $origem): self
    {
        $this->origem = $origem;

        return $this;
    }

    public function getQualis(): ?string
    {
        return $this->qualis;
    }

    public function setQualis(string $qualis): self
    {
        $this->qualis = $qualis;

        return $this;
    }

    public function getPossiveisNomes(): ?array
    {
        return $this->possiveisNomes;
    }

    public function setPossiveisNomes(array $possiveisNomes): self
    {
        $this->possiveisNomes = $possiveisNomes;

        return $this;
    }

    public function getAreaDoConhecimento(): ?string
    {
        return $this->areaDoConhecimento;
    }

    public function setAreaDoConhecimento(string $areaDoConhecimento): self
    {
        $this->areaDoConhecimento = $areaDoConhecimento;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|Artigo[]
     */
    public function getArtigos(): Collection
    {
        return $this->artigos;
    }

    public function addArtigo(Artigo $artigo): self
    {
        if (!$this->artigos->contains($artigo)) {
            $this->artigos[] = $artigo;
            $artigo->setPeriodico($this);
        }

        return $this;
    }

    public function removeArtigo(Artigo $artigo): self
    {
        if ($this->artigos->removeElement($artigo)) {
            // set the owning side to null (unless already changed)
            if ($artigo->getPeriodico() === $this) {
                $artigo->setPeriodico(null);
            }
        }

        return $this;
    }

    public function getGrupo(): ?string
    {
        return $this->grupo;
    }

    public function setGrupo(?string $grupo): self
    {
        $this->grupo = $grupo;

        return $this;
    }

    /**
     * @return Collection|Artigo[]
     */
    public function getArtigosAutores(): Collection
    {
        return $this->artigosAutores;
    }

    public function addArtigosAutore(Artigo $artigosAutore): self
    {
        if (!$this->artigosAutores->contains($artigosAutore)) {
            $this->artigosAutores[] = $artigosAutore;
            $artigosAutore->setPeriodicoAutores($this);
        }

        return $this;
    }

    public function removeArtigosAutore(Artigo $artigosAutore): self
    {
        if ($this->artigosAutores->removeElement($artigosAutore)) {
            // set the owning side to null (unless already changed)
            if ($artigosAutore->getPeriodicoAutores() === $this) {
                $artigosAutore->setPeriodicoAutores(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Artigo[]
     */
    public function getArtigosPeriodicos(): Collection
    {
        return $this->artigosPeriodicos;
    }

    public function addArtigosPeriodico(Artigo $artigosPeriodico): self
    {
        if (!$this->artigosPeriodicos->contains($artigosPeriodico)) {
            $this->artigosPeriodicos[] = $artigosPeriodico;
            $artigosPeriodico->setPeriodicoPeriodicos($this);
        }

        return $this;
    }

    public function removeArtigosPeriodico(Artigo $artigosPeriodico): self
    {
        if ($this->artigosPeriodicos->removeElement($artigosPeriodico)) {
            // set the owning side to null (unless already changed)
            if ($artigosPeriodico->getPeriodicoPeriodicos() === $this) {
                $artigosPeriodico->setPeriodicoPeriodicos(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Artigo[]
     */
    public function getArtigosAutoresEPeriodicos(): Collection
    {
        return $this->artigosAutoresEPeriodicos;
    }

    public function addArtigosAutoresEPeriodico(Artigo $artigosAutoresEPeriodico): self
    {
        if (!$this->artigosAutoresEPeriodicos->contains($artigosAutoresEPeriodico)) {
            $this->artigosAutoresEPeriodicos[] = $artigosAutoresEPeriodico;
            $artigosAutoresEPeriodico->setPeriodicosAutoresEPeriodicos($this);
        }

        return $this;
    }

    public function removeArtigosAutoresEPeriodico(Artigo $artigosAutoresEPeriodico): self
    {
        if ($this->artigosAutoresEPeriodicos->removeElement($artigosAutoresEPeriodico)) {
            // set the owning side to null (unless already changed)
            if ($artigosAutoresEPeriodico->getPeriodicosAutoresEPeriodicos() === $this) {
                $artigosAutoresEPeriodico->setPeriodicosAutoresEPeriodicos(null);
            }
        }

        return $this;
    }

    public function getIndiceH5Autores(): ?int
    {
        return $this->indiceH5Autores;
    }

    public function setIndiceH5Autores(?int $indiceH5Autores): self
    {
        $this->indiceH5Autores = $indiceH5Autores;

        return $this;
    }

    public function getIndiceH5Periodicos(): ?int
    {
        return $this->indiceH5Periodicos;
    }

    public function setIndiceH5Periodicos(?int $indiceH5Periodicos): self
    {
        $this->indiceH5Periodicos = $indiceH5Periodicos;

        return $this;
    }

    public function getIndiceH5AutoresEPeriodicos(): ?int
    {
        return $this->indiceH5AutoresEPeriodicos;
    }

    public function setIndiceH5AutoresEPeriodicos(?int $indiceH5AutoresEPeriodicos): self
    {
        $this->indiceH5AutoresEPeriodicos = $indiceH5AutoresEPeriodicos;

        return $this;
    }

    public function getMedianaH5Autores(): ?int
    {
        return $this->medianaH5Autores;
    }

    public function setMedianaH5Autores(?int $medianaH5Autores): self
    {
        $this->medianaH5Autores = $medianaH5Autores;

        return $this;
    }

    public function getMedianaH5Periodicos(): ?int
    {
        return $this->medianaH5Periodicos;
    }

    public function setMedianaH5Periodicos(?int $medianaH5Periodicos): self
    {
        $this->medianaH5Periodicos = $medianaH5Periodicos;

        return $this;
    }

    public function getMedianaH5AutoresEPeriodicos(): ?int
    {
        return $this->medianaH5AutoresEPeriodicos;
    }

    public function setMedianaH5AutoresEPeriodicos(?int $medianaH5AutoresEPeriodicos): self
    {
        $this->medianaH5AutoresEPeriodicos = $medianaH5AutoresEPeriodicos;

        return $this;
    }

    public function getIndiceH5AutoresDoPeriodico(): ?int
    {
        return $this->IndiceH5AutoresDoPeriodico;
    }

    public function setIndiceH5AutoresDoPeriodico(?int $IndiceH5AutoresDoPeriodico): self
    {
        $this->IndiceH5AutoresDoPeriodico = $IndiceH5AutoresDoPeriodico;

        return $this;
    }

    public function getMedianaH5AutoresDoPeriodico(): ?int
    {
        return $this->medianaH5AutoresDoPeriodico;
    }

    public function setMedianaH5AutoresDoPeriodico(?int $medianaH5AutoresDoPeriodico): self
    {
        $this->medianaH5AutoresDoPeriodico = $medianaH5AutoresDoPeriodico;

        return $this;
    }

    /**
     * @return Collection|Artigo[]
     */
    public function getArtigosAutoresDoPeriodico(): Collection
    {
        return $this->artigosAutoresDoPeriodico;
    }

    public function addArtigosAutoresDoPeriodico(Artigo $artigosAutoresDoPeriodico): self
    {
        if (!$this->artigosAutoresDoPeriodico->contains($artigosAutoresDoPeriodico)) {
            $this->artigosAutoresDoPeriodico[] = $artigosAutoresDoPeriodico;
            $artigosAutoresDoPeriodico->setPeriodicoAutoresDoPeriodico($this);
        }

        return $this;
    }

    public function removeArtigosAutoresDoPeriodico(Artigo $artigosAutoresDoPeriodico): self
    {
        if ($this->artigosAutoresDoPeriodico->removeElement($artigosAutoresDoPeriodico)) {
            // set the owning side to null (unless already changed)
            if ($artigosAutoresDoPeriodico->getPeriodicoAutoresDoPeriodico() === $this) {
                $artigosAutoresDoPeriodico->setPeriodicoAutoresDoPeriodico(null);
            }
        }

        return $this;
    }

    public function limparArtigos()
    {
        $this->artigosAutores->clear();
        $this->artigosPeriodicos->clear();
        $this->artigosAutoresEPeriodicos->clear();
        $this->artigosAutoresDoPeriodico->clear();
    }

    public function limparArtigosColetaInicial()
    {
        $this->artigos->clear();
    }
}
