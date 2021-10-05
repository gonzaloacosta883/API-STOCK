<?php

namespace App\Entity;

use App\Entity\Producto;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use App\Repository\CategoriaRepository;

/**
 * @ORM\Entity(repositoryClass=CategoriaRepository::class)
 */
class Categoria
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
    * @ORM\OneToMany(targetEntity=Producto::class, mappedBy="categoria")
    */
    private $productos;

    public function __construct()
    {
        $this->productos = new ArrayCollection();
    }

    public function addProducto(Producto $producto) {
        $this->productos[] = $producto;
    }

    public function getProductos() {
        return $this->productos;
    }

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
}
