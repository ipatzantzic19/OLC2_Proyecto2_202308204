#!/bin/bash

# Script de prueba rápida para verificar todos los archivos ARM64
# Uso: cd Backend && chmod +x test_all_arm64.sh && ./test_all_arm64.sh

RESET='\033[0m'
GREEN='\033[0;32m'
CYAN='\033[0;36m'
YELLOW='\033[1;33m'
BOLD='\033[1m'

echo -e "${BOLD}========== COMPILADOR GOLAMPI → ARM64 - SUITE COMPLETA ==========${RESET}\n"

test_files=(
    "test/test_basic.go"
    "test/test_conditional.go"
    "test/test_loop.go"
    "test/test_array.go"
    "test/test_function.go"
    "test/test_operators.go"
    "test/test_switch.go"
    "test/test_complex.go"
)

success_count=0
failed_count=0

for test_file in "${test_files[@]}"; do
    if [ ! -f "$test_file" ]; then
        echo -e "${YELLOW}⚠️  Saltando: $test_file (no existe)${RESET}"
        continue
    fi
    
    filename=$(basename "$test_file" .go)
    echo -e "${CYAN}📝 Compilando: $filename${RESET}"
    
    # Ejecutar compilación y contar líneas generadas
    output=$(php test/test_compile.php "$test_file" 2>&1)
    
    # Verificar si fue exitoso
    if echo "$output" | grep -q "✅ COMPILACIÓN EXITOSA"; then
        ((success_count++))
        # Contar líneas de assembly
        lines=$(echo "$output" | grep "CÓDIGO ENSAMBLADOR ARM64 COMPLETO" | grep -oP '\(\K[0-9]+(?= líneas)' | head -1)
        echo -e "   ${GREEN}✅ Exitoso${RESET} - $lines líneas de assembly"
    else
        ((failed_count++))
        errors=$(echo "$output" | grep "Total de errores:" | head -1)
        echo -e "   ${YELLOW}⚠️  Con errores${RESET} - $errors"
    fi
    
    echo ""
done

echo -e "${BOLD}========== RESUMEN ==========${RESET}"
echo -e "Exitosos:  ${GREEN}$success_count${RESET}"
echo -e "Fallidos:  ${YELLOW}$failed_count${RESET}"
echo -e "Total:     ${BOLD}$((success_count + failed_count))${RESET}\n"

if [ $failed_count -eq 0 ]; then
    echo -e "${GREEN}✅ Todos los tests con resultado satisfactorio${RESET}"
else
    echo -e "${YELLOW}⚠️  Algunos tests tuvieron problemas${RESET}"
fi

echo ""
echo -e "${BOLD}Nota:${RESET} Para ver el código completo de cada compilación, usa:"
echo "  php test/test_compile.php test/test_<nombre>.go"
