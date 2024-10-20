<?php
require_once '../vendor/autoload.php';
/**
 * @OA\Info(
 *     title="Book Library API",
 *     version="1.0.0",
 *     description="API для управления библиотекой книг."
 * )
 */

$openapi = \OpenApi\Generator::scan(['../src']);
header('Content-Type: application/json');
echo $openapi->toJson();