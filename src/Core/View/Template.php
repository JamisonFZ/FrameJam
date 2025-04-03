<?php

namespace FrameJam\Core\View;

class Template
{
    private string $template;
    private array $data = [];
    private array $sections = [];
    private ?string $currentSection = null;
    private string $layout = '';
    private array $stack = [];

    public function __construct(string $template, array $data = [])
    {
        $this->template = $template;
        $this->data = $data;
    }

    public function render(): string
    {
        // Primeiro processa o template principal para coletar as seções
        $this->processTemplate($this->template);
        
        // Se houver um layout, processa ele com as seções coletadas
        if ($this->layout) {
            $layoutContent = file_get_contents($this->layout);
            
            // Processa yield no layout com valores padrão
            $layoutContent = preg_replace_callback('/@yield\([\'"]([^\'"]+)[\'"]\)/', function($matches) {
                $name = $matches[1];
                return $this->sections[$name] ?? '';
            }, $layoutContent);
            
            // Processa yield com valores padrão
            $layoutContent = preg_replace_callback('/@yield\([\'"]([^\'"]+)[\'"],\s*[\'"]([^\'"]+)[\'"]\)/', function($matches) {
                $name = $matches[1];
                $default = $matches[2];
                return $this->sections[$name] ?? $default;
            }, $layoutContent);
            
            // Processa o resto do layout
            $layoutContent = $this->processContent($layoutContent);
            
            // Processa foreach no layout
            $layoutContent = preg_replace_callback('/@foreach\((.*?)\)(.*?)@endforeach/s', function($matches) {
                $expression = $matches[1];
                $content = $matches[2];
                return $this->processLoop($expression, $content);
            }, $layoutContent);
            
            // Processa variáveis no layout
            $layoutContent = preg_replace_callback('/{{(.*?)}}/', function($matches) {
                $expression = trim($matches[1]);
                return $this->evaluateExpression($expression);
            }, $layoutContent);
            
            return $layoutContent;
        }

        return $this->processTemplate($this->template);
    }

    private function processTemplate(string $template): string
    {
        $content = file_get_contents($template);
        
        // Processa extends
        $content = preg_replace_callback('/@extends\([\'"]([^\'"]+)[\'"]\)/', function($matches) {
            $layoutPath = dirname($this->template) . '/' . $matches[1];
            if (!str_ends_with($layoutPath, '.php')) {
                $layoutPath .= '.php';
            }
            
            if (!file_exists($layoutPath)) {
                throw new \Exception("Layout {$matches[1]} not found at {$layoutPath}");
            }
            $this->layout = $layoutPath;
            return '';
        }, $content);

        // Processa section
        $content = preg_replace_callback('/@section\([\'"]([^\'"]+)[\'"]\)(.*?)@endsection/s', function($matches) {
            $name = $matches[1];
            $content = $matches[2];
            $this->sections[$name] = $this->processContent($content);
            return '';
        }, $content);

        // Processa include
        $content = preg_replace_callback('/@include\([\'"]([^\'"]+)[\'"]\)/', function($matches) {
            $includePath = $matches[1];
            return $this->processTemplate($includePath);
        }, $content);

        // Processa if
        $content = preg_replace_callback('/@if\((.*?)\)(.*?)@endif/s', function($matches) {
            $condition = $matches[1];
            $content = $matches[2];
            return $this->evaluateCondition($condition) ? $this->processContent($content) : '';
        }, $content);

        // Processa foreach
        $content = preg_replace_callback('/@foreach\((.*?)\)(.*?)@endforeach/s', function($matches) {
            $expression = $matches[1];
            $content = $matches[2];
            return $this->processLoop($expression, $content);
        }, $content);

        // Processa variáveis
        $content = preg_replace_callback('/{{(.*?)}}/', function($matches) {
            $expression = trim($matches[1]);
            return $this->evaluateExpression($expression);
        }, $content);

        return $content;
    }

    private function processContent(string $content): string
    {
        return $this->processContentWithData($content, $this->data);
    }

    private function processContentWithData(string $content, array $data): string
    {
        // Processa variáveis com os dados fornecidos
        $content = preg_replace_callback('/{{(.*?)}}/', function($matches) use ($data) {
            $expression = trim($matches[1]);
            return $this->evaluateExpressionWithData($expression, $data);
        }, $content);

        return $content;
    }

    private function evaluateCondition(string $condition): bool
    {
        $value = $this->evaluateExpressionWithData($condition, $this->data);
        return (bool) $value;
    }

    private function processLoop(string $expression, string $content): string
    {
        $result = '';
        
        try {
            $items = $this->evaluateExpressionWithData($expression, $this->data);
            
            if (is_array($items)) {
                foreach ($items as $key => $value) {
                    // Cria um escopo local para as variáveis do loop
                    $loopData = array_merge($this->data, [
                        'loop' => [
                            'index' => $key,
                            'value' => $value
                        ],
                        'feature' => $value
                    ]);
                    
                    $result .= $this->processContentWithData($content, $loopData);
                }
            }
        } catch (\Throwable $e) {
            // Se houver erro na avaliação da expressão, retorna string vazia
            return '';
        }

        return $result;
    }

    private function evaluateExpression(string $expression): string
    {
        $value = $this->evaluateExpressionWithData($expression, $this->data);
        return $value !== null ? (string) $value : '';
    }

    private function evaluateExpressionWithData(string $expression, array $data): mixed
    {
        // Cria um escopo seguro para as variáveis
        $safeData = array_merge([
            'feature' => null,
            'loop' => null
        ], $data);
        
        extract($safeData);
        
        try {
            // Adiciona verificações de segurança para acessos a arrays
            $expression = preg_replace_callback(
                '/\$(\w+)\[([^\]]+)\]/',
                function($matches) {
                    $var = $matches[1];
                    $key = $matches[2];
                    return "isset(\$$var) && is_array(\$$var) ? \$$var[$key] : null";
                },
                $expression
            );
            
            $value = eval('return ' . $expression . ';');
            return $value;
        } catch (\Throwable $e) {
            return null;
        }
    }
} 