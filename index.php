<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prospecção</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <h1>CNPJ</h1>
    </header>
    <section>
        <form method="get">
            <label for="cnpj">CNPJ</label>
            <input type="text" id="cnpj" name="cnpj" pattern="\d{14}" title="Digite um CNPJ no formato 12345678000199" required>
            <input type="submit" value="Buscar">
        </form>
    </section>

    <section>
        <?php
        session_start(); // Inicia a sessão PHP

        // Função para realizar a busca
        function realizarBusca($cnpj) {
            $url = "https://www.receitaws.com.br/v1/cnpj/$cnpj";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
            return json_decode($response, true);
        }

        // CNPJs para buscar
        $cnpjs = ["02273580000116", "01965363000558", "08381082000173", "08193994000111", "15068359000259", "56990880000900", "18891036000178", "60586450000130", "07500596000138", "91362590000409", "09634192000162", "14386045000150"];

        // Realizar as buscas para cada CNPJ
        foreach ($cnpjs as $cnpj) {
            $data = realizarBusca($cnpj);

            if (!isset($data['status']) || $data['status'] !== 'ERROR') {
                $nome = $data['nome'];
                $bairro = $data['bairro'];
                $uf = $data['uf'];
                $cep = $data['cep'];
                $email = isset($data['email']) ? $data['email'] : 'Não disponível';

                echo "<h2>Resultados da busca para CNPJ: $cnpj</h2>";
                echo "<table>";
                echo "<tr><td><strong>Nome:</strong></td><td>$nome</td></tr>";
                echo "<tr><td><strong>Bairro:</strong></td><td>$bairro</td></tr>";
                echo "<tr><td><strong>UF:</strong></td><td>$uf</td></tr>";
                echo "<tr><td><strong>CEP:</strong></td><td>$cep</td></tr>";
                echo "<tr><td><strong>E-mail:</strong></td><td>$email</td></tr>";
                echo "</table>";
            } else {
                echo "<p>Erro ao buscar informações para o CNPJ informado: $cnpj.</p>";
            }

            // Pausa de 1 minuto entre as buscas
            sleep(60);
        }

        // Verifica se há resultados armazenados na sessão
        if (isset($_SESSION['results'])) {
            foreach ($_SESSION['results'] as $key => $result) {
                echo $result; // Exibe os resultados anteriores
            }
        }

        // Verifica se o CNPJ foi enviado
        if (isset($_GET['cnpj'])) {
            $cnpj = $_GET['cnpj'];
            $url = "https://www.receitaws.com.br/v1/cnpj/$cnpj";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
            $data = json_decode($response, true);
            if (!isset($data['status']) || $data['status'] !== 'ERROR') {
                $nome = $data['nome'];
                $bairro = $data['bairro'];
                $uf = $data['uf'];
                $cep = $data['cep'];
                $email = isset($data['email']) ? $data['email'] : 'Não disponível';

                $result = "<h2>Resultados da busca para CNPJ: $cnpj</h2>";
                // Início da tabela
                $result .= "<table>";
                // Linha para Nome
                $result .= "<tr><td><strong>Nome:</strong></td><td>$nome</td><td><form method='post'><input type='hidden' name='cnpj_excluir' value='$cnpj'><input type='submit' value='Excluir'></form></td></tr>";
                // Linha para Bairro
                $result .= "<tr><td><strong>Bairro:</strong></td><td>$bairro</td></tr>";
                // Linha para UF
                $result .= "<tr><td><strong>UF:</strong></td><td>$uf</td></tr>";
                // Linha para CEP
                $result .= "<tr><td><strong>CEP:</strong></td><td>$cep</td></tr>";
                // Linha para E-mail
                $result .= "<tr><td><strong>E-mail:</strong></td><td>$email</td></tr>";
                // Fim da tabela
                $result .= "</table>";

                // Armazena os resultados na sessão
                $_SESSION['results'][] = $result;

                echo $result; // Exibe os resultados atuais
            } else {
                echo "<p>Erro ao buscar informações para o CNPJ informado: $cnpj.</p>";
            }
        } else {
            echo "<p>Por favor, informe um CNPJ.</p>";
        }

        // Verifica se o CNPJ a ser excluído foi submetido
        if (isset($_POST['cnpj_excluir'])) {
            $cnpj_excluir = $_POST['cnpj_excluir'];
            // Remove o resultado da lista de resultados
            foreach ($_SESSION['results'] as $key => $result) {
                if (strpos($result, $cnpj_excluir) !== false) {
                    unset($_SESSION['results'][$key]);
                    break;
                }
            }
            // Redireciona de volta para a página atual
            header("Location: {$_SERVER['PHP_SELF']}");
            exit();
        }

        // Botão para limpar toda a lista
        if (!empty($_SESSION['results'])) {
            echo "<form method='post'>";
            echo "<input type='hidden' name='limpar_lista' value='true'>";
            echo "<input type='submit' value='Limpar Lista'>";
            echo "</form>";
        }

        // Verifica se o botão para limpar a lista foi clicado
        if (isset($_POST['limpar_lista']) && $_POST['limpar_lista'] === 'true') {
            // Limpa a lista de resultados da sessão
            unset($_SESSION['results']);
            // Redireciona de volta para a página atual
            header("Location: {$_SERVER['PHP_SELF']}");
            exit();
        }
        ?>
    </section>

    <script>
        // Recarregar a página a cada 1 minuto
        setInterval(function() {
            location.reload();
        }, 60000); // 1 minuto = 60 segundos * 1000 milissegundos
    </script>
</body>

</html>
