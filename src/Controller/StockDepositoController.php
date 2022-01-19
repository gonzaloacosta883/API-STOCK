<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/stock/deposito")
 */
class StockDepositoController extends AbstractController
{

    /**
     * @Route("/incrementar", name="stock_deposito_incrementar")
     * Recibe como argumentos un producto y la cantidad que se desea aumentar
     */
    public function incrementar(Request $request): JsonResponse
    {

        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(),true);
        $success = NULL;$message=NULL;$data=NULL;

        $producto = $em->getRepository(Producto::class)->find($data['producto']);
        $deposito = $em->getRepository(Deposito::class)->find($data['deposito']);

        if (!$producto) {
            $success = false;
            $message = "Error: el producto ingresado no existe";
        }
        if(!deposito) {
            $success = false;
            $message = "Error: el deposito ingresado no existe";
        }

        if ($producto and deposito) {
            
            /*Busco si existe el producto en el deposito*/
            $existeEnDeposito = $em->getRepository(StockDeposito::class)
                ->findOneBy(['producto' => $data['producto'], 'deposito' => $data['deposito']]);
    
            if ($existeEnDeposito)
                $existeEnDeposito->incrementarCantidad($data['cantidad']);
            else {
                $stockDeposito = new StockDeposito();
                $stockDeposito->setProducto($producto);
                $stockDeposito->setDeposito($deposito);
                $stockDeposito->incrementarCantidad($data['cantidad']);
    
                $em->persist($stockDeposito);
                $em->flush();
    
                $success = true;
                $message = "Operación Exitosa";
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
     * @Route("/decrementar", name="stock_deposito_incrementar")
     * Recibe como argumentos un producto y la cantidad que se desea aumentar
     */
    public function decrementar(Request $request): JsonResponse 
    {

        $em = $this->getDoctrine()->getManager();
        $data = json_decode($request->getContent(),true);
        $success = NULL;$message=NULL;$data=NULL;

        $producto = $em->getRepository(Producto::class)->find($data['producto']);
        $deposito = $em->getRepository(Deposito::class)->find($data['deposito']);

        if (!$producto) {
            $success = false;
            $message = "Error: el producto ingresado no existe";
        }
        if(!deposito) {
            $success = false;
            $message = "Error: el deposito ingresado no existe";
        }

        if ($producto and deposito) {
            $stockDeposito = $em->getRepository(StockDeposito::class)
                ->findOneBy(['producto' => $data['producto'], 'deposito' => $data['deposito']]);
            
            if ($stockDeposito) {
                $stockDeposito->decrementarCantidad($data['cantidad']);
                $em->persist($stockDeposito);
                $em->flush();

                $success = true;
                $message = "Operación Exitosa";
            }
            else {
                $success = false;
                $message = "Error: no se encontro el registro con el producto y deposito recibido";
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
