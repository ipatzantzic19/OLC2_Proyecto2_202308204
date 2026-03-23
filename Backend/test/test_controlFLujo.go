// ==================== PRUEBA COMPLETA DE NUEVA GRAMÁTICA ====================

func main() {
    fmt.Println("=== 1. Declaración corta y asignaciones ===")
    x := 10
    fmt.Println("x inicial:", x)
    
    x += 5
    fmt.Println("x += 5:", x)
    
    x++
    fmt.Println("x++:", x)
    
    x--
    fmt.Println("x--:", x)
    
    
    fmt.Println("=== 2. FOR con declaración corta ===")
    for i := 0; i < 3; i++ {
        fmt.Println("i:", i)
    }
    
    
    fmt.Println("=== 3. FOR con var ===")
    for var j int32 = 0; j < 3; j++ {
        fmt.Println("j:", j)
    }
    
    
    fmt.Println("=== 4. IF-ELSE-IF ===")
    nota := 85
    
    if nota >= 90 {
        fmt.Println("Calificación: A")
    } else if nota >= 80 {
        fmt.Println("Calificación: B")
                var x int32 = 30

    } else if nota >= 70 {
        fmt.Println("Calificación: C")
    } else {
        fmt.Println("Calificación: F")
    }
    
    
    fmt.Println("=== 5. SWITCH con múltiples valores ===")
    dia := 3
    
    switch dia {
    case 1, 2, 3, 4, 5:
        fmt.Println("Día laboral")
    case 6, 7:
        fmt.Println("Fin de semana")
    default:
        fmt.Println("Inválido")
    }
    
    
    fmt.Println("=== 6. Scopes en bloques ===")
    if true {
        y := 200
        fmt.Println("y dentro del if:", y)
        fmt.Println("x dentro del if:", x)
    }
    
    
    fmt.Println("=== 7. Break y Continue ===")
    for k := 0; k < 5; k++ {
        if k == 2 {
            continue
        }
        if k == 4 {
            break
        }
        fmt.Println("k:", k)
    }
    
    
    
    fmt.Println("=== 8. FOR estilo while ===")
    contador := 3
    for contador > 0 {
        fmt.Println("contador:", contador)
        contador--
    }
    
    
    fmt.Println("=== 9. Scope de FOR ===")
    for m := 0; m < 2; m++ {
        n := m * 10
        fmt.Println("m:", m, "n:", n)
    }
    
    
    fmt.Println("=== Fin de pruebas ===")
}