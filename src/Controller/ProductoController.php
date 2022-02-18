<?php

namespace App\Controller;

use Exception;
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

        $data = json_decode($request->getContent(),true);
        $success = NULL;
        $message=NULL;

        $nombre = $data['nombre'];
        strtoupper(trim($nombre));//Mayus sin espacios

        $em = $this->getDoctrine()->getManager();

        $categoria = $em->getRepository(Categoria::class)->find($data['categoria']);
        if (!$categoria) {
            $success = false;
            $message = "La categoria a la que se quiere asociar el nuevo producto no existe!";
            $data = NULL;
        }
        
        $duplicado = $em->getRepository(Producto::class)->findOneBy(['nombre' => $nombre]);

        if(!empty($duplicado)){
            $success = false;
            $message = "Ya existe un producto con el nombre ingresado!";
            $data = NULL;
        }
        elseif(empty($duplicado) and $categoria) {

            $producto = new Producto();
            $producto->setNombre($nombre);
            $producto->setCodigoColor(trim($data['codigoColor']));
            $producto->setPrecio($data['precio']);
    
            $producto->setCategoria($categoria);
    
            $em->persist($producto);
            $em->flush();
            $success = true;
            $message = "Operación Exitosa";
            $data = NULL;
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
     * @Route("/all", name="get_productos", methods="GET")
     */
    public function getProductos() {
        $em = $this->getDoctrine()->getManager();
        $productos = $em->getRepository(Producto::class)->findAll();
        $arregloProductos = [];
        $message = NULL;

        for ($i=0; $i <count($productos) ; $i++) { 
            $message = 'Operación Exitosa';
            $unProducto = [
                'id' => $productos[$i]->getId(),
                'nombre' => $productos[$i]->getNombre(),
                'codigoColor' => $productos[$i]->getCodigoColor(),
                'precio' => $productos[$i]->getPrecio(),
                'categoria' => [
                    'id' => $productos[$i]->getCategoria()->getId(),
                    'nombre' => $productos[$i]->getCategoria()->getNombre()
                    ]
                ];
            array_push($arregloProductos, $unProducto);
        }

        if(count($productos) == 0) $message = 'No se registran productos';

        $response = new JsonResponse();
        $response->setData([
            'success' => true,
            'message' => $message,
            'data' => $arregloProductos,
        ]);

        return $response;
    }

    /**
     * @Route("/{id}", 
     * name="get_producto_por_id", 
     * methods="GET",
     * requirements={"id"="\d+"},
     * defaults={"id": NULL}
     * )
     */
    public function getProductoPorId($id){

        $message = NULL;
        $data = NULL;
        $success = true;
        $stocks = [];
        
        if (is_null($id)) {
            throw new Exception("Error Processing Request, id indefinido", 1);
        }
        else {

            $em = $this->getDoctrine()->getManager();
            $producto = $em->getRepository(Producto::class)->find($id);
            
            if (!empty($producto)) {
                $data = [
                    'id' => $producto->getId(),
                    'nombre' => $producto->getNombre(),
                    'precio' => $producto->getPrecio(),
                    'codigoColor' => $producto->getCodigoColor(),
                    'categoria' => [
                        'id' => $producto->getCategoria()->getId(),
                        'nombre' => $producto->getCategoria()->getNombre()
                    ],
                    'foto' => $producto->getFoto(),
                    'stocks' => $stocks
                ];
                $message = 'Operación Exitosa';
            }
            else {
                $message = 'Producto no encontrado';
            }
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
     * @Route("/{id}/edit", name="producto_edit", methods="PUT")
     */
    public function edit($id, Request $request): JsonResponse
    {
        $categoria = NULL;
        if (empty($id)) {
            return new JsonReponse([
                'success' => false,
                'message' => 'Error: No ingreso un id valido',
                'data' => NULL,
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $producto = $em->getRepository(Producto::class)
            ->find($id);
        
        if(!$producto)
        {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error: No se encontro un producto para el id recibido por parametro',
                'data' => NULL
            ]);
        }

        $data = json_decode($request->getContent(),true);
        if (
            (isset($data['nombre']) and !empty($data['nombre'])) and//Si existe y no esta vacio
            (isset($data['codigoColor']) and !empty($data['codigoColor'])) and//Si existe y no esta vacio
            (isset($data['precio']) and !empty($data['precio'])) and//Si existe y no esta vacio
            (isset($data['categoria']) and !empty($data['categoria'])) //Si existe y no esta vacio
        ) {
            $categoria = $em->getRepository(Categoria::class)
                ->find($data['categoria']);
            
            if (!$categoria) {
                return new JsonResponse([
                    'success' => false,
                    'message' => "Error: La categoria ingresada no existe",
                    'data' => NULL
                ]);
            }
        }
        else {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: todos los campos son requeridos y no deben estar vacios",
                'data' => NULL
            ]);
        }
        
        $success = false;
        $message = NULL;

        if ($categoria) {
            $producto->setNombre($data['nombre']);
            $producto->setCodigoColor($data['codigoColor']);
            $producto->setPrecio($data['precio']);
            $producto->setCategoria($categoria);
                
            $em->persist($producto);
            $em->flush();

            $success = true;
            $message = "Exito: producto modificado exitosamente";
        }
        else {
            $message = "Error: no se encontro la categoria recibida en el cuerpo de la solicitud";
        }
        
        return new JsonReponse([
            'success' => $success,
            'message' => $message,
            'data' => NULL,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="producto_detele", methods="DELETE")
     */
    public function deleteProducto($id) {
        
        if (empty($id)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error: No ingreso un id valido',
                'data' => NULL
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $producto = $em->getRepository(Producto::class)
            ->find($id);

        if (!$producto) {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: El producto ingresado no existe",
                'data' => NULL
            ]);
        }
        //- No tiene que tener stock en los depositos en los que se encuentre
        $stockDepositos = $producto->getStocks();
        foreach ($stockDepositos as $stockDeposito) {
            if ($stockDeposito->getCantidad() == 0) 
                $em->remove($stockDeposito);
            else {
                return new JsonResponse([
                    'success' => false,
                    'message' => "Error: No se pudo completar la eliminación del producto ya que existe stock del producto que se quiere eliminar en depositos",
                    'data' => NULL
                ]); 
            }
        }
        $em->remove($producto);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'message' => "Producto eliminado exitosamente",
            'data' => NULL
        ]);

    }
}
