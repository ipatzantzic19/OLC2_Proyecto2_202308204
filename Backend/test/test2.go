// Ejemplo 2: Programa con errores semánticos
func main() {
    var x int32 = 10
    var y int32 = 20
    var z string = "Hola"
    
    // Error: variable no declarada
    fmt.Println("Valor de w:", w)
    
    // Error: redeclaración
    var x int32 = 30
    
    // Error: incompatibilidad de tipos
    var suma int32 = x + z
    
    fmt.Println("Fin del programa")
}