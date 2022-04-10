<?php

namespace App\Entity;

use App\Repository\CitacaoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CitacaoRepository::class)
 */
class Citacao
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
     * @ORM\Column(type="array")
     */
    private $autores = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=True)
     */
    private $periodicoNome;

    /**
     * @ORM\Column(type="integer")
     */
    private $ano;

    /**
     * @ORM\ManyToOne(targetEntity=Artigo::class, inversedBy="citacoes", cascade={"persist"})
     */
    private $artigo;

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

    public function setPeriodicoNome(?string $periodicoNome): self
    {
        $this->periodicoNome = $periodicoNome;

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

    public function getArtigo(): ?Artigo
    {
        return $this->artigo;
    }

    public function setArtigo(?Artigo $artigo): self
    {
        $this->artigo = $artigo;

        return $this;
    }
}
