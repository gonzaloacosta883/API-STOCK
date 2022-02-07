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
        $message = NULL;
        $success = NULL;

        strtoupper(trim($nombre));//Mayus sin espacios

        if (empty($nombre))
            throw new Exception("Error Processing Request", 1);

        //Verifico que no exista
        $em = $this->getDoctrine()->getManager();
        $duplicado = $em->getRepository(Categoria::class)
            ->findBy(['nombre' => $nombre]);

        if ($duplicado) {
            $message = "Error: registro duplicado";
            $success = false;
        }
        else {

            $categoria = new Categoria();
            $categoria->setNombre($nombre);
            $em->persist($categoria);
            $em->flush();

            $message = "Operacion Exitosa";
            $success = true;
        }

        $response = new JsonResponse();
        $response->setData([
            'success' => $success,
            'message' => $message,
            'data' => NULL,
        ]);

        return $response;
    }

    /**
     * @Route("/all", name="get_categorias", methods="GET")
     */
    public function getCategorias() {
        $em = $this->getDoctrine()->getManager();
        $categorias = $em->getRepository(Categoria::class)->findAll();
        $arregloCategorias = [];

        $response = new JsonResponse();
        if (!empty($categorias)) {

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
            'message' => 'Operación Exitosa',
            'data' => $arregloCategorias,
        ]);

        return $response;
    }

    /**
     * @Route("/{id}", 
     * name="get_categoria", 
     * methods="GET",
     * requirements={"id"="\d+"},
     * defaults={"id": null}
     * )
     */
    public function getCategoriaPorId($id){
        
        if (is_null($id)) {
            throw new Exception("Error Processing Request, id indefinido", 1);
        }
        
        $message = NULL;
        $data = NULL;

        $em = $this->getDoctrine()->getManager();
        $categoria = $em->getRepository(Categoria::class)->find($id);

        if (!empty($categoria)) {
            $data = [
                'id' => $categoria->getId(),
                'nombre' => $categoria->getNombre(),
            ];
            $message = 'Operación Exitosa';
            $success = true;
        }
        else {
            $message = 'Categoria no encontrada';
            $success = false;
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
     * @Route("/{id}/get/productos/", name="get_productos_por_categoria", methods="GET")
     */
    public function getProductosPorCategoria($id) {
        
        if (is_null($id)) {
            throw new Exception("Error Processing Request, id indefinido", 1);
        }

        $message = NULL;
        $data = NULL;

        $em = $this->getDoctrine()->getManager();
        $productos = $em->getRepository(Producto::class)->findBy(['categoria' => intval($id)]);

        if (!empty($productos)) {
            foreach ($productos as $producto) {
                $data[] = [
                    'id' => $producto->getId(),
                    'nombre' => $producto->getNombre(),
                    'precio' => $producto->getPrecio(),
                    'foto' => $producto->getFoto(),
                ];    
            }
            
            $message = 'Operación Exitosa';
            $success = true;
        }
        else {
            $message = 'No existen productos para la categoria solicitada!';
            $success = false;
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
