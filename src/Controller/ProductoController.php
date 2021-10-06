<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Entity\Categoria;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/producto")
 */
class ProductoController extends AbstractController
{
    
    /**
     * @Route("/add", name="add_producto", methods="POST")
     */
    public function addProducto(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(),true);
        $precio = $data['precio'];
        $id_categoria = $data['categoria'];

        $producto = new Producto();
        $producto->setNombre($data['nombre']);
        $producto->setCodigoColor($data['codigoColor']);
        $producto->setPrecio($precio);

        $categoria = $em->getRepository(Categoria::class)->find($id_categoria);
        $producto->setCategoria($categoria);

        $em->persist($producto);
        $em->flush();

        return new JsonResponse(['status' => 'Opreción Exitosa'], Response::HTTP_CREATED);
    }
}
