<?php

namespace App\Controller;

use Exception;
use App\Entity\Producto;
use App\Entity\Categoria;
use App\Repository\CategoriaRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/categoria")
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

        $response = new JsonResponse();
        $response->setData([
            'success' => true,
            'message' => 'Operacion Exitosa',
            'data' => NULL,
        ]);

        return $response;
    }

    /**
     * @Route("/get", name="get_categorias", methods="GET")
     */
    public function getCategorias() {
        $em = $this->getDoctrine()->getManager();
        $categorias = $em->getRepository(Categoria::class)->findAll();
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

        return $response;
    }

    /**
     * @Route("/get_categoria/{id}", 
     * name="get_categoria", 
     * methods="GET",
     * requirements={"id"="\d+"},
     * defaults={"id": null}
     * )
     */
    public function getCategoriaPorId($id){
        if (!is_null($id)) {
            throw new Exception("Error Processing Request, id indefinido", 1);
        }
        $message = NULL;
        $data = NULL;
        $success = true;

        $em = $this->getDoctrine()->getManager();
        $categoria = $em->getRepository(Categoria::class)->find($id);

        if (!empty($categoria)) {
            $data = [
                'id' => $categoria->getId(),
                'nombre' => $categoria->getNombre(),
            ];
            $message = 'Operación Exitosa';
        }
        else {
            $message = 'Categoria no encontrada';
        }

        $response = new JsonResponse();
        $response->setData([
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ]);
        return $response;
    }

    /**
     * @Route("/get_productos/{id}", name="get_productos_por_categoria", methods="GET")
     */
    public function getProductosPorCategoria($id) {
        
        if (!is_null($id)) {
            throw new Exception("Error Processing Request, id indefinido", 1);
        }

        $message = NULL;
        $data = NULL;
        $success = true;

        $em = $this->getDoctrine()->getManager();
        $producto = $em->getRepository(Producto::class)->findBy(['categoria' => $id]);
        if (!empty($producto)) {
            $data = [
                'id' => $producto->getId(),
                'nombre' => $producto->getNombre(),
                'precio' => $productos[$i]->getPrecio()
            ];
            $message = 'Operación Exitosa';
        }
        else {
            $message = 'No existen productos para la categoria solicitada!';
        }

        $response = new JsonResponse();
        $response->setData([
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ]);
        return $response;
    }
}
