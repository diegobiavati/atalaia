#!/bin/sh

echo "🔍 Iniciando verificações de qualidade..."

# 1. Rodar o Linter (PHPCS)
echo "-----------------------------------"
echo "🎨 Verificando padrão de código (Linter)..."
docker exec -t atalaia-app ./vendor/bin/phpcs
if [ $? -ne 0 ]; then
    echo "❌ Erro de estilo de código. Corrija antes de commitar."
    exit 1
fi

# 2. Rodar Análise Estática (PHPStan)
echo "-----------------------------------"
echo "🧠 Analisando lógica do código (Static Analysis)..."
docker exec -t atalaia-app ./vendor/bin/phpstan analyze
if [ $? -ne 0 ]; then
    echo "❌ Erros de lógica detectados pelo PHPStan."
    exit 1
fi

# 3. Rodar Testes Unitários (PHPUnit)
echo "-----------------------------------"
echo "🧪 Executando testes automatizados..."
docker exec -t atalaia-app ./vendor/bin/phpunit
if [ $? -ne 0 ]; then
    echo "❌ Os testes falharam!"
    exit 1
fi

echo "✅ Tudo limpo! Procedendo com o commit..."