function get_models() {
    global $CFG;

    // Obtiene la URL base desde la configuración del plugin
    $baseUrl = rtrim(get_config('block_openai_chat', 'baseurl'), '/');

    // Asegura que no se duplique el sufijo
    if (!preg_match('#/api/v1/openai$#', $baseUrl)) {
    $baseUrl .= '/api/v1/openai';
    }

    // Obtiene el token de autorización desde la configuración del plugin
    $token = get_config('block_openai_chat', 'apikey');
    if (empty($token)) {
        throw new \Exception('Token de autorización no configurado.');
    }
    // Configura el endpoint para modelos
    $apiUrl = $baseUrl . '/models';

    // Inicializa cURL
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $token,
        'accept: application/json'
    ));

    // Ejecuta la solicitud
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $ph = curl_getinfo($ch);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    $urlUsed = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    if ($httpCode !== 200) {
        throw new \Exception('Error en la solicitud HTTP: Código ' . $httpCode . '. Respuesta: ' . $response);
    }

    // Limpia el contenido para evitar BOM o caracteres extraños
    $response = preg_replace('/^[\x00-\x1F]/', '', $response);

    // Procesa la respuesta
    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
    } 

    // Extrae la lista de modelos desde la clave 'data'
    if (!isset($data['data']) || !is_array($data['data'])) {
       throw new \Exception('Formato inesperado en la respuesta de la API.');
    }
    //if (!empty($token) && ($httpCode != 200 || empty($data['data']))) {
    //    //throw new \Exception('Error al obtener modelos desde la API de anythingLLM.' . $token);
    //    return "";
    //}
    //else {

       // Clasifica todos los modelos como "chat"
    $models = array_map(function($model) {
       return [
            'name' => $model['name'],
            'model' => $model['model'],
            'type' => 'chat' // Establece el tipo como "chat" según el código original
       ];
    }, $data['data']);

    return $models;
}
