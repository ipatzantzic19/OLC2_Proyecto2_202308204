// ==================== PRUEBA DE ERRORES EN ASIGNACIONES ====================

// ❌ ERROR: Declaración corta a nivel global (no permitido)
// x := 100

func main() {
    // ========== ERRORES DE ASIGNACIÓN ==========
    
    // ❌ ERROR: Variable no declarada
    w = 50
    fmt.Println("Esto no se debería imprimir")
    
    
    // ========== ERRORES DE COMPATIBILIDAD DE TIPOS ==========
    var num int32 = 10
    
    // ❌ ERROR: Intentar asignar string a int32
    num = "texto"
    
    
    // ========== ERRORES EN DECLARACIÓN CORTA ==========
    var a int32 = 5
    var b int32 = 10
    
    // ❌ ERROR: Todas las variables ya existen (al menos una debe ser nueva)
    a, b := 15, 20
    
    
    // ========== ERRORES EN ASIGNACIONES COMPUESTAS ==========
    var texto string = "Hola"
    
    // ❌ ERROR: No se puede restar strings
    texto -= "a"
    
    
    // ========== ERRORES DE INCOMPATIBILIDAD DE TIPOS ==========
    var entero int32 = 100
    var cadena string = "50"
    
    // ❌ ERROR: No se puede sumar int32 + string
    entero += cadena
    
    
    fmt.Println("Fin del programa con errores")
}