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
        
        $duplicado = $em->getRepository(Producto::class)->findOneBy(['nombre' => $data['nombre']]);
        if(!empty($duplicado)){
            $response = new JsonResponse();
            $response->setData([
                'success' => false,
                'message' => "Ya existe un producto con el nombre ingresado!",
                'data' => NULL,
            ]);
            return $response;
        }

        $producto = new Producto();
        $producto->setNombre($data['nombre']);
        $producto->setCodigoColor($data['codigoColor']);
        $producto->setPrecio($data['precio']);

        $categoria = $em->getRepository(Categoria::class)->find($data['categoria']);
        $producto->setCategoria($categoria);

        $em->persist($producto);
        $em->flush();

        return new JsonResponse(['status' => 'Opreción Exitosa'], Response::HTTP_CREATED);
    }
}
