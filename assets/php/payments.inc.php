<?php
require_once "values.inc.php";
require_once "stripe/init.php";

$stripe = new Stripe\StripeClient($stripe_skey);

if (isset($_POST["card"]) && isset($_POST["card_exp"]) && isset($_POST["card_cvc"]) && isset($_POST["price"])) {
    $card_number = $_POST["card"];
    $card_exp = explode("/", $_POST["card_exp"]);
    $card_exp_m = $card_exp[0];
    $card_exp_y = $card_exp[1];
    $card_cvc = $_POST["card_cvc"];
    $price = $_POST["price"];
    try {
        $card = $stripe->tokens->create([
            'card' => [
                'number' => $card_number,
                'exp_month' => $card_exp_m,
                'exp_year' => $card_exp_y,
                'cvc' => $card_cvc,
            ],
        ]);

        $response =  $stripe->charges->create([
            "amount" => $price,
            "currency" => "usd",
            "source" => $card["id"]
        ]);
    } catch (Stripe\Exception\ApiErrorException $error) {
        http_response_code(401);
        die(json_encode(["error" => $error->getMessage()]));
    }
}
