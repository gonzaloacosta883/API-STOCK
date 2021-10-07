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

        return new JsonResponse(['status' => 'Operación Exitosa'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/get", name="get_productos", methods="GET")
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
     * @Route("/get/{id}", 
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
        
        if (!is_null($id)) {
            throw new Exception("Error Processing Request, id indefinido", 1);
        }
        else {
            $em = $this->getDoctrine()->getManager();
            $producto = $em->getRepository(Producto::class)->find($id);
            if (!empty($producto)) {
                $stocks = [];
                $stockDeposito = $producto->getStocks();
                /*INFORMACION SOBRE EL SOTCK DE DICHO PRODUCTO EN LOS DISTINTOS ALMACENES*/
                for ($i=0; $i <count($stockDeposito) ; $i++) { 
                    $stocks[] = [
                        'id' => $stockDeposito->getId(),
                        'deposito' => [
                            'nombre' => $stockDeposito->getDeposito()->getNombre(),
                            'direccion' => $stockDeposito->getDeposito()->getDireccion(),
                        ],
                        'cantidad' => $stockDeposito->getCantidad(),
                        'unidades' => $stockDeposito->getUnidades()
                    ];
                }

                $data = [
                    'id' => $producto->getId(),
                    'nombre' => $producto->getNombre(),
                    'precio' => $productos[$i]->getPrecio(),
                    'categoria' => [
                        'id' => $productos[$i]->getCategoria()->getId(),
                        'nombre' => $productos[$i]->getCategoria()->getNombre()
                    ],
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
}
