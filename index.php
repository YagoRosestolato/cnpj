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

        // Verifica se há resultados armazenados na sessão
        if (isset($_SESSION['result'])) {
            echo $_SESSION['result']; // Exibe os resultados anteriores
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
                $result .= "<tr><td><strong>Nome:</strong></td><td>$nome</td></tr>";
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
                $_SESSION['result'] = $result;

                echo $result; // Exibe os resultados atuais
            } else {
                echo "<p>Erro ao buscar informações para o CNPJ informado: $cnpj.</p>";
            }
        } else {
            echo "<p>Por favor, informe um CNPJ.</p>";
        }
        ?>
    </section>
</body>
</html>
