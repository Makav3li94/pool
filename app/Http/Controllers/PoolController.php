<?php

namespace App\Http\Controllers;

use App\Models\Pool;
use Illuminate\Http\Request;

class PoolController extends Controller
{

    public function index()
    {
//                Pool::create([
//            'token1_symbol' => "ETH",
//            'token2_symbol' =>"TET" ,
//            'eth_res' => "10",
//            'tet_res' => "22000",
//            'eth_amount' => "0",
//            'tet_amount' => "0",
//            'eth_buy_price' => "0.0004",
//            'eth_sell_price' => "1945",
//            'eth_global_price' => "1945",
//            'eth_fee' => "0",
//            'tet_fee' => "0",
//            'input_type' =>0 ,
//        ]);
        $pools = Pool::orderBy("id")->get();
        return view('welcome', compact('pools'));

        for ($i = 0; $i <= 1000; $i++) {

//            if ($i % 4 == 0) {
                $pool = Pool::latest('id')->first();
                $ethPoolPrice = $this->getRate(1, $pool->eth_res, $pool->tet_res);
                $tokenPoolPrice = $this->getRate(1, $pool->tet_res, $pool->eth_res);

                $t = rand(1,20);
                if ($t %2 == 0){
                    $r = 2000  + ($t *10);
                }else{
                    $r = 2000  - ($t *10);
                }

                if ($r > $ethPoolPrice && $i != 0) {
                    //give tet
                    $tR = rand(1800, 2200);
                    $this->giveTet($pool, $tR, $tokenPoolPrice, $ethPoolPrice, $r, $realPrice, $tokenPrice, $tet_fee, $eth_res, $tet_res);

                } else {
                    //give eth
                    $eR = rand(1, 2);
                    $this->giveEth($pool, $eR, $tokenPoolPrice, $ethPoolPrice, $r, $realPrice, $tokenPrice, $eth_fee, $eth_res, $tet_res);
//                }
//            } else {
//                $pool = Pool::latest('id')->first();
//                $ethPoolPrice = $this->getRate(1, $pool->eth_res, $pool->tet_res);
//                $tokenPoolPrice = $this->getRate(1, $pool->tet_res, $pool->eth_res);
//                $r = rand(2000, 2300);
//                if ($i % 3 == 0) {
//                    $tR = rand(2000, 4000);
//                    $this->giveTet($pool, $tR, $tokenPoolPrice, $ethPoolPrice, $r, $realPrice, $tokenPrice, $tet_fee, $eth_res, $tet_res);
//                } else {
//                    $eR = rand(1, 2);
//                    $this->giveEth($pool, $eR, $tokenPoolPrice, $ethPoolPrice, $r, $realPrice, $tokenPrice, $eth_fee, $eth_res, $tet_res);
//                }
            }
        }
    }

    public
    function createPool($eth_res, $tet_res, $eth_amount, $tet_amount, $eth_buy_price, $eth_sell_price, $eth_global_price, $eth_fee, $tet_fee, $input_type)
    {
        $pool = Pool::create([
            'token1_symbol' => "ETH",
            'token2_symbol' => "TET",
            'eth_res' => $eth_res,
            'tet_res' => $tet_res,
            'eth_amount' => $eth_amount,
            'tet_amount' => $tet_amount,
            'eth_buy_price' => $eth_buy_price,
            'eth_sell_price' => $eth_sell_price,
            'eth_global_price' => $eth_global_price,
            'eth_fee' => $eth_fee,
            'tet_fee' => $tet_fee,
            'input_type' => $input_type,
        ]);
        return $pool;
    }

    public
    function getRate($inputAmount, $inputReserve, $outputReserve)
    {

        $inputAmountWithFee = $inputAmount * 99;
        $numerator = $inputAmountWithFee * $outputReserve;
        $denominator = ($inputReserve * 100) + $inputAmountWithFee;

        return $numerator / $denominator;
    }

    public
    function giveTet($pool, int $tR, float|int $tokenPoolPrice, float|int $ethPoolPrice, int $r, &$realPrice, &$tokenPrice, &$tet_fee, &$eth_res, &$tet_res): void
    {
        $realPrice = (($pool->eth_res * $tR) / $pool->tet_res);
        $tokenPrice = $this->getRate($tR, $pool->tet_res, $pool->eth_res);
        $tet_fee = $realPrice - $tokenPrice;
        $eth_res = $pool->eth_res - $tokenPrice;
        $tet_res = $pool->tet_res + $tR;
        $this->createPool($eth_res, $tet_res, 0, $tR, $tokenPoolPrice, $ethPoolPrice, $r, 0, $tet_fee, 1);
        echo "TET TO ETH SWAP <br>";
        echo "eth global price : " . $r . "<br>";
        echo "eth pool price : " . $ethPoolPrice . "<br>";
        echo "pool price : " . $tokenPoolPrice . "<br>";
        echo "amount : " . $tR . "<br>";
        echo "real pool eth price : " . $realPrice . "<br>";
        echo "actual pool eth  price : " . $tokenPrice . "<br>";
        echo "tet fee : " . $tet_fee . "<br>";
        echo "eth reserve : " . $eth_res . "<br>";
        echo "tet reserve : " . $tet_res . "<br>";
        echo "////////////////////////////<br>";


    }

    public
    function giveEth($pool, int $eR, float|int $tokenPoolPrice, float|int $ethPoolPrice, int $r, &$realPrice, &$tokenPrice, &$eth_fee, &$eth_res, &$tet_res): void
    {
        $realPrice = (($pool->tet_res * $eR) / $pool->eth_res);
        $tokenPrice = $this->getRate($eR, $pool->eth_res, $pool->tet_res);
        $eth_fee = $realPrice - $tokenPrice;
        $eth_res = $pool->eth_res + $eR;
        $tet_res = $pool->tet_res - $tokenPrice;
        $this->createPool($eth_res, $tet_res, $eR, 0, $tokenPoolPrice, $ethPoolPrice, $r, $eth_fee, 0, 2);
        echo "ETH TO TET SWAP <br>";
        echo "eth global price : " . $r . "<br>";
        echo "eth pool price : " . $ethPoolPrice . "<br>";
        echo "pool price : " . $ethPoolPrice . "<br>";
        echo "amount : " . $eR . "<br>";
        echo "real pool eth price : " . $realPrice . "<br>";
        echo "actual pool tet  price : " . $tokenPrice . "<br>";
        echo "tet fee : " . $eth_fee . "<br>";
        echo "eth reserve : " . $eth_res . "<br>";
        echo "tet reserve : " . $tet_res . "<br>";
        echo "////////////////////////////<br>";
    }
}
