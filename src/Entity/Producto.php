<?php

namespace App\Entity;

use App\Entity\Categoria;
use App\Entity\StockDeposito;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use App\Repository\ProductoRepository;
use Doctrine\Common\Collections\ArrayCollection;

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
     * @ORM\JoinColumn(name="categoria_id", referencedColumnName="id")
     */
    private $categoria;

    /**
     * @ORM\OneToMany(targetEntity=StockDeposito::class, mappedBy="producto")
     */
    private $stocks;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $foto;

    public function __construct() {
        $this->stocks = new ArrayCollection();
    }

    public function addStock(StockDeposito $stock) {
        $this->stocks[] = $stock;
    }

    public function getStocks() {
        return $this->stocks;
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

    public function getFoto()
    {
        return $this->foto;
    }

    public function setFoto($foto)
    {
        $this->foto = $foto;
        return $this;
    }
}
