// ==================== PRUEBAS DE SWITCH-CASE ====================

func main() {
    fmt.Println("=== Prueba 1: Switch simple ===")
    var dia int32 = 3
    
    switch dia {
    case 1:
        fmt.Println("Lunes")
    case 2:
        fmt.Println("Martes")
    case 3:
        fmt.Println("Miércoles")
    case 4:
        fmt.Println("Jueves")
    case 5:
        fmt.Println("Viernes")
    default:
        fmt.Println("Fin de semana")
    }
    
    
    fmt.Println("=== Prueba 2: Switch con default ===")
    var mes int32 = 13
    
    switch mes {
    case 1:
        fmt.Println("Enero")
    case 2:
        fmt.Println("Febrero")
    case 3:
        fmt.Println("Marzo")
    default:
        fmt.Println("Mes inválido")
    }
    
    
    fmt.Println("=== Prueba 3: Switch con strings ===")
    var color string = "rojo"
    
    switch color {
    case "rojo":
        fmt.Println("Color primario: rojo")
    case "azul":
        fmt.Println("Color primario: azul")
    case "amarillo":
        fmt.Println("Color primario: amarillo")
    default:
        fmt.Println("No es un color primario")
    }
    
    
    fmt.Println("=== Prueba 4: Switch con break ===")
    var numero int32 = 2
    
    switch numero {
    case 1:
        fmt.Println("Uno")
    case 2:
        fmt.Println("Dos")
        break
    case 3:
        fmt.Println("Tres")
    default:
        fmt.Println("Otro")
    }
    
    
    fmt.Println("=== Prueba 5: Switch con expresiones booleanas ===")
    var activo bool = true
    
    switch activo {
    case true:
        fmt.Println("Está activo")
    case false:
        fmt.Println("Está inactivo")
    }
    
    
    fmt.Println("=== Fin de pruebas SWITCH ===")
}