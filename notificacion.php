<?php
require 'vendor/autoload.php';

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;

function enviarNotificacion($titulo, $contenido, $imagenUrl = null)
{
    // Ruta al archivo de credenciales JSON
    $keyFilePath = __DIR__ . '/tec-linares-43d04-c889ea0e8b33.json';
    $scope = 'https://www.googleapis.com/auth/firebase.messaging';

    // Cargar las credenciales usando ServiceAccountCredentials
    $credentials = new ServiceAccountCredentials($scope, $keyFilePath);

    // Obtener el token de acceso
    $token = $credentials->fetchAuthToken();
    $accessToken = $token['access_token'];

    // URL de Firebase
    $url = 'https://fcm.googleapis.com/v1/projects/tec-linares-43d04/messages:send';

    // Datos de la notificación
    $data = array(
        'message' => array(
            'topic' => 'notificaciones',  // Tema de notificación
            'notification' => array(
                'title' => $titulo,
                'body' => $contenido
            )
        )
    );

    // Si se pasa una URL de imagen
    if ($imagenUrl !== null) {
        $data['message']['notification']['image'] = $imagenUrl;
    }

    // Encabezados para la solicitud HTTP
    $headers = [
        'Authorization' => 'Bearer ' . $accessToken,
        'Content-Type' => 'application/json'
    ];

    // Crear un cliente HTTP
    $client = new Client();

    // Enviar la solicitud POST a Firebase
    $response = $client->post($url, [
        'headers' => $headers,
        'json' => $data
    ]);

    // Comprobar la respuesta
    if ($response->getStatusCode() == 200) {
        echo 'Notificación enviada con éxito.';
    } else {
        echo 'Error: ' . $response->getBody();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'];
    $contenido = $_POST['contenido'];
    $imagenUrl = isset($_POST['imagenUrl']) && !empty($_POST['imagenUrl']) ? $_POST['imagenUrl'] : null;

    enviarNotificacion($titulo, $contenido, $imagenUrl);
}
?>