<?php

namespace App\Controller;

use Exception;
use App\Entity\Deposito;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#Nelmio\ApiDocBundle
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

/**
 * @Route("/deposito")
 */
class DepositoController extends AbstractController
{
    /**
     * @Route("/add", name="add_deposito", methods="POST")
     */
    public function addDeposito(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(), true);

        if(empty($data['nombre']) or empty($data['direccion'])) 
        {
            throw new Exception("Error Processing Request, parametro/s indefinido/s", 1);
        }

        $nombre = ucfirst(strtolower(trim($data['nombre'])));
        $direccion = ucfirst(strtolower(trim($data['direccion'])));

        $duplicado = $em->getRepository(Deposito::class)->findOneBy(['nombre' => $nombre]);
        /*Empty = vacio*/
        if (!empty($duplicado)) {
            $response = new JsonResponse();
            $response->setData([
                'success' => false,
                'message' => "Ya existe un deposito con el nombre ingresado!",
                'data' => null,
            ]);
            return $response;
        }

        $deposito = new Deposito();
        $deposito->setNombre($nombre);
        $deposito->setDireccion($direccion);
        $em->persist($deposito);
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Operación Exitosa',
            'data' => null,
        ]);
    }

    /**
     * @Route("/get", name="get_depositos", methods="GET")
     */
    public function getDepositos()
    {
        $em = $this->getDoctrine()->getManager();
        $depositos = $em->getRespitory(Deposito::class)->findAll();
        $response = new JsonResponse();
        $data = null;

        /*SI ESTA VACIO*/
        if (empty($depositos)) {
            $success = false;
            $message = "No existen depositos!";
        } else {
            $success = true;
            $message = "Operación Exitosa";
            $data = [];
            for ($i = 0; $i < count($depositos); $i++) {
                $data = [
                    'id' => $depositos[$i]->getId(),
                    'nombre' => $depositos[$i]->getNombre(),
                    'direccion' => $depositos[$i]->getDireccion(),
                ];
            }
        }

        $response->setData([
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ]);
        return $response;
    }

    /**
     * @Route("/get/{id}",
     * name="get_deposito_por_id",
     * methods="GET",
     * requirements={"id"="\d+"},
     * defaults={"id": NULL}
     * )
     */
    public function getDepositoPorId($id)
    {

        $message = null;
        $data = null;
        $success = true;
        if (!is_null($id)) {
            throw new Exception("Error Processing Request, id indefinido", 1);
        } else {
            $em = $this->getDoctrine()->getManager();
            $deposito = $em->getRepository(Deposito::class)->find($id);
            /*SI SE ENCONTRO EL DEPOSITO*/
            if (!empty($deposito)) {
                $stocks = [];
                $inventarioDeposito = $deposito->getStocks();

                for ($i = 0; $i < count($inventarioDeposito); $i++) {
                    $stocks = [
                        'id' => $inventarioDeposito[$i]->getId(),
                        'producto' => [
                            'id' => $inventarioDeposito[$i]->getProducto()->getId(),
                            'nombre' => $inventarioDeposito[$i]->getProducto()->getNombre(),
                            'codigoColor' => $inventarioDeposito[$i]->getProducto()->getCodigoColor(),
                            'precio' => $inventarioDeposito[$i]->getProducto()->getPrecio(),
                            'categoria' => [
                                'id' => $inventarioDeposito[$i]->getProducto()->getCategoria()->getId(),
                                'nombre' => $inventarioDeposito[$i]->getProducto()->getCategoria()->getNombre(),
                            ],
                        ],
                    ];
                }
                $data = [
                    'id' => $deposito->getId(),
                    'nombre' => $deposito->getNombre(),
                    'stocks' => $stocks,
                ];
                $message = 'Operación Exitosa';
            } else {
                $message = 'Categoria no encontrada';
                $success = false;
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
