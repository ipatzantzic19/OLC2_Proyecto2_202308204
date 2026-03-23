// ==================== PRUEBA DE ASIGNACIONES ====================

func main() {
    // ========== 1. DECLARACIÓN TRADICIONAL Y ASIGNACIÓN SIMPLE ==========
    var x int32 = 10
    fmt.Println("Valor inicial de x:", x)
    
    x = 20  // ✅ Asignación simple
    fmt.Println("Después de x = 20:", x)
    
    
    // ========== 2. ASIGNACIONES COMPUESTAS ==========
    x += 5   // x = x + 5 → 25
    fmt.Println("Después de x += 5:", x)
    
    x -= 3   // x = x - 3 → 22
    fmt.Println("Después de x -= 3:", x)
    
    x *= 2   // x = x * 2 → 44
    fmt.Println("Después de x *= 2:", x)
    
    x /= 4   // x = x / 4 → 11
    fmt.Println("Después de x /= 4:", x)
    
    
    // ========== 3. DECLARACIÓN CORTA (DENTRO DE FUNCIÓN) ==========
    y := 100  // ✅ Declaración corta (inferencia de tipo)
    fmt.Println("Declaración corta y :=", y)
    
    z := 200  // ✅ Otra variable nueva
    fmt.Println("Declaración corta z :=", z)
    
    
    // ========== 4. DECLARACIÓN CORTA MÚLTIPLE ==========
    a, b := 10, 20  // ✅ Múltiples variables
    fmt.Println("Declaración múltiple a, b:", a, b)
    
    
    // ========== 5. REASIGNACIÓN CON := (al menos una nueva) ==========
    c, a := 30, 40  // ✅ 'c' es nueva, 'a' se reasigna
    fmt.Println("c es nueva, a reasignada:", c, a)
    
    
    // ========== 6. OPERACIONES CON FLOATS ==========
    var precio float32 = 100.5
    fmt.Println("Precio inicial:", precio)
    
    precio += 50.25  // ✅ Suma float
    fmt.Println("Después de precio += 50.25:", precio)
    
    precio *= 2.0  // ✅ Multiplicación float
    fmt.Println("Después de precio *= 2.0:", precio)
    
    
    // ========== 7. STRINGS ==========
    var nombre string = "Hola"
    fmt.Println("Nombre inicial:", nombre)
    
    nombre = "Mundo"  // ✅ Reasignación simple de string
    fmt.Println("Después de nombre = Mundo:", nombre)
    
    
    // ========== 8. SCOPE Y DECLARACIÓN CORTA ==========
    if true {
        local := 999  // ✅ Variable local al bloque if
        fmt.Println("Variable local en if:", local)
    }
}