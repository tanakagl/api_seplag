<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Configurações
$apiControllerNamespace = 'App\\Http\\Controllers\\Api\\';
$apiControllerPath = app_path('Http/Controllers/Api');

// Verificar se o diretório existe
if (!is_dir($apiControllerPath)) {
    mkdir($apiControllerPath, 0755, true);
    echo "Diretório de controllers API criado: $apiControllerPath\n";
}

// Criar o controller base para documentação se não existir
$swaggerControllerPath = $apiControllerPath . '/SwaggerController.php';
if (!file_exists($swaggerControllerPath)) {
    echo "Criando controller base para documentação...\n";
    $swaggerControllerContent = <<<'EOD'
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *     title="API SEPLAG",
 *     version="1.0.0",
 *     description="API para gerenciamento de servidores públicos, incluindo servidores efetivos, temporários, lotações, endereços e fotografias.",
 *     @OA\Contact(
 *         email="suporte@seplag.gov.br",
 *         name="Equipe de Suporte SEPLAG"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="/api",
 *     description="Servidor da API"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * 
 * @OA\Tag(name="Autenticação", description="Endpoints para autenticação e gerenciamento de tokens")
 * @OA\Tag(name="Servidores Efetivos", description="Gerenciamento de servidores efetivos")
 * @OA\Tag(name="Servidores Temporários", description="Gerenciamento de servidores temporários")
 * @OA\Tag(name="Unidades", description="Gerenciamento de unidades administrativas")
 * @OA\Tag(name="Lotações", description="Gerenciamento de lotações de servidores")
 * @OA\Tag(name="Endereços", description="Gerenciamento de endereços")
 * @OA\Tag(name="Fotografias", description="Upload e gerenciamento de fotografias")
 * @OA\Tag(name="Pessoas", description="Gerenciamento de dados pessoais")
 */
class SwaggerController extends Controller
{
    // Este controller serve apenas para documentação
}
EOD;

    file_put_contents($swaggerControllerPath, $swaggerControllerContent);
    echo "Controller base criado com sucesso!\n";
}

// Obter todas as rotas da API
echo "Analisando rotas da API...\n";
$routes = \Illuminate\Support\Facades\Route::getRoutes();
$apiRoutes = [];

foreach ($routes as $route) {
    if (strpos($route->uri, 'api/') === 0 && $route->getActionName() !== 'Closure') {
        $apiRoutes[] = [
            'method' => implode('|', $route->methods),
            'uri' => $route->uri,
            'name' => $route->getName(),
            'action' => $route->getActionName()
        ];
    }
}

// Agrupar rotas por controller
$routesByController = [];
foreach ($apiRoutes as $route) {
    $actionParts = explode('@', $route['action']);
    if (count($actionParts) < 2) continue;
    
    $controller = $actionParts[0];
    $method = $actionParts[1];
    
    if (!isset($routesByController[$controller])) {
        $routesByController[$controller] = [];
    }
    
    $routesByController[$controller][] = [
        'method' => $route['method'],
        'uri' => $route['uri'],
        'name' => $route['name'],
        'action_method' => $method
    ];
}

// Gerar anotações para cada controller
foreach ($routesByController as $controller => $routes) {
    // Verificar se o controller existe
    if (!class_exists($controller)) {
        echo "Controller não encontrado: $controller\n";
        continue;
    }
    
    $reflectionClass = new ReflectionClass($controller);
    $fileName = $reflectionClass->getFileName();
    
    if (!$fileName) {
        echo "Arquivo não encontrado para: $controller\n";
        continue;
    }
    
    echo "Processando controller: $controller\n";
    
    // Determinar a tag com base no nome do controller
    $tag = "API";
    if (strpos($controller, 'ServidorEfetivo') !== false) {
        $tag = "Servidores Efetivos";
    } elseif (strpos($controller, 'ServidorTemporario') !== false) {
        $tag = "Servidores Temporários";
    } elseif (strpos($controller, 'Unidade') !== false) {
        $tag = "Unidades";
    } elseif (strpos($controller, 'Lotacao') !== false) {
        $tag = "Lotações";
    } elseif (strpos($controller, 'Endereco') !== false) {
        $tag = "Endereços";
    } elseif (strpos($controller, 'Fotografia') !== false) {
        $tag = "Fotografias";
    } elseif (strpos($controller, 'Pessoa') !== false) {
        $tag = "Pessoas";
    } elseif (strpos($controller, 'Auth') !== false) {
        $tag = "Autenticação";
    }
    
    // Adicionar anotação de tag ao controller se não existir
    $content = file_get_contents($fileName);
    if (strpos($content, '@OA\Tag') === false) {
        // Usar uma expressão regular mais simples e segura
        $className = basename(str_replace('\\', '/', $controller));
        $pattern = '/class\s+' . preg_quote($className) . '\s+extends\s+Controller/';
        $replacement = "/**\n * @OA\\Tag(\n *     name=\"$tag\",\n *     description=\"Endpoints para gerenciamento de " . strtolower($tag) . "\"\n * )\n */\nclass " . $className . " extends Controller";
        $content = preg_replace($pattern, $replacement, $content);
    }
    
    // Para cada método no controller, adicionar anotações Swagger
    foreach ($routes as $route) {
        $methodName = $route['action_method'];
        $httpMethod = strtolower(explode('|', $route['method'])[0]);
        $uri = str_replace('api/', '', $route['uri']);
        
        // Verificar se o método existe no controller
        if (!$reflectionClass->hasMethod($methodName)) {
            echo "  Método não encontrado no controller: $methodName\n";
            continue;
        }
        
        // Verificar se o método já tem anotações Swagger
        $reflectionMethod = $reflectionClass->getMethod($methodName);
        $docComment = $reflectionMethod->getDocComment();
        
        if ($docComment && strpos($docComment, '@OA\\') !== false) {
            echo "  Anotação já existe para: $methodName\n";
            continue;
        }
        
        // Analisar parâmetros do método
        $parameters = [];
        preg_match_all('/{([^}]+)}/', $uri, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $param) {
                $parameters[] = [
                    'name' => $param,
                    'in' => 'path',
                    'required' => true,
                    'type' => 'integer'
                ];
            }
        }
        
        // Determinar o tipo de retorno com base no nome do método
        $responseContent = '';
        if (in_array($methodName, ['index', 'all', 'list', 'search'])) {
            $responseContent = "array";
        } elseif (in_array($methodName, ['show', 'get', 'find', 'me'])) {
            $responseContent = "object";
        } elseif (in_array($methodName, ['store', 'create', 'add', 'login'])) {
            $responseContent = "object";
        } elseif (in_array($methodName, ['update', 'edit', 'modify', 'refreshToken'])) {
            $responseContent = "object";
        } elseif (in_array($methodName, ['destroy', 'delete', 'remove', 'logout'])) {
            $responseContent = "message";
        }
        
        // Criar anotação Swagger para o método
        $annotation = "    /**\n";
        $annotation .= "     * @OA\\".ucfirst($httpMethod)."(\n";
        $annotation .= "     *     path=\"/$uri\",\n";
        $annotation .= "     *     summary=\"".ucfirst($methodName)." ".strtolower(str_replace('Controller', '', basename(str_replace('\\', '/', $controller))))."\",\n";
        $annotation .= "     *     tags={\"$tag\"},\n";
        
        // Adicionar segurança para todas as rotas exceto login
        if ($uri != 'login') {
            $annotation .= "     *     security={{\"bearerAuth\":{}}},\n";
        }
        
        // Adicionar parâmetros para rotas com parâmetros na URL
        foreach ($parameters as $param) {
            $annotation .= "     *     @OA\\Parameter(\n";
            $annotation .= "     *         name=\"{$param['name']}\",\n";
            $annotation .= "     *         in=\"{$param['in']}\",\n";
            $annotation .= "     *         required=".($param['required'] ? 'true' : 'false').",\n";
            $annotation .= "     *         @OA\\Schema(type=\"{$param['type']}\")\n";
            $annotation .= "     *     ),\n";
        }
        
        // Adicionar corpo da requisição para métodos POST e PUT
        if ($httpMethod == 'post' || $httpMethod == 'put') {
            $annotation .= "     *     @OA\\RequestBody(\n";
            $annotation .= "     *         required=true,\n";
            $annotation .= "     *         @OA\\JsonContent(\n";
            $annotation .= "     *             type=\"object\"\n";
            $annotation .= "     *         )\n";
            $annotation .= "     *     ),\n";
        }
        
        // Adicionar respostas padrão
        $annotation .= "     *     @OA\\Response(\n";
        $annotation .= "     *         response=200,\n";
        $annotation .= "     *         description=\"Operação bem-sucedida\"";
        
        if ($responseContent) {
            $annotation .= ",\n";
            $annotation .= "     *         @OA\\JsonContent(\n";
            
            if ($responseContent == "array") {
                $annotation .= "     *             @OA\\Property(property=\"data\", type=\"array\", @OA\\Items(type=\"object\"))\n";
            } elseif ($responseContent == "object") {
                $annotation .= "     *             type=\"object\"\n";
            } elseif ($responseContent == "message") {
                $annotation .= "     *             @OA\\Property(property=\"message\", type=\"string\")\n";
            }
            
            $annotation .= "     *         )\n";
        } else {
            $annotation .= "\n";
        }
        
        $annotation .= "     *     ),\n";
        
        if ($uri != 'login') {
            $annotation .= "     *     @OA\\Response(\n";
            $annotation .= "     *         response=401,\n";
            $annotation .= "     *         description=\"Não autorizado\"\n";
            $annotation .= "     *     ),\n";
        }
        
        if ($httpMethod == 'post' || $httpMethod == 'put') {
            $annotation .= "     *     @OA\\Response(\n";
            $annotation .= "     *         response=422,\n";
            $annotation .= "     *         description=\"Erro de validação\"\n";
            $annotation .= "     *     ),\n";
        }
        
        if ($httpMethod == 'get' || $httpMethod == 'delete' || $httpMethod == 'put') {
            $annotation .= "     *     @OA\\Response(\n";
            $annotation .= "     *         response=404,\n";
            $annotation .= "     *         description=\"Recurso não encontrado\"\n";
            $annotation .= "     *     )\n";
        } else {
            // Remover a última vírgula
            $annotation = rtrim($annotation, ",\n") . "\n";
        }
        
        $annotation .= "     * )\n";
        $annotation .= "     */\n";
        
        // Procurar o método no conteúdo do arquivo
        $pattern = "/public\s+function\s+$methodName\s*\(/";
        if (preg_match($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            $position = $matches[0][1];
            
            // Verificar se já existe uma anotação
            $beforeMethod = substr($content, 0, $position);
            $lastComment = strrpos($beforeMethod, '*/');
            
            if ($lastComment !== false && $position - $lastComment < 50) {
                // Já existe uma anotação, pular
                echo "  Anotação já existe para: $methodName\n";
                continue;
            }
            
            // Inserir a anotação antes do método
            $content = substr($content, 0, $position) . $annotation . substr($content, $position);
            echo "  Adicionada anotação para: $methodName\n";
        } else {
            echo "  Método não encontrado no arquivo: $methodName\n";
        }
    }
    
    // Salvar o arquivo modificado
    file_put_contents($fileName, $content);
    echo "Arquivo atualizado: $fileName\n";
}

// Gerar a documentação Swagger
echo "Gerando documentação Swagger...\n";
\Illuminate\Support\Facades\Artisan::call('l5-swagger:generate');
echo \Illuminate\Support\Facades\Artisan::output();

echo "\nDocumentação Swagger gerada com sucesso!\n";
echo "Acesse a documentação em: http://localhost:8000/api/documentation\n";
