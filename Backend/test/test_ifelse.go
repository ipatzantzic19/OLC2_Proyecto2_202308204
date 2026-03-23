// ==================== PRUEBAS DE IF-ELSE ====================

func main() {
    fmt.Println("=== Prueba 1: IF simple ===")
    var x int32 = 10
    
    if x > 5 {
        fmt.Println("x es mayor que 5")
    }
    
    
    fmt.Println("=== Prueba 2: IF-ELSE ===")
    var edad int32 = 18
    
    if edad >= 18 {
        fmt.Println("Es mayor de edad")
    } else {
        fmt.Println("Es menor de edad")
    }
    
    
    fmt.Println("=== Prueba 3: IF-ELSE-IF ===")
    var nota int32 = 85
    
    if nota >= 90 {
        fmt.Println("Calificación: A")
    } else if nota >= 80 {
        fmt.Println("Calificación: B")
    } else if nota >= 70 {
        fmt.Println("Calificación: C")
    } else {
        fmt.Println("Calificación: F")
    }
    
    
    fmt.Println("=== Prueba 4: IF anidado ===")
    var numero int32 = 15
    
    if numero > 0 {
        fmt.Println("El número es positivo")
        
        if numero > 10 {
            fmt.Println("Y es mayor que 10")
        } else {
            fmt.Println("Y es menor o igual que 10")
        }
    } else {
        fmt.Println("El número no es positivo")
    }
    
    
    fmt.Println("=== Prueba 5: Condiciones complejas ===")
    var a int32 = 5
    var b int32 = 10
    
    if a > 0 && b > 0 {
        fmt.Println("Ambos son positivos")
    }
    
    if a < 10 || b < 5 {
        fmt.Println("Al menos uno es menor que su límite")
    }
    
    
    fmt.Println("=== Fin de pruebas IF-ELSE ===")
}