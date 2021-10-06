<?php

namespace App\Controller;

use Exception;
use App\Entity\Categoria;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @ORM\Entity(repositoryClass=CategoriaControllerRepository::class)
 * @Route("/categoria", name="categoria")
 */
class CategoriaController extends AbstractController
{
    /**
     * @Route("/add", name="add_categoria", methods="POST")
     */
    public function addCategoria(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        $nombre = $data['nombre'];

        if (empty($nombre))
            throw new Exception("Error Processing Request", 1);

        $em = $this->getDoctrine()->getManager();
        $categoria = new Categoria();
        $categoria->setNombre($nombre);
        $em->persist($categoria);
        $em->flush();

        return new JsonResponse(['status' => 'Opreción Exitosa'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/get", name="get_categorias", methods={GET})
     */
    public function getCategorias() {
        $em = $this->getDoctrine()->getManager();
        $categorias = $em->findAll();
        $arregloCategorias = [];

        $response = new JsonResponse();
        if (!empty($categorias)) {
            $message = 'Operación Exitosa';
            for ($i=0; $i <count($categorias) ; $i++) { 
                $unaCategoria = [
                    'id' => $categorias[$i]->getId(),
                    'nombre' => $categorias[$i]->getNombre()
                ];
                array_push($arregloCategorias, $unaCategoria);
            }
        }
        
        $response->setData([
            'success' => true,
            'message' => $message,
            'data' => $arregloCategorias,
        ]);
    }

    /**
     * @Route("/get_productos/{id}", name="get_productos_por_categoria")
     */
    public function getProductosPorCategoria($id) {
        
    }
}
