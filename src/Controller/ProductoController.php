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
        $duplicado = $em->getRepository(Producto::class)->findOneBy(['nombre' => $nombre]);

        if(!empty($duplicado)){
            $success = false;
            $message = "Ya existe un producto con el nombre ingresado!";
            $data = NULL;
        }
        else {

            $producto = new Producto();
            $producto->setNombre($nombre);
            $producto->setCodigoColor(trim($data['codigoColor']));
            $producto->setPrecio($data['precio']);
    
            $categoria = $em->getRepository(Categoria::class)->find($data['categoria']);
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
                            'id' => $stockDeposito->getDeposito()->getId(),
                            'nombre' => $stockDeposito->getDeposito()->getNombre(),
                            'direccion' => $stockDeposito->getDeposito()->getDireccion(),
                            'cantidad' => $stockDeposito->getCantidad(),
                            'unidades' => $stockDeposito->getUnidades()
                        ],
                    ];
                }

                $data = [
                    'id' => $producto->getId(),
                    'nombre' => $producto->getNombre(),
                    'precio' => $productos[$i]->getPrecio(),
                    'codigoColor' => $productos[$i]->getCodigoColor(),
                    'categoria' => [
                        'id' => $productos[$i]->getCategoria()->getId(),
                        'nombre' => $productos[$i]->getCategoria()->getNombre()
                    ],
                    'foto' => $productos[$i]->getFoto(),
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
