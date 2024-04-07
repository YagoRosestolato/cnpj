
    <?php
if (isset($_GET['cnpj'])) {
    // Captura os CNPJs enviados pelo formulário
    $cnpjs = $_GET['cnpj'];

    // Separa os CNPJs em um array
    $cnpj_array = explode(",", $cnpjs);

    // Inicia uma lista não ordenada para os resultados
    echo "<section>";
    echo "<ul>";

    // Loop através dos CNPJs
    foreach ($cnpj_array as $cnpj) {
        // Substitua a URL pela correta conforme a documentação da API
        $url = "https://www.receitaws.com.br/v1/cnpj/$cnpj";

        // Inicia o cURL
        $ch = curl_init($url);

        // Configura o cURL para receber a resposta
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Executa a consulta
        $response = curl_exec($ch);

        // Fecha a conexão
        curl_close($ch);

        // Decodifica a resposta JSON
        $data = json_decode($response, true);

        // Verifica se a consulta foi bem-sucedida
        if (!isset($data['status']) || $data['status'] !== 'ERROR') {
            $nome = $data['nome'];
            $bairro = $data['bairro'];
            $uf = $data['uf'];
            $cep = $data['cep'];
            $email = isset($data['email']) ? $data['email'] : 'Não disponível';

            // Exibe as informações em um item de lista
            echo "<li>";
            echo "<strong>Nome:</strong> $nome <br>";
            echo "<strong>Bairro:</strong> $bairro <br>";
            echo "<strong>UF:</strong> $uf <br>";
            echo "<strong>CEP:</strong> $cep <br>";
            echo "<strong>E-mail:</strong> $email <br>";
            echo "</li>";
        } else {
            // Exibe uma mensagem de erro caso a consulta falhe
            echo "<li>Erro ao buscar informações para o CNPJ informado: $cnpj.</li>";
        }
    }

    // Fecha a lista não ordenada
    echo "</ul>";
    echo "</section>";
} else {
    // Mensagem exibida caso nenhum CNPJ tenha sido enviado
    echo "<p>Por favor, informe um ou mais CNPJs.</p>";
}
?>

