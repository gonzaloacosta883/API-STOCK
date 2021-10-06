<?php

namespace App\Entity;

use App\Entity\Categoria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use App\Repository\ProductoRepository;

/**
 * @ORM\Entity(repositoryClass=ProductoRepository::class)
 */
class Producto
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codigoColor;

    /**
     * @ORM\Column(type="float")
     */
    private $precio;

    /**
     * @ORM\ManyToOne(targetEntity=Categoria::class, inversedBy="productos")
     * @JoinColumn(name="categoria_id", referencedColumnName="id")
     */
    private $categoria;


    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getCodigoColor()
    {
        return $this->codigoColor;
    }

    public function setCodigoColor($codigoColor)
    {
        $this->codigoColor = $codigoColor;
        return $this;
    }

    public function getPrecio()
    {
        return $this->precio;
    }

    public function setPrecio($precio)
    {
        $this->precio = $precio;
        return $this;
    }

    public function getCategoria()
    {
        return $this->categoria;
    }

    public function setCategoria(Categoria $categoria)
    {
        $this->categoria = $categoria;
        return $this;
    }
}
