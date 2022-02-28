<?php

namespace App\Controller;

use App\Entity\Deposito;
use App\Entity\Producto;

use App\Entity\StockDeposito;
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
     * @Route("/incrementar", name="stock_deposito_incrementar", methods="POST")
     */
    public function incrementar(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(),true);
        
        if( 
            ( (isset($data['idProducto'])) and (!empty($data['idProducto'])) ) and 
            ( (isset($data['idDeposito'])) and (!empty($data['idDeposito'])) )
        ){}
        else {//uno o ambos ids son nulos
            return new JsonResponse([
                'success' => false,
                'message' => "Error: idProducto y/o idDepositos nulos",
                'data' => NULL
            ]);    
        }

        //Verifico el tipo de los datos recibidos
        if (gettype($data['idProducto']) != 'integer') {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: El idProducto debe ser un dato de tipo numerico",
                'data' => NULL,
            ]);
        }

        if (gettype($data['idDeposito']) != 'integer') {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: El idDeposito debe ser un dato de tipo numerico",
                'data' => NULL,
            ]);
        }


        if ( (isset($data['cantidad'])) and (!empty($data['cantidad'])) ) {}
        else {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: Debe ingresar una cantidad valida",
                'data' => NULL
            ]);
        }

        $em = $this->getDoctrine()->getManager();
        $deposito = $em->getRepository(Deposito::class)
            ->find($data['idDeposito']);
        $producto = $em->getRepository(Producto::class)
            ->find($data['idProducto']);

        if (!$deposito) {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: El deposito ingresado no existe",
                'data' => NULL
            ]);
        }

        if (!$producto) {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: El producto ingresado no existe",
                'data' => NULL
            ]);
        }
            
        /*Busco si existe el producto en el deposito*/
        $existeEnDeposito = $em->getRepository(StockDeposito::class)
            ->findOneBy(['producto' => $data['idProducto'], 'deposito' => $data['idDeposito']]);
    
        if ($existeEnDeposito) {
            $existeEnDeposito->incrementarCantidad($data['cantidad']);
            $em->persist($existeEnDeposito);
        }
        else {
            $stockDeposito = new StockDeposito();
            $stockDeposito->setProducto($producto);
            $stockDeposito->setDeposito($deposito);
            $stockDeposito->incrementarCantidad($data['cantidad']);
            $em->persist($stockDeposito);
        }
        $em->flush();

        return new JsonResponse([
            'success' => true,
            'message' => "Operación Exitosa",
            'data' => NULL,
        ]);
    }

    /**
     * @Route("/decrementar", name="stock_deposito_decrementar")
     * Recibe como argumentos un producto y la cantidad que se desea aumentar
     */
    public function decrementar(Request $request): JsonResponse 
    {

        $data = json_decode($request->getContent(),true);

        //Verifico que los campos existan y no sean nulos
        if( 
            ( (isset($data['idProducto'])) and (!empty($data['idProducto'])) ) and 
            ( (isset($data['idDeposito'])) and (!empty($data['idDeposito'])) )
        ){}
        else {//uno o ambos ids son nulos
            return new JsonResponse([
                'success' => false,
                'message' => "Error: idProducto y/o idDepositos nulos",
                'data' => NULL
            ]);    
        }

        //Verifico el tipo de los datos recibidos
        if (gettype($data['idProducto']) != 'integer') {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: El idProducto debe ser un dato de tipo numerico",
                'data' => NULL,
            ]);
        }

        if (gettype($data['idDeposito']) != 'integer') {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: El idDeposito debe ser un dato de tipo numerico",
                'data' => NULL,
            ]);
        }


        if ( (isset($data['cantidad'])) and (!empty($data['cantidad'])) and (gettype($data['cantidad']) == 'integer') ) {}
        else {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: Debe ingresar una cantidad valida",
                'data' => NULL
            ]);
        }
        
        $em = $this->getDoctrine()->getManager();
        $producto = $em->getRepository(Producto::class)->find($data['idProducto']);
        $deposito = $em->getRepository(Deposito::class)->find($data['idDeposito']);

        if (!$producto) {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: el producto ingresado no existe",
                'data' => NULL
            ]);
        }
        if(!$deposito) {
            return new JsonResponse([
                'success' => false,
                'message' => "Error: el deposito ingresado no existe",
                'data' => NULL
            ]);
        }

        $stockDeposito = $em->getRepository(StockDeposito::class)
            ->findOneBy(['producto' => $data['idProducto'], 'deposito' => $data['idDeposito']]);
        
        if ($stockDeposito) {
            if ($stockDeposito->getCantidad() == 0) {
                return new JsonResponse([
                    'success' => false,
                    'message' => "Advertencia: el stock del Producto en el Deposito que quiere decrementar ya es 0",
                    'data' => NULL,
                ]);       
            }
            
            $stockDeposito->decrementarCantidad($data['cantidad']);
            $em->persist($stockDeposito);
            $em->flush();
        }

        return new JsonResponse([
            'success' => true,
            'message' => "Operación Exitosa",
            'data' => NULL,
        ]);
    }
}
