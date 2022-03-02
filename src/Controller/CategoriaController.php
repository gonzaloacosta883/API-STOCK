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
 * @Route("/_api/1.0/categoria")
 */
class CategoriaController extends AbstractController
{
    /**
     * @Route("/add", name="add_categoria", methods="POST")
     */
    public function addCategoria(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        if( 
            ( (isset($data['nombre'])) and (!empty($data['nombre'])) )
        ){}
        else {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: Nombre nulo",
                'data' => NULL
            ]);
        }

        $nombreCategoria = strtoupper(trim($data['nombre']));//Mayuscula sin espacios

        if (gettype($nombreCategoria) != 'string') {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: Nombre debe ser una cadena",
                'data' => NULL
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $duplicado = $em->getRepository(Categoria::class)
            ->findBy(['nombre' => $nombre]);

        if ($duplicado) {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: Ya existe una categoria con el nombre ingresado",
                'data' => NULL
            ]);
        }
        
        $categoria = new Categoria();
        $categoria->setNombre($nombre);
        $em->persist($categoria);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'message' => "Exito: Operación Exitosa",
            'data' => NULL,
        ]);
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
            return new JsonResponse([
                'success' => true,
                'message' => "Error: debe ingresar un id valido",
                'data' => NULL,
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $categoria = $em->getRepository(Categoria::class)->find($id);

        if (!$categoria) {
            return new JsonResponse([
                'success' => true,
                'message' => "Error: Categoria no encontrada",
                'data' => NULL,
            ]);
        }
        
        $data = [
            'id' => $categoria->getId(),
            'nombre' => $categoria->getNombre(),
        ];

        return new JsonResponse([
            'success' => true,
            'message' => "Exito: Operación Exitosa",
            'data' => $data
        ]);
    }

    /**
     * @Route("/{id}/productos", name="get_productos_por_categoria", methods="GET")
     */
    public function getProductosPorCategoria($id) {
        
        if (is_null($id)) {
            return new JsonResponse([
                'success' => true,
                'message' => "Error: debe ingresar un id valido",
                'data' => NULL,
            ]);
        }

        if (gettype($id) != 'integer') {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: El id debe ser de tipo numerico",
                'data' => NULL,
            ]);
        }

        $arregloProductos = [];

        $em = $this->getDoctrine()->getManager();
        $productos = $em->getRepository(Producto::class)->findBy(['categoria' => $id]);
        $categoria = $em->getRepository(Categoria::class)->findBy($id);

        if (!$productos) {
            return new JsonResponse([
                'success' => false,
                'message' => "No existen productos para la categoria solicitada",
                'data' => NULL
            ]);
        }
        
        foreach ($productos as $producto) {
            $unProducto = [
                'id' => $producto->getId(),
                'nombre' => $producto->getNombre(),
                'precio' => $producto->getPrecio(),
                'foto' => $producto->getFoto(),
            ];
            array_push($arregloProductos, $unProducto);
        }

        $data = [
            'id' => $categoria->getId(),
            'nombre' => $categoria->getNombre(),
            'productos' => $arregloProductos
        ];

        return new JsonReponse([
            'success' => true,
            'message' => "Exito: Operación Exitosa",
            'data' => $data,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="categoria_edit", methods="PUT")
     */
    public function editCategoria($id, Request $request): JsonResponse 
    {
        if (is_null($id)) {
            return new JsonResponse([
                'success' => true,
                'message' => "Error: debe ingresar un id valido",
                'data' => NULL,
            ]);
        }

        if (gettype($id) != 'integer') {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: El id debe ser de tipo numerico",
                'data' => NULL,
            ]);
        }

        $data = json_decode($request->getContent(),true);

        if( (isset($data['nombre'])) and (!empty($data['nombre'])) ){}
        else {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: Debe ingresar un nombre",
                'data' => NULL,
            ]);
        }

        if (gettype($data['nombre']) != 'string') {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: Nombre debe ser una cadena",
                'data' => NULL
            ]);
        }
            
        $em = $this->getDoctrine()->getManager();    
        $categoria = $em->getRepository(Categoria::class)
            ->find($id);
        
        if (!$categoria) {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: La categoria solicitada no existe",
                'data' => NULL,
            ]);
        }
        
        $nombreCategoria = strtoupper(trim($data['nombre']));

        $categoria->setNombre($nombreCategoria);
        $em->persist($categoria);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'message' => "Exito: Categoria editada",
            'data' => NULL,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="categoria_detele", methods="DELETE")
     */
    public function deleteCategoria($id): JsonResponse
    {

        if (empty($id)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error: No ingreso un id valido',
                'data' => NULL
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $categoria = $em->getRepository(Categoria::class)
            ->find($id);

        if (!$categoria) {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: La categoria ingresada no existe",
                'data' => NULL
            ]);
        }

        if (count($categoria->getProductos()) >= 1) {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: La categoria ingresada posee productos asociados, elimine primero los productos o modifique su categoria para poder eliminar la misma",
                'data' => NULL
            ]);
        }

        $em->remove($categoria);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'message' => "Categoria eliminada exitosamente",
            'data' => NULL,
        ]);
    }
}
